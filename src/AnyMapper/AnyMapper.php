<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use WebFu\Proxy\Proxy;
use stdClass;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;
    /** @var string[] */
    private array $allowedDataCasting = [];

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
     * @param class-string|string $className
     * @return mixed[]|object
     */
    public function as(string $className): array|object
    {
        $destination = match ($className) {
            'array' => [],
            'stdClass', 'object' => new stdClass(),
            default => new $className()
        };

        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();

        return $destination;
    }

    /**
     * @param string[] $allowedDataCasting
     */
    public function allowDataCasting(array $allowedDataCasting): self
    {
        $this->allowedDataCasting = $allowedDataCasting;

        return $this;
    }

    private function doMapping(): void
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();
        $destinationTracks = $this->destinationProxy->getAnalyzer()->getInputTrackList();

        foreach ($destinationTracks as $track => $destinationTrack) {
            if (! array_key_exists($track, $sourceTracks)) {
                continue;
            }
            $value = $this->sourceProxy->get($track);
            $sourceType = gettype($value);
            $allowedDestinationDataTypes = $destinationTrack->getDataTypes();

            if (!in_array($sourceType, $allowedDestinationDataTypes)) {
                $value = $this->castOrFail($sourceType, $allowedDestinationDataTypes, $value);
            }

            $this->destinationProxy->set($track, $value);
        }
    }

    /**
     * @param string[] $allowedDestinationDataTypes
     * @param int|float|bool|string|object|mixed[]|null $value
     */
    private function castOrFail(string $sourceType, array $allowedDestinationDataTypes, int|float|bool|string|object|array|null $value): mixed
    {
        foreach ($this->allowedDataCasting as $source => $destination) {
            if ($sourceType !== $source) {
                continue;
            }
            if (! in_array($destination, $allowedDestinationDataTypes)) {
                continue;
            }
            return (new Caster($value))->as($destination);
        }

        throw new MapperException('Cannot convert type ' . $sourceType . ' into any of the following types: '. implode(',', $allowedDestinationDataTypes));
    }
}
