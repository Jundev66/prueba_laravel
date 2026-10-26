<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Mothers;

use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;

final class CurrencyMother
{
    public static function createUsd(): Currency
    {
        $currency = new Currency();
        $currency->id = 1;
        $currency->name = 'US Dollar';
        $currency->symbol = '$';
        $currency->exchange_rate = 1.0000;

        return $currency;
    }

    public static function createEur(): Currency
    {
        $currency = new Currency();
        $currency->id = 2;
        $currency->name = 'Euro';
        $currency->symbol = '€';
        $currency->exchange_rate = 0.9200;

        return $currency;
    }

    public static function createVes(): Currency
    {
        $currency = new Currency();
        $currency->id = 3;
        $currency->name = 'Bolivar';
        $currency->symbol = 'Bs.';
        $currency->exchange_rate = 36.5000;

        return $currency;
    }
}
