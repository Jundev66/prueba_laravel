<?php

declare(strict_types=1);

namespace App\Modules\Product\Application\Find;

final readonly class FindProductQuery
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
