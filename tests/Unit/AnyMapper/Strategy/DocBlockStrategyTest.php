<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixture\Foo;

class DocBlockStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new DocBlockDetectStrategy();
        $actual = $strategy->cast(Foo::class, new ReflectionTypeExtended(['string'], ['class-string']));

        $this->assertSame(Foo::class, $actual);
    }
}
