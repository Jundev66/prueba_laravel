<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Actions;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;

final readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function execute(int $id, UpdateProductDTO $dto): Product
    {
        $product = $this->productRepository->findById($id);

        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        if ($dto->currencyId !== null && !$this->currencyRepository->exists($dto->currencyId)) {
            throw new CurrencyNotFoundException($dto->currencyId);
        }

        return $this->productRepository->update($product, $dto);
    }
}
