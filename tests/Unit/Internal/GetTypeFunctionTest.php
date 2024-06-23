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

namespace WebFu\Tests\Unit\Internal;

use PHPUnit\Framework\TestCase;
use stdClass;

use function WebFu\Internal\get_type;

use WebFu\Tests\Fixtures\Foo;

/**
 * @covers \WebFu\Internal\get_type
 */
class GetTypeFunctionTest extends TestCase
{
    /**
     * @dataProvider elementProvider
     */
    public function testGetType(mixed $value, string $expected): void
    {
        $this->assertEquals($expected, get_type($value));
    }

    /**
     * @return iterable<array{value:mixed, expected:string}>
     */
    public function elementProvider(): iterable
    {
        yield 'null' => [
            'value'    => null,
            'expected' => 'null',
        ];
        yield 'bool' => [
            'value'    => true,
            'expected' => 'bool',
        ];
        yield 'int' => [
            'value'    => 1,
            'expected' => 'int',
        ];
        yield 'float' => [
            'value'    => 1.0,
            'expected' => 'float',
        ];
        yield 'string' => [
            'value'    => 'foo',
            'expected' => 'string',
        ];
        yield 'array' => [
            'value'    => [],
            'expected' => 'array',
        ];
        yield 'resource' => [
            'value'    => fopen(__FILE__, 'r'),
            'expected' => 'resource',
        ];
        yield 'named_object' => [
            'value'    => new Foo(),
            'expected' => Foo::class,
        ];
        yield 'anonymous_object' => [
            'value'    => new class() {},
            'expected' => 'class@anonymous',
        ];
        yield 'stdClass_object' => [
            'value'    => (object) ['foo' => 'bar'],
            'expected' => stdClass::class,
        ];
        yield 'Closure' => [
            'value'    => fn (): bool => true,
            'expected' => 'Closure',
        ];
    }
}
