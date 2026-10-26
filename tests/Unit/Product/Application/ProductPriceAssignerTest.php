<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\AssignPrice\ProductPriceAssigner;
use App\Modules\Product\Domain\Exceptions\BaseCurrencyCannotBeSecondaryException;
use App\Modules\Product\Domain\Exceptions\CurrencyAlreadyAssignedException;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\ProductPrice;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\CurrencyMother;
use Tests\Unit\Product\Mothers\ProductMother;
use Tests\Unit\Product\Mothers\ProductPriceMother;

final class ProductPriceAssignerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_assigns_secondary_currency_price_successfully(): void
    {
        $product = ProductMother::createDefault();
        $currencyEur = CurrencyMother::createEur();
        $expectedPrice = ProductPriceMother::createEur(productId: 1, price: 1100.00);

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('search')->once()->with(2)->andReturn($currencyEur);
        $priceRepo->shouldReceive('searchByProductAndCurrency')->once()->with(1, 2)->andReturn(null);
        $priceRepo->shouldReceive('save')->once()->with(Mockery::type(ProductPrice::class))->andReturn($expectedPrice);

        $assigner = new ProductPriceAssigner($productRepo, $priceRepo, $currencyRepo);
        $result = $assigner(productId: 1, currencyId: 2, price: 1100.00);

        $this->assertSame($expectedPrice, $result);
        $this->assertSame(1100.00, $result->price());
        $this->assertSame(2, $result->currencyId());
    }

    public function test_it_throws_when_currency_is_same_as_product_base_currency(): void
    {
        $product = ProductMother::createDefault();
        $currencyUsd = CurrencyMother::createUsd();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('search')->once()->with(1)->andReturn($currencyUsd);

        $this->expectException(BaseCurrencyCannotBeSecondaryException::class);

        $assigner = new ProductPriceAssigner($productRepo, $priceRepo, $currencyRepo);
        $assigner(productId: 1, currencyId: 1, price: 1200.00);
    }

    public function test_it_throws_when_secondary_price_already_exists_for_currency(): void
    {
        $product = ProductMother::createDefault();
        $currencyEur = CurrencyMother::createEur();
        $existingPrice = ProductPriceMother::createEur();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('search')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('search')->once()->with(2)->andReturn($currencyEur);
        $priceRepo->shouldReceive('searchByProductAndCurrency')->once()->with(1, 2)->andReturn($existingPrice);

        $this->expectException(CurrencyAlreadyAssignedException::class);

        $assigner = new ProductPriceAssigner($productRepo, $priceRepo, $currencyRepo);
        $assigner(productId: 1, currencyId: 2, price: 1100.00);
    }
}
