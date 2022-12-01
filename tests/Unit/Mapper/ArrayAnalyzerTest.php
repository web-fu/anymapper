<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use WebFu\Mapper\ArrayAnalyzer;

class ArrayAnalyzerTest extends TestCase
{
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

    /**
     * @dataProvider gettablePathProvider
     */
    public function testGettablePath(string $path): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertInstanceOf(\Reflector::class, $arrayAnalyzer->getGettableMethod($path));
    }

    public function gettablePathProvider(): \Iterator
    {
        yield ['foo'];
        yield ['bar'];
        yield ['baz'];
    }

    /**
     * @dataProvider gettablePathProvider
     */
    public function testSettablePath(string $path): void
    {
        $array = [
            'foo' => 'foo value',
            'bar' => 'bar value',
            'baz' => 'baz value',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertInstanceOf(\Reflector::class, $arrayAnalyzer->getSettableMethod($path));
    }

    public function settablePathProvider(): \Iterator
    {
        yield ['foo'];
        yield ['bar'];
        yield ['baz'];
    }
}
