<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Exceptions;

use DomainException;

final class CurrencyNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Currency with ID <%d> was not found.', $id));
    }
}
