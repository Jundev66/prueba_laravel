<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\DTOs;

final readonly class AssignProductPriceDTO
{
    public function __construct(
        public int $productId,
        public int $currencyId,
        public float $price,
    ) {
    }

    public static function fromArray(int $productId, array $data): self
    {
        return new self(
            productId: $productId,
            currencyId: (int) $data['currency_id'],
            price: (float) $data['price'],
        );
    }
}
