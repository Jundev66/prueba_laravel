<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Mothers;

use App\Modules\Product\Domain\Product;

final class ProductMother
{
    public static function createDefault(): Product
    {
        return Product::create(
            id: 1,
            name: 'Test Laptop',
            description: 'A powerful laptop for development',
            price: 1200.0000,
            currencyId: 1,
            taxCost: 192.0000,
            manufacturingCost: 600.0000,
            currency: CurrencyMother::createUsd()
        );
    }
}
