<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Application;

use App\Models\User;
use App\Modules\Auth\Application\Logout\RevokeUserTokenCommand;
use App\Modules\Auth\Application\Logout\UserTokenRevoker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserTokenRevokerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_revokes_current_user_token_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $user->withAccessToken($token->accessToken);

        $revoker = new UserTokenRevoker();
        $revoker(new RevokeUserTokenCommand($user));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_it_handles_null_user_gracefully(): void
    {
        $revoker = new UserTokenRevoker();
        $revoker(new RevokeUserTokenCommand(null));

        $this->assertTrue(true);
    }
}
