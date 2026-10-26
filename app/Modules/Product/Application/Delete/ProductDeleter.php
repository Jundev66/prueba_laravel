<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Delete;

use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductDeleter
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(DeleteProductCommand $command): void
    {
        $product = $this->productRepository->search($command->id());

        if ($product === null) {
            throw new ProductNotFoundException($command->id());
        }

        $this->productRepository->delete($command->id());
    }
}
