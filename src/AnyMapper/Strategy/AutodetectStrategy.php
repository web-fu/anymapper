<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\ClassAnalyzer;
use WebFu\AnyMapper\MapperException;
use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\get_type;

class AutodetectStrategy extends StrictStrategy
{
    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType = get_type($value);

        if ($this->isCastable($sourceType, $allowedTypes)) {
            return $value;
        }

        // Autodetect can be used only on classes
        $classDestinationTypes = array_filter(
            $allowedTypes,
            fn (string $type) => class_exists($type)
        );

        foreach ($classDestinationTypes as $class) {
            $analyzer = new ClassAnalyzer($class);

            /* Constructor does not accept parameters */
            if (!$analyzer->getConstructor()?->getNumberOfParameters()) {
                continue;
            }

            /* Constructor require more than one parameter */
            if ($analyzer->getConstructor()->getNumberOfRequiredParameters() > 1) {
                continue;
            }

            $constructorParameters = $analyzer->getConstructor()->getParameters();

            $allowedTypes = $constructorParameters[0]->getType()->getTypeNames();
            foreach ($allowedTypes as $allowedType) {
                if ($sourceType !== $allowedType) {
                    continue;
                }
                return new $class($value);
            }

            $allowedTypes = $constructorParameters[0]->getType()->getTypeNames();
            foreach ($allowedTypes as $allowedType) {
                if ($sourceType !== $allowedType) {
                    continue;
                }
                return new $class($value);
            }
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedTypes));
    }
}
