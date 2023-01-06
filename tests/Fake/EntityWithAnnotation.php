<?php

namespace WebFu\Tests\Fake;

/**
 * @internal
 * @template F of FakeEntity
 */
class EntityWithAnnotation
{
    /**
     * @var string[]
     */
    public array $array = [
        'foo',
        'bar',
    ];

    public int|string|null $unionType;

    /**
     * @return string[]
     */
    public function getArray(int $param): array {
        return [
            'foo',
            'bar',
        ];
    }

    /**
     * @param string[] $parameter
     */
    public function parameter(array $parameter): void
    {
    }

    /**
     * @return array<F>
     */
    public function getFakeEntities(): array
    {
        return [new FakeEntity()];
    }
}
