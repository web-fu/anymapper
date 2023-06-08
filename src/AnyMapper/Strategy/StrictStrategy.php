<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\MapperException;

use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\get_type;

class StrictStrategy implements StrategyInterface
{
    /**
     * @param string[] $allowedTypes
     */
    protected function noCastingNeeded(string $sourceType, array $allowedTypes): bool
    {
        if (in_array('mixed', $allowedTypes)) {
            // Mixed type allowed, no casting needed
            return true;
        }

        if (in_array($sourceType, $allowedTypes)) {
            // Source type is already accepted by destination, no casting needed
            return true;
        }

        if (
            in_array('object', $allowedTypes)
            && (class_exists($sourceType) || $sourceType === 'class@anonymous')
        ) {
            // Source type is a class and object type is accepted, no casting needed
            return true;
        }

        return false;
    }

    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType = get_type($value);

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(', ', $allowedTypes));
    }
}
