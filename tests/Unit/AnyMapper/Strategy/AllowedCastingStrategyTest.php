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

namespace WebFu\Tests\Unit\AnyMapper\Strategy;

use DateTime;
use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AllowedCastingStrategy;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixtures\Foo;

/**
 * @coversNothing
 */
class AllowedCastingStrategyTest extends TestCase
{
    public function testCast(): void
    {
        $strategy = new AllowedCastingStrategy();
        $strategy->allow('string', DateTime::class);
        $actual = $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));

        $this->assertEquals(new DateTime('2022-12-01'), $actual);
    }

    public function testCastFail(): void
    {
        $strategy = new AllowedCastingStrategy();
        $strategy->allow('string', Foo::class);

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type string into any of the following types: '.DateTime::class);

        $strategy->cast('2022-12-01', new ReflectionTypeExtended([DateTime::class]));
    }
}
