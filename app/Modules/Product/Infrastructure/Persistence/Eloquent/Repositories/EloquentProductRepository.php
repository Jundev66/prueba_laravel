<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Currency\Domain\Currency as DomainCurrency;
use App\Modules\Product\Domain\Product as DomainProduct;
use App\Modules\Product\Domain\ProductPrice as DomainProductPrice;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product as EloquentProduct;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice as EloquentProductPrice;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function save(DomainProduct $product): DomainProduct
    {
        $attributes = [
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price(),
            'currency_id' => $product->currencyId(),
            'tax_cost' => $product->taxCost(),
            'manufacturing_cost' => $product->manufacturingCost(),
        ];

        if ($product->id() !== null) {
            $eloquent = EloquentProduct::query()->findOrFail($product->id());
            $eloquent->update($attributes);
        } else {
            $eloquent = EloquentProduct::query()->create($attributes);
        }

        $eloquent->load(['currency', 'prices.currency']);

        return $this->toDomain($eloquent);
    }

    public function search(int $id): ?DomainProduct
    {
        $eloquent = EloquentProduct::query()
            ->with(['currency', 'prices.currency'])
            ->find($id);

        if ($eloquent === null) {
            return null;
        }

        return $this->toDomain($eloquent);
    }

    public function delete(int $id): bool
    {
        $eloquent = EloquentProduct::query()->find($id);

        if ($eloquent === null) {
            return false;
        }

        return (bool) $eloquent->delete();
    }

    /**
     * @return array{items: DomainProduct[], total: int, per_page: int, current_page: int, last_page: int}
     */
    public function searchAll(int $perPage = 15, int $page = 1): array
    {
        $paginator = EloquentProduct::query()
            ->with(['currency', 'prices.currency'])
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = collect($paginator->items())
            ->map(fn (EloquentProduct $p) => $this->toDomain($p))
            ->all();

        return [
            'items' => $items,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    private function toDomain(EloquentProduct $eloquent): DomainProduct
    {
        $currency = $eloquent->currency !== null
            ? DomainCurrency::fromPrimitives($eloquent->currency->toArray())
            : null;

        $prices = $eloquent->prices->map(function (EloquentProductPrice $price): DomainProductPrice {
            $priceCurrency = $price->currency !== null
                ? DomainCurrency::fromPrimitives($price->currency->toArray())
                : null;

            return DomainProductPrice::fromPrimitives($price->toArray(), $priceCurrency);
        })->all();

        return DomainProduct::fromPrimitives($eloquent->toArray(), $currency, $prices);
    }
}
