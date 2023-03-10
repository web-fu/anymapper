<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Reflection\ReflectionTypeExtended;

class StrictStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new StrictStrategy();
        $actual = $strategy->cast(1, new ReflectionTypeExtended(['int']));

        $this->assertSame(1, $actual);
    }
}
