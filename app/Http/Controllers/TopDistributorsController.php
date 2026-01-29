<?php

namespace App\Http\Controllers;

use App\Services\TopDistributorsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TopDistributorsController extends Controller
{
    protected TopDistributorsService $topDistributorsService;

    public function __construct(TopDistributorsService $topDistributorsService)
    {
        $this->topDistributorsService = $topDistributorsService;
    }

    /**
     * Get top distributors ranking.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'distributor_name' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $result = $this->topDistributorsService->getTopDistributors($validated);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'summary' => $result['summary'],
            'total_records' => count($result['data']),
            'filters_applied' => $validated,
        ]);
    }
}
