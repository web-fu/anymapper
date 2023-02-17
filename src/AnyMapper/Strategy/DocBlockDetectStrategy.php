<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

class DocBlockDetectStrategy implements StrategyInterface
{
    public function cast(mixed $value, array $allowedTypes): mixed
    {
        if (!count($allowedTypes)) {
            // Dynamic Properties are allowed, no casting needed
            return $value;
        }

        $sourceType = get_type($value);

        if (in_array($sourceType, $allowedTypes)) {
            // Source type is already accepted by destination, no casting needed
            return $value;
        }

        foreach ($allowedTypes as $allowedType) {
            if (!$this->isCompatible($sourceType, $allowedType)) {
                continue;
            }

            if (
                $sourceType === 'string'
                && $allowedType === 'class-string'
                && !class_exists($value)
            ) {
                throw new MapperException('Class ' . $value . ' does not exists');
            }

            return $value;
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedTypes));
    }

    private function isCompatible(string $sourceType, string $destType): bool
    {
        return match ($sourceType) {
            'bool' => $destType === 'true' || $destType === 'false',
            'int' => $destType === 'positive-int' || $destType === 'negative-int',
            'string' => $destType === 'class-string' || $destType === 'non-empty-string' || $destType === 'non-falsy-string' || $destType === 'literal-string' || $destType === 'numeric-string',
            default => false,
        };
    }
}
