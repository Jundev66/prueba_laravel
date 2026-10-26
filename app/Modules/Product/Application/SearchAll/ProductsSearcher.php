<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\SearchAll;

use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductsSearcher
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * @return array{items: Product[], total: int, per_page: int, current_page: int, last_page: int}
     */
    public function __invoke(SearchProductsQuery $query): array
    {
        return $this->productRepository->searchAll($query->perPage(), $query->page());
    }
}
