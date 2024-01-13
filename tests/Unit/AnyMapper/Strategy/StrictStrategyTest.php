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

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Reflection\ReflectionTypeExtended;

/**
 * @coversNothing
 */
class StrictStrategyTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     *
     * @param string[] $types
     */
    public function testCast(mixed $value, array $types): void
    {
        $strategy = new StrictStrategy();
        $actual   = $strategy->cast($value, new ReflectionTypeExtended($types));

        $this->assertSame($value, $actual);
    }

    /**
     * @return iterable<array{value: mixed, types: string[]}>
     */
    public function typeProvider(): iterable
    {
        yield 'boolean_as_boolean' => [
            'value' => true,
            'types' => ['bool'],
        ];
        yield 'boolean_as_boolean_or_string' => [
            'value' => true,
            'types' => ['bool', 'string'],
        ];
        yield 'boolean_as_mixed' => [
            'value' => true,
            'types' => ['mixed'],
        ];

        yield 'float_as_int' => [
            'value' => 1.0,
            'types' => ['float'],
        ];
        yield 'float_as_int_or_string' => [
            'value' => 1.0,
            'types' => ['float', 'string'],
        ];
        yield 'float_as_mixed' => [
            'value' => 1.0,
            'types' => ['mixed'],
        ];

        yield 'int_as_int' => [
            'value' => 1,
            'types' => ['int'],
        ];
        yield 'int_as_int_or_string' => [
            'value' => 1,
            'types' => ['int', 'string'],
        ];
        yield 'int_as_mixed' => [
            'value' => 1,
            'types' => ['mixed'],
        ];

        yield 'array_as_array' => [
            'value' => [1],
            'types' => ['array'],
        ];
        yield 'array_as_array_or_string' => [
            'value' => [1],
            'types' => ['array', 'string'],
        ];
        yield 'array_as_mixed' => [
            'value' => [1],
            'types' => ['mixed'],
        ];

        yield 'object_as_object' => [
            'value' => (object) ['foo' => 'bar'],
            'types' => ['object'],
        ];
        yield 'object_as_object_or_string' => [
            'value' => (object) ['foo' => 'bar'],
            'types' => ['object', 'string'],
        ];
        yield 'object_as_mixed' => [
            'value' => (object) ['foo' => 'bar'],
            'types' => ['mixed'],
        ];

        yield 'anonymous_class_as_object' => [
            'value' => new class() {
                public string $foo = 'bar';
            },
            'types' => ['object'],
        ];
        yield 'anonymous_class_as_object_or_string' => [
            'value' => new class() {
                public string $foo = 'bar';
            },
            'types' => ['object', 'string'],
        ];
        yield 'anonymous_class_as_mixed' => [
            'value' => new class() {
                public string $foo = 'bar';
            },
            'types' => ['mixed'],
        ];
    }

    public function testCastFail(): void
    {
        $strategy = new StrictStrategy();

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type int into any of the following types: string, boolean');

        $strategy->cast(1, new ReflectionTypeExtended(['string', 'boolean']));
    }
}
