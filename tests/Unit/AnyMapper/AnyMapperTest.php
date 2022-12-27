<?php

declare(strict_types=1);

namespace WebFu\Tests\Unit\AnyMapper;

use PHPUnit\Framework\TestCase;
use WebFu\AnyMapper\AnyMapper;
use WebFu\Tests\Fake\FakeEntity;
use DateTime;

class AnyMapperTest extends TestCase
{
    public function testMapInto(): void
    {
        $class = new FakeEntity();

        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->into($class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testMapAs(): void
    {
        $class = (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->as(FakeEntity::class);

        $this->assertInstanceOf(FakeEntity::class, $class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testAllowDataCasting(): void
    {
        $class = new class() {
            public DateTime $public;
            private DateTime $private;

            /**
             * @return DateTime
             */
            public function getPrivate(): DateTime
            {
                return $this->private;
            }

            /**
             * @param DateTime $private
             */
            public function setPrivate(DateTime $private): void
            {
                $this->private = $private;
            }
        };

        $source = [
            'public' => '2022-12-01',
            'private' => '2022-12-31',
        ];

        (new \WebFu\AnyMapper\AnyMapper())
            ->map($source)
            ->allowDataCasting([
                'string' => DateTime::class,
            ])
            ->into($class);

        $this->assertEquals(new DateTime('2022-12-01 00:00:00'), $class->public);
        $this->assertEquals(new DateTime('2022-12-31 00:00:00'), $class->getPrivate());
    }
}
