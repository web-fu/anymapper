<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use DateTime;
use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use WebFu\Reflection\ReflectionType;
use WebFu\Tests\Fixtures\BackedEnum;
use WebFu\Tests\Fixtures\BasicEnum;
use WebFu\Tests\Fixtures\ClassWithMultipleParameters;
use WebFu\Tests\Fixtures\ClassWithOneParameter;
use WebFu\Tests\Fixtures\ClassWithZeroParameters;

/**
 * @coversNothing
 */
class AutodetectStrategyTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     *
     * @param string[] $types
     */
    public function testCast(mixed $value, mixed $expected, array $types): void
    {
        $strategy = new AutodetectStrategy();
        $actual   = $strategy->cast($value, new ReflectionType($types));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return iterable<array{value: mixed, types: string[]}>
     */
    public function typeProvider(): iterable
    {
        yield 'int_as_int' => [
            'value'    => 1,
            'expected' => 1,
            'types'    => ['int'],
        ];
        yield 'string_as_datetime' => [
            'value'    => '2022-12-01',
            'expected' => new DateTime('2022-12-01'),
            'types'    => [DateTime::class],
        ];
        yield 'int_as_enum' => [
            'value'    => 1,
            'expected' => BackedEnum::ONE,
            'types'    => [BackedEnum::class],
        ];
    }

    /**
     * @dataProvider failTypeProvider
     */
    public function testCastFail(string $className): void
    {
        $strategy = new AutodetectStrategy();

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type int into any of the following types: '.$className);

        $strategy->cast(1, new ReflectionType([$className]));
    }

    /**
     * @return iterable<array{class_name: class-string|string}>
     */
    public function failTypeProvider(): iterable
    {
        yield 'zero_parameters' => ['class_name' => ClassWithZeroParameters::class];
        yield 'one_parameter' => ['class_name' => ClassWithOneParameter::class];
        yield 'multiple_parameters' => ['class_name' => ClassWithMultipleParameters::class];
        yield 'non_existent_class' => ['class_name' => 'NonExistentClass'];
        yield 'basic_enum' => ['class_name' => BasicEnum::class];
    }
}
