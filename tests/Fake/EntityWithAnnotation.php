<?php

declare(strict_types=1);

namespace WebFu\Tests\Fake;

use DateTime as DT;

/**
 * @internal
 * @template F of Foo
 */
class EntityWithAnnotation
{
    /** @var DT */
    private $DT;

    /** @var F */
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

    public function setDTValue($DT): void
    {
        $this->DT = $DT;
    }

    /**
     * @return F
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param F $foo
     */
    public function setFoo($foo): void
    {
        $this->foo = $foo;
    }
}
