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
        ], $arrayAnalyzer->getGettablePaths());
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
        ], $arrayAnalyzer->getSettablePaths());
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

        $this->assertInstanceOf(\Reflector::class, $arrayAnalyzer->getGettablePath($path));
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

        $this->assertInstanceOf(\Reflector::class, $arrayAnalyzer->getSettablePath($path));
    }

    public function settablePathProvider(): \Iterator
    {
        yield ['foo'];
        yield ['bar'];
        yield ['baz'];
    }
}
