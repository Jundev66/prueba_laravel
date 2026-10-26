<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Create;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductCreator
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function __invoke(CreateProductRequest $request): Product
    {
        $currency = $this->currencyRepository->search($request->currencyId);

        if ($currency === null) {
            throw new CurrencyNotFoundException($request->currencyId);
        }

        $product = Product::create(
            id: null,
            name: $request->name,
            description: $request->description,
            price: $request->price,
            currencyId: $request->currencyId,
            taxCost: $request->taxCost,
            manufacturingCost: $request->manufacturingCost,
            currency: $currency,
        );

        return $this->productRepository->save($product);
    }
}
