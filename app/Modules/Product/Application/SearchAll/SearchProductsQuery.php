<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\SearchAll;

final readonly class SearchProductsQuery
{
    public function __construct(
        private int $perPage = 15,
        private int $page = 1,
    ) {
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
