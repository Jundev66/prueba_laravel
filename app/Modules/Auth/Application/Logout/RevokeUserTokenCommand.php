<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Logout;

use App\Models\User;

final readonly class RevokeUserTokenCommand
{
    public function __construct(
        private ?User $user,
    ) {
    }

    public function user(): ?User
    {
        return $this->user;
    }
}
