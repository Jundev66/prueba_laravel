<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\Product;
use App\Modules\Product\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'currency_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'currency_id');
    }
}
