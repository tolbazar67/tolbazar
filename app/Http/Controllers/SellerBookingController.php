<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SellerBranch;
use App\Models\User;
use App\Services\SellerPaymentService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerBookingController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected SellerPaymentService $sellerPayment
    ) {}

    // ============================================================
    // SELLER MODE GUARDS
    // ============================================================
    private function guardSellerMode(): User
    {
        $user = auth()->user();

        if (!$user) abort(403, 'Unauthenticated.');
        if ($user->active_mode !== 'seller') abort(403, 'You must be in seller mode.');
        if (!$user->isSellerOwner() && !$user->isBranchStaff()) abort(403, 'Only seller accounts can access bookings.');

        return $user;
    }

    private function getSellerId(User $user): int
    {
        if ($user->isSellerOwner()) return $user->sellerProfile->id;
        if ($user->isBranchStaff()) {
            $staff = $user->branchStaff;
            if (!$staff) abort(403, 'Branch assignment missing.');
            return $staff->seller_id;
        }
        abort(403, 'Seller not found.');
    }

    private function baseQuery(User $user)
    {
        $sellerId = $this->getSellerId($user);

        $q = Booking::query()
            ->with(['branch', 'walletTransaction'])
            ->where('seller_id', $sellerId);

        if ($user->isBranchStaff()) {
            $q->where('branch_id', $user->branchStaff->branch_id);
        }

        return $q;
    }

    private function resolveBooking(string $reference): Booking
    {
        $user  = $this->guardSellerMode();
        $query = $this->baseQuery($user);

        return $query->where('reference', $reference)->firstOrFail();
    }

    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $user  = $this->guardSellerMode();
        $query = $this->baseQuery($user);

        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'processing', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment') && in_array($request->payment, ['unpaid', 'partial', 'paid', 'refunded'])) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('service') && $request->service !== '') {
            $query->where('service_code', $request->service);
        }
        if ($request->filled('branch') && $user->isSellerOwner()) {
            $query->where('branch_id', $request->branch);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('reference', 'like', "%{$q}%")
                  ->orWhere('customer_name', 'like', "%{$q}%")
                  ->orWhere('customer_email', 'like', "%{$q}%")
                  ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest'      => $query->reorder('created_at'),
            'amount_high' => $query->reorder('total', 'desc'),
            'amount_low'  => $query->reorder('total'),
            'priority'    => $query->orderByRaw("FIELD(status, 'pending', 'confirmed', 'processing', 'completed', 'cancelled')")->orderByDesc('created_at'),
            default       => $query->reorder('created_at', 'desc'),
        };

        $bookings = $query->paginate(15)->withQueryString();

        $statsQuery = $this->baseQuery($user);

        $stats = [
            'total'      => (clone $statsQuery)->count(),
            'pending'    => (clone $statsQuery)->where('status', 'pending')->count(),
            'confirmed'  => (clone $statsQuery)->where('status', 'confirmed')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'completed'  => (clone $statsQuery)->where('status', 'completed')->count(),
            'cancelled'  => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue_total' => (clone $statsQuery)->where('status', 'completed')->sum('total'),
            'revenue_month' => (clone $statsQuery)->where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'due_total' => (clone $statsQuery)->whereIn('status', ['pending', 'confirmed', 'processing'])->sum('due_amount'),
            'cash_pending_count' => (clone $statsQuery)
                ->where('due_amount', '>', 0)
                ->whereIn('status', ['processing', 'completed'])
                ->count(),
            'cash_pending_amount' => (clone $statsQuery)
                ->where('due_amount', '>', 0)
                ->whereIn('status', ['processing', 'completed'])
                ->sum('due_amount'),
        ];

        $serviceCodes = $this->baseQuery($user)->distinct()->pluck('service_code')->filter()->values();

        $branches = $user->isSellerOwner()
            ? SellerBranch::where('seller_id', $this->getSellerId($user))->where('status', 1)->get()
            : collect();

        return view('seller.bookings.index', compact('bookings', 'stats', 'serviceCodes', 'branches'));
    }

    // ============================================================
    // SHOW — loads paymentCollections for history display
    // ============================================================
    public function show(string $reference)
    {
        $user  = $this->guardSellerMode();
        $query = $this->baseQuery($user);

        $booking = $query->where('reference', $reference)
            ->with(['paymentCollections.collector'])
            ->firstOrFail();

        $customer = User::find($booking->user_id);

        return view('seller.bookings.show', compact('booking', 'customer'));
    }

    // ============================================================
    // ACCEPT
    // ============================================================
    public function accept(string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be accepted.');
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking accepted. Customer has been notified.');
    }

    // ============================================================
    // REJECT
    // ============================================================
    public function reject(Request $request, string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be rejected.');
        }

        $request->validate(['reason' => 'required|string|max:500']);

        DB::beginTransaction();
        try {
            $result = $this->refundAndReverse($booking, 'rejected');

            $booking->update([
                'status'         => 'cancelled',
                'payment_status' => ($result['refunded'] > 0 || $result['cash_refund_owed'] > 0)
                                        ? 'refunded'
                                        : $booking->payment_status,
                'due_amount'     => 0,
                'seller_amount'  => 0,
                'notes'          => trim(($booking->notes ?? '') . "\n\n[Seller rejected] " . $request->reason),
            ]);

            DB::commit();

            $msg = 'Booking rejected.';
            if ($result['refunded'] > 0) {
                $msg .= ' Rs. ' . number_format($result['refunded'], 2) . ' refunded to customer wallet.';
            }
            if ($result['cash_refund_owed'] > 0) {
                $msg .= ' Rs. ' . number_format($result['cash_refund_owed'], 2) . ' CASH refund owed to customer — handle manually.';
            }
            if ($result['reversed'] > 0) {
                $msg .= ' Rs. ' . number_format($result['reversed'], 2) . ' reversed from your wallet.';
            }

            return redirect()->route('seller.bookings.show', $booking->reference)->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Seller reject failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    // ============================================================
    // START PROCESSING
    // ============================================================
    public function startProcessing(string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed bookings can be started.');
        }

        $booking->update(['status' => 'processing']);

        return back()->with('success', 'Booking marked as processing.');
    }

    // ============================================================
    // COMPLETE — auto-marks paid only if due is zero
    // ============================================================
    public function complete(string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if (!in_array($booking->status, ['confirmed', 'processing'])) {
            return back()->with('error', 'Only confirmed or processing bookings can be completed.');
        }

        $update = ['status' => 'completed'];

        if ((float) $booking->due_amount <= 0) {
            $update['payment_status'] = 'paid';
        }

        $booking->update($update);

        $msg = 'Booking marked as completed.';
        if ((float) $booking->due_amount > 0) {
            $msg .= ' Rs. ' . number_format($booking->due_amount, 2) . ' still due — collect from customer.';
        }

        return back()->with('success', $msg);
    }

    // ============================================================
    // COLLECT PAYMENT — cash collection (works for COD AND wallet-advance)
    // ============================================================
    public function collectPayment(Request $request, string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if ((float) $booking->due_amount <= 0) {
            return back()->with('error', 'No payment is due for this booking.');
        }

        // ✅ FIX: COD-only restriction removed — wallet-advance bookings can also collect cash
        // The original check was: if ($booking->payment_method !== 'cod') { ...error }
        // Removed because wallet-advance bookings legitimately have cash due.

        if (!in_array($booking->status, ['processing', 'completed'])) {
            return back()->with('error', 'Mark booking as "Processing" or "Completed" first, then collect payment.');
        }

        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . (float) $booking->due_amount,
            'reference_note' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $collector  = auth()->user();
            $collection = $this->sellerPayment->recordCollection(
                $booking,
                $collector,
                'cash',
                (float) $request->amount,
                $request->reference_note,
                $request->notes
            );

            $this->walletService->audit(
                $collector,
                'cash_collected',
                'info',
                "Rs. {$collection->amount} cash collected for booking {$booking->reference}"
            );

            DB::commit();

            $msg = 'Cash Rs. ' . number_format($collection->amount, 2) . ' recorded.';
            $fresh = $booking->fresh();
            if ((float) $fresh->due_amount <= 0) {
                $msg .= ' Booking is now FULLY PAID.';
            } else {
                $msg .= ' Remaining due: Rs. ' . number_format($fresh->due_amount, 2);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Cash collection failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
            return back()->with('error', 'Collection failed: ' . $e->getMessage());
        }
    }

        // ============================================================
    // INVOICE — Seller-facing
    // ============================================================
    public function invoice(string $reference)
    {
        $user  = $this->guardSellerMode();
        $query = $this->baseQuery($user);

        $booking = $query->where('reference', $reference)
            ->with(['paymentCollections.collector', 'walletTransaction'])
            ->firstOrFail();

        $customer = User::find($booking->user_id);

        return view('seller.invoice', compact('booking', 'customer'));
    }

    // ============================================================
    // CANCEL
    // ============================================================
    public function cancel(Request $request, string $reference)
    {
        $booking = $this->resolveBooking($reference);

        if (!in_array($booking->status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'This booking cannot be cancelled at its current status.');
        }

        $request->validate(['reason' => 'required|string|max:500']);

        DB::beginTransaction();
        try {
            $result = $this->refundAndReverse($booking, 'cancelled');

            $booking->update([
                'status'         => 'cancelled',
                'payment_status' => ($result['refunded'] > 0 || $result['cash_refund_owed'] > 0)
                                        ? 'refunded'
                                        : $booking->payment_status,
                'due_amount'     => 0,
                'seller_amount'  => 0,
                'notes'          => trim(($booking->notes ?? '') . "\n\n[Seller cancelled] " . $request->reason),
            ]);

            DB::commit();

            $msg = 'Booking cancelled.';
            if ($result['refunded'] > 0) {
                $msg .= ' Rs. ' . number_format($result['refunded'], 2) . ' refunded to customer wallet.';
            }
            if ($result['cash_refund_owed'] > 0) {
                $msg .= ' Rs. ' . number_format($result['cash_refund_owed'], 2) . ' CASH refund owed to customer — handle manually.';
            }
            if ($result['reversed'] > 0) {
                $msg .= ' Rs. ' . number_format($result['reversed'], 2) . ' reversed from your wallet.';
            }

            return redirect()->route('seller.bookings.show', $booking->reference)->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Seller cancel failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    // ============================================================
    // HELPER — Refund customer + reverse seller atomically
    // ============================================================
    /**
     * Handles BOTH wallet-paid and cash-collected amounts correctly:
     *
     *  - Wallet portion → refunded automatically to customer's wallet
     *  - Cash portion   → flagged as "cash refund owed" in notes + audit log
     *                     (physical cash cannot be auto-refunded)
     *  - Seller credit  → reversed from seller's wallet
     *
     * Legacy fallback: if wallet_paid_amount/cash_collected_amount were never
     * set (bookings created before tracking was added), the older single-field
     * paid_amount is used based on the original payment_method.
     */
    private function refundAndReverse(Booking $booking, string $reason): array
    {
        $walletPaid = (float) ($booking->wallet_paid_amount ?? 0);
        $cashPaid   = (float) ($booking->cash_collected_amount ?? 0);

        // Legacy fallback — old bookings only have paid_amount
        if ($walletPaid <= 0 && $cashPaid <= 0 && (float) $booking->paid_amount > 0) {
            if ($booking->payment_method === 'wallet') {
                $walletPaid = (float) $booking->paid_amount;
            } else {
                $cashPaid = (float) $booking->paid_amount;
            }
        }

        $refunded = 0.0;
        $reversed = 0.0;
        $cashRefundOwed = 0.0;

        $customer = User::find($booking->user_id);

        // ---------- 1. Refund wallet portion ----------
        if ($walletPaid > 0 && $customer) {
            $wallet = $this->walletService->getOrCreate($customer);

            $this->walletService->credit(
                $wallet,
                $walletPaid,
                'refund',
                "Booking {$reason} refund (wallet portion) — {$booking->reference}",
                $booking
            );

            $this->walletService->audit(
                $customer,
                'booking_refund',
                'info',
                "Rs. {$walletPaid} wallet refunded ({$reason}) for booking {$booking->reference}"
            );

            $refunded = $walletPaid;
        }

        // ---------- 2. Flag cash refund as owed ----------
        if ($cashPaid > 0) {
            $cashRefundOwed = $cashPaid;

            $booking->notes = trim(
                ($booking->notes ?? '') .
                "\n\n[CASH REFUND OWED: Rs. " . number_format($cashPaid, 2) . " to customer — {$reason}]"
            );
            $booking->save();

            $this->walletService->audit(
                $customer,
                'cash_refund_owed',
                'warning',
                "Rs. {$cashPaid} cash refund owed ({$reason}) for booking {$booking->reference}"
            );
        }

        // ---------- 3. Reverse seller credit ----------
        $reversed = $this->sellerPayment->reverseSellerPayment($booking, $reason);

        return [
            'refunded'         => $refunded,
            'reversed'         => $reversed,
            'cash_refund_owed' => $cashRefundOwed,
        ];
    }
}
