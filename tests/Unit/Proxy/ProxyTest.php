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

namespace WebFu\Tests\Unit\Proxy;

use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;
use WebFu\Proxy\Proxy;
use WebFu\Proxy\ProxyException;

/**
 * @coversNothing
 */
class ProxyTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     *
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
            'path'     => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'class.array' => [
            'element' => new class() {
                /**
                 * @var int[]
                 */
                public array $list = [0, 1, 2];
            },
            'path'     => 'list',
            'expected' => [0, 1, 2],
        ];
        yield 'class.class' => [
            'element' => new class() {
                public object $object;

                public function __construct()
                {
                    $this->object       = new stdClass();
                    $this->object->test = 'test';
                }
            },
            'path'     => 'object',
            'expected' => (object) ['test' => 'test'],
        ];
        yield 'class.complex' => [
            'element' => new class() {
                /**
                 * @var object[]
                 */
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
            'path'     => 'object_list.0.string',
            'expected' => 'test',
        ];
        yield 'array.scalar' => [
            'element'  => ['scalar' => 'scalar'],
            'path'     => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array.array' => [
            'element'  => ['list' => [0, 1, 2]],
            'path'     => 'list',
            'expected' => [0, 1, 2],
        ];
        yield 'array.class' => [
            'element'  => ['object' => (object) ['test' => 'test']],
            'path'     => 'object',
            'expected' => (object) ['test' => 'test'],
        ];
        yield 'array.complex' => [
            'element' => ['objectList' => [
                new class() {
                    public string $string = 'test';
                },
            ]],
            'path'     => 'object_list.0.string',
            'expected' => 'test',
        ];
    }

    public function testGetFail(): void
    {
        $element = ['foo' => 1];

        $proxy = new Proxy($element);

        $this->expectException(ProxyException::class);
        $this->expectExceptionMessage('bar gettable not found');

        $proxy->get('bar');
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
            'path'     => 'scalar',
            'value'    => 'scalar',
            'expected' => 'scalar',
        ];
        yield 'array' => [
            'element' => new class() {
                /**
                 * @var int[]
                 */
                public array $list;
            },
            'path'     => 'list',
            'value'    => [0, 1, 2],
            'expected' => [0, 1, 2],
        ];
        yield 'element' => [
            'element' => new class() {
                public object $object;
            },
            'path'     => 'object',
            'value'    => new DateTime('2022-01-01'),
            'expected' => new DateTime('2022-01-01'),
        ];
        yield 'complex' => [
            'element' => new class() {
                /**
                 * @var object[]
                 */
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
            'path'     => 'object_list.0.string',
            'value'    => 'test',
            'expected' => 'test',
        ];
    }

    public function testSetFail(): void
    {
        $element = ['foo' => 1];

        $proxy = new Proxy($element);

        $this->expectException(ProxyException::class);
        $this->expectExceptionMessage('bar settable not found');

        $proxy->set('bar', 2);
    }
}
