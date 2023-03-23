<?php

declare(strict_types=1);

namespace WebFu\Tests\Fixture;

class ClassWithOneParameter
{
    public function __construct(private string $param1)
    {
    }
}