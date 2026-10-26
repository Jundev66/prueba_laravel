<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Repositories;

use App\Modules\Product\Domain\ProductPrice;

interface ProductPriceRepositoryInterface
{
    public function save(ProductPrice $price): ProductPrice;

    /**
     * @return ProductPrice[]
     */
    public function searchByProduct(int $productId): array;

    public function searchByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice;
}
