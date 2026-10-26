<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

final class InvalidProductAttributeException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'INVALID_PRODUCT_ATTRIBUTE',
                'message' => $this->getMessage(),
            ],
        ], 422);
    }
}
