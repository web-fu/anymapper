<?php

namespace WebFu\AnyMapper;

use WebFu\Analyzer\Track;
use WebFu\Proxy\Proxy;

class StrictStrategy implements StrategyInterface
{
    private bool $allowedDynamicProperties = false;

    public function __construct(
        private Proxy $sourceProxy,
        private Proxy $destinationProxy,
        /** @var array<string[]> */
        private array $allowedDataCasting,
    )
    {
    }

    public function run(): void {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();

        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $destinationTrack = $this->destinationProxy->getAnalyzer()->getInputTrack($trackName);

            if (! $destinationTrack && ! $this->allowedDynamicProperties) {
                continue;
            }

            $sourceValue = $this->sourceProxy->get($trackName);

            $destinationValue = $this->castOrFail($sourceValue, $destinationTrack);

            $this->destinationProxy->set($trackName, $destinationValue);
        }
    }

    private function castOrFail(mixed $value, Track|null $destinationTrack): mixed
    {
        $allowedDestinationDataTypes = $destinationTrack?->getDataTypes();

        if (is_null($allowedDestinationDataTypes)) {
            // Dynamic Properties are allowed, no casting needed
            assert($this->allowedDynamicProperties);
            return $value;
        }

        $sourceType = gettype($value);

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