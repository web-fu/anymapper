<?php

declare(strict_types=1);

namespace WebFu\Tests\Benchmark\AnyMapper;

use WebFu\AnyMapper\AnyMapper;
use WebFu\AnyMapper\Strategy\CallbackCastingStrategy;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\Tests\Fixture\ChildClass;
use WebFu\Tests\Fixture\EntityWithAnnotation;

class AnyMapperBench
{
    /**
     * @Revs(1000)
     */
    public function benchMapAs(): void
    {
        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])
            ->as(ChildClass::class)
            ->run();
    }

    /**
     * @Revs(1000)
     */
    public function benchMapInto(): void
    {
        $class = new ChildClass();

        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])
            ->into($class)
            ->run();
    }

    /**
     * @Revs(1000)
     */
    public function benchSerialize(): void
    {
        $class = new class () {
            public string $public = 'public';
            private string $value;

            public function __construct()
            {
                $this->value = 'construct';
            }

            public function getValue(): string
            {
                return $this->value;
            }

            public function getClass(): object
            {
                return new class () {
                    public string $element = 'element';
                };
            }

            /**
             * @return string[]
             */
            public function getArray(): array
            {
                return [
                    'foo',
                    'bar',
                ];
            }
        };

        (new AnyMapper())
            ->map($class)
            ->serialize();
    }

    /**
     * @Revs(1000)
     */
    public function benchDocBlockStrategy(): void
    {
        /** @var EntityWithAnnotation $class */
        (new AnyMapper())->map([
            'foo' => 1,
        ])->using(new DocBlockDetectStrategy())
            ->as(EntityWithAnnotation::class)
            ->run();
    }

    /**
     * @Revs(1000)
     */
    public function benchCallbackCastingStrategy(): void
    {
        $class = new class () {
            public int $value;
        };

        (new AnyMapper())
            ->map([
                'value' => true,
            ])
            ->using(
                (new CallbackCastingStrategy())
                    ->addMethod('bool', 'int', static fn (bool $value): int => (int) $value)
            )
            ->into($class)
            ->run();
    }
}
