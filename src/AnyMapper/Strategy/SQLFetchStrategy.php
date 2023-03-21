<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

class SQLFetchStrategy extends DataCastingStrategy
{
    protected array $allowedDataCasting = [
        'string' => ['int', 'float', \DateTime::class],
    ];
}
