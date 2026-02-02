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

namespace WebFu\Tests;

use PHPUnit\Framework\TestCase;

use function WebFu\camelcase_to_underscore;

/**
 * @covers \WebFu\camelcase_to_underscore
 *
 * @group unit
 */
class CamelCaseToUnderscoreTest extends TestCase
{
    public function testCamelCaseToUnderscore(): void
    {
        $this->assertEquals('foo', camelcase_to_underscore('foo'));
        $this->assertEquals('foo_bar', camelcase_to_underscore('fooBar'));
        $this->assertEquals('foo_bar_baz', camelcase_to_underscore('fooBarBaz'));
    }
}
