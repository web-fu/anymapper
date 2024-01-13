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

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ArrayAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\Reflection\ReflectionTypeExtended;

/**
 * @coversNothing
 */
class ArrayAnalyzerTest extends TestCase
{
    /**
     * @dataProvider arrayDataProvider
     *
     * @param mixed[] $array
     * @param mixed[] $expected
     */
    public function testGetOutputTrackList(array $array, array $expected): void
    {
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getOutputTrackList());
    }

    /**
     * @dataProvider arrayDataProvider
     *
     * @param mixed[] $array
     * @param mixed[] $expected
     */
    public function testGetInputTrackList(array $array, array $expected): void
    {
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getInputTrackList());
    }

    /**
     * @return iterable<mixed>
     */
    public function arrayDataProvider(): iterable
    {
        yield 'string index' => [
            'array' => [
                'fooIndex' => 'foo',
            ],
            'expected' => [
                'foo_index' => new Track('fooIndex', TrackType::STRING_INDEX, new ReflectionTypeExtended(['string'])),
            ],
        ];
        yield 'numeric index' => [
            'array' => [
                'foo',
            ],
            'expected' => [
                '0' => new Track(0, TrackType::NUMERIC_INDEX, new ReflectionTypeExtended(['string'])),
            ],
        ];
        yield 'mixed indexes' => [
            'array' => [
                'bar',
                'fooIndex' => 'foo',
            ],
            'expected' => [
                '0'         => new Track(0, TrackType::NUMERIC_INDEX, new ReflectionTypeExtended(['string'])),
                'foo_index' => new Track('fooIndex', TrackType::STRING_INDEX, new ReflectionTypeExtended(['string'])),
            ],
        ];
    }

    /**
     * @dataProvider trackDataProvider
     */
    public function testInputTrack(Track|null $expected, string $track): void
    {
        $array = [
            'foo',
            'bar' => 'baz',
        ];
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getInputTrack($track));
    }

    /**
     * @dataProvider trackDataProvider
     */
    public function testOutputTrack(Track|null $expected, string $track): void
    {
        $array = [
            'foo',
            'bar' => 'baz',
        ];
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getOutputTrack($track));
    }

    /**
     * @return iterable<array{expected:Track|null, track:string}>
     */
    public function trackDataProvider(): iterable
    {
        yield 'numeric' => [
            'expected' => new Track(0, TrackType::NUMERIC_INDEX, new ReflectionTypeExtended(['string'])),
            'track'    => '0',
        ];
        yield 'string' => [
            'expected' => new Track('bar', TrackType::STRING_INDEX, new ReflectionTypeExtended(['string'])),
            'track'    => 'bar',
        ];
        yield 'null' => [
            'expected' => null,
            'track'    => 'does_not_exist',
        ];
    }
}
