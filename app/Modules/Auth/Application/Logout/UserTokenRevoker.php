<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Logout;

use App\Models\User;

final readonly class UserTokenRevoker
{
    public function __invoke(?User $user): void
    {
        $user?->currentAccessToken()?->delete();
    }
}
