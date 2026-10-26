<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Actions;

use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListProductPricesAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductPriceRepositoryInterface $productPriceRepository,
    ) {
    }

    public function execute(int $productId): Collection
    {
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new ProductNotFoundException($productId);
        }

        return $this->productPriceRepository->getPricesByProductId($productId);
    }
}
