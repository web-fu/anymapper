<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

interface StrategyInterface
{
    /**
     * @param DataType::*[] $allowedTypes
     */
    public function cast(mixed $value, array $allowedTypes): mixed;
}