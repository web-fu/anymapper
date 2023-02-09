<?php

declare(strict_types=1);

namespace WebFu\Internal;

use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionType;
use Exception;

/**
 * @internal
 *
 * @return string[]
 */
function reflection_type_names(ReflectionType|ReflectionNamedType|ReflectionUnionType|null $type): array
{
    if (null === $type) {
        return ['void'];
    }

    if ($type instanceof ReflectionNamedType) {
        return [$type->getName()];
    }

    if (!$type instanceof ReflectionUnionType) {
        throw new Exception('This exception cannot be thrown');
    }

    return array_map(fn (ReflectionNamedType $type): string => $type->getName(), $type->getTypes());
}
