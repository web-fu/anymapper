<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Proxy\Proxy;

class StrictStrategyTest extends TestCase
{
    public function testRun(): void
    {
        $class = new class {
            public int $int;
        };

        $sourceProxy = new Proxy([
            'int' => 1,
        ]);
        $destinationProxy = new Proxy(
            $class
        );

        $strategy = new StrictStrategy();
        $strategy->init($sourceProxy, $destinationProxy);
        $strategy->run();

        $this->assertSame(1, $class->int);
    }
}