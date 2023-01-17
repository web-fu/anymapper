<?php

declare(strict_types=1);

namespace WebFu\Reflection;

use ReflectionUnionType;
use ReflectionNamedType;

class ExtendedReflectionProperty
{
    private \ReflectionProperty $reflectionProperty;

    /**
     * @param object|class-string $objectOrClass
     */
    public function __construct(object|string $objectOrClass, string $name)
    {
        $this->reflectionProperty = new \ReflectionProperty($objectOrClass, $name);
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        $type = $this->reflectionProperty->getType();

        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }

        if ($type instanceof ReflectionUnionType) {
            return array_map(function (ReflectionNamedType $type): string {
                return $type->getName();
            }, $type->getTypes());
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function getDocTypes(): array
    {
        $docBlock = Reflection::sanitizeDocBlock($this->reflectionProperty);
        $annotation = preg_replace('/@var\s/', '$1', $docBlock) ?: '';

        return explode('|', $annotation);
    }
}
