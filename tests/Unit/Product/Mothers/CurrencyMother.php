<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Mothers;

use App\Modules\Currency\Domain\Currency;

final class CurrencyMother
{
    public static function createUsd(): Currency
    {
        return Currency::create(
            id: 1,
            name: 'US Dollar',
            symbol: '$',
            exchangeRate: 1.0000
        );
    }

    public static function createEur(): Currency
    {
        return Currency::create(
            id: 2,
            name: 'Euro',
            symbol: '€',
            exchangeRate: 0.9200
        );
    }

    public static function createVes(): Currency
    {
        return Currency::create(
            id: 3,
            name: 'Bolivar',
            symbol: 'Bs.',
            exchangeRate: 36.5000
        );
    }
}
