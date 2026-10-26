<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Currency\Domain\Currency as DomainCurrency;
use App\Modules\Product\Domain\Product as DomainProduct;
use App\Modules\Product\Domain\ProductPrice as DomainProductPrice;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Filters\ProductEloquentFilter;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product as EloquentProduct;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice as EloquentProductPrice;
use Illuminate\Support\Facades\DB;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly ProductEloquentFilter $filter = new ProductEloquentFilter(),
    ) {
    }
    public function save(DomainProduct $product): DomainProduct
    {
        return DB::transaction(function () use ($product): DomainProduct {
            if ($product->id() !== null) {
                $eloquent = EloquentProduct::query()->findOrFail($product->id());
            } else {
                $eloquent = new EloquentProduct();
            }

            $eloquent->name = $product->name();
            $eloquent->description = $product->description();
            $eloquent->price = $product->price();
            $eloquent->currency_id = $product->currencyId();
            $eloquent->tax_cost = $product->taxCost();
            $eloquent->manufacturing_cost = $product->manufacturingCost();
            $eloquent->save();

            $eloquent->load(['currency', 'prices.currency']);

            return $this->toDomain($eloquent);
        });
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
        return DB::transaction(function () use ($id): bool {
            $eloquent = EloquentProduct::query()->find($id);

            if ($eloquent === null) {
                return false;
            }

            return (bool) $eloquent->delete();
        });
    }

    /**
     * @return array{items: DomainProduct[], total: int, per_page: int, current_page: int, last_page: int}
     */
    public function searchAll(
        ?string $query = null,
        ?int $currencyId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        int $perPage = 15,
        int $page = 1
    ): array {
        $builder = EloquentProduct::query()->with(['currency', 'prices.currency']);

        $this->filter->apply($builder, $query, $currencyId, $minPrice, $maxPrice);

        $paginator = $builder->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

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
