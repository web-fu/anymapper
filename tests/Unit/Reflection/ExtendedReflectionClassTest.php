<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Reflection;

use WebFu\Reflection\ExtendedReflectionClass;
use PHPUnit\Framework\TestCase;
use WebFu\Tests\Fake\EntityWithAnnotation;
use WebFu\Tests\Fake\FakeEntity;

class ExtendedReflectionClassTest extends TestCase
{
    public function testGetDocTags(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            '@internal',
            '@template F of FakeEntity',
        ], $reflectionClass->getDocTags());
    }

    public function testGetTenplates(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            'F' => FakeEntity::class
        ], $reflectionClass->getTemplates());
    }

    public function testGetUseStatements(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            [
                'class' => 'DateTime',
                'as' => 'DT',
            ],
        ], $reflectionClass->getUseStatements());
    }
}
