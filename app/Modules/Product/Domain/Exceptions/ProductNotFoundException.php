<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

final class ProductNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Product with ID %d was not found.', $id));
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'PRODUCT_NOT_FOUND',
                'message' => $this->getMessage(),
            ],
        ], 404);
    }
}
