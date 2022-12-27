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
            'parent' => new Element('parent', ElementSource::PROPERTY, ['mixed']),
            'public' => new Element('public', ElementSource::PROPERTY, ['string']),
            'trait' => new Element('trait', ElementSource::PROPERTY, ['mixed']),
            'parent_property' => new Element('getParentProperty', ElementSource::METHOD, ['mixed']),
            'property_true' => new Element('isPropertyTrue', ElementSource::METHOD, ['bool']),
            '__get' => new Element('__get', ElementSource::METHOD, ['mixed']),
            'trait_property' => new Element('getTraitProperty', ElementSource::METHOD, ['mixed']),
            'by_constructor' => new Element('getByConstructor', ElementSource::METHOD, ['string']),
            'by_setter' => new Element('getBySetter', ElementSource::METHOD, ['string']),
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent' => new Element('parent', ElementSource::PROPERTY, ['mixed']),
            'public' => new Element('public', ElementSource::PROPERTY, ['string']),
            'trait' => new Element('trait', ElementSource::PROPERTY, ['mixed']),
            'parent_property' => new Element('setParentProperty', ElementSource::METHOD, ['mixed']),
            'by_setter' => new Element('setBySetter', ElementSource::METHOD, ['string']),
            '__set' => new Element('__set', ElementSource::METHOD, ['mixed']),
            'trait_property' => new Element('setTraitProperty', ElementSource::METHOD, ['mixed']),
        ], $gettablePathMap);
    }

    /**
     * @dataProvider outputTrackProvider
     */
    public function testGetOutputTrack(Element|null $expected, string $track): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEquals($expected, $classAnalyzer->getOutputTrack($track));
    }

    /**
     * @return iterable<mixed>
     */
    public function outputTrackProvider(): iterable
    {
        yield 'parent' => [
            'expected' => new Element('parent', ElementSource::PROPERTY, ['mixed']),
            'track' => 'parent',
        ];
        yield 'public' => [
            'expected' => new Element('public', ElementSource::PROPERTY, ['string']),
            'track' => 'public',
        ];
        yield 'trait' => [
            'expected' => new Element('trait', ElementSource::PROPERTY, ['mixed']),
            'track' => 'trait',
        ];
        yield 'parent_property' => [
            'expected' => new Element('getParentProperty', ElementSource::METHOD, ['mixed']),
            'track' => 'parent_property',
        ];
        yield 'property_true' => [
            'expected' => new Element('isPropertyTrue', ElementSource::METHOD, ['bool']),
            'track' => 'property_true',
        ];
        yield '__get' => [
            'expected' => new Element('__get', ElementSource::METHOD, ['mixed']),
            'track' => '__get',
        ];
        yield 'trait_property' => [
            'expected' => new Element('getTraitProperty', ElementSource::METHOD, ['mixed']),
            'track' => 'trait_property',
        ];
        yield 'by_constructor' => [
            'expected' => new Element('getByConstructor', ElementSource::METHOD, ['string']),
            'track' => 'by_constructor',
        ];
        yield 'by_setter' => [
            'expected' => new Element('getBySetter', ElementSource::METHOD, ['string']),
            'track' => 'by_setter',
        ];
        yield 'null' => [
            'expected' => null,
            'track' => 'does_not_exists',
        ];
    }

    /**
     * @dataProvider inputTrackProvider
     */
    public function testGetInputTrack(Element|null $expected, string $track): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEquals($expected, $classAnalyzer->getInputTrack($track));
    }

    /**
     * @return iterable<mixed>
     */
    public function inputTrackProvider(): iterable
    {
        yield 'parent' => [
            'element' => new Element('parent', ElementSource::PROPERTY, ['mixed']),
            'path' => 'parent',
        ];
        yield 'public' => [
            'element' => new Element('public', ElementSource::PROPERTY, ['string']),
            'path' => 'public',
        ];
        yield 'trait' => [
            'element' => new Element('trait', ElementSource::PROPERTY, ['mixed']),
            'path' => 'trait',
        ];
        yield 'parent_property' => [
            'element' => new Element('setParentProperty', ElementSource::METHOD, ['mixed']),
            'path' => 'parent_property',
        ];
        yield 'by_setter' => [
            'element' => new Element('setBySetter', ElementSource::METHOD, ['string']),
            'path' => 'by_setter',
        ];
        yield '__set' => [
            'element' => new Element('__set', ElementSource::METHOD, ['mixed']),
            'path' => '__set',
        ];
        yield 'trait_property' => [
            'element' => new Element('setTraitProperty', ElementSource::METHOD, ['mixed']),
            'path' => 'trait_property',
        ];
        yield 'null' => [
            'expected' => null,
            'track' => 'does_not_exists',
        ];
    }
}
