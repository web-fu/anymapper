<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Proxy;

use PHPUnit\Framework\TestCase;
use WebFu\Proxy\Proxy;
use stdClass;
use DateTime;

class ProxyTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     * @param mixed[]|object $element
     */
    public function testGet(object|array $element, string $path, mixed $expected): void
    {
        $proxy = new Proxy($element);

        $this->assertEquals($expected, $proxy->get($path));
    }

    /**
     * @return iterable<mixed>
     */
    public function getDataProvider(): iterable
    {
        yield 'class.scalar' => [
            'element' => new class() {
                public string $scalar = 'scalar';
            },
            'path' => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'class.array' => [
            'element' => new class() {
                /** @var int[] $list */
                public array $list = [0, 1, 2];
            },
            'path' => 'list',
            'expected' => [0, 1, 2],
        ];
        yield 'class.class' => [
            'element' => new class() {
                public object $object;

                public function __construct()
                {
                    $this->object = new stdClass();
                    $this->object->test = 'test';
                }
            },
            'path' => 'object',
            'expected' => (object) ['test' => 'test'],
        ];
        yield 'class.complex' => [
            'element' => new class() {
                /** @var object[] $objectList */
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
            'path' => 'object_list.0.string',
            'expected' => 'test',
        ];
        yield 'array.scalar' => [
            'element' => ['scalar' => 'scalar'],
            'path' => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array.array' => [
            'element' => ['list' => [0, 1, 2]],
            'path' => 'list',
            'expected' => [0, 1, 2],
        ];
        yield 'array.class' => [
            'element' => ['object' => (object) ['test' => 'test']],
            'path' => 'object',
            'expected' => (object) ['test' => 'test'],
        ];
        yield 'array.complex' => [
            'element' => ['objectList' => [
                new class() {
                    public string $string = 'test';
                },
            ]],
            'path' => 'object_list.0.string',
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

    /**
     * @return iterable<mixed>
     */
    public function setDataProvider(): iterable
    {
        yield 'scalar' => [
            'element' => new class() {
                public string $scalar;
            },
            'path' => 'scalar',
            'value' => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array' => [
            'element' => new class() {
                /** @var int[] */
                public array $list;
            },
            'path' => 'list',
            'value' => [0, 1, 2],
            'expected' => [0, 1, 2],
        ];
        yield 'element' => [
            'element' => new class() {
                public object $object;
            },
            'path' => 'object',
            'value' => new DateTime('2022-01-01'),
            'expected' => new DateTime('2022-01-01'),
        ];
        yield 'complex' => [
            'element' => new class() {
                /** @var object[] $objectList */
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
            'path' => 'object_list.0.string',
            'value' => 'test',
            'expected' => 'test',
        ];
    }
}
