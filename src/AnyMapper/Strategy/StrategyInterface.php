<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Reflection\ReflectionTypeExtended;

interface StrategyInterface
{
    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed;
}
