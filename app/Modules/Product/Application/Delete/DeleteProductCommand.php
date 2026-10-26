<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Delete;

final readonly class DeleteProductCommand
{
    public function __construct(
        private int $id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
