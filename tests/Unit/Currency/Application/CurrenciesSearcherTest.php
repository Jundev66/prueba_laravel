<?php

declare(strict_types=1);

namespace Tests\Unit\Currency\Application;

use App\Modules\Currency\Application\SearchAll\CurrenciesSearcher;
use App\Modules\Currency\Application\SearchAll\SearchCurrenciesQuery;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\CurrencyMother;

final class CurrenciesSearcherTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_returns_all_currencies(): void
    {
        $currencies = [CurrencyMother::createUsd(), CurrencyMother::createEur()];

        $currencyRepo = Mockery::mock(CurrencyRepositoryInterface::class);
        $currencyRepo->shouldReceive('searchAll')
            ->once()
            ->with('Dollar')
            ->andReturn($currencies);

        $searcher = new CurrenciesSearcher($currencyRepo);
        $result = $searcher(new SearchCurrenciesQuery('Dollar'));

        $this->assertSame($currencies, $result);
        $this->assertCount(2, $result);
    }
}
