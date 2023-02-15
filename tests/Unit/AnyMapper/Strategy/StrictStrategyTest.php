<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\AnyMapper\Strategy\StrictStrategy;

class StrictStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $destinationTrack = new Track('value', TrackType::PROPERTY, ['int']);

        $strategy = new StrictStrategy();
        $actual = $strategy->cast(1, $destinationTrack);

        $this->assertSame(1, $actual);
    }
}
