<?php
declare(strict_types=1);

namespace WebFu\Reflection;
use ReflectionProperty;
use ReflectionMethod;
use Reflector;
use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;

class Reflection
{
    public static function types(ReflectionProperty|ReflectionMethod|ReflectionParameter $reflector): array
    {
        $annotation = '';
        $type = null;

        if ($reflector instanceof ReflectionProperty) {
            $docBlock = self::sanitizeDocBlock($reflector);
            $annotation = preg_replace('/@var\s+/', '$1', $docBlock);
            $type = $reflector->getType();
        }

        if ($reflector instanceof ReflectionMethod) {
            $docBlock = self::sanitizeDocBlock($reflector);
            $annotation = preg_replace('/@return\s/', '$1', $docBlock);
            $type = $reflector->getReturnType();
        }

        if ($reflector instanceof ReflectionParameter) {
            $name = $reflector->getName();
            $docBlock = self::sanitizeDocBlock($reflector->getDeclaringFunction());
            $annotation = preg_replace('/@param\s+(\w+(\[\])*)\s+\$'.$name.'/', '$1', $docBlock);
            $type = $reflector->getType();
        }

        if ($annotation) {
            return explode('|', $annotation);
        }

        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }

        if ($type instanceof ReflectionUnionType) {
            return array_map(function (ReflectionNamedType $type): string {
                return $type->getName();
            }, $type->getTypes());
        }

        return ['void'];
    }

    public static function sanitizeDocBlock(Reflector $reflector): string
    {
        $docComment = preg_replace('#^\s*/\*\*([^/]+)\*/\s*$#', '$1', $reflector->getDocComment() ?: '');

        return trim(preg_replace('/^\s*\*\s*(\S*)/m', '$1', $docComment));
    }
}