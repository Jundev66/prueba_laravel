<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain;

use App\Modules\Currency\Domain\Currency;

final class Product
{
    /**
     * @param ProductPrice[] $prices
     */
    private function __construct(
        private ?int $id,
        private string $name,
        private ?string $description,
        private float $price,
        private int $currencyId,
        private float $taxCost,
        private float $manufacturingCost,
        private ?Currency $currency = null,
        private array $prices = [],
    ) {
    }

    /**
     * @param ProductPrice[] $prices
     */
    public static function create(
        ?int $id,
        string $name,
        ?string $description,
        float $price,
        int $currencyId,
        float $taxCost,
        float $manufacturingCost,
        ?Currency $currency = null,
        array $prices = [],
    ): self {
        return new self($id, $name, $description, $price, $currencyId, $taxCost, $manufacturingCost, $currency, $prices);
    }

    public static function fromPrimitives(array $data, ?Currency $currency = null, array $prices = []): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (string) $data['name'],
            isset($data['description']) ? (string) $data['description'] : null,
            (float) $data['price'],
            (int) $data['currency_id'],
            (float) $data['tax_cost'],
            (float) $data['manufacturing_cost'],
            $currency,
            $prices
        );
    }

    public function id(): ?int
    {
        return $this->id;
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

    public function currency(): ?Currency
    {
        return $this->currency;
    }

    /**
     * @return ProductPrice[]
     */
    public function prices(): array
    {
        return $this->prices;
    }

    public function grossMargin(): float
    {
        return (float) ($this->price - ($this->taxCost + $this->manufacturingCost));
    }

    public function update(
        ?string $name = null,
        ?string $description = null,
        ?float $price = null,
        ?int $currencyId = null,
        ?float $taxCost = null,
        ?float $manufacturingCost = null,
    ): void {
        if ($name !== null) {
            $this->name = $name;
        }
        if ($description !== null) {
            $this->description = $description;
        }
        if ($price !== null) {
            $this->price = $price;
        }
        if ($currencyId !== null) {
            $this->currencyId = $currencyId;
        }
        if ($taxCost !== null) {
            $this->taxCost = $taxCost;
        }
        if ($manufacturingCost !== null) {
            $this->manufacturingCost = $manufacturingCost;
        }
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency_id' => $this->currencyId,
            'tax_cost' => $this->taxCost,
            'manufacturing_cost' => $this->manufacturingCost,
            'gross_margin' => $this->grossMargin(),
            'currency' => $this->currency?->toPrimitives(),
            'prices' => array_map(fn (ProductPrice $p) => $p->toPrimitives(), $this->prices),
        ];
    }
}
