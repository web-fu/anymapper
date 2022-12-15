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

    public function testGetGettablePathMap(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getGettablePathMap();

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

    public function testGetSettablePathMap(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $gettablePathMap = $classAnalyzer->getSettablePathMap();

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
        ], $classAnalyzer->getGettableNames());
    }

    /**
     * @dataProvider gettablePathProvider
     */
    public function testGettablePath(string $path): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertInstanceOf(\Reflector::class, $classAnalyzer->getGettableMethod($path));
    }

    public function gettablePathProvider(): iterable
    {
        yield 'overrodePublic' => ['overrodePublic'];
        yield 'public' => ['public'];
        yield 'parent' => ['parent'];
        yield 'trait' => ['trait'];
        yield 'getByConstructor' => ['getByConstructor'];
        yield 'getBySetter' => ['getBySetter'];
        yield 'isStandard' => ['isStandard'];
        yield 'getParentProperty' => ['getParentProperty'];
        yield 'isParentProperty' => ['isParentProperty'];
        yield 'getTraitProperty' => ['getTraitProperty'];
        yield 'isTraitProperty' => ['isTraitProperty'];
        yield '__get' => ['__get'];
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
        ], $classAnalyzer->getSettableNames());
    }

    /**
     * @dataProvider settablePathProvider
     */
    public function testSettablePath(string $path): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertInstanceOf(\Reflector::class, $classAnalyzer->getSettableMethod($path));
    }

    public function settablePathProvider(): iterable
    {
        yield 'overrodePublic' => ['overrodePublic'];
        yield 'public' => ['public'];
        yield 'parent' => ['parent'];
        yield 'trait' => ['trait'];
        yield 'setBySetter' => ['setBySetter'];
        yield 'setOverrodePublic' => ['setOverrodePublic'];
        yield 'setParentProperty' => ['setParentProperty'];
        yield 'setTraitProperty' => ['setTraitProperty'];
        yield '__set' => ['__set'];
    }

    public function testGetPropertyValue(): void
    {
        $class = new FakeEntity();
        $class->public = 'public';
        $classAnalyzer = new ClassAnalyzer($class);

        $this->assertEquals('public', $classAnalyzer->getPropertyValue('public'));
    }

    public function testSetPropertyValue(): void
    {
        $class = new FakeEntity();
        $classAnalyzer = new ClassAnalyzer($class);
        $classAnalyzer->setPropertyValue('public', 'public');

        $this->assertEquals('public', $class->public);
    }
}
