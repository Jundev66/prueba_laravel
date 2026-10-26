<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Repositories;

use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Product;

    public function create(CreateProductDTO $dto): Product;

    public function update(Product $product, UpdateProductDTO $dto): Product;

    public function delete(Product $product): bool;
}
