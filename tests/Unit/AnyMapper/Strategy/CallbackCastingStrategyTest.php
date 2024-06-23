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
use WebFu\AnyMapper\Strategy\CallbackCastingStrategy;
use WebFu\Reflection\ReflectionType;

/**
 * @coversDefaultClass \WebFu\AnyMapper\Strategy\CallbackCastingStrategy
 */
class CallbackCastingStrategyTest extends TestCase
{
    /**
     * @covers ::addMethod
     * @covers ::cast
     */
    public function testCast(): void
    {
        $strategy = new CallbackCastingStrategy();
        $strategy->addMethod('string', 'int', fn (string $value) => (int) $value);
        $actual = $strategy->cast('1', new ReflectionType(['int']));

        $this->assertEquals(1, $actual);
    }

    /**
     * @covers ::addMethod
     * @covers ::cast
     */
    public function testCastFail(): void
    {
        $strategy = new CallbackCastingStrategy();
        $strategy->addMethod('string', 'int', fn (string $value) => (int) $value);

        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Cannot convert type string into any of the following types: '.DateTime::class);

        $strategy->cast('2022-12-01', new ReflectionType([DateTime::class]));
    }
}
