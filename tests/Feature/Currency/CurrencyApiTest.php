<?php

declare(strict_types=1);

namespace Tests\Feature\Currency;

use App\Models\User;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CurrencySeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_requests_to_currencies_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(401);
    }

    public function test_it_can_list_all_supported_currencies_with_correct_structure(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/currencies');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'symbol',
                        'exchange_rate',
                    ],
                ],
            ]);
    }

    public function test_it_returns_expected_catalogue_currencies_data(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/currencies');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'US Dollar')
            ->assertJsonPath('data.0.symbol', '$')
            ->assertJsonPath('data.0.exchange_rate', 1)
            ->assertJsonPath('data.1.name', 'Euro')
            ->assertJsonPath('data.1.symbol', '€')
            ->assertJsonPath('data.2.name', 'Bolivar')
            ->assertJsonPath('data.2.symbol', 'Bs.');
    }
}
