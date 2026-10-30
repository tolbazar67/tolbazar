<?php

namespace App\Services;

use App\Models\CustomerChargeRule;
use App\Models\ServiceTaxRule;
use Illuminate\Support\Collection;

class CheckoutCalculator
{
    public function calculate(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['qty'];
        }
        $subtotal = round($subtotal, 2);

        $serviceIds  = collect($items)->pluck('service_id')->filter()->unique()->values()->all();
        $categoryIds = collect($items)->pluck('category_id')->filter()->unique()->values()->all();
        $sellerIds   = collect($items)->pluck('seller_id')->filter()->unique()->values()->all();

        $taxRules    = $this->loadTaxRules($serviceIds, $categoryIds, $sellerIds);
        $chargeRules = $this->loadChargeRules($serviceIds, $categoryIds, $sellerIds);

        /* ---------- Charges ---------- */
        $chargesBreakdown    = [];
        $chargesTotal        = 0.0;
        $taxableChargesTotal = 0.0;

        foreach ($chargeRules as $rule) {
            $base   = $this->chargeBase($rule, $items, $chargeRules, $subtotal);
            $amount = $this->chargeAmount($rule, $base);
            if ($amount <= 0) continue;

            $chargesTotal += $amount;
            if ($rule->is_taxable) {
                $taxableChargesTotal += $amount;
            }

            $chargesBreakdown[] = [
                'id'            => $rule->id,
                'seller_id'     => $rule->seller_id,
                'name'          => $rule->name,
                'code'          => $rule->charge_code,
                'type'          => $rule->charge_type,
                'rate'          => (float) $rule->rate,
                'base'          => round($base, 2),
                'amount'        => $amount,
                'is_taxable'    => (bool) $rule->is_taxable,
                'is_refundable' => (bool) $rule->is_refundable,
            ];
        }
        $chargesTotal = round($chargesTotal, 2);

        /* ---------- Taxes ---------- */
        $taxBreakdown = [];
        $taxTotal     = 0.0;

        foreach ($taxRules as $rule) {
            $base = $this->taxBase($rule, $items, $taxRules, $subtotal, $taxableChargesTotal);
            if ($base <= 0) continue;

            $result = $this->taxAmount($rule, $base);

            if (! $rule->is_inclusive) {
                $taxTotal += $result['amount'];
            }

            $taxBreakdown[] = [
                'id'           => $rule->id,
                'seller_id'    => $rule->seller_id ?? null,
                'name'         => $rule->name,
                'code'         => $rule->tax_code,
                'type'         => $rule->tax_type,
                'rate'         => (float) $rule->rate,
                'base'         => round($base, 2),
                'amount'       => $result['amount'],
                'is_inclusive' => (bool) $rule->is_inclusive,
                'applies_to'   => $rule->applies_to,
            ];
        }
        $taxTotal = round($taxTotal, 2);

