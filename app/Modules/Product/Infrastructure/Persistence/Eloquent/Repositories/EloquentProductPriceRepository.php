<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Currency\Domain\Currency as DomainCurrency;
use App\Modules\Product\Domain\ProductPrice as DomainProductPrice;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice as EloquentProductPrice;

use Illuminate\Support\Facades\DB;

final class EloquentProductPriceRepository implements ProductPriceRepositoryInterface
{
    public function save(DomainProductPrice $price): DomainProductPrice
    {
        return DB::transaction(function () use ($price): DomainProductPrice {
            $eloquent = EloquentProductPrice::query()
                ->where('product_id', $price->productId())
                ->where('currency_id', $price->currencyId())
                ->first();

            if ($eloquent === null) {
                $eloquent = new EloquentProductPrice();
                $eloquent->product_id = $price->productId();
                $eloquent->currency_id = $price->currencyId();
            }

            $eloquent->price = $price->price();
            $eloquent->save();

            $eloquent->load('currency');

            $currency = $eloquent->currency !== null
                ? DomainCurrency::fromPrimitives($eloquent->currency->toArray())
                : null;

            return DomainProductPrice::fromPrimitives($eloquent->toArray(), $currency);
        });
    }

    /**
     * @return DomainProductPrice[]
     */
    public function searchByProduct(int $productId): array
    {
        return EloquentProductPrice::query()
            ->with('currency')
            ->where('product_id', $productId)
            ->get()
            ->map(function (EloquentProductPrice $item): DomainProductPrice {
                $currency = $item->currency !== null
                    ? DomainCurrency::fromPrimitives($item->currency->toArray())
                    : null;

                return DomainProductPrice::fromPrimitives($item->toArray(), $currency);
            })
            ->all();
    }

    public function searchByProductAndCurrency(int $productId, int $currencyId): ?DomainProductPrice
    {
        $item = EloquentProductPrice::query()
            ->with('currency')
            ->where('product_id', $productId)
            ->where('currency_id', $currencyId)
            ->first();

        if ($item === null) {
            return null;
        }

        $currency = $item->currency !== null
            ? DomainCurrency::fromPrimitives($item->currency->toArray())
            : null;

        return DomainProductPrice::fromPrimitives($item->toArray(), $currency);
    }
}
