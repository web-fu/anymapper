<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;

use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use WebFu\Proxy\Proxy;
use DateTime;

class AutodetectStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $class = new class () {
            public DateTime $value;
        };

        $sourceProxy = new Proxy([
            'value' => '2022-12-01',
        ]);
        $destinationProxy = new Proxy(
            $class
        );
        $destinationTrack = new Track('value', TrackType::PROPERTY, [DateTime::class]);

        $strategy = new AutodetectStrategy();
        $strategy->init($sourceProxy, $destinationProxy);
        $actual = $strategy->cast('2022-12-01', $destinationTrack);

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }
}
