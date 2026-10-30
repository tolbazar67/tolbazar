<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PartnerCommissionRule;
use App\Models\PaymentCollection;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SellerPaymentService
{
    public function __construct(protected WalletService $walletService) {}

    /* =============================================================
     |  COMMISSION SPLIT — unchanged
     | ============================================================= */

    public function calculateSplit(Booking $booking): array
    {
        $gross = round((float) $booking->paid_amount, 2);
        if ($gross <= 0) {
            return ['gross' => 0.0, 'commission' => 0.0, 'seller_amount' => 0.0];
        }

        $commission = 0.0;
        $rule = $this->findCommissionRule($booking);

        if ($rule) {
            $type = strtolower((string) $rule->commission_type);
            $rate = (float) $rule->rate;

            if ($type === 'fixed') {
                $commission = $rate;
            } elseif ($type === 'percentage') {
                $commission = round($gross * ($rate / 100), 2);
            }

            if ($rule->min_amount !== null && $commission < (float) $rule->min_amount) {
                $commission = (float) $rule->min_amount;
            }
            if ($rule->max_amount !== null && $commission > (float) $rule->max_amount) {
                $commission = (float) $rule->max_amount;
            }
            if ($commission > $gross) {
                $commission = $gross;
            }
        }

        return [
            'gross'         => $gross,
            'commission'    => round($commission, 2),
            'seller_amount' => max(0, round($gross - $commission, 2)),
        ];
    }

    /* =============================================================
     |  CREDIT SELLER — NEW PROFESSIONAL FLOW
     |
     |  1. Credit FULL gross to seller wallet
     |  2. Debit commission from seller wallet
     |  3. Credit commission to commission account
     |
     |  Seller transaction history shows:
     |    +Rs.1000  (Booking payment received)
     |    -Rs.100   (Commission on booking)
     |
     |  Commission account sees:
     |    +Rs.100   (Commission from booking X)
     | ============================================================= */

    public function creditSeller(Booking $booking): array
    {
        $totalPaid = round((float) $booking->paid_amount, 2);
        if ($totalPaid <= 0) {
            return ['gross' => null, 'commission' => null];
        }

        $sellerUser = $this->resolveSellerUser($booking);
        if (!$sellerUser) {
            Log::warning('Seller user not found for booking', ['booking_id' => $booking->id]);
            return ['gross' => null, 'commission' => null];
        }

        // Cumulative split as of CURRENT paid_amount
        $split = $this->calculateSplit($booking);
        $totalGross      = $split['gross'];
        $totalCommission = $split['commission'];
        $totalNet        = $split['seller_amount'];

        // Snapshot already credited
        $alreadyGross      = round((float) ($booking->seller_gross_amount ?? 0), 2);
        $alreadyCommission = round((float) ($booking->seller_commission_amount ?? 0), 2);

        $deltaGross      = round($totalGross - $alreadyGross, 2);
        $deltaCommission = round($totalCommission - $alreadyCommission, 2);

        if ($deltaGross <= 0 && $deltaCommission <= 0) {
            return ['gross' => null, 'commission' => null];
        }

        $sellerWallet = $this->walletService->getOrCreate($sellerUser);
        $customer     = $booking->user_id ? User::find($booking->user_id) : null;
        $commissionUser = $this->resolveCommissionUser();

        // ---------- 1. Credit FULL gross to seller ----------
        $grossTxn = null;
        if ($deltaGross > 0) {
            $grossTxn = $this->walletService->credit(
                $sellerWallet,
                $deltaGross,
                'booking_received',
                "Booking payment received — {$booking->reference}",
                $booking,
                [
                    'payment_role'      => 'seller_gross_credit',
                    'from_user_id'      => $customer?->id,
                    'from_user_name'    => $customer?->name ?? $booking->customer_name,
                    'from_user_email'   => $customer?->email ?? $booking->customer_email,
                    'booking_reference' => $booking->reference,
                    'delta_gross'       => $deltaGross,
                    'total_paid'        => $totalPaid,
                    'total_gross'       => $totalGross,
                ]
            );
        }

        // ---------- 2 & 3. Commission split ----------
        $commissionTxn = null;
        if ($deltaCommission > 0 && $commissionUser && $commissionUser->id !== $sellerUser->id) {
            // 2. Debit commission from seller
            $commissionTxn = $this->walletService->debit(
                $sellerWallet,
                $deltaCommission,
                'commission_deducted',
                "Commission on booking — {$booking->reference}",
                $booking,
                [
                    'payment_role'       => 'seller_commission_deduction',
                    'to_commission_user' => $commissionUser->id,
                    'booking_reference'  => $booking->reference,
                    'delta_commission'   => $deltaCommission,
                    'total_commission'   => $totalCommission,
                ]
            );

            // 3. Credit commission to platform account
            $commissionWallet = $this->walletService->getOrCreate($commissionUser);

            $this->walletService->credit(
                $commissionWallet,
                $deltaCommission,
                'commission_received',
                "Platform commission from booking — {$booking->reference}",
                $booking,
                [
                    'payment_role'      => 'platform_commission_credit',
                    'from_seller_id'    => $sellerUser->id,
                    'from_seller_name'  => $sellerUser->name,
                    'from_seller_email' => $sellerUser->email,
                    'booking_reference' => $booking->reference,
                    'delta_commission'  => $deltaCommission,
                    'total_commission'  => $totalCommission,
                ]
            );

            $this->walletService->audit(
                $sellerUser,
                'commission_deducted',
                'info',
                "Commission Rs. {$deltaCommission} deducted from seller for booking {$booking->reference}"
            );
        } elseif ($deltaCommission > 0 && $commissionUser && $commissionUser->id === $sellerUser->id) {
            // Edge case: seller IS the commission account (unlikely) — skip transfer
            Log::warning('Seller is also commission account — skipping commission transfer', [
                'seller_id' => $sellerUser->id,
                'booking_id' => $booking->id,
            ]);
        } elseif ($deltaCommission > 0) {
            // No commission account configured
            Log::critical('Commission account not configured — commission not transferred', [
                'booking_id' => $booking->id,
                'amount' => $deltaCommission,
            ]);
        }

        // ---------- Update booking snapshot ----------
        $booking->update([
            'seller_amount'            => $totalNet,
            'commission_amount'        => $totalCommission,
            'seller_gross_amount'      => $totalGross,
            'seller_commission_amount' => $totalCommission,
            'seller_transaction_id'    => $grossTxn?->id ?? $booking->seller_transaction_id,
        ]);

        return ['gross' => $grossTxn, 'commission' => $commissionTxn];
    }

    /* =============================================================
     |  REVERSE — undo everything (gross + commission)
     | ============================================================= */

    public function reverseSellerPayment(Booking $booking, string $reason): float
    {
        $creditedGross      = round((float) ($booking->seller_gross_amount ?? 0), 2);
        $creditedCommission = round((float) ($booking->seller_commission_amount ?? 0), 2);

        if ($creditedGross <= 0 && $creditedCommission <= 0) {
            return 0;
        }

        $sellerUser = $this->resolveSellerUser($booking);
        if (!$sellerUser) {
            return 0;
        }

        $sellerWallet = $this->walletService->getOrCreate($sellerUser);
        $commissionUser = $this->resolveCommissionUser();

        try {
            // 1. Reverse the gross credit
            if ($creditedGross > 0) {
                $this->walletService->debit(
                    $sellerWallet,
                    $creditedGross,
                    'booking_reversal',
                    "Booking {$reason} — payment reversal — {$booking->reference}",
                    $booking,
                    [
                        'payment_role'      => 'seller_gross_reversal',
                        'reason'            => $reason,
                        'booking_reference' => $booking->reference,
                    ]
                );
            }

            // 2. Reverse commission: credit back to seller
            if ($creditedCommission > 0) {
                $this->walletService->credit(
                    $sellerWallet,
                    $creditedCommission,
                    'commission_reversal',
                    "Commission reversal — {$booking->reference}",
                    $booking,
                    [
                        'payment_role'      => 'seller_commission_reversal',
                        'reason'            => $reason,
                        'booking_reference' => $booking->reference,
                    ]
                );

                // 3. Debit commission account (reverse the credit)
                if ($commissionUser && $commissionUser->id !== $sellerUser->id) {
                    $commissionWallet = $this->walletService->getOrCreate($commissionUser);
                    $this->walletService->debit(
                        $commissionWallet,
                        $creditedCommission,
                        'commission_reversal',
                        "Commission reversal from booking — {$booking->reference}",
                        $booking,
                        [
                            'payment_role'      => 'platform_commission_reversal',
                            'reason'            => $reason,
                            'from_seller_id'    => $sellerUser->id,
                            'booking_reference' => $booking->reference,
                        ]
                    );
                }
            }

            // 4. Reverse cash collector debts (if any non-seller collected cash)
            foreach ($booking->paymentCollections()->where('method', 'cash')->get() as $col) {
                $collector = User::find($col->collected_by);
                if (!$collector) continue;
                if ($this->isSellerOrStaff($booking, $collector)) continue;

                $collectorWallet = $this->walletService->getOrCreate($collector);

                try {
                    $this->walletService->credit(
                        $collectorWallet,
                        (float) $col->amount,
                        'cash_handover_reversal',
                        "Cash debt reversed (booking {$reason}) — {$booking->reference}",
                        $booking,
                        [
                            'payment_role'      => 'collector_debt_reversal',
                            'collection_id'     => $col->id,
                            'booking_reference' => $booking->reference,
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::warning('Failed to reverse collector debt', [
                        'collector_id' => $collector->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // 5. Zero the snapshots
            $booking->update([
                'seller_amount'            => 0,
                'commission_amount'        => 0,
                'seller_gross_amount'      => 0,
                'seller_commission_amount' => 0,
                'seller_transaction_id'    => null,
            ]);

            $this->walletService->audit(
                $sellerUser,
                'seller_payment_reversed',
                'warning',
                "Reversal of Rs. {$creditedGross} for booking {$booking->reference} ({$reason})"
            );

            return $creditedGross;
        } catch (\Throwable $e) {
            Log::error('Seller reversal failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /* =============================================================
     |  COLLECT PAYMENT — cash flow with collector debt
     | ============================================================= */

    public function recordCollection(
        Booking $booking,
        User $collector,
        string $method,
        float $amount,
        ?string $referenceNote = null,
        ?string $notes = null
    ): PaymentCollection {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Collection amount must be positive.');
        }

        $remainingDue = round((float) $booking->due_amount, 2);
        if ($amount > $remainingDue + 0.01) {
            throw new \RuntimeException(
                "Collection amount (Rs. {$amount}) exceeds due (Rs. {$remainingDue})."
            );
        }

        $isCash = $method === 'cash';
        $collectorIsSeller = $this->isSellerOrStaff($booking, $collector);

        // ---------- 1. Cash collector debt (if external user) ----------
        if ($isCash && !$collectorIsSeller) {
            $collectorWallet = $this->walletService->getOrCreate($collector);

            try {
                $this->walletService->debitAllowNegative(
                    $collectorWallet,
                    $amount,
                    'cash_in_hand',
                    "Cash collected from customer — booking {$booking->reference} — owed to system",
                    $booking,
                    [
                        'payment_role'      => 'cash_collector_debt',
                        'collected_for_seller_id' => $booking->seller_id,
                        'booking_reference' => $booking->reference,
                        'customer_name'     => $booking->customer_name,
                    ]
                );

                $this->walletService->audit(
                    $collector,
                    'cash_collection_debt',
                    'info',
                    "Rs. {$amount} cash collected (owed to system) for booking {$booking->reference}"
                );
            } catch (\Throwable $e) {
                Log::error('Failed to record collector debt', [
                    'collector_id' => $collector->id,
                    'error' => $e->getMessage(),
                ]);
                throw new \RuntimeException(
                    'Could not record cash collection debt: ' . $e->getMessage()
                );
            }
        }

        // ---------- 2. Update booking payment tracking ----------
        $newPaidAmount    = round((float) $booking->paid_amount + $amount, 2);
        $newDueAmount     = round(max(0, (float) $booking->due_amount - $amount), 2);
        $newPaymentStatus = $newDueAmount <= 0 ? 'paid' : 'partial';

        $cashField = $isCash ? 'cash_collected_amount' : 'wallet_paid_amount';

        $booking->update([
            'paid_amount'          => $newPaidAmount,
            'due_amount'           => $newDueAmount,
            'payment_status'       => $newPaymentStatus,
            $cashField             => round((float) $booking->{$cashField} + $amount, 2),
            'payment_collected_at' => $newDueAmount <= 0 ? now() : $booking->payment_collected_at,
            'payment_collected_by' => $newDueAmount <= 0 ? $collector->id : $booking->payment_collected_by,
        ]);

        // ---------- 3. Create collection audit record ----------
        $collection = PaymentCollection::create([
            'reference'      => 'PC' . strtoupper(Str::random(6)) . now()->format('ymdHis'),
            'booking_id'     => $booking->id,
            'collected_by'   => $collector->id,
            'method'         => $method,
            'amount'         => $amount,
            'reference_note' => $referenceNote,
            'notes'          => $notes,
            'meta'           => [
                'booking_reference' => $booking->reference,
                'customer_name'     => $booking->customer_name,
                'paid_after'        => $newPaidAmount,
                'due_after'         => $newDueAmount,
                'collector_is_seller' => $collectorIsSeller,
            ],
        ]);

        // ---------- 4. Credit seller (gross + commission split) ----------
        $this->creditSeller($booking->fresh());

        return $collection;
    }

    /* =============================================================
     |  HELPERS
     | ============================================================= */

    private function isSellerOrStaff(Booking $booking, User $collector): bool
    {
        $sellerUser = $this->resolveSellerUser($booking);

        // Same as seller owner?
        if ($sellerUser && (int) $sellerUser->id === (int) $collector->id) {
            return true;
        }

        // Branch staff of this seller?
        if ($collector->isBranchStaff() && $collector->branchStaff) {
            return (int) $collector->branchStaff->seller_id === (int) $booking->seller_id;
        }

        return false;
    }

    private function findCommissionRule(Booking $booking): ?PartnerCommissionRule
    {
        $serviceId = null;
        if (!empty($booking->service_code)) {
            $serviceId = Service::where('code', $booking->service_code)->value('id');
        }

        $query = PartnerCommissionRule::active();

        if ($serviceId) {
            $query->where(function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId)->orWhereNull('service_id');
            });
        } else {
            $query->whereNull('service_id');
        }

        if (!empty($booking->seller_id)) {
            $query->where(function ($q) use ($booking) {
                $q->where('seller_id', $booking->seller_id)->orWhereNull('seller_id');
            });
        } else {
            $query->whereNull('seller_id');
        }

        if (!empty($booking->branch_id)) {
            $query->where(function ($q) use ($booking) {
                $q->where('branch_id', $booking->branch_id)->orWhereNull('branch_id');
            });
        } else {
            $query->whereNull('branch_id');
        }

        $query->orderByRaw('(seller_id IS NOT NULL) DESC')
              ->orderByRaw('(branch_id IS NOT NULL) DESC')
              ->orderByRaw('(service_id IS NOT NULL) DESC')
              ->orderByRaw('(category_id IS NOT NULL) DESC');

        if (Schema::hasColumn('partner_commission_rules', 'priority')) {
            $query->orderByDesc('priority');
        }

        $query->orderByDesc('id');

        return $query->first();
    }

    private function resolveSellerUser(Booking $booking): ?User
    {
        $seller = $booking->seller;
        if (!$seller) return null;
        return User::find($seller->user_id);
    }

    /**
     * Resolve the platform commission account.
     * Priority:
     *   1. config('services.commission.user_id')
     *   2. First user with 'Super Admin' role
     *   3. null (logs critical)
     */
    private function resolveCommissionUser(): ?User
    {
        $configId = config('services.commission.user_id');

        if ($configId) {
            $user = User::find($configId);
            if ($user) {
                return $user;
            }
            Log::warning('COMMISSION_USER_ID points to non-existent user', ['id' => $configId]);
        }

        $superAdmin = User::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin');
        })->first();

        if ($superAdmin) {
            Log::warning('Using first Super Admin as commission account — set COMMISSION_USER_ID for clarity', [
                'user_id' => $superAdmin->id,
            ]);
            return $superAdmin;
        }

        Log::critical('No commission account available — commission will be skipped. Set COMMISSION_USER_ID in .env');
        return null;
    }
}
