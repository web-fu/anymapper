<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\Tests\Unit\Analyzer;

use PHPUnit\Framework\TestCase;
use stdClass;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\Reflection\ReflectionType;
use WebFu\Tests\Fixtures\ChildClass;

/**
 * @coversNothing
 */
class ClassAnalyzerTest extends TestCase
{
    public function testGetConstructor(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);
        $constructor   = $classAnalyzer->getConstructor();

        $this->assertNotNull($constructor);
    }

    public function testGetConstructorReturnNull(): void
    {
        $classAnalyzer = new ClassAnalyzer(stdClass::class);
        $this->assertNull($classAnalyzer->getConstructor());
    }

    public function testGetGetters(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);
        $getters       = $classAnalyzer->getGetters();

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
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);
        $setters       = $classAnalyzer->getSetters();

        $this->assertEqualsCanonicalizing([
            'setBySetter',
            'setParentProperty',
            'setTraitProperty',
            '__set',
        ], array_keys($setters));
    }

    public function testGetProperties(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);
        $properties    = $classAnalyzer->getProperties();

        $this->assertEqualsCanonicalizing([
            'public',
            'parent',
            'trait',
        ], array_keys($properties));
    }

    public function testGetGenerators(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);
        $generators    = $classAnalyzer->getGenerators();

        $this->assertEqualsCanonicalizing([
            'createStatic',
            'createSelf',
            'create',
        ], array_keys($generators));
    }

    public function testGetOutputTrackList(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getOutputTrackList();

        $this->assertEquals([
            'parent'          => new Track('parent', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'public'          => new Track('public', TrackType::PROPERTY, new ReflectionType(['string'])),
            'trait'           => new Track('trait', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'parent_property' => new Track('getParentProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'property_true'   => new Track('isPropertyTrue', TrackType::METHOD, new ReflectionType(['bool'])),
            '__get'           => new Track('__get', TrackType::METHOD, new ReflectionType(['mixed'])),
            'trait_property'  => new Track('getTraitProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'by_constructor'  => new Track('getByConstructor', TrackType::METHOD, new ReflectionType(['string'])),
            'by_setter'       => new Track('getBySetter', TrackType::METHOD, new ReflectionType(['string'])),
        ], $gettablePathMap);
    }

    public function testGetInputTrackList(): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getInputTrackList();

        $this->assertEquals([
            'parent'          => new Track('parent', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'public'          => new Track('public', TrackType::PROPERTY, new ReflectionType(['string'])),
            'trait'           => new Track('trait', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'parent_property' => new Track('setParentProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'by_setter'       => new Track('setBySetter', TrackType::METHOD, new ReflectionType(['string'])),
            '__set'           => new Track('__set', TrackType::METHOD, new ReflectionType(['mixed'])),
            'trait_property'  => new Track('setTraitProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
        ], $gettablePathMap);
    }

    /**
     * @dataProvider outputTrackProvider
     */
    public function testGetOutputTrack(Track|null $expected, string $track): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEquals($expected, $classAnalyzer->getOutputTrack($track));
    }

    /**
     * @return iterable<mixed>
     */
    public function outputTrackProvider(): iterable
    {
        yield 'parent' => [
            'expected' => new Track('parent', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'track'    => 'parent',
        ];
        yield 'public' => [
            'expected' => new Track('public', TrackType::PROPERTY, new ReflectionType(['string'])),
            'track'    => 'public',
        ];
        yield 'trait' => [
            'expected' => new Track('trait', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'track'    => 'trait',
        ];
        yield 'parent_property' => [
            'expected' => new Track('getParentProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'track'    => 'parent_property',
        ];
        yield 'property_true' => [
            'expected' => new Track('isPropertyTrue', TrackType::METHOD, new ReflectionType(['bool'])),
            'track'    => 'property_true',
        ];
        yield '__get' => [
            'expected' => new Track('__get', TrackType::METHOD, new ReflectionType(['mixed'])),
            'track'    => '__get',
        ];
        yield 'trait_property' => [
            'expected' => new Track('getTraitProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'track'    => 'trait_property',
        ];
        yield 'by_constructor' => [
            'expected' => new Track('getByConstructor', TrackType::METHOD, new ReflectionType(['string'])),
            'track'    => 'by_constructor',
        ];
        yield 'by_setter' => [
            'expected' => new Track('getBySetter', TrackType::METHOD, new ReflectionType(['string'])),
            'track'    => 'by_setter',
        ];
        yield 'null' => [
            'expected' => null,
            'track'    => 'does_not_exists',
        ];
    }

    /**
     * @dataProvider inputTrackProvider
     */
    public function testGetInputTrack(Track|null $expected, string $track): void
    {
        $class         = new ChildClass();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEquals($expected, $classAnalyzer->getInputTrack($track));
    }

    /**
     * @return iterable<mixed>
     */
    public function inputTrackProvider(): iterable
    {
        yield 'parent' => [
            'element' => new Track('parent', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'path'    => 'parent',
        ];
        yield 'public' => [
            'element' => new Track('public', TrackType::PROPERTY, new ReflectionType(['string'])),
            'path'    => 'public',
        ];
        yield 'trait' => [
            'element' => new Track('trait', TrackType::PROPERTY, new ReflectionType(['mixed'])),
            'path'    => 'trait',
        ];
        yield 'parent_property' => [
            'element' => new Track('setParentProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'path'    => 'parent_property',
        ];
        yield 'by_setter' => [
            'element' => new Track('setBySetter', TrackType::METHOD, new ReflectionType(['string'])),
            'path'    => 'by_setter',
        ];
        yield '__set' => [
            'element' => new Track('__set', TrackType::METHOD, new ReflectionType(['mixed'])),
            'path'    => '__set',
        ];
        yield 'trait_property' => [
            'element' => new Track('setTraitProperty', TrackType::METHOD, new ReflectionType(['mixed'])),
            'path'    => 'trait_property',
        ];
        yield 'null' => [
            'expected' => null,
            'track'    => 'does_not_exists',
        ];
    }
}
