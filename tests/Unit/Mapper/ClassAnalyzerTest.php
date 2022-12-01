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

    public function gettablePathProvider(): \Iterator
    {
        yield ['overrodePublic'];
        yield ['public'];
        yield ['parent'];
        yield ['trait'];
        yield ['getByConstructor'];
        yield ['getBySetter'];
        yield ['isStandard'];
        yield ['getParentProperty'];
        yield ['isParentProperty'];
        yield ['getTraitProperty'];
        yield ['isTraitProperty'];
        yield ['__get'];
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

    public function settablePathProvider(): \Iterator
    {
        yield ['overrodePublic'];
        yield ['public'];
        yield ['parent'];
        yield ['trait'];
        yield ['setBySetter'];
        yield ['setOverrodePublic'];
        yield ['setParentProperty'];
        yield ['setTraitProperty'];
        yield ['__set'];
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
