<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\AnyMapper;
use WebFu\AnyMapper\Strategy\DataCastingStrategy;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\Tests\Fixture\EntityWithAnnotation;
use WebFu\Tests\Fixture\ChildClass;
use DateTime;
use stdClass;

class AnyMapperTest extends TestCase
{
    public function testMapInto(): void
    {
        $class = new ChildClass();

        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->into($class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testMapAs(): void
    {
        $class = (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->as(ChildClass::class);

        $this->assertInstanceOf(ChildClass::class, $class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testSerialize(): void
    {
        $class = new class () {
            public string $public = 'public';
            private string $value;

            public function __construct()
            {
                $this->value = 'construct';
            }

            public function getValue(): string
            {
                return $this->value;
            }

            public function getClass(): object
            {
                return new class () {
                    public string $element = 'element';
                };
            }

            /**
             * @return string[]
             */
            public function getArray(): array
            {
                return [
                    'foo',
                    'bar',
                ];
            }
        };

        $serialized = (new AnyMapper())->map($class)->serialize();

        $this->assertEquals([
            'public' => 'public',
            'value' => 'construct',
            'class' => [
                'element' => 'element',
            ],
            'array' => [
                'foo',
                'bar',
            ],
        ], $serialized);
    }

    public function testAllowDynamicProperties(): void
    {
        $class = (new AnyMapper())->map([
            'foo' => 1,
            'bar' => 'bar',
            'array' => [
                'foo',
                'bar',
            ],
        ])->as(stdClass::class);

        assert(property_exists($class, 'foo'));
        assert(property_exists($class, 'bar'));
        assert(property_exists($class, 'array'));

        $this->assertSame(1, $class->foo);
        $this->assertSame('bar', $class->bar);
        $this->assertEquals(['foo', 'bar'], $class->array);
    }

    public function testUsing(): void
    {
        $class = new class () {
            public DateTime $value;
        };

        $source = [
            'value' => '2022-12-01',
        ];

        (new AnyMapper())
            ->map($source)
            ->using(
                (new DataCastingStrategy())->allow('string', DateTime::class)
            )
            ->into($class);

        $this->assertEquals(new DateTime('2022-12-01'), $class->value);
    }

    public function testUseDocBlocks(): void
    {
        /** @var EntityWithAnnotation $class */
        $class = (new AnyMapper())->map([
            'foo' => 1,
        ])->using(new DocBlockDetectStrategy())
            ->as(EntityWithAnnotation::class);

        $this->assertSame(1, $class->getFoo()->getValue());
    }
}
