<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Product\Application\DTOs\AssignProductPriceDTO;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;

final class EloquentProductPriceRepository implements ProductPriceRepositoryInterface
{
    public function getPricesByProductId(int $productId): Collection
    {
        return ProductPrice::query()
            ->with('currency')
            ->where('product_id', $productId)
            ->get();
    }

    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice
    {
        return ProductPrice::query()
            ->where('product_id', $productId)
            ->where('currency_id', $currencyId)
            ->first();
    }

    public function create(AssignProductPriceDTO $dto): ProductPrice
    {
        $price = ProductPrice::query()->create([
            'product_id' => $dto->productId,
            'currency_id' => $dto->currencyId,
            'price' => $dto->price,
        ]);

        $price->load('currency');

        return $price;
    }
}
