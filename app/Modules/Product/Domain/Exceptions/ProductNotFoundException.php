<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;

final class ProductNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Product with ID <%d> was not found.', $id));
    }
}
