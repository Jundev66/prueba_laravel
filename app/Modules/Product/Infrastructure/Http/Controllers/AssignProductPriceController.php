<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\Actions\AssignProductPriceAction;
use App\Modules\Product\Application\DTOs\AssignProductPriceDTO;
use App\Modules\Product\Infrastructure\Http\Requests\StoreProductPriceRequest;
use App\Modules\Product\Infrastructure\Http\Resources\ProductPriceResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class AssignProductPriceController extends Controller
{
    public function __construct(
        private readonly AssignProductPriceAction $action,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/products/{id}/prices',
        summary: 'Assign a secondary currency price to a product',
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['currency_id', 'price'],
                properties: [
                    new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1380.00),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Price assigned successfully'),
            new OA\Response(response: 404, description: 'Product or currency not found'),
            new OA\Response(response: 422, description: 'Validation error, currency already assigned, or base currency clash'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(int $id, StoreProductPriceRequest $request): JsonResponse
    {
        $dto = AssignProductPriceDTO::fromArray($id, $request->validated());
        $price = $this->action->execute($dto);

        return (new ProductPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }
}
