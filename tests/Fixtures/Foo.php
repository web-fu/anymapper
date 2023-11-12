<?php

declare(strict_types=1);

namespace WebFu\Tests\Fixtures;

class Foo
{
    public function __construct(private int $i = -1)
    {
    }

    public function getValue(): int
    {
        return $this->i;
    }
}
