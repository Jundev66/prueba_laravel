<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Actions;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\Actions\CreateProductAction;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class CreateProductActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_product_successfully_when_currency_exists(): void
    {
        $dto = ProductMother::createDto(['currency_id' => 1]);
        $expectedProduct = ProductMother::createDefault();

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepository = Mockery::mock(CurrencyRepositoryInterface::class);

        $currencyRepository->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        $productRepository->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andReturn($expectedProduct);

        $action = new CreateProductAction($productRepository, $currencyRepository);
        $result = $action->execute($dto);

        $this->assertSame($expectedProduct, $result);
    }

    public function test_it_throws_exception_when_currency_does_not_exist(): void
    {
        $dto = ProductMother::createDto(['currency_id' => 999]);

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $currencyRepository = Mockery::mock(CurrencyRepositoryInterface::class);

        $currencyRepository->shouldReceive('exists')
            ->once()
            ->with(999)
            ->andReturn(false);

        $productRepository->shouldNotReceive('create');

        $this->expectException(CurrencyNotFoundException::class);

        $action = new CreateProductAction($productRepository, $currencyRepository);
        $action->execute($dto);
    }
}
