<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Logout\UserTokenRevoker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class LogoutController extends Controller
{
    public function __construct(
        private readonly UserTokenRevoker $revoker,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Revoke active API Bearer token',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token revoked successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Token revoked successfully.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        ($this->revoker)($request->user());

        return response()->json([
            'message' => 'Token revoked successfully.',
        ]);
    }
}
