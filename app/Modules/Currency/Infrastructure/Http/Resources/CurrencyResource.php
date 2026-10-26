<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Http\Resources;

use App\Modules\Currency\Domain\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Currency $resource
 */
final class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Currency) {
            return [
                'id' => $this->resource->id(),
                'name' => $this->resource->name(),
                'symbol' => $this->resource->symbol(),
                'exchange_rate' => $this->resource->exchangeRate(),
            ];
        }

        return [
            'id' => $this->resource['id'] ?? $this->id,
            'name' => $this->resource['name'] ?? $this->name,
            'symbol' => $this->resource['symbol'] ?? $this->symbol,
            'exchange_rate' => (float) ($this->resource['exchange_rate'] ?? $this->exchange_rate),
        ];
    }
}
