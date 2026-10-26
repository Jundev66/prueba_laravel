<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\DTOs;

final readonly class CreateProductDTO
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

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            price: (float) $data['price'],
            currencyId: (int) $data['currency_id'],
            taxCost: (float) $data['tax_cost'],
            manufacturingCost: (float) $data['manufacturing_cost'],
        );
    }
}
