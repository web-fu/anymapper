<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Reflection;

use PHPUnit\Framework\TestCase;
use WebFu\Reflection\Reflection;
use WebFu\Tests\Fixture\EntityWithAnnotation;
use ReflectionProperty;
use ReflectionParameter;
use ReflectionMethod;
use ReflectionClass;
use WebFu\Tests\Fixture\Foo;

class ReflectionTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     * @param string[] $expected
     */
    public function testTypes(ReflectionProperty|ReflectionMethod|ReflectionParameter $reflection, array $expected): void
    {
        $types = Reflection::types($reflection);

        $this->assertEquals($expected, $types);
    }

    /**
     * @return iterable<mixed>
     */
    public function typeProvider(): iterable
    {
        yield 'property' => [
            'reflection' => new ReflectionProperty(EntityWithAnnotation::class, 'array'),
            'expected' => [
                'string[]',
            ],
        ];
        yield 'union_type' => [
            'reflection' => new ReflectionProperty(EntityWithAnnotation::class, 'unionType'),
            'expected' => [
                'string',
                'int',
                'null',
            ],
        ];
        yield 'method_get' => [
            'reflection' => new ReflectionMethod(EntityWithAnnotation::class, 'getStringArray'),
            'expected' => [
                'string[]',
            ],
        ];
        yield 'method_set' => [
            'reflection' => (new ReflectionMethod(EntityWithAnnotation::class, 'setStringArray'))->getParameters()[0],
            'expected' => [
                'string[]',
            ],
        ];
        yield 'parameter' => [
            'reflection' => new ReflectionParameter([EntityWithAnnotation::class, 'parameter'], 'parameter'),
            'expected' => [
                'string[]',
            ],
        ];
    }

    /**
     * @dataProvider templateProvider
     * @param string[] $expected
     */
    public function testTemplates(ReflectionClass|ReflectionProperty|ReflectionMethod $reflection, array $expected): void
    {
        $templates = Reflection::templates($reflection);
        $this->assertEquals($expected, $templates);
    }

    /**
     * @return iterable<mixed>
     */
    public function templateProvider(): iterable
    {
        yield 'class' => [
            'reflection' => new ReflectionClass(EntityWithAnnotation::class),
            'expected' => [
                'F' => Foo::class,
            ],
        ];
    }
}
