<?php

declare(strict_types=1);

namespace WebFu\Reflection;

use ReflectionUnionType;
use ReflectionNamedType;
use function WebFu\Internal\reflection_type_names;

class ExtendedReflectionProperty extends \ReflectionProperty
{
    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        $type = $this->getType();

        return reflection_type_names($type);
    }

    /**
     * @return string[]
     */
    public function getDocTypes(): array
    {
        $docBlock = Reflection::sanitizeDocBlock($this);
        $annotation = preg_replace('/@var\s/', '$1', $docBlock) ?: '';

        return explode('|', $annotation);
    }

    public function isReadOnly(): bool
    {
        return PHP_VERSION_ID >= 80100 && parent::isReadOnly();
    }
}
