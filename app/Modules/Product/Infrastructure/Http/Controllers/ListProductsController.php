<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\SearchAll\ProductsSearcher;
use App\Modules\Product\Application\SearchAll\SearchProductsQuery;
use App\Modules\Product\Infrastructure\Http\Requests\ListProductsRequest;
use App\Modules\Product\Infrastructure\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class ListProductsController extends Controller
{
    public function __construct(
        private readonly ProductsSearcher $searcher,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/products',
        summary: 'Get paginated list of products with optional filters',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                description: 'Search keyword matching product name',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Laptop')
            ),
            new OA\Parameter(
                name: 'currency_id',
                in: 'query',
                description: 'Filter products by base currency ID',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'min_price',
                in: 'query',
                description: 'Filter products with price greater than or equal to value',
                required: false,
                schema: new OA\Schema(type: 'number', format: 'float', example: 100.00)
            ),
            new OA\Parameter(
                name: 'max_price',
                in: 'query',
                description: 'Filter products with price less than or equal to value',
                required: false,
                schema: new OA\Schema(type: 'number', format: 'float', example: 2500.00)
            ),
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
                description: 'Paginated list of products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 16'),
                                    new OA\Property(property: 'description', type: 'string', example: 'High performance laptop'),
                                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1500.00),
                                    new OA\Property(
                                        property: 'currency',
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                                            new OA\Property(property: 'symbol', type: 'string', example: '$'),
                                            new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 1.0),
                                        ]
                                    ),
                                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 240.00),
                                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 850.00),
                                    new OA\Property(property: 'gross_margin', type: 'number', format: 'float', example: 410.00, description: 'Calculated as price - (tax_cost + manufacturing_cost)'),
                                    new OA\Property(
                                        property: 'prices',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                                new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 1380.00),
                                                new OA\Property(
                                                    property: 'currency',
                                                    type: 'object',
                                                    properties: [
                                                        new OA\Property(property: 'id', type: 'integer', example: 2),
                                                        new OA\Property(property: 'name', type: 'string', example: 'Euro'),
                                                        new OA\Property(property: 'symbol', type: 'string', example: '€'),
                                                        new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 0.92),
                                                    ]
                                                ),
                                            ]
                                        )
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', example: 10),
                                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 422,
                description: 'Query parameter validation error (e.g. per_page out of range [1-100], invalid currency_id)'
            ),
        ]
    )]
    public function __invoke(ListProductsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = isset($validated['q']) ? (string) $validated['q'] : null;
        $currencyId = isset($validated['currency_id']) ? (int) $validated['currency_id'] : null;
        $minPrice = isset($validated['min_price']) ? (float) $validated['min_price'] : null;
        $maxPrice = isset($validated['max_price']) ? (float) $validated['max_price'] : null;
        $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 15;
        $page = isset($validated['page']) ? (int) $validated['page'] : 1;

        $result = ($this->searcher)(new SearchProductsQuery(
            query: $query,
            currencyId: $currencyId,
            minPrice: $minPrice,
            maxPrice: $maxPrice,
            perPage: $perPage,
            page: $page
        ));

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
