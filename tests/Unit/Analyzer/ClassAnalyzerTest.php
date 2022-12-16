<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\ElementAnalyzer;
use WebFu\Analyzer\ElementType;
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
            'parent' => new ElementAnalyzer('parent', ElementType::PROPERTY),
            'public' => new ElementAnalyzer('public', ElementType::PROPERTY),
            'trait' => new ElementAnalyzer('trait', ElementType::PROPERTY),
            'get_parent_property' => new ElementAnalyzer('getParentProperty', ElementType::METHOD),
            'is_parent_property' => new ElementAnalyzer('isParentProperty', ElementType::METHOD),
            'is_standard' => new ElementAnalyzer('isStandard', ElementType::METHOD),
            '__get' => new ElementAnalyzer('__get', ElementType::METHOD),
            'get_trait_property' => new ElementAnalyzer('getTraitProperty', ElementType::METHOD),
            'is_trait_property' => new ElementAnalyzer('isTraitProperty', ElementType::METHOD),
            'get_by_constructor' => new ElementAnalyzer('getByConstructor', ElementType::METHOD),
            'get_by_setter' => new ElementAnalyzer('getBySetter', ElementType::METHOD),
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent' => new ElementAnalyzer('parent', ElementType::PROPERTY),
            'public' => new ElementAnalyzer('public', ElementType::PROPERTY),
            'trait' => new ElementAnalyzer('trait', ElementType::PROPERTY),
            'set_parent_property' => new ElementAnalyzer('setParentProperty', ElementType::METHOD),
            'set_by_setter' => new ElementAnalyzer('setBySetter', ElementType::METHOD),
            '__set' => new ElementAnalyzer('__set', ElementType::METHOD),
            'set_trait_property' => new ElementAnalyzer('setTraitProperty', ElementType::METHOD),
        ], $gettablePathMap);
    }
}
