<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use DomainException;

final class TooManyLoginAttemptsException extends DomainException
{
    public function __construct(int $seconds)
    {
        parent::__construct(sprintf('Too many login attempts. Please try again in %d seconds.', $seconds));
    }
}
