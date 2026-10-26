<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\SearchAll;

final readonly class SearchProductsQuery
{
    public function __construct(
        private ?string $query = null,
        private ?int $currencyId = null,
        private ?float $minPrice = null,
        private ?float $maxPrice = null,
        private int $perPage = 15,
        private int $page = 1,
    ) {
    }

    public function query(): ?string
    {
        return $this->query;
    }

    public function currencyId(): ?int
    {
        return $this->currencyId;
    }

    public function minPrice(): ?float
    {
        return $this->minPrice;
    }

    public function maxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function page(): int
    {
        return $this->page;
    }
}
