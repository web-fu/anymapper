<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Proxy\Proxy;

class StrictStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $class = new class () {
            public int $value;
        };

        $sourceProxy = new Proxy([
            'value' => 1,
        ]);
        $destinationProxy = new Proxy(
            $class
        );
        $destinationTrack = new Track('value', TrackType::PROPERTY, ['int']);

        $strategy = new StrictStrategy();
        $strategy->init($sourceProxy, $destinationProxy);
        $actual = $strategy->cast(1, $destinationTrack);

        $this->assertSame(1, $actual);
    }
}
