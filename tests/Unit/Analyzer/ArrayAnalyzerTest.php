<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ArrayAnalyzer;
use WebFu\Analyzer\ElementAnalyzer;
use WebFu\Analyzer\ElementType;

class ArrayAnalyzerTest extends TestCase
{
    /** @dataProvider arrayDataProvider */
    public function testGetOutputTrackList(array $array, array $expected): void
    {
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getOutputTrackList());
    }

    /** @dataProvider arrayDataProvider */
    public function testGetInputTrackList(array $array, array $expected): void
    {
        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEquals($expected, $arrayAnalyzer->getInputTrackList());
    }


    public function arrayDataProvider(): iterable
    {
        yield 'string index' => [
            'array' => [
                'fooIndex' => 'foo'
            ],
            'expected' =>  [
                'foo_index' => new ElementAnalyzer('fooIndex', ElementType::STRING_INDEX),
            ]
        ];
        yield 'numeric index' => [
            'array' => [
                'foo'
            ],
            'expected' =>  [
                '0' => new ElementAnalyzer(0, ElementType::NUMERIC_INDEX),
            ]
        ];
        yield 'mixed indexes' => [
            'array' => [
                'bar',
                'fooIndex' => 'foo'
            ],
            'expected' =>  [
                '0' => new ElementAnalyzer(0, ElementType::NUMERIC_INDEX),
                'foo_index' => new ElementAnalyzer('fooIndex', ElementType::STRING_INDEX),
            ]
        ];
    }
}
