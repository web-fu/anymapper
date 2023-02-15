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

    abstract public function cast(mixed $value, Track|null $destinationTrack): mixed;
}
