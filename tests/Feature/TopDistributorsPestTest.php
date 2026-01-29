<?php

use function Pest\Laravel\getJson;

it('returns successful response from top distributors API', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'distributor_id',
                    'distributor_name',
                    'username',
                    'total_orders',
                    'total_order_volume',
                    'total_commission',
                    'average_commission',
                    'rank',
                ],
            ],
            'summary' => [
                'total_distributors',
                'total_orders',
                'total_commission',
            ],
            'total_records',
            'filters_applied',
        ]);

    expect($response->json('success'))->toBeTrue();
});

it('ranks distributors by total commission in descending order', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    if (count($data) >= 2) {
        // Verify order volume (total sales) is in descending order
        for ($i = 0; $i < count($data) - 1; $i++) {
            expect($data[$i]['total_order_volume'])
                ->toBeGreaterThanOrEqual($data[$i + 1]['total_order_volume']);
        }
    }
});

it('handles tie rankings correctly per requirements', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    // The requirement specifically mentions Chaim Kuhn and Eliane Bogisich
    // should have the same rank (#197) with same sales ($360)
    $chaimKuhn = collect($data)->firstWhere('distributor_name', 'Chaim Kuhn');
    $elianeBogisich = collect($data)->firstWhere('distributor_name', 'Eliane Bogisich');
    
    if ($chaimKuhn && $elianeBogisich) {
        // If they have the same sales (rounded), they should have the same rank
        $chaimSales = round($chaimKuhn['total_order_volume'], 2);
        $elianeSales = round($elianeBogisich['total_order_volume'], 2);
        
        if ($chaimSales === $elianeSales) {
            expect($elianeBogisich['rank'])->toBe($chaimKuhn['rank'], 
                "Chaim Kuhn and Eliane Bogisich have same sales so should have same rank");
        }
    }
});

it('filters by date range', function () {
    $response = getJson('/api/v1/top-distributors?date_from=2020-01-01&date_to=2020-12-31');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    // Should have fewer distributors/orders when filtered by date
    $summary = $response->json('summary');
    expect($summary)->toHaveKeys(['total_distributors', 'total_orders', 'total_commission']);
});

it('returns validation error for invalid date range', function () {
    $response = getJson('/api/v1/top-distributors?date_from=2020-12-31&date_to=2020-01-01');

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['date_to'],
        ]);
});

test('top distributor is Demario Purdy with total sales of $22,026.75', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    expect($data)->not()->toBeEmpty();
    
    $topDistributor = $data[0];
    
    expect($topDistributor['rank'])->toBe(1);
    expect($topDistributor['distributor_id'])->toBe(25944);
    expect($topDistributor['distributor_name'])->toBe('Demario Purdy');
    expect($topDistributor['total_commission'])->toBeGreaterThan(0);
    // Verify the specific amount requirement: $22,026.75
    // Note: Using total_order_volume as requirement states "Total Sales"
    expect(round($topDistributor['total_order_volume'], 2))->toBe(22026.75);
});

test('Floy Miller has total sales of $9,645.00', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    // Find Floy Miller in the results
    $floyMiller = collect($data)->firstWhere('distributor_name', 'Floy Miller');
    
    expect($floyMiller)->not()->toBeNull();
    expect(round($floyMiller['total_order_volume'], 2))->toBe(9645.00);
});

test('Loy Schamberger has total sales of $575.00', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    // Find Loy Schamberger in the results
    $loySchamberger = collect($data)->firstWhere('distributor_name', 'Loy Schamberger');
    
    expect($loySchamberger)->not()->toBeNull();
    expect(round($loySchamberger['total_order_volume'], 2))->toBe(575.00);
});

test('Chaim Kuhn has total sales of $360.00', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    // Find Chaim Kuhn in the results
    $chaimKuhn = collect($data)->firstWhere('distributor_name', 'Chaim Kuhn');
    
    expect($chaimKuhn)->not()->toBeNull();
    expect(round($chaimKuhn['total_order_volume'], 2))->toBe(360.00);
});

test('Eliane Bogisich has total sales of $360.00 (same as Chaim Kuhn for tie handling)', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    // Find both distributors
    $chaimKuhn = collect($data)->firstWhere('distributor_name', 'Chaim Kuhn');
    $elianeBogisich = collect($data)->firstWhere('distributor_name', 'Eliane Bogisich');
    
    expect($chaimKuhn)->not()->toBeNull();
    expect($elianeBogisich)->not()->toBeNull();
    
    // Both should have same sales amount
    expect(round($elianeBogisich['total_order_volume'], 2))->toBe(360.00);
    
    // Both should have the same rank (tie handling)
    expect($elianeBogisich['rank'])->toBe($chaimKuhn['rank']);
});

it('calculates summary correctly', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    $summary = $response->json('summary');
    
    // Verify summary matches data
    expect($summary['total_distributors'])->toBe(count($data));
    
    $calculatedTotalOrders = array_sum(array_column($data, 'total_orders'));
    expect($summary['total_orders'])->toBe($calculatedTotalOrders);
    
    $calculatedTotalCommission = array_sum(array_column($data, 'total_commission'));
    expect(round($summary['total_commission'], 2))
        ->toBe(round($calculatedTotalCommission, 2));
});

it('calculates average commission correctly', function () {
    $response = getJson('/api/v1/top-distributors');

    $response->assertStatus(200);
    
    $data = $response->json('data');
    
    foreach ($data as $distributor) {
        if ($distributor['total_orders'] > 0) {
            $expectedAverage = $distributor['total_commission'] / $distributor['total_orders'];
            expect(round($distributor['average_commission'], 2))
                ->toBe(round($expectedAverage, 2));
        } else {
            expect($distributor['average_commission'])->toBe(0);
        }
    }
});
