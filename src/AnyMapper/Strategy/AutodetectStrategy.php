<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\AnyMapper\MapperException;
use function WebFu\Internal\get_type;
use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;

class AutodetectStrategy extends AbstractStrategy
{
    protected function cast(mixed $value, Track|null $destinationTrack): mixed
    {
        $allowedDestinationDataTypes = $destinationTrack?->getDataTypes();

        if (is_null($allowedDestinationDataTypes)) {
            // Dynamic Properties are allowed, no casting needed
            return $value;
        }

        $sourceType = get_type($value);

        if (in_array($sourceType, $allowedDestinationDataTypes)) {
            // Source type is already accepted by destination, no casting needed
            return $value;
        }

        // Autodetect can be used only on classes
        $classDestinationTypes = array_filter($allowedDestinationDataTypes,
            fn (string $type) => class_exists($type)
        );

        foreach ($classDestinationTypes as $class) {
            $analyzer = new ClassAnalyzer($class);

            $constructorParameters = $analyzer->getConstructor()?->getParameters() ?? [];
            // Autodetect can be used only if constructor can accept parameters
            if (!count($constructorParameters)) {
                continue;
            }

            $constructorParametersSkippable = array_filter($constructorParameters,
                fn (ReflectionParameter $parameter): bool => $parameter->isDefaultValueAvailable() || $parameter->isOptional()
            );

            // Autodetect can be used only on unary constructors
            if (count($constructorParameters) - count($constructorParametersSkippable) > 1) {
                continue;
            }

            $allowedTypes = $this->getParameterType($constructorParameters[0]);
            foreach ($allowedTypes as $allowedType) {
                if ($sourceType !== $allowedType) {
                    continue;
                }
                return new $class($value);
            }
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedDestinationDataTypes));
    }

    /** @return string[] */
    private function getParameterType(ReflectionParameter $parameter): array
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }

        if ($type instanceof ReflectionUnionType) {
            return array_map(
                fn (ReflectionNamedType $type): string => $type->getName(), $type->getTypes()
            );
        }
        return [];
    }
}