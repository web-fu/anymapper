<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use WebFu\Mapper\ClassAnalyzer;
use WebFu\Tests\Fake\FakeEntity;

class ClassAnalyzerTest extends TestCase
{
    public function testGetConstructor(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $constructor = $classAnalyzer->getConstructor();

        $this->assertNotNull($constructor);
    }

    public function testGetGetters(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $getters = $classAnalyzer->getGetters();

        $this->assertEqualsCanonicalizing([
            'getByConstructor',
            'getBySetter',
            'isStandard',
            'getParentProperty',
            'isParentProperty',
            'getTraitProperty',
            'isTraitProperty',
            '__get',
        ], array_keys($getters));
    }

    public function testGetSetters(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $setters = $classAnalyzer->getSetters();

        $this->assertEqualsCanonicalizing([
            'setBySetter',
            'setOverrodePublic',
            'setParentProperty',
            'setTraitProperty',
            '__set',
        ], array_keys($setters));
    }

    public function testGetProperties(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $properties = $classAnalyzer->getProperties();

        $this->assertEqualsCanonicalizing([
            'overrodePublic',
            'public',
            'parent',
            'trait',
        ], array_keys($properties));
    }

    public function testGetGenerators(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $generators = $classAnalyzer->getGenerators();

        $this->assertEqualsCanonicalizing([
            'createStatic',
            'createSelf',
            'create',
        ], array_keys($generators));
    }

    public function testGettablePaths(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEqualsCanonicalizing([
            'overrodePublic',
            'public',
            'parent',
            'trait',
            'getByConstructor',
            'getBySetter',
            'isStandard',
            'getParentProperty',
            'isParentProperty',
            'getTraitProperty',
            'isTraitProperty',
            '__get',
        ], $classAnalyzer->getGettablePaths());
    }

    public function testSettablePaths(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEqualsCanonicalizing([
            'overrodePublic',
            'public',
            'parent',
            'trait',
            'setBySetter',
            'setOverrodePublic',
            'setParentProperty',
            'setTraitProperty',
            '__set',
        ], $classAnalyzer->getSettablePaths());
    }
}
