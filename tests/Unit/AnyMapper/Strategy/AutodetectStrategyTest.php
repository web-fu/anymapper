<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;

use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use DateTime;

class AutodetectStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new AutodetectStrategy();
        $actual = $strategy->cast('2022-12-01', [DateTime::class]);

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }
}
