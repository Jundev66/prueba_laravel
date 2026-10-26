<?php

declare(strict_types=1);

use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Domain\Exceptions\TooManyLoginAttemptsException;
use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Exceptions\InvalidCurrencyAttributeException;
use App\Modules\Product\Domain\Exceptions\BaseCurrencyCannotBeSecondaryException;
use App\Modules\Product\Domain\Exceptions\CurrencyAlreadyAssignedException;
use App\Modules\Product\Domain\Exceptions\InvalidProductAttributeException;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Shared\Infrastructure\Middleware\SecurityHeadersMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $e, Request $request): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Unauthenticated.',
                ],
            ], 401);
        });

        $exceptions->render(function (ProductNotFoundException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'PRODUCT_NOT_FOUND',
                    'message' => $e->getMessage(),
                ],
            ], 404);
        });

        $exceptions->render(function (CurrencyNotFoundException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'CURRENCY_NOT_FOUND',
                    'message' => $e->getMessage(),
                ],
            ], 404);
        });

        $exceptions->render(function (InvalidCurrencyAttributeException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_CURRENCY_ATTRIBUTE',
                    'message' => $e->getMessage(),
                ],
            ], 422);
        });

        $exceptions->render(function (CurrencyAlreadyAssignedException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'CURRENCY_PRICE_ALREADY_EXISTS',
                    'message' => $e->getMessage(),
                ],
            ], 422);
        });

        $exceptions->render(function (BaseCurrencyCannotBeSecondaryException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'BASE_CURRENCY_CANNOT_BE_SECONDARY',
                    'message' => $e->getMessage(),
                ],
            ], 422);
        });

        $exceptions->render(function (InvalidProductAttributeException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_PRODUCT_ATTRIBUTE',
                    'message' => $e->getMessage(),
                ],
            ], 422);
        });

        $exceptions->render(function (InvalidCredentialsException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => $e->getMessage(),
                ],
            ], 401);
        });

        $exceptions->render(function (TooManyLoginAttemptsException $e): JsonResponse {
            return response()->json([
                'error' => [
                    'code' => 'TOO_MANY_LOGIN_ATTEMPTS',
                    'message' => $e->getMessage(),
                ],
            ], 429);
        });
    })->create();
