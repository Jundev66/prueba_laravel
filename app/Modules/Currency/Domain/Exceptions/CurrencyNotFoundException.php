<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

final class CurrencyNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Currency with ID %d was not found.', $id));
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'CURRENCY_NOT_FOUND',
                'message' => $this->getMessage(),
            ],
        ], 404);
    }
}
