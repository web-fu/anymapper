<?php

declare(strict_types=1);

namespace WebFu\Tests\Fake;

class FakeParentEntity
{
    public mixed $parent;
    private mixed $parentProperty;

    public function getParentProperty(): mixed
    {
        return $this->parentProperty;
    }

    public function setParentProperty(mixed $parentProperty): void
    {
        $this->parentProperty = $parentProperty;
    }
}
