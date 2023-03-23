<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;

use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use DateTime;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixture\ClassWithMultipleParameters;
use WebFu\Tests\Fixture\ClassWithOneParameter;
use WebFu\Tests\Fixture\ClassWithZeroParameters;

class AutodetectStrategyTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     * @param string[] $types
     */
    public function testCast(mixed $value, mixed $expected, array $types): void
    {
        $strategy = new AutodetectStrategy();
        $actual = $strategy->cast($value, new ReflectionTypeExtended($types));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return iterable<array{value: mixed, types: string[]}>
     */
    public function typeProvider(): iterable
    {
       yield 'int_as_int' => [
           'value' => 1,
           'expected' => 1,
           'types' => ['int'],
       ];
        yield 'string_as_datetime' => [
            'value' => '2022-12-01',
            'expected' => new DateTime('2022-12-01'),
            'types' => [DateTime::class],
        ];
    }

    /**
     * @dataProvider failTypeProvider
     */
    public function testCastFail(string $className): void
    {
        $strategy = new AutodetectStrategy();

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type int into any of the following types: ' . $className);

        $strategy->cast(1, new ReflectionTypeExtended([$className]));
    }

    public function failTypeProvider(): iterable
    {
        yield 'zero_parameters' => ['class_name' => ClassWithZeroParameters::class];
        yield 'one_parameter' => ['class_name' => ClassWithOneParameter::class];
        yield 'multiple_parameters' => ['class_name' => ClassWithMultipleParameters::class];
    }
}
