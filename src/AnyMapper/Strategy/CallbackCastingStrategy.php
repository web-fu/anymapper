<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\MapperException;
use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\get_type;

class CallbackCastingStrategy extends StrictStrategy
{
    /** @var array<string, array<string, callable>> */
    protected array $methods = [];

    public function addMethod(string $from, string $to, callable $callback): self
    {
        $this->methods[$from][$to] = $callback;

        return $this;
    }

    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType = get_type($value);

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        foreach ($this->methods[$sourceType] ?? [] as $to => $callback) {
            if (! in_array($to, $allowedTypes)) {
                continue;
            }
            return $callback($value);
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedTypes));
    }
}
