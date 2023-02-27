<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\Reflection;

use WebFu\Reflection\ExtendedReflectionClass;
use PHPUnit\Framework\TestCase;
use WebFu\Tests\Fixture\EntityWithAnnotation;
use WebFu\Tests\Fixture\Foo;
use DateTime;

class ExtendedReflectionClassTest extends TestCase
{
    public function testGetDocTags(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            '@internal',
            '@template F of Foo',
        ], $reflectionClass->getDocTags());
    }

    public function testGetTemplates(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            [
                'class' => Foo::class,
                'as' => 'F',
            ],
        ], $reflectionClass->getTemplates());
    }

    public function testGetUseStatements(): void
    {
        $reflectionClass = new ExtendedReflectionClass(EntityWithAnnotation::class);
        $this->assertEquals([
            [
                'class' => DateTime::class,
                'as' => 'DT',
            ],
        ], $reflectionClass->getUseStatements());
    }
}
