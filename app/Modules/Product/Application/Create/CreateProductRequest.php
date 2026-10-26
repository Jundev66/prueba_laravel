<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Create;

final readonly class CreateProductRequest
{
    public function __construct(
        private string $name,
        private ?string $description,
        private float $price,
        private int $currencyId,
        private float $taxCost,
        private float $manufacturingCost,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function currencyId(): int
    {
        return $this->currencyId;
    }

    public function taxCost(): float
    {
        return $this->taxCost;
    }

    public function manufacturingCost(): float
    {
        return $this->manufacturingCost;
    }
}
