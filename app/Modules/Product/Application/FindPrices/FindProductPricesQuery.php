<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\FindPrices;

final readonly class FindProductPricesQuery
{
    public function __construct(
        private int $productId,
    ) {
    }

    public function productId(): int
    {
        return $this->productId;
    }
}
