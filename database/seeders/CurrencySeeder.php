<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'id' => 1,
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000,
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.9200,
            ],
            [
                'id' => 3,
                'name' => 'Bolivar',
                'symbol' => 'Bs.',
                'exchange_rate' => 36.5000,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['id' => $currency['id']],
                [
                    'name' => $currency['name'],
                    'symbol' => $currency['symbol'],
                    'exchange_rate' => $currency['exchange_rate'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
