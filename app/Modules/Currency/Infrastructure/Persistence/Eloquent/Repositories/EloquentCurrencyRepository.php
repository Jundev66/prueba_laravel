<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Currency\Domain\Currency as DomainCurrency;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Filters\CurrencyEloquentFilter;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency as EloquentCurrency;

final class EloquentCurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(
        private readonly CurrencyEloquentFilter $filter = new CurrencyEloquentFilter(),
    ) {
    }

    /**
     * @return DomainCurrency[]
     */
    public function searchAll(?string $query = null): array
    {
        $builder = EloquentCurrency::query()->orderBy('id');

        $this->filter->apply($builder, $query);

        return $builder->get()
            ->map(fn (EloquentCurrency $c) => DomainCurrency::fromPrimitives($c->toArray()))
            ->all();
    }

    public function search(int $id): ?DomainCurrency
    {
        $currency = EloquentCurrency::query()->find($id);

        if ($currency === null) {
            return null;
        }

        return DomainCurrency::fromPrimitives($currency->toArray());
    }

    public function exists(int $id): bool
    {
        return EloquentCurrency::query()->where('id', $id)->exists();
    }
}
