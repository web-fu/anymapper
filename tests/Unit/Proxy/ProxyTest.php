<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Proxy;

use PHPUnit\Framework\TestCase;
use WebFu\Proxy\Proxy;

class ProxyTest extends TestCase
{
    public function testGet(): void
    {
        $class = new class() {
            public array $objectList;
        };
        $class->objectList = [
            new class() {
                public string $string = 'test';
            },
        ];

        $proxy = new Proxy($class);

        $this->assertSame('test', $proxy->get('objectList.0.string'));
    }

    public function testSet(): void
    {
        $class = new class() {
            public array $objectList;
        };
        $class->objectList = [
            new class() {
                public string $string;
            },
        ];

        $proxy = new Proxy($class);
        $proxy->set('objectList.0.string', 'test');

        $this->assertSame('test', $class->objectList[0]->string);
    }
}
