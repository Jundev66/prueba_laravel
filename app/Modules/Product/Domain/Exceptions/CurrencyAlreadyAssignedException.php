<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;

final class CurrencyAlreadyAssignedException extends DomainException
{
    public function __construct(int $currencyId)
    {
        parent::__construct(sprintf('A price for currency ID <%d> is already assigned to this product.', $currencyId));
    }
}
