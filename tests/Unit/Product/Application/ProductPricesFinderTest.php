<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Product\Application\FindPrices\ProductPricesFinder;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;
use Tests\Unit\Product\Mothers\ProductPriceMother;

final class ProductPricesFinderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_finds_prices_for_existing_product(): void
    {
        $product = ProductMother::createDefault();
        $prices = [ProductPriceMother::createEur()];

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $priceRepo->shouldReceive('searchByProduct')->once()->with(1)->andReturn($prices);

        $finder = new ProductPricesFinder($productRepo, $priceRepo);
        $result = $finder(1);

        $this->assertSame($prices, $result);
        $this->assertCount(1, $result);
    }

    public function test_it_throws_when_finding_prices_for_non_existent_product(): void
    {
        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(99)->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $finder = new ProductPricesFinder($productRepo, $priceRepo);
        $finder(99);
    }
}
