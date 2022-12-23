<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

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
