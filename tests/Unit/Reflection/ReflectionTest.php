<?php
declare(strict_types=1);

namespace WebFu\Tests\Unit\Reflection;

use PHPUnit\Framework\TestCase;
use WebFu\Reflection\Reflection;
use WebFu\Tests\Fake\EntityWithAnnotation;
use ReflectionProperty;
use ReflectionParameter;
use ReflectionMethod;

class ReflectionTest extends TestCase
{
    /**
     * @dataProvider reflectionProvider
     */
    public function testTypes(ReflectionProperty|ReflectionMethod|ReflectionParameter $reflection, array $expected): void
    {
        $types = Reflection::types($reflection);

        $this->assertEquals($expected, $types);
    }

    public function reflectionProvider(): iterable
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
        yield 'method' => [
            'reflection' => new ReflectionMethod(EntityWithAnnotation::class, 'getArray'),
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
}