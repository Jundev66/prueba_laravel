<?php

declare(strict_types=1);

namespace Tests\Unit\Currency\Infrastructure;

use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Filters\CurrencyEloquentFilter;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models\Currency;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CurrencyEloquentFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
    }

    public function test_it_filters_currencies_by_name(): void
    {
        $filter = new CurrencyEloquentFilter();
        $builder = Currency::query();

        $filter->apply($builder, 'Dollar');

        $results = $builder->get();

        $this->assertCount(1, $results);
        $this->assertSame('US Dollar', $results->first()->name);
    }

    public function test_it_returns_all_when_query_is_null_or_empty(): void
    {
        $filter = new CurrencyEloquentFilter();
        $builder = Currency::query();

        $filter->apply($builder, null);

        $this->assertCount(3, $builder->get());
    }
}
