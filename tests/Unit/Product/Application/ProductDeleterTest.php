<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Product\Application\Delete\ProductDeleter;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductDeleterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_deletes_an_existing_product(): void
    {
        $product = ProductMother::createDefault();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $productRepo->shouldReceive('delete')->once()->with(1)->andReturn(true);

        $deleter = new ProductDeleter($productRepo);
        $result = $deleter(1);

        $this->assertTrue($result);
    }

    public function test_it_throws_when_deleting_non_existent_product(): void
    {
        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $productRepo->shouldReceive('search')->once()->with(99)->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $deleter = new ProductDeleter($productRepo);
        $deleter(99);
    }
}
