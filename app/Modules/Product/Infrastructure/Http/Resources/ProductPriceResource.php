<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Resources;

use App\Modules\Currency\Infrastructure\Http\Resources\CurrencyResource;
use App\Modules\Product\Domain\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ProductPrice $resource
 */
final class ProductPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof ProductPrice) {
            return [
                'id' => $this->resource->id(),
                'product_id' => $this->resource->productId(),
                'currency_id' => $this->resource->currencyId(),
                'currency' => $this->resource->currency() !== null ? new CurrencyResource($this->resource->currency()) : null,
                'price' => $this->resource->price(),
            ];
        }

        return [
            'id' => $this->resource['id'] ?? $this->id,
            'product_id' => $this->resource['product_id'] ?? $this->product_id,
            'currency_id' => $this->resource['currency_id'] ?? $this->currency_id,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'price' => (float) ($this->resource['price'] ?? $this->price),
        ];
    }
}
