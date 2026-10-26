<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\User;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CurrencySeeder::class);
        $this->user = User::factory()->create();
        $this->currency = Currency::query()->firstOrFail();
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }

    public function test_it_can_list_products_paginated(): void
    {
        Product::query()->create([
            'name' => 'Test Laptop',
            'description' => 'Laptop description',
            'price' => 1200.0000,
            'currency_id' => $this->currency->id,
            'tax_cost' => 192.0000,
            'manufacturing_cost' => 600.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'currency' => ['id', 'name', 'symbol', 'exchange_rate'],
                        'tax_cost',
                        'manufacturing_cost',
                        'gross_margin',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_it_can_filter_products_by_search_query_and_price_range(): void
    {
        Product::query()->create([
            'name' => 'MacBook Pro 14',
            'description' => 'M3 chip powerhouse',
            'price' => 1999.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => 300.00,
            'manufacturing_cost' => 900.00,
        ]);

        Product::query()->create([
            'name' => 'Budget Smartphone',
            'description' => 'Android phone',
            'price' => 200.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => 30.00,
            'manufacturing_cost' => 90.00,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/products?q=MacBook&min_price=1000&max_price=2500');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'MacBook Pro 14');
    }

    public function test_it_can_create_a_product_with_valid_data(): void
    {
        $payload = [
            'name' => 'Mechanical Keyboard',
            'description' => 'Tactile switches',
            'price' => 120.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => 19.20,
            'manufacturing_cost' => 45.00,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Product created successfully.');

        $this->assertDatabaseHas('products', [
            'name' => 'Mechanical Keyboard',
            'currency_id' => 1,
        ]);
    }

    public function test_it_fails_creating_product_with_invalid_currency(): void
    {
        $payload = [
            'name' => 'Invalid Product',
            'price' => 100.00,
            'currency_id' => 9999,
            'tax_cost' => 10.00,
            'manufacturing_cost' => 50.00,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_it_fails_creating_product_with_negative_price(): void
    {
        $payload = [
            'name' => 'Negative Price Product',
            'price' => -10.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => 10.00,
            'manufacturing_cost' => 50.00,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_it_can_get_product_by_id(): void
    {
        $product = Product::query()->create([
            'name' => 'Tablet Pro',
            'description' => '11 inch screen',
            'price' => 800.0000,
            'currency_id' => $this->currency->id,
            'tax_cost' => 128.0000,
            'manufacturing_cost' => 400.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/products/'.$product->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Tablet Pro');
    }

    public function test_it_returns_404_for_non_existent_product(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/products/99999');

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'PRODUCT_NOT_FOUND');
    }

    public function test_it_can_update_product(): void
    {
        $product = Product::query()->create([
            'name' => 'Old Name',
            'description' => 'Old description',
            'price' => 500.0000,
            'currency_id' => $this->currency->id,
            'tax_cost' => 80.0000,
            'manufacturing_cost' => 250.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/products/'.$product->id, [
                'name' => 'New Name',
                'price' => 550.00,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Product updated successfully.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    public function test_it_can_delete_product(): void
    {
        $product = Product::query()->create([
            'name' => 'To Delete',
            'price' => 100.0000,
            'currency_id' => $this->currency->id,
            'tax_cost' => 16.0000,
            'manufacturing_cost' => 50.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/products/'.$product->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
