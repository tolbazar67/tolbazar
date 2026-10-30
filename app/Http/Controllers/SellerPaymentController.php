<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaymentCollection;
use App\Models\SellerBranch;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class SellerPaymentController extends Controller
{
    private function guardSellerMode(): User
    {
        $user = auth()->user();
        if (!$user) abort(403);
        if ($user->active_mode !== 'seller') abort(403);
        if (!$user->isSellerOwner() && !$user->isBranchStaff()) abort(403);
        return $user;
    }

    private function getSellerId(User $user): int
    {
        if ($user->isSellerOwner()) return $user->sellerProfile->id;
        if ($user->isBranchStaff()) return $user->branchStaff->seller_id;
        abort(403);
    }

    private function getSellerUser(User $user): ?User
    {
        return $user->isSellerOwner()
            ? $user
            : User::find($user->branchStaff->seller->user_id ?? 0);
    }

    // ============================================================
    // PAYMENTS RECEIVED — wallet transactions + COD summary
    // ============================================================
    public function index(Request $request)
    {
        $user       = $this->guardSellerMode();
        $sellerId   = $this->getSellerId($user);
        $sellerUser = $this->getSellerUser($user);
        $wallet     = $sellerUser?->wallet;

        // ---------- Wallet transactions (includes commission entries) ----------
        $query = WalletTransaction::query()
            ->where('wallet_id', $wallet?->id)
            ->whereIn('category', [
                'booking_received',      // Gross credit
                'commission_deducted',   // Commission deduction
                'booking_reversal',      // Full reversal
                'commission_reversal',   // Commission returned
            ])
            ->latest();

        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhere('transaction_id', 'like', "%{$q}%");
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Enrich with booking
        $bookingRefs = $transactions->getCollection()
            ->filter(fn($t) => !empty($t->meta['booking_reference']))
            ->pluck('meta')
            ->pluck('booking_reference')
            ->filter()
            ->unique()
            ->values();

        $bookingsMap = Booking::whereIn('reference', $bookingRefs)
            ->get()
            ->keyBy('reference');

        $transactions->getCollection()->transform(function ($txn) use ($bookingsMap) {
            $ref = $txn->meta['booking_reference'] ?? null;
            $txn->booking = $ref ? ($bookingsMap[$ref] ?? null) : null;
            return $txn;
        });

        // ---------- Aggregate Stats from Bookings (authoritative) ----------
        $statsBookings = Booking::where('seller_id', $sellerId);

        if ($user->isBranchStaff()) {
            $statsBookings->where('branch_id', $user->branchStaff->branch_id);
        }

        $grossTotal      = (float) (clone $statsBookings)->sum('seller_gross_amount');
        $commissionTotal = (float) (clone $statsBookings)->sum('seller_commission_amount');
        $netTotal        = (float) (clone $statsBookings)->sum('seller_amount');
        $countReceived   = (clone $statsBookings)->where('seller_gross_amount', '>', 0)->count();

        $monthBookings = (clone $statsBookings)
            ->where('seller_gross_amount', '>', 0)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);

        $monthGross      = (float) (clone $monthBookings)->sum('seller_gross_amount');
        $monthCommission = (float) (clone $monthBookings)->sum('seller_commission_amount');
        $monthNet        = (float) (clone $monthBookings)->sum('seller_amount');

        $reverseTotal = (float) WalletTransaction::where('wallet_id', $wallet?->id)
            ->where('category', 'booking_reversal')
            ->sum('amount');

        // ---------- COD / Cash Stats ----------
        $codBase = Booking::where('seller_id', $sellerId)
            ->where('payment_method', 'cod')
            ->where('due_amount', '>', 0)
            ->whereNotIn('status', ['cancelled']);

        if ($user->isBranchStaff()) {
            $codBase->where('branch_id', $user->branchStaff->branch_id);
        }

        $cashCollectedBase = PaymentCollection::where('method', 'cash')
            ->whereHas('booking', function ($q) use ($sellerId, $user) {
                $q->where('seller_id', $sellerId);
                if ($user->isBranchStaff()) {
                    $q->where('branch_id', $user->branchStaff->branch_id);
                }
            });

        $codStats = [
            'pending_count'        => (clone $codBase)->count(),
            'pending_amount'       => (clone $codBase)->sum('due_amount'),
            'cash_collected_total' => (clone $cashCollectedBase)->sum('amount'),
            'cash_collected_today' => (clone $cashCollectedBase)->whereDate('created_at', today())->sum('amount'),
        ];

        $stats = [
            'gross_total'      => $grossTotal,
            'commission_total' => $commissionTotal,
            'net_total'        => $netTotal,
            'reversed_total'   => $reverseTotal,
            'count_received'   => $countReceived,
            'month_gross'      => $monthGross,
            'month_commission' => $monthCommission,
            'month_net'        => $monthNet,
            'cod'              => $codStats,
        ];

        return view('seller.payments.index', compact('wallet', 'transactions', 'stats'));
    }

    // ============================================================
    // PRODUCTS
    // ============================================================
    public function products(Request $request)
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        $query = Booking::query()
            ->where('seller_id', $sellerId)
            ->where('seller_gross_amount', '>', 0)
            ->with(['branch']);

        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branchStaff->branch_id);
        }

        if ($request->filled('from'))    $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))      $query->whereDate('created_at', '<=', $request->to);
        if ($request->filled('service')) $query->where('service_code', $request->service);

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'advance') {
                $query->where('payment_status', 'partial');
            } elseif ($request->payment_type === 'full') {
                $query->where('payment_status', 'paid');
            }
        }

        $bookings = $query->get();

        $productStats = [];

        foreach ($bookings as $b) {
            $bookingGross = (float) $b->seller_gross_amount;
            $bookingNet   = (float) $b->seller_amount;
            $bookingComm  = (float) $b->seller_commission_amount;

            if ($bookingGross <= 0) continue;

            $isAdvance = $b->payment_status === 'partial';

            foreach ($b->items as $item) {
                $pid       = $item['product_id'] ?? 0;
                $name      = $item['name'] ?? 'Unknown';
                $qty       = (int) ($item['qty'] ?? 1);
                $itemGross = ((float) ($item['price'] ?? 0)) * $qty;

                $ratio    = $bookingGross > 0 ? $itemGross / $bookingGross : 0;
                $itemNet  = round($bookingNet * $ratio, 2);
                $itemComm = round($bookingComm * $ratio, 2);

                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'product_id'     => $pid,
                        'name'           => $name,
                        'thumbnail'      => $item['thumbnail'] ?? null,
                        'qty_sold'       => 0,
                        'gross_revenue'  => 0.0,
                        'commission'     => 0.0,
                        'net_revenue'    => 0.0,
                        'orders'         => 0,
                        'advance_orders' => 0,
                        'full_orders'    => 0,
                        'advance_amount' => 0.0,
                        'full_amount'    => 0.0,
                    ];
                }

                $productStats[$pid]['qty_sold']      += $qty;
                $productStats[$pid]['gross_revenue'] += $itemGross;
                $productStats[$pid]['commission']    += $itemComm;
                $productStats[$pid]['net_revenue']   += $itemNet;
                $productStats[$pid]['orders']        += 1;

                if ($isAdvance) {
                    $productStats[$pid]['advance_orders'] += 1;
                    $productStats[$pid]['advance_amount'] += $itemGross;
                } else {
                    $productStats[$pid]['full_orders'] += 1;
                    $productStats[$pid]['full_amount'] += $itemGross;
                }
            }
        }

        uasort($productStats, fn($a, $b) => $b['net_revenue'] <=> $a['net_revenue']);

        $stats = [
            'total_products'   => count($productStats),
            'total_orders'     => $bookings->count(),
            'gross_revenue'    => $bookings->sum('seller_gross_amount'),
            'commission_total' => $bookings->sum('seller_commission_amount'),
            'net_revenue'      => $bookings->sum('seller_amount'),
            'advance_orders'   => $bookings->where('payment_status', 'partial')->count(),
            'full_orders'      => $bookings->where('payment_status', 'paid')->count(),
            'advance_amount'   => $bookings->where('payment_status', 'partial')->sum('seller_gross_amount'),
            'full_amount'      => $bookings->where('payment_status', 'paid')->sum('seller_gross_amount'),
        ];

        $serviceCodes = Booking::where('seller_id', $sellerId)
            ->where('seller_gross_amount', '>', 0)
            ->distinct()->pluck('service_code')->filter()->values();

        return view('seller.payments.products', compact('productStats', 'stats', 'serviceCodes'));
    }

    // ============================================================
    // COLLECTIONS — cash collection history
    // ============================================================
    public function collections(Request $request)
    {
        $user       = $this->guardSellerMode();
        $sellerId   = $this->getSellerId($user);

        $query = PaymentCollection::query()
            ->where('method', 'cash')
            ->whereHas('booking', function ($q) use ($sellerId, $user) {
                $q->where('seller_id', $sellerId);
                if ($user->isBranchStaff()) {
                    $q->where('branch_id', $user->branchStaff->branch_id);
                }
            })
            ->with(['booking', 'collector'])
            ->latest();

        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('reference', 'like', "%{$q}%")
                  ->orWhere('reference_note', 'like', "%{$q}%")
                  ->orWhereHas('booking', fn($b) => $b->where('reference', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%"));
            });
        }

        $collections = $query->paginate(20)->withQueryString();

        // ✅ Branch-scope fixed
        $baseStats = PaymentCollection::where('method', 'cash')
            ->whereHas('booking', function ($q) use ($sellerId, $user) {
                $q->where('seller_id', $sellerId);
                if ($user->isBranchStaff()) {
                    $q->where('branch_id', $user->branchStaff->branch_id);
                }
            });

        $stats = [
            'total_collected' => (clone $baseStats)->sum('amount'),
            'today_collected' => (clone $baseStats)->whereDate('created_at', today())->sum('amount'),
            'month_collected' => (clone $baseStats)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'count'           => (clone $baseStats)->count(),
        ];

        return view('seller.payments.collections', compact('collections', 'stats'));
    }

    // ============================================================
    // PENDING COD
    // ============================================================
    public function pendingCod(Request $request)
    {
        $user     = $this->guardSellerMode();
        $sellerId = $this->getSellerId($user);

        $query = Booking::query()
            ->where('seller_id', $sellerId)
            ->where('payment_method', 'cod')
            ->where('due_amount', '>', 0)
            ->whereNotIn('status', ['cancelled'])
            ->with(['branch'])
            ->latest();

        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branchStaff->branch_id);
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'processing', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('reference', 'like', "%{$q}%")
                  ->orWhere('customer_name', 'like', "%{$q}%")
                  ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        $pending = $query->paginate(20)->withQueryString();

        $totalQuery = Booking::where('seller_id', $sellerId)
            ->where('payment_method', 'cod')
            ->where('due_amount', '>', 0)
            ->whereNotIn('status', ['cancelled']);

        if ($user->isBranchStaff()) {
            $totalQuery->where('branch_id', $user->branchStaff->branch_id);
        }

        $totalDue = $totalQuery->sum('due_amount');

        $statusCounts = [
            'pending'    => (clone $totalQuery)->where('status', 'pending')->count(),
            'confirmed'  => (clone $totalQuery)->where('status', 'confirmed')->count(),
            'processing' => (clone $totalQuery)->where('status', 'processing')->count(),
            'completed'  => (clone $totalQuery)->where('status', 'completed')->count(),
        ];

        return view('seller.payments.pending-cod', compact('pending', 'totalDue', 'statusCounts'));
    }
}
