<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Clean Architecture RESTful API for Products and Multi-Currency Pricing.',
    title: 'Products Multi-Currency API'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'Sanctum',
    scheme: 'bearer',
    description: 'Enter your Bearer token obtained from /api/v1/auth/login'
)]
final class OpenApiDocumentation
{
}
