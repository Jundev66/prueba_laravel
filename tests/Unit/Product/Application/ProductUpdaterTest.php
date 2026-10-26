<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\Update\ProductUpdater;
use App\Modules\Product\Application\Update\UpdateProductRequest;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductUpdaterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_updates_product_attributes_successfully(): void
    {
        $product = ProductMother::createDefault();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $productRepo->shouldReceive('save')->once()->with(Mockery::type(Product::class))->andReturn($product);

        $updater = new ProductUpdater($productRepo, $currencyRepo);
        $result = $updater(new UpdateProductRequest(id: 1, name: 'Renamed Laptop', price: 1350.00));

        $this->assertSame('Renamed Laptop', $result->name());
        $this->assertSame(1350.00, $result->price());
    }

    public function test_it_throws_when_updating_non_existent_product(): void
    {
        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(99)->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $updater = new ProductUpdater($productRepo, $currencyRepo);
        $updater(new UpdateProductRequest(id: 99, name: 'Non existent'));
    }

    public function test_it_throws_when_updating_with_invalid_currency(): void
    {
        $product = ProductMother::createDefault();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('exists')->once()->with(999)->andReturn(false);

        $this->expectException(CurrencyNotFoundException::class);

        $updater = new ProductUpdater($productRepo, $currencyRepo);
        $updater(new UpdateProductRequest(id: 1, currencyId: 999));
    }
}
