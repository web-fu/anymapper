<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\Track;
use WebFu\AnyMapper\Caster;
use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

class DataCastingStrategy extends AbstractStrategy
{
    /** @var array<string[]> */
    private array $allowedDataCasting = [];

    public function allow(string $from, string $to): self
    {
        $this->allowedDataCasting[$from][] = $to;

        return $this;
    }

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

        $allowedDataCasting = $this->allowedDataCasting[$sourceType] ?? [];

        foreach ($allowedDataCasting as $to) {
            if (! in_array($to, $allowedDestinationDataTypes)) {
                continue;
            }
            return (new Caster($value))->as($to);
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedDestinationDataTypes));
    }
}
