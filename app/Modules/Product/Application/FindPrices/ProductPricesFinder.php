<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\FindPrices;

use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\ProductPrice;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductPricesFinder
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductPriceRepositoryInterface $productPriceRepository,
    ) {
    }

    /**
     * @return ProductPrice[]
     */
    public function __invoke(int $productId): array
    {
        $product = $this->productRepository->search($productId);

        if ($product === null) {
            throw new ProductNotFoundException($productId);
        }

        return $this->productPriceRepository->searchByProduct($productId);
    }
}
