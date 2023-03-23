<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Strategy\SQLFetchStrategy;
use WebFu\Reflection\ReflectionTypeExtended;
use DateTime;

class SQLFetchStrategyTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     * @param string[] $types
     */
    public function testCast(mixed $value, mixed $expected, array $types): void
    {
        $strategy = new SQLFetchStrategy();
        $actual = $strategy->cast($value, new ReflectionTypeExtended($types));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return iterable<array{value: mixed, types: string[]}>
     */
    public function typeProvider(): iterable
    {
        yield 'string_as_boolean_true' => [
            'value' => '1',
            'expected' => true,
            'types' => ['bool'],
        ];

        yield 'string_as_boolean_false' => [
            'value' => '0',
            'expected' => false,
            'types' => ['bool'],
        ];

        yield 'string_as_int' => [
            'value' => '1',
            'expected' => 1,
            'types' => ['int'],
        ];

        yield 'string_as_float' => [
            'value' => '0.5',
            'expected' => 0.5,
            'types' => ['float'],
        ];

        yield 'string_as_datetime' => [
            'value' => '2023-01-01',
            'expected' => new DateTime('2023-01-01'),
            'types' => [DateTime::class],
        ];
    }
}
