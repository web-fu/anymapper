<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\Tests\Fixture\FakeEntity;
use stdClass;

class ClassAnalyzerTest extends TestCase
{
    public function testGetConstructor(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $constructor = $classAnalyzer->getConstructor();

        $this->assertNotNull($constructor);
    }

    public function testGetConstructorReturnNull(): void
    {
        $classAnalyzer = new ClassAnalyzer(stdClass::class);
        $this->assertNull($classAnalyzer->getConstructor());
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
            'parent' => new Track('parent', TrackType::PROPERTY, ['mixed']),
            'public' => new Track('public', TrackType::PROPERTY, ['string']),
            'trait' => new Track('trait', TrackType::PROPERTY, ['mixed']),
            'parent_property' => new Track('getParentProperty', TrackType::METHOD, ['mixed']),
            'property_true' => new Track('isPropertyTrue', TrackType::METHOD, ['bool']),
            '__get' => new Track('__get', TrackType::METHOD, ['mixed']),
            'trait_property' => new Track('getTraitProperty', TrackType::METHOD, ['mixed']),
            'by_constructor' => new Track('getByConstructor', TrackType::METHOD, ['string']),
            'by_setter' => new Track('getBySetter', TrackType::METHOD, ['string']),
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent' => new Track('parent', TrackType::PROPERTY, ['mixed']),
            'public' => new Track('public', TrackType::PROPERTY, ['string']),
            'trait' => new Track('trait', TrackType::PROPERTY, ['mixed']),
            'parent_property' => new Track('setParentProperty', TrackType::METHOD, ['mixed']),
            'by_setter' => new Track('setBySetter', TrackType::METHOD, ['string']),
            '__set' => new Track('__set', TrackType::METHOD, ['mixed']),
            'trait_property' => new Track('setTraitProperty', TrackType::METHOD, ['mixed']),
        ], $gettablePathMap);
    }

    /**
     * @dataProvider outputTrackProvider
     */
    public function testGetOutputTrack(Track|null $expected, string $track): void
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
            'expected' => new Track('parent', TrackType::PROPERTY, ['mixed']),
            'track' => 'parent',
        ];
        yield 'public' => [
            'expected' => new Track('public', TrackType::PROPERTY, ['string']),
            'track' => 'public',
        ];
        yield 'trait' => [
            'expected' => new Track('trait', TrackType::PROPERTY, ['mixed']),
            'track' => 'trait',
        ];
        yield 'parent_property' => [
            'expected' => new Track('getParentProperty', TrackType::METHOD, ['mixed']),
            'track' => 'parent_property',
        ];
        yield 'property_true' => [
            'expected' => new Track('isPropertyTrue', TrackType::METHOD, ['bool']),
            'track' => 'property_true',
        ];
        yield '__get' => [
            'expected' => new Track('__get', TrackType::METHOD, ['mixed']),
            'track' => '__get',
        ];
        yield 'trait_property' => [
            'expected' => new Track('getTraitProperty', TrackType::METHOD, ['mixed']),
            'track' => 'trait_property',
        ];
        yield 'by_constructor' => [
            'expected' => new Track('getByConstructor', TrackType::METHOD, ['string']),
            'track' => 'by_constructor',
        ];
        yield 'by_setter' => [
            'expected' => new Track('getBySetter', TrackType::METHOD, ['string']),
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
    public function testGetInputTrack(Track|null $expected, string $track): void
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
            'element' => new Track('parent', TrackType::PROPERTY, ['mixed']),
            'path' => 'parent',
        ];
        yield 'public' => [
            'element' => new Track('public', TrackType::PROPERTY, ['string']),
            'path' => 'public',
        ];
        yield 'trait' => [
            'element' => new Track('trait', TrackType::PROPERTY, ['mixed']),
            'path' => 'trait',
        ];
        yield 'parent_property' => [
            'element' => new Track('setParentProperty', TrackType::METHOD, ['mixed']),
            'path' => 'parent_property',
        ];
        yield 'by_setter' => [
            'element' => new Track('setBySetter', TrackType::METHOD, ['string']),
            'path' => 'by_setter',
        ];
        yield '__set' => [
            'element' => new Track('__set', TrackType::METHOD, ['mixed']),
            'path' => '__set',
        ];
        yield 'trait_property' => [
            'element' => new Track('setTraitProperty', TrackType::METHOD, ['mixed']),
            'path' => 'trait_property',
        ];
        yield 'null' => [
            'expected' => null,
            'track' => 'does_not_exists',
        ];
    }
}
