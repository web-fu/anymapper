<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\AnyMapper\Caster\DefaultCaster;
use WebFu\AnyMapper\Caster\CasterInterface;
use WebFu\AnyMapper\MapperException;
use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\get_type;

class AllowedCastingStrategy extends StrictStrategy
{
    protected CasterInterface $caster;

    public function __construct(CasterInterface|null $caster = null)
    {
        $this->caster = $caster ?: new DefaultCaster();
    }

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

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        $allowedDataCasting = $this->allowedDataCasting[$sourceType] ?? [];

        foreach ($allowedDataCasting as $to) {
            if (! in_array($to, $allowedTypes)) {
                continue;
            }
            return $this->caster->cast($value, $to);
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedTypes));
    }
}
