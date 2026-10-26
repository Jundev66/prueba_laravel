<?php

declare(strict_types=1);

namespace Tests\Unit\Currency\Domain;

use App\Modules\Currency\Domain\Currency;
use App\Modules\Currency\Domain\Exceptions\InvalidCurrencyAttributeException;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function test_it_creates_currency_successfully(): void
    {
        $currency = Currency::create(1, 'US Dollar', '$', 1.0);

        $this->assertSame(1, $currency->id());
        $this->assertSame('US Dollar', $currency->name());
        $this->assertSame('$', $currency->symbol());
        $this->assertSame(1.0, $currency->exchangeRate());
    }

    public function test_it_throws_when_name_is_empty(): void
    {
        $this->expectException(InvalidCurrencyAttributeException::class);

        Currency::create(1, '', '$', 1.0);
    }

    public function test_it_throws_when_symbol_is_empty(): void
    {
        $this->expectException(InvalidCurrencyAttributeException::class);

        Currency::create(1, 'Euro', '', 0.92);
    }

    public function test_it_throws_when_rate_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyAttributeException::class);

        Currency::create(1, 'Euro', '€', -0.5);
    }
}
