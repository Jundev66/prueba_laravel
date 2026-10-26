<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Exceptions;

use DomainException;

final class InvalidCurrencyAttributeException extends DomainException
{
    public static function emptyName(): self
    {
        return new self('Currency name cannot be empty.');
    }

    public static function emptySymbol(): self
    {
        return new self('Currency symbol cannot be empty.');
    }

    public static function invalidRate(float $rate): self
    {
        return new self(sprintf('Exchange rate must be greater than zero. Given: <%f>.', $rate));
    }
}
