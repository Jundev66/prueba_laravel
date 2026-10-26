<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Persistence\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

final class CurrencyEloquentFilter
{
    /**
     * @param Builder<\App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency> $builder
     * @return Builder<\App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency>
     */
    public function apply(Builder $builder, ?string $name = null): Builder
    {
        return $builder->when(
            $name !== null && trim($name) !== '',
            fn (Builder $b) => $b->where('name', 'like', '%'.trim($name).'%')
        );
    }
}
