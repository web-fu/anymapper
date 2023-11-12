<?php

declare(strict_types=1);

namespace WebFu\Tests\Integration\Analyzer;

use PHPUnit\Framework\TestCase;
use WebFu\Analyzer\ClassAnalyzer;
use WebFu\Analyzer\Track;
use WebFu\Analyzer\TrackType;
use WebFu\Reflection\ReflectionTypeExtended;
use WebFu\Tests\Fixtures\EntityWithAnnotation;
use DateTime;
use WebFu\Tests\Fixtures\Foo;

class ClassAnalyzerTest extends TestCase
{
    public function testGetInputTrack(): void
    {
        $classAnalyzer = new ClassAnalyzer(EntityWithAnnotation::class);
        $inputTrack = $classAnalyzer->getInputTrack('d_t_value');

        $this->assertEquals(new Track('setDTValue', TrackType::METHOD, new ReflectionTypeExtended(['mixed'], [DateTime::class])), $inputTrack);

        $inputTrack = $classAnalyzer->getInputTrack('foo');

        $this->assertEquals(new Track('setFoo', TrackType::METHOD, new ReflectionTypeExtended(['mixed'], [Foo::class])), $inputTrack);
    }
}
