<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\Find\FindProductQuery;
use App\Modules\Product\Application\Find\ProductFinder;
use App\Modules\Product\Infrastructure\Http\Resources\ProductResource;
use OpenApi\Attributes as OA;

final class GetProductController extends Controller
{
    public function __construct(
        private readonly ProductFinder $finder,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/products/{id}',
        summary: 'Get product by ID',
        tags: ['Products'],
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
                description: 'Product details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
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
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Product not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(int $id): ProductResource
    {
        $product = ($this->finder)(new FindProductQuery($id));

        return new ProductResource($product);
    }
}
