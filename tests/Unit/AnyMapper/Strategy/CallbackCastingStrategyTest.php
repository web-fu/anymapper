<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Strategy\CallbackCastingStrategy;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\AnyMapper\MapperException;
use DateTime;

class CallbackCastingStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new CallbackCastingStrategy();
        $strategy->addMethod('string', 'int', fn (string $value) => (int) $value);
        $actual = $strategy->cast('1', new ReflectionTypeExtended(['int']));

        $this->assertEquals(1, $actual);
    }

    public function testCastFail(): void
    {
        $strategy = new CallbackCastingStrategy();
        $strategy->addMethod('string', 'int', fn (string $value) => (int) $value);

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type string into any of the following types: '. DateTime::class);

        $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));
    }
}
