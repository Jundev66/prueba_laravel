<?php

declare(strict_types=1);

namespace App\Modules\Currency\Application\SearchAll;

use App\Modules\Currency\Domain\Currency;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;

final readonly class CurrenciesSearcher
{
    public function __construct(
        private CurrencyRepositoryInterface $repository,
    ) {
    }

    /**
     * @return Currency[]
     */
    public function __invoke(SearchCurrenciesQuery $query): array
    {
        return $this->repository->searchAll($query->query());
    }
}
