<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Application\Actions\CreateProductAction;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Infrastructure\Http\Requests\StoreProductRequest;
use App\Modules\Product\Infrastructure\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class CreateProductController extends Controller
{
    public function __construct(
        private readonly CreateProductAction $action,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/products',
        summary: 'Create a new product',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 16'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Workstation laptop with 32GB RAM'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1500.00, description: 'Base price of the product in the base currency'),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1, description: 'ID of the base currency (1 = USD, 2 = EUR, 3 = VES). Consult GET /api/v1/currencies'),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 240.00, description: 'Tax cost of the product'),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 850.00, description: 'Manufacturing cost of the product'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Currency not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(StoreProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromArray($request->validated());
        $product = $this->action->execute($dto);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }
}
