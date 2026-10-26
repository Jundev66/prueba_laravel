<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100);
                $table->string('symbol', 10);
                $table->decimal('exchange_rate', 12, 4);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('currencies')) {
            Schema::dropIfExists('currencies');
        }
    }
};
