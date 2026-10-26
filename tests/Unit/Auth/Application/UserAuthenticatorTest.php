<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Application;

use App\Models\User;
use App\Modules\Auth\Application\Authenticate\AuthenticateUserRequest;
use App\Modules\Auth\Application\Authenticate\UserAuthenticator;
use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Domain\Exceptions\TooManyLoginAttemptsException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

final class UserAuthenticatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('admin@example.com|127.0.0.1');
    }

    public function test_it_authenticates_user_successfully(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $authenticator = new UserAuthenticator();
        $result = $authenticator(new AuthenticateUserRequest(
            email: 'admin@example.com',
            password: 'password123',
            clientIp: '127.0.0.1'
        ));

        $this->assertSame('Bearer', $result['token_type']);
        $this->assertNotEmpty($result['access_token']);
        $this->assertSame('admin@example.com', $result['user']['email']);
    }

    public function test_it_throws_when_credentials_are_invalid(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->expectException(InvalidCredentialsException::class);

        $authenticator = new UserAuthenticator();
        $authenticator(new AuthenticateUserRequest(
            email: 'admin@example.com',
            password: 'wrong_password',
            clientIp: '127.0.0.1'
        ));
    }

    public function test_it_throws_when_too_many_attempts_occur(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $authenticator = new UserAuthenticator();

        for ($i = 0; $i < 5; $i++) {
            try {
                $authenticator(new AuthenticateUserRequest(
                    email: 'admin@example.com',
                    password: 'wrong_password',
                    clientIp: '127.0.0.1'
                ));
            } catch (InvalidCredentialsException) {
            }
        }

        $this->expectException(TooManyLoginAttemptsException::class);

        $authenticator(new AuthenticateUserRequest(
            email: 'admin@example.com',
            password: 'wrong_password',
            clientIp: '127.0.0.1'
        ));
    }
}
