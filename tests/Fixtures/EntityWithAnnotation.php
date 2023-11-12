<?php

declare(strict_types=1);

namespace WebFu\Tests\Fixtures;

use DateTime as DT;

/**
 * @internal
 */
class EntityWithAnnotation
{
    /** @var DT */
    private $DT;

    /** @var Foo */
    private $foo;

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
    public function getStringArray(int $param): array
    {
        return [
            'foo',
            'bar',
        ];
    }

    /**
     * @param string[] $array
     * @return $this
     */
    public function setStringArray(array $array): self
    {
        return $this;
    }

    /**
     * @param string[] $parameter
     */
    public function parameter(array $parameter): void
    {
    }

    /**
     * @return DT
     */
    public function getDTValue()
    {
        return $this->DT;
    }

    /**
     * @param DT $DT
     */
    public function setDTValue($DT): void
    {
        $this->DT = $DT;
    }

    /**
     * @return Foo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param Foo $foo
     */
    public function setFoo($foo): void
    {
        $this->foo = $foo;
    }
}
