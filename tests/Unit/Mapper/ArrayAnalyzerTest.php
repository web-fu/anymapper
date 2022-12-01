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
            'foo',
            'bar',
            'baz',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            0,
            1,
            2
        ], $arrayAnalyzer->getGettablePaths());
    }

    public function testSettablePaths(): void
    {
        $array = [
            'foo',
            'bar',
            'baz',
        ];

        $arrayAnalyzer = new ArrayAnalyzer($array);

        $this->assertEqualsCanonicalizing([
            0,
            1,
            2
        ], $arrayAnalyzer->getSettablePaths());
    }
}
