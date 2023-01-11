<?php
declare(strict_types=1);

namespace WebFu\Reflection;

use ReflectionProperty;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionClass;
use ReflectionFunctionAbstract;

class Reflection
{
    /**
     * @return string[]
     */
    public static function types(ReflectionProperty|ReflectionMethod|ReflectionParameter $reflector): array
    {
        $annotation = '';
        $type = null;

        if ($reflector instanceof ReflectionProperty) {
            $docBlock = self::sanitizeDocBlock($reflector);
            $annotation = preg_replace('/@var\s/', '$1', $docBlock);
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
            $annotation = preg_replace('/@param\s(\w+(\[\])*)\s\$'.$name.'/', '$1', $docBlock);
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

    public static function namespace(ReflectionClass|ReflectionProperty|ReflectionMethod $reflector): string
    {
        if ($reflector instanceof ReflectionClass) {
            return $reflector->getNamespaceName();
        }

        return $reflector->getDeclaringClass()->getNamespaceName();
    }

    /**
     * @return array<string, string>|null
     */
    public static function templates(ReflectionClass|ReflectionProperty|ReflectionMethod $reflector): array|null
    {
        $docBlock = self::sanitizeDocBlock($reflector);
        preg_match('/@template\s(?<template>\w+)\sof\s(?<type>\w+)/', $docBlock, $matches);

        if (isset($matches['template']) and isset($matches['type'])) {
            return [$matches['template'] => self::namespace($reflector).'\\'.$matches['type']];
        }

        return null;
    }

    public static function sanitizeDocBlock(ReflectionClass|ReflectionProperty|ReflectionMethod|ReflectionFunctionAbstract $reflector): string
    {
        /** @var string $docComment */
        $docComment = preg_replace('#^\s*/\*\*([^/]+)\*/\s*$#', '$1', $reflector->getDocComment() ?: '');

        /** @phpstan-ignore-next-line */
        return trim(preg_replace('/^\s*\*\s*(.+)/m', '$1', $docComment));
    }
}
