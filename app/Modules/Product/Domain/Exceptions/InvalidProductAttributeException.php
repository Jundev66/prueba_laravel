<?php

declare(strict_types=1);

namespace App\Modules\Product\Domain\Exceptions;

use DomainException;

final class InvalidProductAttributeException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