        return [
            'subtotal'          => $subtotal,
            'charges_amount'    => $chargesTotal,
            'tax_amount'        => $taxTotal,
            'grand_total'       => round($subtotal + $chargesTotal + $taxTotal, 2),
            'charges_breakdown' => $chargesBreakdown,
            'tax_breakdown'     => $taxBreakdown,
        ];
    }

    /* =============================================================
     |  RULE LOADERS — STRICT per-seller mode (default)
     |  Global rules sirf config flag true garepaxi apply
     | ============================================================= */

    private function loadChargeRules(array $serviceIds, array $categoryIds, array $sellerIds)
    {
        $query = CustomerChargeRule::active()
            ->where(function ($q) use ($serviceIds) {
                $q->whereIn('service_id', $serviceIds)->orWhereNull('service_id');
            })
            ->where(function ($q) use ($categoryIds) {
                $q->whereNull('category_id');
                if (!empty($categoryIds)) $q->orWhereIn('category_id', $categoryIds);
            });

        if (config('charges.apply_global_rules', false)) {
            // Global + seller fallback
            $query->where(function ($q) use ($sellerIds) {
                $q->whereNull('seller_id');
                if (!empty($sellerIds)) $q->orWhereIn('seller_id', $sellerIds);
            });
        } else {
            // STRICT: sirf seller-specific rules
            if (empty($sellerIds)) {
                return collect();   // seller_id chaina bhane charge chaina
            }
            $query->whereIn('seller_id', $sellerIds);
        }

        return $query->orderBy('id')->get();
    }

    private function loadTaxRules(array $serviceIds, array $categoryIds, array $sellerIds)
    {
        $query = ServiceTaxRule::active()
            ->where(function ($q) use ($serviceIds) {
                $q->whereIn('service_id', $serviceIds)->orWhereNull('service_id');
            })
            ->where(function ($q) use ($categoryIds) {
                $q->whereNull('category_id');
                if (!empty($categoryIds)) $q->orWhereIn('category_id', $categoryIds);
            });

        if ($this->hasSellerColumn('service_tax_rules')) {
            if (config('charges.apply_global_tax', false)) {
                $query->where(function ($q) use ($sellerIds) {
                    $q->whereNull('seller_id');
                    if (!empty($sellerIds)) $q->orWhereIn('seller_id', $sellerIds);
                });
            } else {
                if (empty($sellerIds)) return collect();
                $query->whereIn('seller_id', $sellerIds);
            }
        }

        return $query->orderBy('priority')->get();
    }

    private function hasSellerColumn(string $table): bool
    {
        static $cache = [];
        if (!array_key_exists($table, $cache)) {
            $cache[$table] = \Schema::hasColumn($table, 'seller_id');
        }
        return $cache[$table];
    }

    /* =============================================================
     |  CHARGE BASE — seller + category scoped
     | ============================================================= */

    private function chargeBase($rule, array $items, Collection $allRules, float $subtotal): float
    {
        $scoped = $items;

        if (!empty($rule->seller_id)) {
            $scoped = array_filter($scoped, fn ($i) =>
                (int)($i['seller_id'] ?? 0) === (int)$rule->seller_id
            );
        }

        if (!empty($rule->category_id)) {
            $scoped = array_filter($scoped, fn ($i) =>
                (int)($i['category_id'] ?? 0) === (int)$rule->category_id
            );
        }

        $scoped = array_values($scoped);
        $scopedSubtotal = 0.0;
        foreach ($scoped as $i) {
            $scopedSubtotal += (float) $i['price'] * (int) $i['qty'];
        }
        $scopedSubtotal = round($scopedSubtotal, 2);

        return match (strtolower((string) $rule->calculation_basis)) {
            'per_order', 'order'  => 1.0,
            'per_item', 'item'    => (float) collect($scoped)->sum('qty'),
            'subtotal', 'default' => $scopedSubtotal,
            default               => $scopedSubtotal,
        };
    }

    private function chargeAmount($rule, float $base): float
    {
        $type = strtolower((string) $rule->charge_type);
        $rate = (float) $rule->rate;

        $amount = $type === 'fixed' ? $rate : round($base * ($rate / 100), 2);

        if ($rule->min_charge !== null && $amount < (float) $rule->min_charge) {
            $amount = (float) $rule->min_charge;
        }
        if ($rule->max_charge !== null && $amount > (float) $rule->max_charge) {
            $amount = (float) $rule->max_charge;
        }

        return round($amount, 2);
    }

    /* =============================================================
     |  TAX BASE — seller + category scoped
     | ============================================================= */

    private function taxBase($rule, array $items, Collection $allRules, float $subtotal, float $taxableCharges): float
    {
        $scoped = $items;

        if ($this->hasSellerColumn('service_tax_rules') && !empty($rule->seller_id)) {
            $scoped = array_filter($scoped, fn ($i) =>
                (int)($i['seller_id'] ?? 0) === (int)$rule->seller_id
            );
        }

        if (!empty($rule->category_id)) {
            $scoped = array_filter($scoped, fn ($i) =>
                (int)($i['category_id'] ?? 0) === (int)$rule->category_id
            );
        }

        $scoped = array_values($scoped);
        $scopedSubtotal = 0.0;
        foreach ($scoped as $i) {
            $scopedSubtotal += (float) $i['price'] * (int) $i['qty'];
        }
        $scopedSubtotal = round($scopedSubtotal, 2);

        $appliesTo = strtolower((string) ($rule->applies_to ?? 'product'));

        return match ($appliesTo) {
            'all'                    => round($scopedSubtotal + $taxableCharges, 2),
            'product', 'service', '' => $scopedSubtotal,
            default                  => $scopedSubtotal,
        };
    }

    private function taxAmount($rule, float $base): array
    {
        $type = strtolower((string) $rule->tax_type);
        $rate = (float) $rule->rate;

        if ($type === 'fixed') {
            return ['amount' => round($rate, 2)];
        }

        if ($rule->is_inclusive) {
            $baseWithoutTax = $base / (1 + ($rate / 100));
            return ['amount' => round($base - $baseWithoutTax, 2)];
        }

        return ['amount' => round($base * ($rate / 100), 2)];
    }
}
