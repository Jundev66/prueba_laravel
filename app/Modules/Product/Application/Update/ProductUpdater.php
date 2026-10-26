<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Update;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductUpdater
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function __invoke(
        int $id,
        ?string $name = null,
        ?string $description = null,
        ?float $price = null,
        ?int $currencyId = null,
        ?float $taxCost = null,
        ?float $manufacturingCost = null,
    ): Product {
        $product = $this->productRepository->search($id);

        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        if ($currencyId !== null && !$this->currencyRepository->exists($currencyId)) {
            throw new CurrencyNotFoundException($currencyId);
        }

        $product->update($name, $description, $price, $currencyId, $taxCost, $manufacturingCost);

        return $this->productRepository->save($product);
    }
}
