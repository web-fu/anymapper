<?php

declare(strict_types=1);

/** @return string[] */
function reflection_type_names(\ReflectionNamedType|\ReflectionUnionType|null $type): array
{
    if (null === $type) {
        return ['void'];
    }

    if ($type instanceof \ReflectionNamedType) {
        return [$type->getName()];
    }

    return array_map(function (\ReflectionNamedType $type): string {
        return $type->getName();
    }, $type->getTypes());
}