<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Find;

use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductFinder
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(int $id): Product
    {
        $product = $this->productRepository->search($id);

        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }
}
