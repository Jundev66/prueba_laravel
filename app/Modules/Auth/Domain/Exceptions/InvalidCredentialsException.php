<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

final class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The provided credentials do not match our records.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'INVALID_CREDENTIALS',
                'message' => $this->getMessage(),
            ],
        ], 401);
    }
}
