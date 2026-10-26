<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Repositories;

use App\Modules\Product\Domain\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): Product;

    public function search(int $id): ?Product;

    public function delete(int $id): bool;

    /**
     * @return array{items: Product[], total: int, per_page: int, current_page: int, last_page: int}
     */
    public function searchAll(int $perPage = 15, int $page = 1): array;
}
