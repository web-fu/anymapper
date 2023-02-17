<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\ClassAnalyzer;
use WebFu\AnyMapper\MapperException;
use ReflectionParameter;

use function WebFu\Internal\get_type;
use function WebFu\Internal\reflection_type_names;

class AutodetectStrategy implements StrategyInterface
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

        // Autodetect can be used only on classes
        $classDestinationTypes = array_filter(
            $allowedTypes,
            fn (string $type) => class_exists($type)
        );

        foreach ($classDestinationTypes as $class) {
            $analyzer = new ClassAnalyzer($class);

            $constructorParameters = $analyzer->getConstructor()?->getParameters() ?? [];
            // Autodetect can be used only if constructor can accept parameters
            if (!count($constructorParameters)) {
                continue;
            }

            $constructorParametersSkippable = array_filter(
                $constructorParameters,
                fn (ReflectionParameter $parameter): bool => $parameter->isDefaultValueAvailable() or $parameter->isOptional()
            );

            // Autodetect can be used only on unary constructors
            if (count($constructorParameters) - count($constructorParametersSkippable) > 1) {
                continue;
            }

            $allowedTypes = reflection_type_names($constructorParameters[0]->getType());
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
