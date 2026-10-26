<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListCurrenciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
        ];
    }
}
