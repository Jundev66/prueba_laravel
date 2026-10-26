<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Create;

final readonly class CreateProductRequest
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $currencyId,
        public float $taxCost,
        public float $manufacturingCost,
    ) {
    }
}
