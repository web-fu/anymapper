<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use WebFu\Analyzer\Track;
use WebFu\Proxy\Proxy;
use stdClass;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;
    /** @var array<string[]> */
    private array $allowedDataCasting = [];
    private bool $allowedDynamicProperties = false;

    /**
     * @param mixed[]|object $source
     */
    public function map(array|object $source): self
    {
        $this->sourceProxy = new Proxy($source);

        return $this;
    }

    /**
     * @param mixed[]|object $destination
     */
    public function into(array|object $destination): void
    {
        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();
    }

    /**
     * @param class-string $className
     */
    public function as(string $className): object
    {
        $destination = new $className();

        if ($className === stdClass::class) {
            return (object) $this->serialize();
        }

        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();

        return $destination;
    }

    /**
     * @return mixed[]
     */
    public function serialize(): array
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();

        $output = [];
        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $value = $this->sourceProxy->get((string) $trackName);
            if (is_array($value) || is_object($value)) {
                $value = (new self())->map($value)->serialize();
            }
            $output[$trackName] = $value;
        }

        return $output;
    }

    public function allowDataCasting(string $from, string $to): self
    {
        $this->allowedDataCasting[$from][] = $to;

        return $this;
    }

    public function allowDynamicProperties(bool $allow = true): self
    {
        $this->allowedDynamicProperties = $allow;

        return $this;
    }

    private function doMapping(): void
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();

        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $destinationTrack = $this->destinationProxy->getAnalyzer()->getInputTrack($trackName);

            if (! $destinationTrack && ! $this->allowedDynamicProperties) {
                continue;
            }

            $sourceValue = $this->sourceProxy->get($trackName);

            $destinationValue = $this->assign($sourceValue, $destinationTrack);

            $this->destinationProxy->set($trackName, $destinationValue);
        }
    }

    private function assign(mixed $value, Track|null $destinationTrack): mixed
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
