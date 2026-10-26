<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Infrastructure\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

final class LoginController extends Controller
{
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
        $throttleKey = Str::transliterate(Str::lower($request->validated('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'error' => [
                    'code' => 'TOO_MANY_LOGIN_ATTEMPTS',
                    'message' => sprintf('Too many login attempts. Please try again in %d seconds.', $seconds),
                ],
            ], 429);
        }

        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user === null || !Hash::check((string) $request->validated('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'The provided credentials do not match our records.',
                ],
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        $token = $user->createToken('api_token', ['*'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
