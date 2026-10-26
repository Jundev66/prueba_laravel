<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'id' => 1,
                'name' => 'Laptop Pro 16',
                'description' => 'High performance workstation laptop with 32GB RAM and 1TB SSD',
                'price' => 1500.0000,
                'currency_id' => 1,
                'tax_cost' => 240.0000,
                'manufacturing_cost' => 850.0000,
                'prices' => [
                    ['currency_id' => 2, 'price' => 1380.0000],
                    ['currency_id' => 3, 'price' => 54750.0000],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Mechanical Keyboard',
                'description' => 'Wireless mechanical keyboard with tactile switches and RGB lighting',
                'price' => 120.0000,
                'currency_id' => 1,
                'tax_cost' => 19.2000,
                'manufacturing_cost' => 45.0000,
                'prices' => [
                    ['currency_id' => 2, 'price' => 110.4000],
                    ['currency_id' => 3, 'price' => 4380.0000],
                ],
            ],
        ];

        foreach ($products as $item) {
            DB::table('products')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'currency_id' => $item['currency_id'],
                    'tax_cost' => $item['tax_cost'],
                    'manufacturing_cost' => $item['manufacturing_cost'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            foreach ($item['prices'] as $priceItem) {
                DB::table('product_prices')->updateOrInsert(
                    [
                        'product_id' => $item['id'],
                        'currency_id' => $priceItem['currency_id'],
                    ],
                    [
                        'price' => $priceItem['price'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
