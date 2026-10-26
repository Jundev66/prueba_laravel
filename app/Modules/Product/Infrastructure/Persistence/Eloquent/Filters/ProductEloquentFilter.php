<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Persistence\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

final class ProductEloquentFilter
{
    /**
     * @param Builder<\App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product> $builder
     * @return Builder<\App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product>
     */
    public function apply(
        Builder $builder,
        ?string $name = null,
        ?int $currencyId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
    ): Builder {
        return $builder
            ->when($name !== null && trim($name) !== '', fn (Builder $b) => $this->filterByName($b, $name))
            ->when($currencyId !== null, fn (Builder $b) => $this->filterByCurrency($b, $currencyId))
            ->when($minPrice !== null, fn (Builder $b) => $this->filterByMinPrice($b, $minPrice))
            ->when($maxPrice !== null, fn (Builder $b) => $this->filterByMaxPrice($b, $maxPrice));
    }

    private function filterByName(Builder $builder, string $name): void
    {
        $term = '%'.trim($name).'%';
        $builder->where('name', 'like', $term);
    }

    private function filterByCurrency(Builder $builder, int $currencyId): void
    {
        $builder->where('currency_id', $currencyId);
    }

    private function filterByMinPrice(Builder $builder, float $minPrice): void
    {
        $builder->where('price', '>=', $minPrice);
    }

    private function filterByMaxPrice(Builder $builder, float $maxPrice): void
    {
        $builder->where('price', '<=', $maxPrice);
    }
}
