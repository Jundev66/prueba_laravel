<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Product\Application\SearchAll\ProductsSearcher;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductsSearcherTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_searches_paginated_products(): void
    {
        $products = [ProductMother::createDefault()];
        $expectedResult = [
            'items' => $products,
            'total' => 1,
            'per_page' => 15,
            'current_page' => 1,
            'last_page' => 1,
        ];

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $productRepo->shouldReceive('searchAll')
            ->once()
            ->with(15, 1)
            ->andReturn($expectedResult);

        $searcher = new ProductsSearcher($productRepo);
        $result = $searcher(15, 1);

        $this->assertSame($expectedResult, $result);
        $this->assertCount(1, $result['items']);
    }
}
