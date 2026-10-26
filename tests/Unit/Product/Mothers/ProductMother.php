<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Mothers;

use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;

final class ProductMother
{
    public static function createDefault(): Product
    {
        $product = new Product();
        $product->id = 1;
        $product->name = 'Test Laptop';
        $product->description = 'A powerful laptop for development';
        $product->price = 1200.0000;
        $product->currency_id = 1;
        $product->tax_cost = 192.0000;
        $product->manufacturing_cost = 600.0000;

        return $product;
    }

    public static function createDto(array $overrides = []): CreateProductDTO
    {
        return new CreateProductDTO(
            name: $overrides['name'] ?? 'Test Laptop',
            description: $overrides['description'] ?? 'A powerful laptop for development',
            price: $overrides['price'] ?? 1200.00,
            currencyId: $overrides['currency_id'] ?? 1,
            taxCost: $overrides['tax_cost'] ?? 192.00,
            manufacturingCost: $overrides['manufacturing_cost'] ?? 600.00,
        );
    }
}
