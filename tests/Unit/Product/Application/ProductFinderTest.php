<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Product\Application\Find\FindProductQuery;
use App\Modules\Product\Application\Find\ProductFinder;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductFinderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_finds_an_existing_product(): void
    {
        $expectedProduct = ProductMother::createDefault();

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $productRepository->shouldReceive('search')
            ->once()
            ->with(1)
            ->andReturn($expectedProduct);

        $finder = new ProductFinder($productRepository);
        $product = $finder(new FindProductQuery(1));

        $this->assertSame($expectedProduct, $product);
        $this->assertSame(1, $product->id());
    }

    public function test_it_throws_exception_when_product_not_found(): void
    {
        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $productRepository->shouldReceive('search')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $finder = new ProductFinder($productRepository);
        $finder(new FindProductQuery(999));
    }
}
