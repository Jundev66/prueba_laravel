<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;

final class BaseCurrencyCannotBeSecondaryException extends DomainException
{
    public function __construct(int $currencyId)
    {
        parent::__construct(sprintf('Currency ID <%d> is the base currency of this product and cannot be registered as a secondary price.', $currencyId));
    }
}
