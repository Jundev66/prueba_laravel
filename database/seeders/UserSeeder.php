<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Lead Architect',
                'password' => Hash::make('password123'),
            ]
        );

        $rawToken = 'catalog_access_token';
        $hashedToken = hash('sha256', $rawToken);

        DB::table('personal_access_tokens')->updateOrInsert(
            [
                'id' => 1,
            ],
            [
                'tokenable_type' => User::class,
                'tokenable_id' => $user->id,
                'name' => 'default_admin_token',
                'token' => $hashedToken,
                'abilities' => json_encode(['*']),
                'last_used_at' => null,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
