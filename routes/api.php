<?php

declare(strict_types=1);

use App\Modules\Auth\Infrastructure\Http\Controllers\LoginController;
use App\Modules\Auth\Infrastructure\Http\Controllers\LogoutController;
use App\Modules\Product\Infrastructure\Http\Controllers\AssignProductPriceController;
use App\Modules\Product\Infrastructure\Http\Controllers\CreateProductController;
use App\Modules\Product\Infrastructure\Http\Controllers\DeleteProductController;
use App\Modules\Product\Infrastructure\Http\Controllers\GetProductController;
use App\Modules\Product\Infrastructure\Http\Controllers\ListProductPricesController;
use App\Modules\Product\Infrastructure\Http\Controllers\ListProductsController;
use App\Modules\Product\Infrastructure\Http\Controllers\UpdateProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', LoginController::class)->name('login');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
        Route::post('/auth/logout', LogoutController::class);

        Route::get('/currencies', \App\Modules\Currency\Infrastructure\Http\Controllers\ListCurrenciesController::class);

        Route::get('/products', ListProductsController::class);
        Route::post('/products', CreateProductController::class);
        Route::get('/products/{id}', GetProductController::class)->whereNumber('id');
        Route::match(['put', 'patch'], '/products/{id}', UpdateProductController::class)->whereNumber('id');
        Route::delete('/products/{id}', DeleteProductController::class)->whereNumber('id');

        Route::get('/products/{id}/prices', ListProductPricesController::class)->whereNumber('id');
        Route::post('/products/{id}/prices', AssignProductPriceController::class)->whereNumber('id');
    });
});

