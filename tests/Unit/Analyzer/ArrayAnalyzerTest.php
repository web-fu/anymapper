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
                'foo_index' => new Element('fooIndex', ElementSource::STRING_INDEX),
            ],
        ];
        yield 'numeric index' => [
            'array' => [
                'foo',
            ],
            'expected' => [
                '0' => new Element(0, ElementSource::NUMERIC_INDEX),
            ],
        ];
        yield 'mixed indexes' => [
            'array' => [
                'bar',
                'fooIndex' => 'foo',
            ],
            'expected' => [
                '0' => new Element(0, ElementSource::NUMERIC_INDEX),
                'foo_index' => new Element('fooIndex', ElementSource::STRING_INDEX),
            ],
        ];
    }
}
