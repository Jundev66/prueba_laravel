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
            new OA\Response(response: 200, description: 'Product details retrieved successfully'),
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
