<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Proxy;

use PHPUnit\Framework\TestCase;
use WebFu\Proxy\Proxy;
use WebFu\Tests\Fake\FakeEntity;

class ProxyTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     */
    public function testGet(object $class, string $path, mixed $expected): void
    {
        $proxy = new Proxy($class);

        $this->assertEquals($expected, $proxy->get($path));
    }

    public function getDataProvider(): iterable
    {
        yield 'scalar' => [
            'class' => new class() {
                public string $scalar = 'scalar';
            },
            'path' => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array' => [
            'class' => new class() {
                public array $list = [0, 1, 2];
            },
            'path' => 'list',
            'expected' => [0, 1, 2],
        ];
        yield 'class' => [
            'class' => new class() {
                public object $object;

                public function __construct()
                {
                    $this->object = new \DateTime('2022-01-01');
                }
            },
            'path' => 'object',
            'expected' => new \DateTime('2022-01-01'),
        ];
        yield 'complex' => [
            'class' => new class() {
                public array $objectList;

                public function __construct()
                {
                    $this->objectList = [
                        new class() {
                            public string $string = 'test';
                        },
                    ];
                }
            },
            'path' => 'objectList.0.string',
            'expected' => 'test',
        ];
    }

    /**
     * @dataProvider setDataProvider
     */
    public function testSet(object $class, string $path, mixed $value, mixed $expected): void
    {
        $proxy = new Proxy($class);
        $proxy->set($path, $value);

        $this->assertEquals($expected, $proxy->get($path));
    }

    public function setDataProvider(): iterable
    {
        yield 'scalar' => [
            'class' => new class() {
                public string $scalar;
            },
            'path' => 'scalar',
            'value' => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array' => [
            'class' => new class() {
                public array $list;
            },
            'path' => 'list',
            'value' => [0, 1, 2],
            'expected' => [0, 1, 2],
        ];
        yield 'class' => [
            'class' => new class() {
                public object $object;
            },
            'path' => 'object',
            'value' => new \DateTime('2022-01-01'),
            'expected' => new \DateTime('2022-01-01'),
        ];
        yield 'complex' => [
            'class' => new class() {
                public array $objectList;

                public function __construct()
                {
                    $this->objectList = [
                        new class() {
                            public string $string;
                        },
                    ];
                }
            },
            'path' => 'objectList.0.string',
            'value' => 'test',
            'expected' => 'test',
        ];
    }

    public function testGetPathMap()
    {
        $class = new FakeEntity();

        $proxy = new Proxy($class);

        $pathMap = $proxy->getPathMap();

        $this->assertEquals([
            'get' => [
                'parent' => 'parent',
                'public' => 'public',
                'overrode_public' => 'overrodePublic',
                'trait' => 'trait',
                'get_parent_property' => 'getParentProperty',
                'parent_property' => 'isParentProperty',
                'is_parent_property' => 'isParentProperty',
                'get_by_constructor' => 'getByConstructor',
                'by_constructor' => 'getByConstructor',
                'get_by_setter' => 'getBySetter',
                'by_setter' => 'getBySetter',
                'is_standard' => 'isStandard',
                'standard' => 'isStandard',
                '__get' => '__get',
                'get_trait_property' => 'getTraitProperty',
                'trait_property' => 'isTraitProperty',
                'is_trait_property' => 'isTraitProperty',
            ],
            'set' => [
                'parent' => 'parent',
                'public' => 'public',
                'overrode_public' => 'setOverrodePublic',
                'trait' => 'trait',
                'set_parent_property' => 'setParentProperty',
                'parent_property' => 'setParentProperty',
                'set_by_setter' => 'setBySetter',
                'by_setter' => 'setBySetter',
                'set_overrode_public' => 'setOverrodePublic',
                '__set' => '__set',
                'set_trait_property' => 'setTraitProperty',
                'trait_property' => 'setTraitProperty',
            ],
        ], $pathMap);
    }
}
