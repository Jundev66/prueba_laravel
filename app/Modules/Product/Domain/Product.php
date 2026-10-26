<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain;

use App\Modules\Currency\Domain\Currency;
use App\Modules\Product\Domain\Exceptions\InvalidProductAttributeException;

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
        self::guardName($name);
        self::guardPrice($price);
        self::guardCurrencyId($currencyId);
        self::guardCost($taxCost, 'Tax cost');
        self::guardCost($manufacturingCost, 'Manufacturing cost');

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

    public function changeName(string $name): void
    {
        self::guardName($name);
        $this->name = $name;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function changePrice(float $price): void
    {
        self::guardPrice($price);
        $this->price = $price;
    }

    public function changeCurrencyId(int $currencyId): void
    {
        self::guardCurrencyId($currencyId);
        $this->currencyId = $currencyId;
    }

    public function changeTaxCost(float $taxCost): void
    {
        self::guardCost($taxCost, 'Tax cost');
        $this->taxCost = $taxCost;
    }

    public function changeManufacturingCost(float $manufacturingCost): void
    {
        self::guardCost($manufacturingCost, 'Manufacturing cost');
        $this->manufacturingCost = $manufacturingCost;
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

    private static function guardName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidProductAttributeException('Product name cannot be empty.');
        }
    }

    private static function guardPrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidProductAttributeException('Product price must be greater than or equal to 0.');
        }
    }

    private static function guardCurrencyId(int $currencyId): void
    {
        if ($currencyId <= 0) {
            throw new InvalidProductAttributeException('Product currency ID must be a valid positive integer.');
        }
    }

    private static function guardCost(float $cost, string $field): void
    {
        if ($cost < 0) {
            throw new InvalidProductAttributeException(sprintf('%s must be greater than or equal to 0.', $field));
        }
    }
}
