<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\Caster;
use WebFu\AnyMapper\MapperException;

use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\get_type;

class DataCastingStrategy extends StrictStrategy
{
    /** @var array<string[]> */
    protected array $allowedDataCasting = [];

    public function allow(string $from, string $to): self
    {
        $this->allowedDataCasting[$from][] = $to;

        return $this;
    }

    public function cast(mixed $value, ReflectionTypeExtended $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType = get_type($value);

        if ($this->isAllowed($sourceType, $allowedTypes)) {
            return $value;
        }

        $allowedDataCasting = $this->allowedDataCasting[$sourceType] ?? [];

        foreach ($allowedDataCasting as $to) {
            if (! in_array($to, $allowedTypes)) {
                continue;
            }
            return (new Caster($value))->as($to);
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedTypes));
    }
}
