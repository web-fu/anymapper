<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ArrayAnalyzer;

class ArrayAnalyzerTest extends TestCase
{
    public function testGetOutputTrackList(): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ], $arrayAnalyzer->getOutputTrackList());
    }

    public function testGetInputTrackList(): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ], $arrayAnalyzer->getInputTrackList());
    }

    public function testGettablePaths(): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            'foo',
            'bar',
            'baz',
        ], $arrayAnalyzer->getGettableNames());
    }

    public function testSettablePaths(): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            'foo',
            'bar',
            'baz',
        ], $arrayAnalyzer->getSettableNames());
    }
}
