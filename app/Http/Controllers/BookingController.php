<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CheckoutCalculator;
use App\Services\SellerPaymentService;
use App\Services\WalletService;
use App\Support\BookingMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /** Minimum advance amount as % of total */
    private const MIN_ADVANCE_PERCENT = 20;
    /** Minimum advance amount in Rs. */
    private const MIN_ADVANCE_ABSOLUTE = 100;

    public function __construct(
        protected WalletService $walletService,
        protected CheckoutCalculator $calculator,
        protected SellerPaymentService $sellerPayment
    ) {}

    // ============================================================
    // STEP 1 — Start non-cart booking
    // ============================================================
    public function start(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $product = Product::with(['service', 'branch.seller'])
            ->where('id', $request->product_id)
            ->where('is_active', 1)
            ->firstOrFail();

        $mode = BookingMode::for($product->service?->code);

        if (BookingMode::isCart($mode)) {
            return back()->with('error', 'This product should be added to cart.');
        }

        $this->validateModeMeta($mode, $request);
        $meta = $this->extractMeta($mode, $request);

        Session::put('booking_draft', [
            'product_id' => $product->id,
            'mode'       => $mode,
            'quantity'   => (int) $request->input('quantity', 1),
            'meta'       => $meta,
        ]);

        return redirect()->route('checkout');
    }

    // ============================================================
    // STEP 2 — Show checkout
    // ============================================================
    public function checkout(Request $request)
    {
        // Wallet info for logged-in users
        $user = auth()->user();
        $wallet = null;
        $walletBalance = 0.0;
        $walletHasPin = false;

        if ($user) {
            $wallet        = $this->walletService->getOrCreate($user);
            $walletBalance = (float) $wallet->balance;
            $walletHasPin  = $wallet->hasPin();
        }

        // ✅ B6 — Issue idempotency token for this checkout session
        $bookingToken = Str::random(48);
        Session::put('booking_token', $bookingToken);

        // ============================================================
        // PATH A — Direct booking
        // ============================================================
        if (Session::has('booking_draft')) {
            $draft = Session::get('booking_draft');

            $product = Product::with(['service', 'branch.seller'])
                ->find($draft['product_id']);

            if (!$product) {
                Session::forget('booking_draft');
                return redirect()->route('services.index')
                    ->with('error', 'Product no longer available.');
            }

            $qty   = max(1, (int) ($draft['quantity'] ?? 1));
            $price = (float) $product->price;

            // ✅ CHANGE 1: seller_id thapyo
            $calc = $this->calculator->calculate([[
                'product_id'  => $product->id,
                'service_id'  => $product->service_id,
                'category_id' => $product->service_category_id ?? null,
                'seller_id'   => $product->branch?->seller_id,   // ✅ NAYA
                'price'       => $price,
                'qty'         => $qty,
            ]]);

            $summary = [
                'items' => [[
                    'product'  => $product,
                    'quantity' => $qty,
                    'price'    => $price,
                    'meta'     => $draft['meta'] ?? [],
                ]],
                'subtotal'          => $calc['subtotal'],
                'tax'               => $calc['tax_amount'],
                'charges'           => $calc['charges_amount'],
                'total'             => $calc['grand_total'],
                'tax_breakdown'     => $calc['tax_breakdown'],
                'charges_breakdown' => $calc['charges_breakdown'],
            ];

            $mode     = $draft['mode'];
            $isDirect = true;

            return view('public.checkout', compact(
                'summary', 'mode', 'product', 'isDirect',
                'wallet', 'walletBalance', 'walletHasPin',
                'bookingToken'
            ));
        }

        // ============================================================
        // PATH B — Cart
        // ============================================================
        $items = CartItem::with(['product.service', 'product.branch.seller'])
            ->where('session_id', Session::getId())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // ✅ B4 — Reject multi-seller cart (safer than misattributing funds)
        $sellerIds = $items
            ->map(fn ($i) => $i->product?->branch?->seller_id)
            ->filter()
            ->unique();

        if ($sellerIds->count() > 1) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart contains items from multiple sellers. Please checkout each seller\'s items separately.');
        }

        $firstService = $items->first()->product->service;
        $mode = BookingMode::for($firstService?->code);

        $summaryItems = $items->map(fn($i) => [
            'product'  => $i->product,
            'quantity' => $i->quantity,
            'price'    => (float) $i->unit_price,
            'meta'     => $i->meta ?? [],
        ])->all();

        // ✅ CHANGE 2: seller_id thapyo
        $calc = $this->calculator->calculate($items->map(fn($i) => [
            'product_id'  => $i->product_id,
            'service_id'  => $i->product->service_id,
            'category_id' => $i->product->service_category_id ?? null,
            'seller_id'   => $i->product?->branch?->seller_id,   // ✅ NAYA
            'price'       => (float) $i->unit_price,
            'qty'         => (int) $i->quantity,
        ])->all());

        $summary = [
            'items'             => $summaryItems,
            'subtotal'          => $calc['subtotal'],
            'tax'               => $calc['tax_amount'],
            'charges'           => $calc['charges_amount'],
            'total'             => $calc['grand_total'],
            'tax_breakdown'     => $calc['tax_breakdown'],
            'charges_breakdown' => $calc['charges_breakdown'],
        ];

        $isDirect = false;

        return view('public.checkout', compact(
            'summary', 'mode', 'isDirect',
            'wallet', 'walletBalance', 'walletHasPin',
            'bookingToken'
        ));
    }

    // ============================================================
    // STEP 3 — Store booking with smart payment
    // ============================================================
    public function store(Request $request)
    {
        // ---------- Base validation ----------
        $rules = [
            'customer_name'  => 'required|string|max:150',
            'customer_email' => 'required|email|max:200',
            'customer_phone' => 'required|string|max:30',
            'payment_method' => 'required|in:cod,wallet',
            'notes'          => 'nullable|string|max:1000',
            '_booking_token' => 'required|string|max:64',
        ];

        if ($request->payment_method === 'wallet') {
            $rules['wallet_pin']     = 'required|digits:6';
            $rules['payment_type']   = 'required|in:full,advance';
            $rules['advance_amount'] = 'nullable|numeric|min:0';

            if (!auth()->check()) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'Please login to pay with wallet.',
                ]);
            }
        }

        $request->validate($rules);

        // ---------- B6: Idempotency token check ----------
        $submittedToken = (string) $request->input('_booking_token');
        $sessionToken   = (string) Session::pull('booking_token'); // pull = remove

        if ($sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
            return back()->withInput()->withErrors([
                '_booking_token' => 'This checkout session has expired or was already submitted. Please refresh the checkout page and try again.',
            ]);
        }

        // ---------- Resolve items / branch / seller ----------
        $draft = Session::get('booking_draft');
        $productIdsForStock = []; // [productId => qty]

        if ($draft) {
            $product = Product::with(['service', 'branch.seller'])->find($draft['product_id']);

            if (!$product) {
                Session::forget('booking_draft');
                return redirect()->route('services.index')
                    ->with('error', 'Product no longer available.');
            }

            if (!$product->branch || !$product->branch->seller) {
                return back()->withInput()->with('error', 'Product branch or seller is not configured.');
            }

            $mode = $draft['mode'];
            $qty  = max(1, (int) ($draft['quantity'] ?? 1));

            $items = [[
                'product_id' => $product->id,
                'name'       => $product->name,
                'thumbnail'  => $product->thumbnail,
                'qty'        => $qty,
                'price'      => (float) $product->price,
                'meta'       => $draft['meta'] ?? [],
            ]];

            // ✅ CHANGE 3: seller_id thapyo
            $calc = $this->calculator->calculate([[
                'product_id'  => $product->id,
                'service_id'  => $product->service_id,
                'category_id' => $product->service_category_id ?? null,
                'seller_id'   => $product->branch?->seller_id,   // ✅ NAYA
                'price'       => (float) $product->price,
                'qty'         => $qty,
            ]]);

            $branch      = $product->branch;
            $seller      = $branch->seller;
            $serviceCode = $product->service?->code;
            $meta        = $draft['meta'] ?? [];

            $productIdsForStock[$product->id] = $qty;

        } else {
            $cartItems = CartItem::with(['product.service', 'product.branch.seller'])
                ->where('session_id', Session::getId())
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty.');
            }

            // ✅ B4 — Reject multi-seller cart
            $sellerIds = $cartItems
                ->map(fn ($i) => $i->product?->branch?->seller_id)
                ->filter()
                ->unique();

            if ($sellerIds->count() > 1) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart contains items from multiple sellers. Please checkout each seller\'s items separately.');
            }

            $first = $cartItems->first();

            if (!$first->product?->branch?->seller) {
                return back()->with('error', 'One of the products is no longer available.');
            }

            $mode        = BookingMode::for($first->product->service?->code);
            $branch      = $first->product->branch;
            $seller      = $branch->seller;
            $serviceCode = $first->product->service?->code;

            $items = $cartItems->map(fn($i) => [
                'product_id' => $i->product_id,
                'name'       => $i->product->name,
                'thumbnail'  => $i->product->thumbnail,
                'qty'        => $i->quantity,
                'price'      => (float) $i->unit_price,
                'meta'       => $i->meta ?? [],
            ])->all();

            // ✅ CHANGE 4: seller_id thapyo
            $calc = $this->calculator->calculate($cartItems->map(fn($i) => [
                'product_id'  => $i->product_id,
                'service_id'  => $i->product->service_id,
                'category_id' => $i->product->service_category_id ?? null,
                'seller_id'   => $i->product?->branch?->seller_id,   // ✅ NAYA
                'price'       => (float) $i->unit_price,
                'qty'         => (int) $i->quantity,
            ])->all());

            foreach ($cartItems as $ci) {
                $productIdsForStock[$ci->product_id] =
                    ($productIdsForStock[$ci->product_id] ?? 0) + (int) $ci->quantity;
            }

            $meta = [];
        }

        $total = round((float) $calc['grand_total'], 2);

        // ============================================================
        // PAYMENT RESOLUTION
        // ============================================================
        $paidAmount       = 0.0;
        $dueAmount        = $total;
        $paymentStatus    = 'unpaid';
        $walletTxnId      = null;
        $wallet           = null;
        $payAmount        = 0.0;
        $walletPaidAmount = 0.0;
        $cashCollected    = 0.0;

        if ($total <= 0) {
            $paidAmount    = 0;
            $dueAmount     = 0;
            $paymentStatus = 'paid';
        } elseif ($request->payment_method === 'wallet') {
            $wallet = $this->walletService->getOrCreate(auth()->user());

            if (!$wallet->isActive()) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'Your wallet is not active. Please contact support.',
                ]);
            }

            if (!$wallet->hasPin()) {
                return back()->withInput()->withErrors([
                    'wallet_pin' => 'Please set your wallet PIN before paying. Go to Wallet → Security.',
                ]);
            }

            if (!$wallet->verifyPin($request->wallet_pin)) {
                $this->walletService->audit(
                    auth()->user(),
                    'pin_failed',
                    'critical',
                    'Wrong PIN on booking checkout'
                );
                return back()->withInput()->withErrors([
                    'wallet_pin' => 'Incorrect wallet PIN. Please try again.',
                ]);
            }

            $minAdvance = max(
                self::MIN_ADVANCE_ABSOLUTE,
                round($total * (self::MIN_ADVANCE_PERCENT / 100), 2)
            );

            if ($request->payment_type === 'full') {
                $payAmount = $total;
            } else {
                $payAmount = round((float) $request->advance_amount, 2);

                if ($payAmount < $minAdvance) {
                    return back()->withInput()->withErrors([
                        'advance_amount' => 'Minimum advance is Rs. '
                            . number_format($minAdvance, 2)
                            . ' (' . self::MIN_ADVANCE_PERCENT . '% of total).',
                    ]);
                }

                if ($payAmount >= $total) {
                    return back()->withInput()->withErrors([
                        'advance_amount' => 'Advance must be less than total. Choose "Full Payment" instead.',
                    ]);
                }
            }

            if ($payAmount > (float) $wallet->balance) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'Insufficient wallet balance. Available: Rs. '
                        . number_format($wallet->balance, 2),
                ]);
            }

            $paidAmount       = $payAmount;
            $walletPaidAmount = $payAmount;
            $dueAmount        = round($total - $payAmount, 2);
            $paymentStatus    = $dueAmount > 0 ? 'partial' : 'paid';
        }
        // else: COD → paid=0, due=total, unpaid

        // ============================================================
        // CREATE BOOKING (atomic)
        // ============================================================
        DB::beginTransaction();
        try {
            // ✅ B5 — Decrement stock atomically inside transaction
            foreach ($productIdsForStock as $productId => $qtyNeeded) {
                /** @var Product|null $lockedProduct */
                $lockedProduct = Product::lockForUpdate()->find($productId);

                if (!$lockedProduct) {
                    throw new \RuntimeException("Product #{$productId} no longer exists.");
                }

                if ($lockedProduct->track_stock) {
                    if ((int) $lockedProduct->stock_quantity < $qtyNeeded) {
                        throw new \RuntimeException(
                            "Insufficient stock for \"{$lockedProduct->name}\" — only "
                            . (int) $lockedProduct->stock_quantity . ' left.'
                        );
                    }

                    $lockedProduct->decrement('stock_quantity', $qtyNeeded);

                    $lockedProduct->refresh();
                    if ((int) $lockedProduct->stock_quantity <= 0) {
                        $lockedProduct->update(['in_stock' => 0]);
                    }
                }
            }

            // 1. Debit customer wallet
            if ($request->payment_method === 'wallet' && $paidAmount > 0) {
                $txn = $this->walletService->debit(
                    $wallet,
                    $paidAmount,
                    'booking_payment',
                    'Booking payment via wallet (' . $request->payment_type . ')',
                    null,
                    [
                        'payment_type' => $request->payment_type,
                        'total'        => $total,
                        'due'          => $dueAmount,
                    ]
                );
                $walletTxnId = $txn->id;
            }

            // 2. Create booking
            $booking = Booking::create([
                'reference'             => $this->generateBookingReference(),
                'user_id'               => auth()->id(),
                'customer_name'         => $request->customer_name,
                'customer_email'        => $request->customer_email,
                'customer_phone'        => $request->customer_phone,
                'service_code'          => $serviceCode,
                'booking_mode'          => $mode,
                'seller_id'             => $seller->id,
                'branch_id'             => $branch->id,
                'items'                 => $items,
                'subtotal'              => $calc['subtotal'],
                'tax_amount'            => $calc['tax_amount'],
                'charges_amount'        => $calc['charges_amount'],
                'tax_breakdown'         => $calc['tax_breakdown'],
                'charges_breakdown'     => $calc['charges_breakdown'],
                'total'                 => $total,
                'paid_amount'           => $paidAmount,
                'due_amount'            => $dueAmount,
                'wallet_paid_amount'    => $walletPaidAmount,
                'cash_collected_amount' => $cashCollected,
                'wallet_transaction_id' => $walletTxnId,
                'status'                => 'pending',
                'payment_status'        => $paymentStatus,
                'payment_method'        => $request->payment_method,
                'meta'                  => $meta,
                'notes'                 => $request->notes,
            ]);

            // 3. Credit seller (idempotent, delta-aware)
            if ($paidAmount > 0) {
                $this->sellerPayment->creditSeller($booking);
            }

            DB::commit();

            // ✅ B13 — Session cleanup AFTER commit (draft not lost on failure)
            Session::forget('booking_draft');
            if (!$draft) {
                CartItem::where('session_id', Session::getId())->delete();
            }

            // ✅ B7 — Authorize success page for this session
            Session::put("booking_success.{$booking->reference}", true);

            return redirect()->route('booking.success', $booking->reference);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-issue a fresh token so customer can retry without reload
            Session::put('booking_token', Str::random(48));

            return back()->withInput()->withErrors([
                'booking' => 'Failed to create booking: ' . $e->getMessage(),
            ]);
        }
    }

    // ============================================================
    // SUCCESS PAGE
    // ============================================================
    public function success(string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        $isOwner   = auth()->check() && (int) $booking->user_id === (int) auth()->id();
        $inSession = Session::has("booking_success.{$reference}");

        abort_unless($isOwner || $inSession, 403, 'You are not authorized to view this booking.');

        return view('public.booking-success', compact('booking'));
    }

    // ============================================================
    // MY BOOKINGS — list & filters
    // ============================================================
    public function myBookings(Request $request)
    {
        $query = Booking::where('user_id', auth()->id())
            ->with(['seller', 'branch'])
            ->latest();

        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'processing', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment') && in_array($request->payment, ['unpaid', 'partial', 'paid', 'refunded'])) {
            $query->where('payment_status', $request->payment);
        }

        if ($q = $request->get('q')) {
            $query->where('reference', 'like', "%{$q}%");
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest'      => $query->reorder('created_at'),
            'amount_high' => $query->reorder('total', 'desc'),
            'amount_low'  => $query->reorder('total'),
            default       => $query->reorder('created_at', 'desc'),
        };

        $bookings = $query->paginate(10)->withQueryString();

        $userBookings = Booking::where('user_id', auth()->id());
        $stats = [
            'total'     => (clone $userBookings)->count(),
            'pending'   => (clone $userBookings)->where('status', 'pending')->count(),
            'completed' => (clone $userBookings)->where('status', 'completed')->count(),
            'cancelled' => (clone $userBookings)->where('status', 'cancelled')->count(),
            'due_total' => (clone $userBookings)
                ->whereIn('status', ['pending', 'confirmed', 'processing'])
                ->sum('due_amount'),
        ];

        return view('public.my-bookings', compact('bookings', 'stats'));
    }

    // ============================================================
    // SHOW single booking
    // ============================================================
    public function showBooking(string $reference)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->where('reference', $reference)
            ->with(['seller', 'branch', 'walletTransaction'])
            ->firstOrFail();

        return view('public.booking-detail', compact('booking'));
    }

    // ============================================================
    // CANCEL — wallet-refund cash-owed + seller reverse + stock restore
    // ============================================================
    public function cancelBooking(Request $request, string $reference)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::where('user_id', auth()->id())
                ->where('reference', $reference)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                DB::rollBack();
                return back()->with('error', 'This booking cannot be cancelled at its current status.');
            }

            $walletPortion = (float) ($booking->wallet_paid_amount ?? 0);
            $cashPortion   = (float) ($booking->cash_collected_amount ?? 0);

            if ($walletPortion <= 0 && $cashPortion <= 0 && (float) $booking->paid_amount > 0) {
                if ($booking->payment_method === 'wallet') {
                    $walletPortion = (float) $booking->paid_amount;
                } else {
                    $cashPortion = (float) $booking->paid_amount;
                }
            }

            $refunded       = 0.0;
            $reversed       = 0.0;
            $cashRefundOwed = 0.0;

            if ($walletPortion > 0) {
                $wallet = $this->walletService->getOrCreate(auth()->user());

                $this->walletService->credit(
                    $wallet,
                    $walletPortion,
                    'refund',
                    "Booking cancellation refund (wallet portion) — {$booking->reference}",
                    $booking
                );

                $this->walletService->audit(
                    auth()->user(),
                    'booking_cancelled_refund',
                    'info',
                    "Rs. {$walletPortion} wallet refunded for cancelled booking {$booking->reference}"
                );

                $refunded = $walletPortion;
            }

            if ($cashPortion > 0) {
                $cashRefundOwed = $cashPortion;
                $booking->notes = trim(
                    ($booking->notes ?? '') .
                    "\n\n[CASH REFUND OWED (customer cancelled): Rs. "
                    . number_format($cashPortion, 2) . ']'
                );

                $this->walletService->audit(
                    auth()->user(),
                    'cash_refund_owed',
                    'warning',
                    "Rs. {$cashPortion} cash refund owed for cancelled booking {$booking->reference}"
                );
            }

            $reversed = $this->sellerPayment->reverseSellerPayment($booking, 'cancelled_by_customer');

            $booking->status         = 'cancelled';
            $booking->payment_status = ($refunded > 0 || $cashRefundOwed > 0)
                ? 'refunded'
                : $booking->payment_status;
            $booking->due_amount     = 0;
            $booking->seller_amount  = 0;
            $booking->save();

            foreach (($booking->items ?? []) as $item) {
                $pid = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['qty'] ?? 0);
                if ($pid <= 0 || $qty <= 0) continue;

                $prod = Product::lockForUpdate()->find($pid);
                if ($prod && $prod->track_stock) {
                    $prod->increment('stock_quantity', $qty);
                    $prod->refresh();
                    if ((int) $prod->stock_quantity > 0) {
                        $prod->update(['in_stock' => 1]);
                    }
                }
            }

            DB::commit();

            $msg = 'Booking cancelled successfully.';
            if ($refunded > 0) {
                $msg .= ' Rs. ' . number_format($refunded, 2) . ' refunded to your wallet.';
            }
            if ($cashRefundOwed > 0) {
                $msg .= ' Rs. ' . number_format($cashRefundOwed, 2)
                    . ' cash refund owed — the seller will contact you.';
            }

            return redirect()->route('my-bookings.show', $booking->reference)
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed', [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    // ============================================================
    // INVOICE — Customer-facing
    // ============================================================
    public function invoice(string $reference)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->where('reference', $reference)
            ->with(['seller', 'branch', 'walletTransaction'])
            ->firstOrFail();

        return view('public.invoice', compact('booking'));
    }

    // ============================================================
    // PAY REMAINING DUE
    // ============================================================
    public function payRemaining(Request $request, string $reference)
    {
        $request->validate(['wallet_pin' => 'required|digits:6']);

        $wallet = $this->walletService->getOrCreate(auth()->user());

        if (!$wallet->verifyPin($request->wallet_pin)) {
            $this->walletService->audit(
                auth()->user(),
                'pin_failed',
                'critical',
                'Wrong PIN on remaining payment'
            );
            return back()->withErrors(['wallet_pin' => 'Incorrect wallet PIN.']);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::where('user_id', auth()->id())
                ->where('reference', $reference)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $booking->due_amount <= 0) {
                DB::rollBack();
                return back()->with('error', 'No payment is due for this booking.');
            }

            if ($booking->status === 'cancelled') {
                DB::rollBack();
                return back()->with('error', 'Cannot pay for a cancelled booking.');
            }

            if ((float) ($booking->cash_collected_amount ?? 0) > 0) {
                DB::rollBack();
                return back()->with('error',
                    'Cash has already been partially collected on this booking. Please coordinate with the seller for the remaining amount.');
            }

            $due = round((float) $booking->due_amount, 2);

            if ($due > (float) $wallet->balance) {
                DB::rollBack();
                return back()->withErrors([
                    'wallet_pin' => 'Insufficient wallet balance. Available: Rs. '
                        . number_format($wallet->balance, 2),
                ]);
            }

            $this->walletService->debit(
                $wallet,
                $due,
                'booking_payment',
                "Remaining due payment — {$booking->reference}",
                $booking
            );

            $newWalletPaid = round((float) ($booking->wallet_paid_amount ?? 0) + $due, 2);
            $newPaidAmount = round((float) $booking->paid_amount + $due, 2);

            $booking->update([
                'paid_amount'        => $newPaidAmount,
                'wallet_paid_amount' => $newWalletPaid,
                'due_amount'         => 0,
                'payment_status'     => 'paid',
            ]);

            $this->sellerPayment->creditSeller($booking->fresh());

            $this->walletService->audit(
                auth()->user(),
                'booking_paid_fully',
                'info',
                "Booking {$booking->reference} fully paid (Rs. {$due})"
            );

            DB::commit();

            return redirect()->route('my-bookings.show', $booking->reference)
                ->with('success', 'Payment completed! Rs. ' . number_format($due, 2) . ' paid successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Remaining payment failed', [
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['wallet_pin' => 'Payment failed: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // HELPERS
    // ============================================================

    private function generateBookingReference(): string
    {
        $attempts = 0;
        do {
            $ref = 'BK' . strtoupper(Str::random(6)) . now()->format('ymdHis');
            $exists = Booking::where('reference', $ref)->exists();
            $attempts++;
        } while ($exists && $attempts < 5);

        if ($exists) {
            $ref = 'BK' . strtoupper(Str::random(10)) . now()->format('ymdHisv');
        }

        return $ref;
    }

    private function validateModeMeta(string $mode, Request $request): void
    {
        $rules = match ($mode) {
            BookingMode::BOOKING => [
                'check_in'  => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests'    => 'required|integer|min:1|max:50',
                'rooms'     => 'required|integer|min:1|max:20',
            ],
            BookingMode::APPOINTMENT => [
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|string|max:10',
            ],
            BookingMode::ENROLLMENT => ['batch' => 'required|string|max:50'],
            BookingMode::ROUTE => [
                'pickup'      => 'required|string|max:255',
                'dropoff'     => 'required|string|max:255',
                'travel_date' => 'required|date|after_or_equal:today',
                'passengers'  => 'required|integer|min:1|max:50',
            ],
            BookingMode::SERVICE => [
                'issue'          => 'required|string|max:2000',
                'preferred_date' => 'required|date|after_or_equal:today',
            ],
            BookingMode::QUOTE => [
                'site_address' => 'required|string|max:500',
                'project_size' => 'nullable|string|max:255',
                'requirements' => 'nullable|string|max:2000',
            ],
            BookingMode::PACKAGE => [
                'departure_date' => 'required|date|after_or_equal:today',
                'travelers'      => 'required|integer|min:1|max:50',
                'room_type'      => 'required|string|max:50',
            ],
            default => [],
        };

        if (!empty($rules)) {
            $request->validate($rules);
        }
    }

    private function extractMeta(string $mode, Request $request): array
    {
        return match ($mode) {
            BookingMode::BOOKING => [
                'check_in'  => $request->check_in,
                'check_out' => $request->check_out,
                'guests'    => $request->guests,
                'rooms'     => $request->rooms,
            ],
            BookingMode::APPOINTMENT => [
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
            ],
            BookingMode::ENROLLMENT => ['batch' => $request->batch],
            BookingMode::ROUTE => [
                'pickup'      => $request->pickup,
                'dropoff'     => $request->dropoff,
                'travel_date' => $request->travel_date,
                'passengers'  => $request->passengers,
            ],
            BookingMode::SERVICE => [
                'issue'          => $request->issue,
                'preferred_date' => $request->preferred_date,
            ],
            BookingMode::QUOTE => [
                'site_address' => $request->site_address,
                'project_size' => $request->project_size,
                'requirements' => $request->requirements,
            ],
            BookingMode::PACKAGE => [
                'departure_date' => $request->departure_date,
                'travelers'      => $request->travelers,
                'room_type'      => $request->room_type,
            ],
            default => [],
        };
    }
}
