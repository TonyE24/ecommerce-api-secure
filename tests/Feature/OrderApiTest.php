<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_with_insufficient_stock_does_not_change_any_inventory(): void
    {
        $user = User::factory()->create();
        $availableProduct = Product::create([
            'name' => 'Available item',
            'price' => 10.25,
            'stock' => 5,
        ]);
        $unavailableProduct = Product::create([
            'name' => 'Unavailable item',
            'price' => 4.50,
            'stock' => 1,
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                ['product_id' => $availableProduct->id, 'quantity' => 2],
                ['product_id' => $unavailableProduct->id, 'quantity' => 2],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors('items');

        $this->assertSame(5, $availableProduct->fresh()->stock);
        $this->assertSame(1, $unavailableProduct->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_sums_duplicate_lines_and_stores_total_to_the_cent(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Notebook',
            'price' => 12.35,
            'stock' => 5,
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.total', '37.05');

        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(1, Order::count());
    }

    public function test_order_history_requires_authentication(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
    }
}
