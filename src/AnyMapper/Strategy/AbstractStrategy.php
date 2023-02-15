<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Strategy;

use WebFu\Analyzer\Track;

abstract class AbstractStrategy
{
    abstract public function cast(mixed $value, Track|null $destinationTrack): mixed;
}
