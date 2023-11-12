<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixtures\ClassWithMultipleParameters;
use WebFu\Tests\Fixtures\ClassWithOneParameter;
use WebFu\Tests\Fixtures\ClassWithZeroParameters;
use WebFu\Tests\Fixtures\Foo;

class DocBlockStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new DocBlockDetectStrategy();
        $actual = $strategy->cast(Foo::class, new ReflectionTypeExtended(['string'], ['class-string']));

        $this->assertSame(Foo::class, $actual);
    }

    /**
     * @dataProvider failTypeProvider
     */
    public function testCastFail(string $className): void
    {
        $strategy = new AutodetectStrategy();

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type int into any of the following types: string');

        $strategy->cast(1, new ReflectionTypeExtended(['string'], ['class-string']));
    }

    /**
     * @return iterable<array{class_name: class-string}>
     */
    public function failTypeProvider(): iterable
    {
        yield 'zero_parameters' => ['class_name' => ClassWithZeroParameters::class];
        yield 'one_parameter' => ['class_name' => ClassWithOneParameter::class];
        yield 'multiple_parameters' => ['class_name' => ClassWithMultipleParameters::class];
    }
}
