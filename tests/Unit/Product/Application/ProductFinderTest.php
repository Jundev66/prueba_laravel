<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Product\Application\Find\ProductFinder;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductFinderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_finds_an_existing_product(): void
    {
        $expectedProduct = ProductMother::createDefault();

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $productRepository->shouldReceive('search')
            ->once()
            ->with(1)
            ->andReturn($expectedProduct);

        $finder = new ProductFinder($productRepository);
        $product = $finder(1);

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
        $finder(999);
    }
}
