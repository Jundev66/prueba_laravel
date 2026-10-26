<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Authenticate;

final readonly class AuthenticateUserRequest
{
    public function __construct(
        private string $email,
        private string $password,
        private string $clientIp,
    ) {
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function clientIp(): string
    {
        return $this->clientIp;
    }
}
