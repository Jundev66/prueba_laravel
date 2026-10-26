<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Domain;

use App\Modules\Product\Domain\Exceptions\InvalidProductAttributeException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Product\Mothers\ProductMother;

final class ProductTest extends TestCase
{
    public function test_it_changes_attributes_successfully(): void
    {
        $product = ProductMother::createDefault();

        $product->changeName('Updated Laptop');
        $product->changePrice(1400.00);
        $product->changeTaxCost(200.00);
        $product->changeManufacturingCost(700.00);

        $this->assertSame('Updated Laptop', $product->name());
        $this->assertSame(1400.00, $product->price());
        $this->assertSame(500.00, $product->grossMargin());
    }

    public function test_it_throws_when_name_is_empty(): void
    {
        $product = ProductMother::createDefault();

        $this->expectException(InvalidProductAttributeException::class);
        $product->changeName('   ');
    }

    public function test_it_throws_when_price_is_negative(): void
    {
        $product = ProductMother::createDefault();

        $this->expectException(InvalidProductAttributeException::class);
        $product->changePrice(-5.0);
    }
}
