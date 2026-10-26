<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\Delete\DeleteProductCommand;
use App\Modules\Product\Application\Delete\ProductDeleter;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

final class DeleteProductController extends Controller
{
    public function __construct(
        private readonly ProductDeleter $deleter,
    ) {
    }

    #[OA\Delete(
        path: '/api/v1/products/{id}',
        summary: 'Delete a product',
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
            new OA\Response(response: 204, description: 'Product deleted successfully'),
            new OA\Response(response: 404, description: 'Product not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(int $id): Response
    {
        ($this->deleter)(new DeleteProductCommand($id));

        return response()->noContent();
    }
}
