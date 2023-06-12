<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use DateTime;

class SQLFetchStrategy extends AllowedCastingStrategy
{
    protected array $allowedDataCasting = [
        'string' => [
            'bool',
            'int',
            'float',
            DateTime::class,
        ],
    ];
}
