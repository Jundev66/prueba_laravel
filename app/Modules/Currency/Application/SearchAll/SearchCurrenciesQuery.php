<?php

declare(strict_types=1);

namespace App\Modules\Currency\Application\SearchAll;

final readonly class SearchCurrenciesQuery
{
    public function __construct(
        private ?string $query = null,
    ) {
    }

    public function query(): ?string
    {
        return $this->query;
    }
}
