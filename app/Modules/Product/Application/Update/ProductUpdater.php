<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Update;

use App\Modules\Currency\Domain\Exceptions\CurrencyNotFoundException;
use App\Modules\Currency\Domain\Repositories\CurrencyRepositoryInterface;
use App\Modules\Product\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Product\Domain\Product;
use App\Modules\Product\Domain\Repositories\ProductRepositoryInterface;

final readonly class ProductUpdater
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function __invoke(UpdateProductRequest $request): Product
    {
        $product = $this->productRepository->search($request->id());

        if ($product === null) {
            throw new ProductNotFoundException($request->id());
        }

        if ($request->name() !== null) {
            $product->changeName($request->name());
        }

        if ($request->description() !== null) {
            $product->changeDescription($request->description());
        }

        if ($request->price() !== null) {
            $product->changePrice($request->price());
        }

        if ($request->currencyId() !== null) {
            if (!$this->currencyRepository->exists($request->currencyId())) {
                throw new CurrencyNotFoundException($request->currencyId());
            }
            $product->changeCurrencyId($request->currencyId());
        }

        if ($request->taxCost() !== null) {
            $product->changeTaxCost($request->taxCost());
        }

        if ($request->manufacturingCost() !== null) {
            $product->changeManufacturingCost($request->manufacturingCost());
        }

        return $this->productRepository->save($product);
    }
}
