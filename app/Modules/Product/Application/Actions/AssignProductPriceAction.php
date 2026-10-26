<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Actions;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Application\DTOs\AssignProductPriceDTO;
use App\Modules\Product\Domain\Exceptions\BaseCurrencyCannotBeSecondaryException;
use App\Modules\Product\Domain\Exceptions\CurrencyAlreadyAssignedException;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Repositories\ProductPriceRepositoryInterface;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;

final readonly class AssignProductPriceAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductPriceRepositoryInterface $productPriceRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function execute(AssignProductPriceDTO $dto): ProductPrice
    {
        $product = $this->productRepository->findById($dto->productId);

        if ($product === null) {
            throw new ProductNotFoundException($dto->productId);
        }

        if (!$this->currencyRepository->exists($dto->currencyId)) {
            throw new CurrencyNotFoundException($dto->currencyId);
        }

        if ($product->currency_id === $dto->currencyId) {
            throw new BaseCurrencyCannotBeSecondaryException($dto->currencyId);
        }

        $existing = $this->productPriceRepository->findByProductAndCurrency($dto->productId, $dto->currencyId);

        if ($existing !== null) {
            throw new CurrencyAlreadyAssignedException($dto->currencyId);
        }

        return $this->productPriceRepository->create($dto);
    }
}
