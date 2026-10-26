<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Infrastructure;

use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Filters\ProductEloquentFilter;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductEloquentFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
    }

    public function test_it_filters_products_by_name(): void
    {
        Product::query()->create([
            'name' => 'Dell XPS 15',
            'description' => 'Workstation',
            'price' => 1800.00,
            'currency_id' => 1,
            'tax_cost' => 200.00,
            'manufacturing_cost' => 800.00,
        ]);

        Product::query()->create([
            'name' => 'Logitech Mouse',
            'description' => 'Wireless mouse',
            'price' => 50.00,
            'currency_id' => 1,
            'tax_cost' => 5.00,
            'manufacturing_cost' => 15.00,
        ]);

        $filter = new ProductEloquentFilter();
        $builder = Product::query();

        $filter->apply($builder, name: 'Dell');

        $results = $builder->get();

        $this->assertCount(1, $results);
        $this->assertSame('Dell XPS 15', $results->first()->name);
    }

    public function test_it_filters_products_by_currency_and_price_range(): void
    {
        Product::query()->create([
            'name' => 'Laptop A',
            'price' => 500.00,
            'currency_id' => 1,
            'tax_cost' => 50.00,
            'manufacturing_cost' => 200.00,
        ]);

        Product::query()->create([
            'name' => 'Laptop B',
            'price' => 1500.00,
            'currency_id' => 1,
            'tax_cost' => 150.00,
            'manufacturing_cost' => 600.00,
        ]);

        Product::query()->create([
            'name' => 'Laptop C',
            'price' => 1500.00,
            'currency_id' => 2,
            'tax_cost' => 150.00,
            'manufacturing_cost' => 600.00,
        ]);

        $filter = new ProductEloquentFilter();
        $builder = Product::query();

        $filter->apply($builder, currencyId: 1, minPrice: 1000.00, maxPrice: 2000.00);

        $results = $builder->get();

        $this->assertCount(1, $results);
        $this->assertSame('Laptop B', $results->first()->name);
    }
}
