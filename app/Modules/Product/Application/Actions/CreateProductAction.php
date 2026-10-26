<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Actions;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;

final readonly class CreateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function execute(CreateProductDTO $dto): Product
    {
        if (!$this->currencyRepository->exists($dto->currencyId)) {
            throw new CurrencyNotFoundException($dto->currencyId);
        }

        return $this->productRepository->create($dto);
    }
}
