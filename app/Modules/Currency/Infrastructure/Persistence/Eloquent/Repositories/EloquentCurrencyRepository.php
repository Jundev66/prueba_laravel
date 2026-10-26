<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCurrencyRepository implements CurrencyRepositoryInterface
{
    public function all(): Collection
    {
        return Currency::query()->orderBy('name')->get();
    }

    public function findById(int $id): ?Currency
    {
        return Currency::query()->find($id);
    }

    public function exists(int $id): bool
    {
        return Currency::query()->where('id', $id)->exists();
    }
}
