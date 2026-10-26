<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_CREDENTIALS');
    }

    public function test_login_triggers_rate_limiting_after_excessive_failures(): void
    {
        User::factory()->create([
            'email' => 'victim@example.com',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'victim@example.com',
                'password' => 'wrong_password',
            ]);
        }

        $blockedResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'victim@example.com',
            'password' => 'wrong_password',
        ]);

        $blockedResponse->assertStatus(429)
            ->assertJsonPath('error.code', 'TOO_MANY_LOGIN_ATTEMPTS');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Token revoked successfully.');
    }
}
