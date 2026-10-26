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

    public function __invoke(AssignProductPriceRequest $request): void
    {
        $product = $this->productRepository->search($request->productId());

        if ($product === null) {
            throw new ProductNotFoundException($request->productId());
        }

        $currency = $this->currencyRepository->search($request->currencyId());

        if ($currency === null) {
            throw new CurrencyNotFoundException($request->currencyId());
        }

        if ($product->currencyId() === $request->currencyId()) {
            throw new BaseCurrencyCannotBeSecondaryException($request->currencyId());
        }

        $existing = $this->productPriceRepository->searchByProductAndCurrency($request->productId(), $request->currencyId());

        if ($existing !== null) {
            throw new CurrencyAlreadyAssignedException($request->currencyId());
        }

        $productPrice = ProductPrice::create(
            id: null,
            productId: $request->productId(),
            currencyId: $request->currencyId(),
            price: $request->price(),
            currency: $currency,
        );

        $this->productPriceRepository->save($productPrice);
    }
}
