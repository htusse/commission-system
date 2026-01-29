<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class CommissionService
{
    /**
     * Commission tier thresholds and percentages.
     * Referred Distributors => Commission %
     */
    protected const COMMISSION_TIERS = [
        ['min' => 30, 'max' => PHP_INT_MAX, 'rate' => 0.30],
        ['min' => 21, 'max' => 29, 'rate' => 0.20],
        ['min' => 11, 'max' => 20, 'rate' => 0.15],
        ['min' => 5, 'max' => 10, 'rate' => 0.10],
        ['min' => 0, 'max' => 4, 'rate' => 0.05],
    ];

    /**
     * Calculate commission rate based on referred distributors count.
     *
     * @param  int  $referredDistributorsCount
     * @return float
     */
    public function getCommissionRate(int $referredDistributorsCount): float
    {
        foreach (self::COMMISSION_TIERS as $tier) {
            if ($referredDistributorsCount >= $tier['min'] && $referredDistributorsCount <= $tier['max']) {
                return $tier['rate'];
            }
        }

        return 0.05; // Default to lowest tier
    }

    /**
     * Calculate commission for an order.
     *
     * @param  Order  $order
     * @return array
     */
    public function calculateCommission(Order $order): array
    {
        $purchaser = $order->purchaser;
        $referrer = $purchaser->referrer;

        // No commission if purchaser has no referrer
        if (!$referrer) {
            return [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'order_date' => $order->order_date,
                'order_total' => $order->total,
                'purchaser_id' => $purchaser->id,
                'purchaser_name' => $purchaser->full_name,
                'referrer_id' => null,
                'referrer_name' => null,
                'referred_distributors' => 0,
                'commission_rate' => 0,
                'commission_amount' => 0,
            ];
        }

        // No commission if referrer is not a distributor
        if (!$referrer->isDistributor()) {
            return [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'order_date' => $order->order_date,
                'order_total' => $order->total,
                'purchaser_id' => $purchaser->id,
                'purchaser_name' => $purchaser->full_name,
                'referrer_id' => $referrer->id,
                'referrer_name' => $referrer->full_name,
                'referred_distributors' => 0,
                'commission_rate' => 0,
                'commission_amount' => 0,
            ];
        }

        // Count referred distributors at order date
        $referredDistributorsCount = $referrer->getReferredDistributorsCountAsOf(
            $order->order_date->format('Y-m-d')
        );

        // Calculate commission
        $commissionRate = $this->getCommissionRate($referredDistributorsCount);
        $orderTotal = $order->total;
        $commissionAmount = $orderTotal * $commissionRate;

        return [
            'order_id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'order_date' => $order->order_date,
            'order_total' => $orderTotal,
            'purchaser_id' => $purchaser->id,
            'purchaser_name' => $purchaser->full_name,
            'referrer_id' => $referrer->id,
            'referrer_name' => $referrer->full_name,
            'referred_distributors' => $referredDistributorsCount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
        ];
    }

    /**
     * Calculate commissions for multiple orders.
     *
     * @param  Collection  $orders
     * @return Collection
     */
    public function calculateCommissions(Collection $orders): Collection
    {
        return $orders->map(function ($order) {
            return $this->calculateCommission($order);
        });
    }

    /**
     * Get commission report with filters.
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getCommissionReport(array $filters = []): Collection
    {
        $query = Order::with(['purchaser.referrer.categories', 'purchaser.categories', 'orderItems.product']);

        // Filter by distributor (referrer)
        if (!empty($filters['distributor_id'])) {
            $query->whereHas('purchaser', function ($q) use ($filters) {
                $q->where('referred_by', $filters['distributor_id']);
            });
        }

        // Filter by distributor name
        if (!empty($filters['distributor_name'])) {
            $query->whereHas('purchaser.referrer', function ($q) use ($filters) {
                $searchTerm = '%' . $filters['distributor_name'] . '%';
                $q->where(function ($query) use ($searchTerm) {
                    $query->where('first_name', 'LIKE', $searchTerm)
                        ->orWhere('last_name', 'LIKE', $searchTerm)
                        ->orWhere('username', 'LIKE', $searchTerm);
                });
            });
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('order_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('order_date', '<=', $filters['date_to']);
        }

        // Filter by invoice number
        if (!empty($filters['invoice_number'])) {
            $query->where('invoice_number', 'LIKE', '%' . $filters['invoice_number'] . '%');
        }

        $orders = $query->orderBy('order_date', 'desc')->get();

        return $this->calculateCommissions($orders);
    }
}
