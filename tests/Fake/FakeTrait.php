<?php

declare(strict_types=1);

namespace WebFu\Tests\Fake;

trait FakeTrait
{
    public mixed $trait;
    private mixed $traitProperty;

    public function getTraitProperty(): mixed
    {
        return $this->traitProperty;
    }

    public function setTraitProperty(mixed $traitProperty): void
    {
        $this->traitProperty = $traitProperty;
    }
}
