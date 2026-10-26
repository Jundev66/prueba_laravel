<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Repositories;

use App\Modules\Product\Application\DTOs\AssignProductPriceDTO;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;

interface ProductPriceRepositoryInterface
{
    public function getPricesByProductId(int $productId): Collection;

    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice;

    public function create(AssignProductPriceDTO $dto): ProductPrice;
}
