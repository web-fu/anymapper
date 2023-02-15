<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\AnyMapper\Strategy\DataCastingStrategy;
use DateTime;

class DataCastingStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new DataCastingStrategy();
        $strategy->allow('string', DateTime::class);
        $actual = $strategy->cast('2022-12-01', [DateTime::class]);

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }
}
