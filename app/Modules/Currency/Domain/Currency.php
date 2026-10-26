<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain;

final class Currency
{
    private function __construct(
        private int $id,
        private string $name,
        private string $symbol,
        private float $exchangeRate,
    ) {
    }

    public static function create(int $id, string $name, string $symbol, float $exchangeRate): self
    {
        return new self($id, $name, $symbol, $exchangeRate);
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            (int) $data['id'],
            (string) $data['name'],
            (string) $data['symbol'],
            (float) $data['exchange_rate']
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function exchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'exchange_rate' => $this->exchangeRate,
        ];
    }
}
