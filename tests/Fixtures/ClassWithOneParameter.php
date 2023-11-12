<?php

declare(strict_types=1);

namespace WebFu\Tests\Fixtures;

class ClassWithOneParameter
{
    public function __construct(private string $param1)
    {
    }
}
