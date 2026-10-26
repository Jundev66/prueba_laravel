<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->decimal('price', 12, 4);
                $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
                $table->decimal('tax_cost', 12, 4);
                $table->decimal('manufacturing_cost', 12, 4);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::dropIfExists('products');
        }
    }
};
