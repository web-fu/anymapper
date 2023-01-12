<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Caster;
use DateTime;
use stdClass;

class CasterTest extends TestCase
{

    /**
     * @dataProvider castProvider
     * @param int|float|bool|string|object|mixed[]|null $value
     * @param int|float|bool|string|object|mixed[]|null $expected
     */
    public function testAs(int|float|bool|string|object|array|null $value, string $type, int|float|bool|string|object|array|null $expected): void
    {
        $actual = (new Caster($value))->as($type);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return iterable<mixed>
     */
    public function castProvider(): iterable
    {
        $class = new stdClass();
        $class->foo = 1;
        $class->bar = 'bar';

        yield 'true_to_int' => [
            'value' => true,
            'type' => 'int',
            'expected' => 1,
        ];
        yield 'false_to_int' => [
            'value' => false,
            'type' => 'int',
            'expected' => 0,
        ];
        yield 'true_to_string' => [
            'value' => true,
            'type' => 'string',
            'expected' => '1',
        ];
        yield 'false_to_string' => [
            'value' => false,
            'type' => 'string',
            'expected' => '',
        ];
        yield 'int_to_true' => [
            'value' => 1,
            'type' => 'bool',
            'expected' => true,
        ];
        yield 'int_to_false' => [
            'value' => 0,
            'type' => 'bool',
            'expected' => false,
        ];
        yield 'int_to_float' => [
            'value' => 1,
            'type' => 'float',
            'expected' => 1.0,
        ];
        yield 'int_to_string' => [
            'value' => 1,
            'type' => 'string',
            'expected' => '1',
        ];
        yield 'double_to_string' => [
            'value' => 1.5,
            'type' => 'string',
            'expected' => '1.5',
        ];
        yield 'string_to_true' => [
            'value' => 'non empty string',
            'type' => 'bool',
            'expected' => true,
        ];
        yield 'string_to_false' => [
            'value' => '',
            'type' => 'bool',
            'expected' => false,
        ];
        yield 'string_to_int' => [
            'value' => '1',
            'type' => 'int',
            'expected' => 1,
        ];
        yield 'string_to_float' => [
            'value' => '1.5',
            'type' => 'float',
            'expected' => 1.5,
        ];
        yield 'string_to_DateTime' => [
            'value' => '2022-12-01',
            'type' => 'DateTime',
            'expected' => new DateTime('2022-12-01'),
        ];
        yield 'array_to_object' => [
            'value' => ['foo' => 1, 'bar' => 'bar'],
            'type' => 'object',
            'expected' => $class,
        ];
        yield 'array_to_string' => [
            'value' => ['foo' => 1, 'bar' => 'bar'],
            'type' => 'string',
            'expected' =>
                'array (' . PHP_EOL .
                '  \'foo\' => 1,' . PHP_EOL .
                '  \'bar\' => \'bar\',' . PHP_EOL .
                ')',
        ];
        yield 'object_to_array' => [
            'value' => $class,
            'type' => 'array',
            'expected' => ['foo' => 1, 'bar' => 'bar'],
        ];
        yield 'object_to_string' => [
            'value' => $class,
            'type' => 'string',
            'expected' =>  '(object) array(' . PHP_EOL .
                '   \'foo\' => 1,' . PHP_EOL .
                '   \'bar\' => \'bar\',' . PHP_EOL .
                ')',
        ];
    }
}
