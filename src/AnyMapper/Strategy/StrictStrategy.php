<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\Track;
use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

class StrictStrategy implements StrategyInterface
{
    public function cast(mixed $value, Track|null $destinationTrack): mixed
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

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedDestinationDataTypes));
    }
}
