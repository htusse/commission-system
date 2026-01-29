<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommissionReportController extends Controller
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Get commission report with optional filters.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'distributor_id' => 'nullable|integer|exists:users,id',
                'distributor_name' => 'nullable|string|max:255',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'invoice_number' => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $report = $this->commissionService->getCommissionReport($validated);

        return response()->json([
            'success' => true,
            'data' => $report,
            'total_records' => $report->count(),
            'filters_applied' => $validated,
        ]);
    }

    /**
     * Get order details with items.
     *
     * @param  int  $orderId
     * @return JsonResponse
     */
    public function show(int $orderId): JsonResponse
    {
        $order = Order::with(['orderItems.product', 'purchaser.referrer'])
            ->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $commission = $this->commissionService->calculateCommission($order);

        return response()->json([
            'success' => true,
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'total' => $order->total,
                ],
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_sku' => $item->product->sku,
                        'product_name' => $item->product->name,
                        'price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ];
                }),
                'purchaser' => [
                    'id' => $order->purchaser->id,
                    'name' => $order->purchaser->full_name,
                    'username' => $order->purchaser->username,
                ],
                'commission' => $commission,
            ],
        ]);
    }
}
