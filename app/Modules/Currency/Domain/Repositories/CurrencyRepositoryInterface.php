<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Repositories;

use App\Modules\Currency\Domain\Currency;

interface CurrencyRepositoryInterface
{
    /**
     * @return Currency[]
     */
    public function searchAll(?string $query = null): array;

    public function search(int $id): ?Currency;

    public function exists(int $id): bool;
}
