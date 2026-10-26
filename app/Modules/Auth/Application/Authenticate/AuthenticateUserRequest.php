<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Authenticate;

final readonly class AuthenticateUserRequest
{
    public function __construct(
        public string $email,
        public string $password,
        public string $clientIp,
    ) {
    }
}
