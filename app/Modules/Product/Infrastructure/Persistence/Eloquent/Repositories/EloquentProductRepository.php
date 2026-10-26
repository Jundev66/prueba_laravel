<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('currency')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::query()
            ->with(['currency', 'prices.currency'])
            ->find($id);
    }

    public function create(CreateProductDTO $dto): Product
    {
        $product = Product::query()->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'currency_id' => $dto->currencyId,
            'tax_cost' => $dto->taxCost,
            'manufacturing_cost' => $dto->manufacturingCost,
        ]);

        $product->load('currency');

        return $product;
    }

    public function update(Product $product, UpdateProductDTO $dto): Product
    {
        $product->update($dto->toFilteredArray());
        $product->load('currency');

        return $product;
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
