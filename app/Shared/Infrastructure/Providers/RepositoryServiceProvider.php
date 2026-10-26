<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Currency\Infrastructure\Persistence\Eloquent\Repositories\EloquentCurrencyRepository;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductPriceRepository;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(ProductPriceRepositoryInterface::class, EloquentProductPriceRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, EloquentCurrencyRepository::class);
    }
}
