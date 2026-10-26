<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\DTOs;

final readonly class UpdateProductDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?float $price = null,
        public ?int $currencyId = null,
        public ?float $taxCost = null,
        public ?float $manufacturingCost = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) ? (string) $data['name'] : null,
            description: array_key_exists('description', $data) ? ($data['description'] !== null ? (string) $data['description'] : null) : null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            currencyId: isset($data['currency_id']) ? (int) $data['currency_id'] : null,
            taxCost: isset($data['tax_cost']) ? (float) $data['tax_cost'] : null,
            manufacturingCost: isset($data['manufacturing_cost']) ? (float) $data['manufacturing_cost'] : null,
        );
    }

    public function toFilteredArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency_id' => $this->currencyId,
            'tax_cost' => $this->taxCost,
            'manufacturing_cost' => $this->manufacturingCost,
        ], fn ($value) => $value !== null);
    }
}
