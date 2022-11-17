<?php

declare(strict_types=1);

namespace MapperTest;

use PHPUnit\Framework\TestCase;
use WebFu\Mapper\ClassAnalyzer;

class ClassAnalyzerTest extends TestCase
{
    private object $class;

    public function setUp(): void
    {
        $this->class = new class() {
            public $public_1;
            public $public_2;
            public $public_3;

            public function __construct()
            {
            }

            public function getPublic(): void
            {
            }

            public function isPublic(): void
            {
            }

            public function __get($key): void
            {
            }

            public function getty(): void
            {
            }

            protected function getProtected(): void
            {
            }

            private function getPrivate(): void
            {
            }

            public function setPublic(): void
            {
            }

            public function __set($key, $value): void
            {
            }
        };
    }

    public function testGetConstructor(): void
    {
        $classAnalyzer = new ClassAnalyzer($this->class);
        $constructor = $classAnalyzer->getConstructor();

        $this->assertNotNull($constructor);
    }

    public function testGetGetters(): void
    {
        $classAnalyzer = new ClassAnalyzer($this->class);
        $getters = $classAnalyzer->getGetters();

        $this->assertEquals([
            'getPublic',
            'isPublic',
            '__get',
        ], array_keys($getters));
    }

    public function testGetSetters(): void
    {
        $classAnalyzer = new ClassAnalyzer($this->class);
        $setters = $classAnalyzer->getSetters();

        $this->assertEquals([
            'setPublic',
            '__set',
        ], array_keys($setters));
    }

    public function testGetProperties(): void
    {
        $classAnalyzer = new ClassAnalyzer($this->class);
        $properties = $classAnalyzer->getProperties();

        $this->assertEquals([
            'public_1',
            'public_2',
            'public_3',
        ], array_keys($properties));
    }
}
