<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\AssignPrice;

final readonly class AssignProductPriceRequest
{
    public function __construct(
        private int $productId,
        private int $currencyId,
        private float $price,
    ) {
    }

    public function productId(): int
    {
        return $this->productId;
    }

    public function currencyId(): int
    {
        return $this->currencyId;
    }

    public function price(): float
    {
        return $this->price;
    }
}
