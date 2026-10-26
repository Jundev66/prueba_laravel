<?php

declare(strict_types=1);

namespace App\Modules\Currency\Application\Actions;

use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListCurrenciesAction
{
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function execute(): Collection
    {
        return $this->currencyRepository->all();
    }
}
