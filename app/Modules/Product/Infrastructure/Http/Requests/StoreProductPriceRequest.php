<?php

declare(strict_types=1);

namespace App\Modules\Product\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
