<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;

use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use WebFu\Proxy\Proxy;
use DateTime;

class AutodetectStrategyTest extends TestCase
{
    public function testRun(): void
    {
        $class = new class {
            public DateTime $value;
        };

        $sourceProxy = new Proxy([
            'value' => '2022-12-01',
        ]);
        $destinationProxy = new Proxy(
            $class
        );

        $strategy = new AutodetectStrategy();
        $strategy->init($sourceProxy, $destinationProxy);
        $strategy->run();

        $this->assertEquals(new DateTime('2022-12-01'), $class->value);
    }
}