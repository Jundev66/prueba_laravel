<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Resources;

use App\Modules\Currency\Infrastructure\Http\Resources\CurrencyResource;
use App\Modules\Product\Domain\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Product $resource
 */
final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Product) {
            return [
                'id' => $this->resource->id(),
                'name' => $this->resource->name(),
                'description' => $this->resource->description(),
                'price' => $this->resource->price(),
                'currency' => $this->resource->currency() !== null ? new CurrencyResource($this->resource->currency()) : null,
                'tax_cost' => $this->resource->taxCost(),
                'manufacturing_cost' => $this->resource->manufacturingCost(),
                'gross_margin' => $this->resource->grossMargin(),
                'prices' => ProductPriceResource::collection($this->resource->prices()),
            ];
        }

        return [
            'id' => $this->resource['id'] ?? $this->id,
            'name' => $this->resource['name'] ?? $this->name,
            'description' => $this->resource['description'] ?? $this->description,
            'price' => (float) ($this->resource['price'] ?? $this->price),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'tax_cost' => (float) ($this->resource['tax_cost'] ?? $this->tax_cost),
            'manufacturing_cost' => (float) ($this->resource['manufacturing_cost'] ?? $this->manufacturing_cost),
            'gross_margin' => method_exists($this->resource, 'getGrossMargin') ? $this->resource->getGrossMargin() : null,
            'prices' => ProductPriceResource::collection($this->whenLoaded('prices')),
        ];
    }
}
