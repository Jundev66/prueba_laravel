<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Shared\Infrastructure\Providers\RepositoryServiceProvider;

return [
    AppServiceProvider::class,
    RepositoryServiceProvider::class,
];
