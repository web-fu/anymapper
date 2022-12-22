<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use WebFu\Mapper\AnyMapper;
use WebFu\Tests\Fake\FakeEntity;

class AnyMapperTest extends TestCase
{
    public function testMap(): void
    {
        $class = new FakeEntity();

        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->into($class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }
}