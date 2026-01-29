<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TopDistributorsService
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Get top distributors ranked by total commission.
     *
     * @param  array  $filters
     * @return array
     */
    public function getTopDistributors(array $filters = []): array
    {
        // Get all distributors
        $distributors = User::whereHas('categories', function ($query) {
            $query->where('category_id', 1); // Distributor category
        });

        // Apply distributor name filter
        if (!empty($filters['distributor_name'])) {
            $distributors->where(function ($query) use ($filters) {
                $searchTerm = $filters['distributor_name'];
                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('username', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchTerm . '%']);
            });
        }

        $distributors = $distributors->get();

        $rankings = [];

        foreach ($distributors as $distributor) {
            $stats = $this->calculateDistributorStats($distributor, $filters);
            
            if ($stats['total_orders'] > 0) {
                $rankings[] = $stats;
            }
        }

        // Sort by total order volume (total sales) descending
        usort($rankings, function ($a, $b) {
            return $b['total_order_volume'] <=> $a['total_order_volume'];
        });

        // Add rank numbers (same rank for ties)
        $currentRank = 1;
        $previousVolume = null;
        
        foreach ($rankings as $index => &$ranking) {
            // Round to 2 decimal places for comparison to handle floating point precision
            $currentVolume = round($ranking['total_order_volume'], 2);
            $prevVolumeRounded = $previousVolume !== null ? round($previousVolume, 2) : null;
            
            if ($prevVolumeRounded !== null && $currentVolume < $prevVolumeRounded) {
                $currentRank = $index + 1;
            }
            $ranking['rank'] = $currentRank;
            $previousVolume = $ranking['total_order_volume'];
        }

        // Calculate summary
        $summary = [
            'total_distributors' => count($rankings),
            'total_orders' => array_sum(array_column($rankings, 'total_orders')),
            'total_commission' => array_sum(array_column($rankings, 'total_commission')),
        ];

        return [
            'data' => $rankings,
            'summary' => $summary,
        ];
    }

    /**
     * Calculate statistics for a single distributor.
     *
     * @param  User  $distributor
     * @param  array  $filters
     * @return array
     */
    protected function calculateDistributorStats(User $distributor, array $filters): array
    {
        // Build query for orders where this user is the referrer
        // Use Eloquent for better relationship handling and avoid redundant loading
        $ordersQuery = \App\Models\Order::query()
            ->whereHas('purchaser', function ($query) use ($distributor) {
                $query->where('referred_by', $distributor->id);
            })
            ->with(['purchaser.referrer.categories', 'purchaser.categories', 'orderItems.product']);

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $ordersQuery->where('order_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $ordersQuery->where('order_date', '<=', $filters['date_to']);
        }

        $orders = $ordersQuery->get();

        if ($orders->isEmpty()) {
            return [
                'distributor_id' => $distributor->id,
                'distributor_name' => $distributor->full_name,
                'username' => $distributor->username,
                'total_orders' => 0,
                'total_order_volume' => 0,
                'total_commission' => 0,
                'average_commission' => 0,
            ];
        }

        $totalCommission = 0;
        $totalOrderVolume = 0;

        // Calculate commission for each order using already loaded relationships
        foreach ($orders as $order) {
            $commission = $this->commissionService->calculateCommission($order);
            $totalCommission += $commission['commission_amount'];
            $totalOrderVolume += $commission['order_total'];
        }

        return [
            'distributor_id' => $distributor->id,
            'distributor_name' => $distributor->full_name,
            'username' => $distributor->username,
            'total_orders' => $orders->count(),
            'total_order_volume' => $totalOrderVolume,
            'total_commission' => $totalCommission,
            'average_commission' => $orders->count() > 0 ? $totalCommission / $orders->count() : 0,
        ];
    }
}
