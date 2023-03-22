<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Internal;

use PHPUnit\Framework\TestCase;
use WebFu\Tests\Fixture\Foo;

use stdClass;

use function WebFu\Internal\get_type;

class GetTypeFunctionTest extends TestCase
{
    /**
     * @dataProvider elementProvider
     */
    public function test_get_type(mixed $value, string $expected): void
    {
        $this->assertEquals($expected, get_type($value));
    }

    /**
     * @return iterable<array{value:mixed, expected:string}>
     */
    public function elementProvider(): iterable
    {
        yield 'null' => [
            'value' => null,
            'expected' => 'null',
        ];
        yield 'bool' => [
            'value' => true,
            'expected' => 'bool',
        ];
        yield 'int' => [
            'value' => 1,
            'expected' => 'int',
        ];
        yield 'float' => [
            'value' => 1.0,
            'expected' => 'float',
        ];
        yield 'string' => [
            'value' => 'foo',
            'expected' => 'string',
        ];
        yield 'array' => [
            'value' => [],
            'expected' => 'array',
        ];
        yield 'resource' => [
            'value' => fopen(__FILE__, 'r'),
            'expected' => 'resource',
        ];
        yield 'named_object' => [
            'value' => new Foo(),
            'expected' => Foo::class,
        ];
        yield 'anonymous_object' => [
            'value' => new class () {},
            'expected' => 'class@anonymous',
        ];
        yield 'stdClass_object' => [
            'value' => (object) ['foo' => 'bar'],
            'expected' => stdClass::class,
        ];
        yield 'Closure' => [
            'value' => fn (): bool => true,
            'expected' => 'Closure',
        ];
    }
}
