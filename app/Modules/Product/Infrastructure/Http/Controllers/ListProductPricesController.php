<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\FindPrices\FindProductPricesQuery;
use App\Modules\Product\Application\FindPrices\ProductPricesFinder;
use App\Modules\Product\Infrastructure\Http\Resources\ProductPriceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ListProductPricesController extends Controller
{
    public function __construct(
        private readonly ProductPricesFinder $finder,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/products/{id}/prices',
        summary: 'Get all secondary prices for a product',
        tags: ['Product Prices'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product secondary prices retrieved successfully',
                content: new OA\JsonContent(
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
                )
            ),
            new OA\Response(response: 404, description: 'Product not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(int $id): AnonymousResourceCollection
    {
        $prices = ($this->finder)(new FindProductPricesQuery($id));

        return ProductPriceResource::collection($prices);
    }
}
