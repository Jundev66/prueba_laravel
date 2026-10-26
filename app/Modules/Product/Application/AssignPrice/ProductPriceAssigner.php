<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\AssignPrice;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Domain\Exceptions\BaseCurrencyCannotBeSecondaryException;
use App\Modules\Product\Domain\Exceptions\CurrencyAlreadyAssignedException;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\ProductPrice;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductPriceAssigner
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductPriceRepositoryInterface $productPriceRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function __invoke(int $productId, int $currencyId, float $price): ProductPrice
    {
        $product = $this->productRepository->search($productId);

        if ($product === null) {
            throw new ProductNotFoundException($productId);
        }

        $currency = $this->currencyRepository->search($currencyId);

        if ($currency === null) {
            throw new CurrencyNotFoundException($currencyId);
        }

        if ($product->currencyId() === $currencyId) {
            throw new BaseCurrencyCannotBeSecondaryException($currencyId);
        }

        $existing = $this->productPriceRepository->searchByProductAndCurrency($productId, $currencyId);

        if ($existing !== null) {
            throw new CurrencyAlreadyAssignedException($currencyId);
        }

        $productPrice = ProductPrice::create(
            id: null,
            productId: $productId,
            currencyId: $currencyId,
            price: $price,
            currency: $currency,
        );

        return $this->productPriceRepository->save($productPrice);
    }
}
