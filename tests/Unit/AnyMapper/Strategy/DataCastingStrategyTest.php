<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\DataCastingStrategy;
use DateTime;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixture\Foo;

class DataCastingStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new DataCastingStrategy();
        $strategy->allow('string', DateTime::class);
        $actual = $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }

    public function testCastFail(): void
    {
        $strategy = new DataCastingStrategy();
        $strategy->allow('string', Foo::class);

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type string into any of the following types: '. DateTime::class);

        $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));
    }
}
