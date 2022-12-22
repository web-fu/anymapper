<?php

declare(strict_types=1);

namespace WebFu\Mapper;

use WebFu\Proxy\Proxy;
use stdClass;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;

    /**
     * @param mixed[]|object $source
     */
    public function map(array|object $source): self
    {
        $this->sourceProxy = new Proxy($source);

        return $this;
    }

    public function into(array|object $destination): void
    {
        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();
    }

    public function as(string $name): array|object
    {
        $destination = match ($name) {
            'array' => [],
            'stdClass', 'object' => new stdClass(),
            default => new $name()
        };

        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();

        return $destination;
    }

    private function doMapping(): void
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();
        $destinationTracks = $this->destinationProxy->getAnalyzer()->getInputTrackList();

        $mappedTracks = array_intersect(array_keys($sourceTracks), array_keys($destinationTracks));

        foreach ($mappedTracks as $track) {
            $value = $this->sourceProxy->get($track);
            $this->destinationProxy->set($track, $value);
        }
    }
}
