<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\User;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductPriceApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

    private Currency $usd;

    private Currency $eur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CurrencySeeder::class);
        $this->user = User::factory()->create();

        $this->usd = Currency::query()->where('name', 'US Dollar')->firstOrFail();
        $this->eur = Currency::query()->where('name', 'Euro')->firstOrFail();

        $this->product = Product::query()->create([
            'name' => 'Smartphone Galaxy',
            'description' => 'Flagship smartphone',
            'price' => 1000.0000,
            'currency_id' => $this->usd->id,
            'tax_cost' => 160.0000,
            'manufacturing_cost' => 500.0000,
        ]);
    }

    public function test_it_can_list_prices_for_a_product(): void
    {
        ProductPrice::query()->create([
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
            'price' => 920.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/products/'.$this->product->id.'/prices');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.currency_id', $this->eur->id)
            ->assertJsonPath('data.0.price', 920);
    }

    public function test_it_can_assign_secondary_price_to_product(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products/'.$this->product->id.'/prices', [
                'currency_id' => $this->eur->id,
                'price' => 920.00,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Product price assigned successfully.');

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
        ]);
    }

    public function test_it_rejects_assigning_same_currency_as_base_currency(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products/'.$this->product->id.'/prices', [
                'currency_id' => $this->usd->id,
                'price' => 1000.00,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'BASE_CURRENCY_CANNOT_BE_SECONDARY');
    }

    public function test_it_rejects_duplicate_secondary_currency_for_same_product(): void
    {
        ProductPrice::query()->create([
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
            'price' => 920.0000,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/products/'.$this->product->id.'/prices', [
                'currency_id' => $this->eur->id,
                'price' => 950.00,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'CURRENCY_PRICE_ALREADY_EXISTS');
    }
}
