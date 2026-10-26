<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\SearchAll\ProductsSearcher;
use App\Modules\Product\Application\SearchAll\SearchProductsQuery;
use App\Modules\Product\Infrastructure\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class ListProductsController extends Controller
{
    public function __construct(
        private readonly ProductsSearcher $searcher,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/products',
        summary: 'Get paginated list of products',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of products retrieved successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $result = ($this->searcher)(new SearchProductsQuery($perPage, $page));

        return response()->json([
            'data' => ProductResource::collection($result['items']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page'],
            ],
            'links' => [
                'first' => url('/api/v1/products?page=1'),
                'last' => url('/api/v1/products?page='.$result['last_page']),
            ],
        ]);
    }
}
