<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

use WebFu\Reflection\ReflectionTypeExtended;

class StrictStrategy implements StrategyInterface
{
    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType   = get_type($value);

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        throw new MapperException('Cannot convert type '.$sourceType.' into any of the following types: '.implode(', ', $allowedTypes));
    }

    /**
     * @param string[] $allowedTypes
     */
    protected function noCastingNeeded(string $sourceType, array $allowedTypes): bool
    {
        if (in_array('mixed', $allowedTypes, true)) {
            // Mixed type allowed, no casting needed
            return true;
        }

        if (in_array($sourceType, $allowedTypes, true)) {
            // Source type is already accepted by destination, no casting needed
            return true;
        }

        if (
            in_array('object', $allowedTypes, true)
            && (class_exists($sourceType) || 'class@anonymous' === $sourceType)
        ) {
            // Source type is a class and object type is accepted, no casting needed
            return true;
        }

        return false;
    }
}
