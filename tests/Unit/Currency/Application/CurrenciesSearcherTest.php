<?php

declare(strict_types=1);

namespace Tests\Unit\Currency\Application;

use App\Modules\Currency\Application\SearchAll\CurrenciesSearcher;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\CurrencyMother;

final class CurrenciesSearcherTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_all_currencies(): void
    {
        $currencies = [CurrencyMother::createUsd(), CurrencyMother::createEur()];

        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);
        $currencyRepo->shouldReceive('searchAll')
            ->once()
            ->andReturn($currencies);

        $searcher = new CurrenciesSearcher($currencyRepo);
        $result = $searcher();

        $this->assertSame($currencies, $result);
        $this->assertCount(2, $result);
    }
}
