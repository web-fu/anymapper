<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Resolver;

use WebFu\Resolver\TypeResolver;
use PHPUnit\Framework\TestCase;
use WebFu\Tests\Fake\Foo;

class TypeResolverTest extends TestCase
{
    /**
     * @param string[] $expected
     * @dataProvider elementProvider
     */
    public function testResolve(mixed $value, array $expected): void
    {
        $resolver = new TypeResolver($value);
        $this->assertEquals($expected, $resolver->resolve());
    }

    public function elementProvider(): iterable
    {

        yield 'null' => [
            'value' => null,
            'expected' => ['null']
        ];
        yield 'bool' => [
            'value' => true,
            'expected' => ['bool']
        ];
        yield 'int' => [
            'value' => 1,
            'expected' => ['int']
        ];
        yield 'float' => [
            'value' => 1.0,
            'expected' => ['float']
        ];
        yield 'string' => [
            'value' => 'foo',
            'expected' => ['string']
        ];
        yield 'array' => [
            'value' => [],
            'expected' => ['array']
        ];
        yield 'resource' => [
            'value' => fopen(__FILE__, 'r'),
            'expected' => ['resource']
        ];
        yield 'named_object' => [
            'value' => new Foo(),
            'expected' => [Foo::class],
        ];
        yield 'anonymous_object' => [
            'value' => new class() {},
            'expected' => ['class@anonymous'],
        ];
    }
}
