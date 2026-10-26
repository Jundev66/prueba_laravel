<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain;

use App\Modules\Currency\Domain\Currency;
use App\Modules\Product\Domain\Exceptions\InvalidProductAttributeException;

final class ProductPrice
{
    private function __construct(
        private ?int $id,
        private int $productId,
        private int $currencyId,
        private float $price,
        private ?Currency $currency = null,
    ) {
    }

    public static function create(?int $id, int $productId, int $currencyId, float $price, ?Currency $currency = null): self
    {
        self::guardPrice($price);
        self::guardId($productId, 'Product ID');
        self::guardId($currencyId, 'Currency ID');

        return new self($id, $productId, $currencyId, $price, $currency);
    }

    public static function fromPrimitives(array $data, ?Currency $currency = null): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['product_id'],
            (int) $data['currency_id'],
            (float) $data['price'],
            $currency
        );
    }

    public function id(): ?int
    {
        return $this->id;
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

    public function currency(): ?Currency
    {
        return $this->currency;
    }

    public function changePrice(float $newPrice): void
    {
        self::guardPrice($newPrice);
        $this->price = $newPrice;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'currency_id' => $this->currencyId,
            'price' => $this->price,
            'currency' => $this->currency?->toPrimitives(),
        ];
    }

    private static function guardPrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidProductAttributeException('Price must be greater than or equal to 0.');
        }
    }

    private static function guardId(int $id, string $field): void
    {
        if ($id <= 0) {
            throw new InvalidProductAttributeException(sprintf('%s must be a valid positive integer.', $field));
        }
    }
}
