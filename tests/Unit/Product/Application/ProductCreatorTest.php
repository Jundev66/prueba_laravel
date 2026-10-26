<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\Create\ProductCreator;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\CurrencyMother;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductCreatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_product_successfully_when_currency_exists(): void
    {
        $currency = CurrencyMother::createUsd();
        $expectedProduct = ProductMother::createDefault();

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepository = Mockery::mock(CurrencyRepositoryInterface::class);

        $currencyRepository->shouldReceive('search')
            ->once()
            ->with(1)
            ->andReturn($currency);

        $productRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Product::class))
            ->andReturn($expectedProduct);

        $creator = new ProductCreator($productRepository, $currencyRepository);
        $result = $creator(
            name: 'Test Laptop',
            description: 'A powerful laptop for development',
            price: 1200.00,
            currencyId: 1,
            taxCost: 192.00,
            manufacturingCost: 600.00
        );

        $this->assertSame($expectedProduct, $result);
        $this->assertSame('Test Laptop', $result->name());
        $this->assertSame(408.0, $result->grossMargin());
    }

    public function test_it_throws_exception_when_currency_does_not_exist(): void
    {
        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepository = Mockery::mock(CurrencyRepositoryInterface::class);

        $currencyRepository->shouldReceive('search')
            ->once()
            ->with(999)
            ->andReturn(null);

        $productRepository->shouldNotReceive('save');

        $this->expectException(CurrencyNotFoundException::class);

        $creator = new ProductCreator($productRepository, $currencyRepository);
        $creator(
            name: 'Test Laptop',
            description: null,
            price: 1200.00,
            currencyId: 999,
            taxCost: 192.00,
            manufacturingCost: 600.00
        );
    }
}
