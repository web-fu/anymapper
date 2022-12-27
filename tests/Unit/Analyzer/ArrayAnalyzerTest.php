<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ArrayAnalyzer;
use WebFu\Analyzer\Element;
use WebFu\Analyzer\ElementSource;

class ArrayAnalyzerTest extends TestCase
{
    /**
     * @dataProvider arrayDataProvider
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
                'foo_index' => new Element('fooIndex', ElementSource::STRING_INDEX, ['string']),
            ],
        ];
        yield 'numeric index' => [
            'array' => [
                'foo',
            ],
            'expected' => [
                '0' => new Element(0, ElementSource::NUMERIC_INDEX, ['string']),
            ],
        ];
        yield 'mixed indexes' => [
            'array' => [
                'bar',
                'fooIndex' => 'foo',
            ],
            'expected' => [
                '0' => new Element(0, ElementSource::NUMERIC_INDEX, ['string']),
                'foo_index' => new Element('fooIndex', ElementSource::STRING_INDEX, ['string']),
            ],
        ];
    }

    /**
     * @dataProvider trackDataProvider
     */
    public function testInputTrack(Element|null $expected, string $track): void
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
    public function testOutputTrack(Element|null $expected, string $track): void
    {
        $array = [
            'foo',
            'bar' => 'baz',
        ];
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getOutputTrack($track));
    }

    /**
     * @return iterable<mixed>
     */
    public function trackDataProvider(): iterable
    {
        yield 'numeric' => [
            'expected' => new Element(0, ElementSource::NUMERIC_INDEX, ['string']),
            'track' => '0',
        ];
        yield 'string' => [
            'expected' => new Element('bar', ElementSource::STRING_INDEX, ['string']),
            'track' => 'bar',
        ];
        yield 'null' => [
            'expected' => null,
            'track' => 'does_not_exist',
        ];
    }
}
