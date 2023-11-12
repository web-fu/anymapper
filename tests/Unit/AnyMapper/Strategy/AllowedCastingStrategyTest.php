<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AllowedCastingStrategy;
use DateTime;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixtures\Foo;

class AllowedCastingStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new AllowedCastingStrategy();
        $strategy->allow('string', DateTime::class);
        $actual = $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }

    public function testCastFail(): void
    {
        $strategy = new AllowedCastingStrategy();
        $strategy->allow('string', Foo::class);

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type string into any of the following types: '. DateTime::class);

        $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));
    }
}
