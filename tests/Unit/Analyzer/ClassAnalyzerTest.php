<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
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

    public function testGetOutputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getOutputTrackList();

        $this->assertEquals([
            'parent' => 'parent',
            'public' => 'public',
            'overrode_public' => 'overrodePublic',
            'trait' => 'trait',
            'get_parent_property' => 'getParentProperty',
            'parent_property' => 'isParentProperty',
            'is_parent_property' => 'isParentProperty',
            'get_by_constructor' => 'getByConstructor',
            'by_constructor' => 'getByConstructor',
            'get_by_setter' => 'getBySetter',
            'by_setter' => 'getBySetter',
            'is_standard' => 'isStandard',
            'standard' => 'isStandard',
            '__get' => '__get',
            'get_trait_property' => 'getTraitProperty',
            'trait_property' => 'isTraitProperty',
            'is_trait_property' => 'isTraitProperty',
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent' => 'parent',
            'public' => 'public',
            'overrode_public' => 'setOverrodePublic',
            'trait' => 'trait',
            'set_parent_property' => 'setParentProperty',
            'parent_property' => 'setParentProperty',
            'set_by_setter' => 'setBySetter',
            'by_setter' => 'setBySetter',
            'set_overrode_public' => 'setOverrodePublic',
            '__set' => '__set',
            'set_trait_property' => 'setTraitProperty',
            'trait_property' => 'setTraitProperty',
        ], $gettablePathMap);
    }

    public function testGettablePaths(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEqualsCanonicalizing([
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
        ], $classAnalyzer->getGettableNames());
    }

    public function testSettablePaths(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEqualsCanonicalizing([
            'public',
            'parent',
            'trait',
            'setBySetter',
            'setParentProperty',
            'setTraitProperty',
            '__set',
        ], $classAnalyzer->getSettableNames());
    }
}
