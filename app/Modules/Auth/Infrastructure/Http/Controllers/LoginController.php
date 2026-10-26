<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Authenticate\AuthenticateUserRequest;
use App\Modules\Auth\Application\Authenticate\UserAuthenticator;
use App\Modules\Auth\Infrastructure\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class LoginController extends Controller
{
    public function __construct(
        private readonly UserAuthenticator $authenticator,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Authenticate user and obtain API Bearer token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authentication successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                        new OA\Property(property: 'access_token', type: 'string', example: '1|xxxxxxxxxxxxxxxxxxxxxxxx'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Lead Architect'),
                                new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 429, description: 'Too many login attempts'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $command = new AuthenticateUserRequest(
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
            clientIp: (string) $request->ip()
        );

        $result = ($this->authenticator)($command);

        return response()->json($result);
    }
}
