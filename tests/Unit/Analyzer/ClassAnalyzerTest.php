<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Element;
use WebFu\Analyzer\ElementSource;
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
            'isPropertyTrue',
            'getParentProperty',
            'getTraitProperty',
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
            'parent' => new Element('parent', ElementSource::PROPERTY),
            'public' => new Element('public', ElementSource::PROPERTY),
            'trait' => new Element('trait', ElementSource::PROPERTY),
            'parent_property' => new Element('getParentProperty', ElementSource::METHOD),
            'property_true' => new Element('isPropertyTrue', ElementSource::METHOD),
            '__get' => new Element('__get', ElementSource::METHOD),
            'trait_property' => new Element('getTraitProperty', ElementSource::METHOD),
            'by_constructor' => new Element('getByConstructor', ElementSource::METHOD),
            'by_setter' => new Element('getBySetter', ElementSource::METHOD),
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent' => new Element('parent', ElementSource::PROPERTY),
            'public' => new Element('public', ElementSource::PROPERTY),
            'trait' => new Element('trait', ElementSource::PROPERTY),
            'parent_property' => new Element('setParentProperty', ElementSource::METHOD),
            'by_setter' => new Element('setBySetter', ElementSource::METHOD),
            '__set' => new Element('__set', ElementSource::METHOD),
            'trait_property' => new Element('setTraitProperty', ElementSource::METHOD),
        ], $gettablePathMap);
    }
}
