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

namespace WebFu\Tests\Integration\Analyzer;

use DateTime;
use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\Reflection\ReflectionType;
use WebFu\Tests\Fixtures\EntityWithAnnotation;
use WebFu\Tests\Fixtures\Foo;

/**
 * @coversDefaultClass \WebFu\Analyzer\ClassAnalyzer
 */
class ClassAnalyzerTest extends TestCase
{
    /**
     * @covers ::getInputTrack
     */
    public function testGetInputTrack(): void
    {
        $classAnalyzer = new ClassAnalyzer(EntityWithAnnotation::class);
        $inputTrack    = $classAnalyzer->getInputTrack('d_t_value');

        $this->assertEquals(new Track('setDTValue', TrackType::METHOD, new ReflectionType(['mixed'], [DateTime::class])), $inputTrack);

        $inputTrack = $classAnalyzer->getInputTrack('foo');

        $this->assertEquals(new Track('setFoo', TrackType::METHOD, new ReflectionType(['mixed'], [Foo::class])), $inputTrack);
    }
}
