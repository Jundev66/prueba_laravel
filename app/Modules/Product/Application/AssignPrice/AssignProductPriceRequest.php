<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\AssignPrice;

final readonly class AssignProductPriceRequest
{
    public function __construct(
        public int $productId,
        public int $currencyId,
        public float $price,
    ) {
    }
}
