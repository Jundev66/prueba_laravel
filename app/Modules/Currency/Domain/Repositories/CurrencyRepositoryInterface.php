<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Repositories;

use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

interface CurrencyRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Currency;

    public function exists(int $id): bool;
}
