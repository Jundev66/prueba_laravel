<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use DomainException;
use Illuminate\Http\JsonResponse;

final class TooManyLoginAttemptsException extends DomainException
{
    public function __construct(int $seconds)
    {
        parent::__construct(sprintf('Too many login attempts. Please try again in %d seconds.', $seconds));
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'TOO_MANY_LOGIN_ATTEMPTS',
                'message' => $this->getMessage(),
            ],
        ], 429);
    }
}
