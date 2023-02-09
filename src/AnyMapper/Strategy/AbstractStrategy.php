<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\Track;
use WebFu\Proxy\Proxy;

abstract class AbstractStrategy
{
    protected Proxy $sourceProxy;
    protected Proxy $destinationProxy;

    public function init(Proxy $sourceProxy, Proxy $destinationProxy): self
    {
        $this->sourceProxy = $sourceProxy;
        $this->destinationProxy = $destinationProxy;

        return $this;
    }

    public function run(): void {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();

        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $destinationTrack = $this->destinationProxy->getAnalyzer()->getInputTrack($trackName);

            if (! $destinationTrack) {
                continue;
            }

            $sourceValue = $this->sourceProxy->get($trackName);

            $destinationValue = $this->cast($sourceValue, $destinationTrack);

            $this->destinationProxy->set($trackName, $destinationValue);
        }
    }

    abstract protected function cast(mixed $value, Track|null $destinationTrack): mixed;
}