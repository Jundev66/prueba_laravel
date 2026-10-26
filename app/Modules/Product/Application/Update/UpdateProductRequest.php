<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Update;

final readonly class UpdateProductRequest
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $description = null,
        public ?float $price = null,
        public ?int $currencyId = null,
        public ?float $taxCost = null,
        public ?float $manufacturingCost = null,
    ) {
    }
}
