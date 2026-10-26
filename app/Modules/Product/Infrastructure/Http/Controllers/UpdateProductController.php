<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\Update\ProductUpdater;
use App\Modules\Product\Application\Update\UpdateProductRequest as ApplicationUpdateRequest;
use App\Modules\Product\Infrastructure\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class UpdateProductController extends Controller
{
    public function __construct(
        private readonly ProductUpdater $updater,
    ) {
    }

    #[OA\Put(
        path: '/api/v1/products/{id}',
        summary: 'Update an existing product',
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 16 v2'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Updated description'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1600.00),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 250.00),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 870.00),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Product updated successfully.'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Product or currency not found'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(int $id, UpdateProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new ApplicationUpdateRequest(
            id: $id,
            name: isset($validated['name']) ? (string) $validated['name'] : null,
            description: array_key_exists('description', $validated) ? ($validated['description'] !== null ? (string) $validated['description'] : null) : null,
            price: isset($validated['price']) ? (float) $validated['price'] : null,
            currencyId: isset($validated['currency_id']) ? (int) $validated['currency_id'] : null,
            taxCost: isset($validated['tax_cost']) ? (float) $validated['tax_cost'] : null,
            manufacturingCost: isset($validated['manufacturing_cost']) ? (float) $validated['manufacturing_cost'] : null
        );

        ($this->updater)($command);

        return response()->json([
            'message' => 'Product updated successfully.',
        ], 200);
    }
}
