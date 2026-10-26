<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Actions;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\Actions\AssignProductPriceAction;
use App\Modules\Product\Application\DTOs\AssignProductPriceDTO;
use App\Modules\Product\Domain\Exceptions\BaseCurrencyCannotBeSecondaryException;
use App\Modules\Product\Domain\Exceptions\CurrencyAlreadyAssignedException;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class AssignProductPriceActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_assigns_secondary_currency_price_successfully(): void
    {
        $dto = new AssignProductPriceDTO(productId: 1, currencyId: 2, price: 1100.00);
        $product = ProductMother::createDefault();
        $product->currency_id = 1;

        $productPrice = new ProductPrice();
        $productPrice->id = 1;
        $productPrice->product_id = 1;
        $productPrice->currency_id = 2;
        $productPrice->price = 1100.00;

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('findById')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('exists')->once()->with(2)->andReturn(true);
        $priceRepo->shouldReceive('findByProductAndCurrency')->once()->with(1, 2)->andReturn(null);
        $priceRepo->shouldReceive('create')->once()->with($dto)->andReturn($productPrice);

        $action = new AssignProductPriceAction($productRepo, $priceRepo, $currencyRepo);
        $result = $action->execute($dto);

        $this->assertSame($productPrice, $result);
    }

    public function test_it_throws_when_currency_is_same_as_product_base_currency(): void
    {
        $dto = new AssignProductPriceDTO(productId: 1, currencyId: 1, price: 1200.00);
        $product = ProductMother::createDefault();
        $product->currency_id = 1;

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('findById')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('exists')->once()->with(1)->andReturn(true);

        $this->expectException(BaseCurrencyCannotBeSecondaryException::class);

        $action = new AssignProductPriceAction($productRepo, $priceRepo, $currencyRepo);
        $action->execute($dto);
    }

    public function test_it_throws_when_secondary_price_already_exists_for_currency(): void
    {
        $dto = new AssignProductPriceDTO(productId: 1, currencyId: 2, price: 1100.00);
        $product = ProductMother::createDefault();
        $product->currency_id = 1;

        $existingPrice = new ProductPrice();

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $priceRepo = Mockery::mock(ProductPriceRepositoryInterface::class);
        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);

        $productRepo->shouldReceive('findById')->once()->with(1)->andReturn($product);
        $currencyRepo->shouldReceive('exists')->once()->with(2)->andReturn(true);
        $priceRepo->shouldReceive('findByProductAndCurrency')->once()->with(1, 2)->andReturn($existingPrice);

        $this->expectException(CurrencyAlreadyAssignedException::class);

        $action = new AssignProductPriceAction($productRepo, $priceRepo, $currencyRepo);
        $action->execute($dto);
    }
}
