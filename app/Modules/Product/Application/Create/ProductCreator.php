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

    public function __invoke(
        string $name,
        ?string $description,
        float $price,
        int $currencyId,
        float $taxCost,
        float $manufacturingCost,
    ): Product {
        $currency = $this->currencyRepository->search($currencyId);

        if ($currency === null) {
            throw new CurrencyNotFoundException($currencyId);
        }

        $product = Product::create(
            id: null,
            name: $name,
            description: $description,
            price: $price,
            currencyId: $currencyId,
            taxCost: $taxCost,
            manufacturingCost: $manufacturingCost,
            currency: $currency,
        );

        return $this->productRepository->save($product);
    }
}
