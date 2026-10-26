<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Update;

final readonly class UpdateProductRequest
{
    public function __construct(
        private int $id,
        private ?string $name = null,
        private ?string $description = null,
        private ?float $price = null,
        private ?int $currencyId = null,
        private ?float $taxCost = null,
        private ?float $manufacturingCost = null,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function price(): ?float
    {
        return $this->price;
    }

    public function currencyId(): ?int
    {
        return $this->currencyId;
    }

    public function taxCost(): ?float
    {
        return $this->taxCost;
    }

    public function manufacturingCost(): ?float
    {
        return $this->manufacturingCost;
    }
}
