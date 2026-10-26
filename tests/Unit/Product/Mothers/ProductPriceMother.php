<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Mothers;

use App\Modules\Product\Domain\ProductPrice;

final class ProductPriceMother
{
    public static function createEur(int $productId = 1, float $price = 1100.00): ProductPrice
    {
        return ProductPrice::create(
            id: 1,
            productId: $productId,
            currencyId: 2,
            price: $price,
            currency: CurrencyMother::createEur()
        );
    }
}
