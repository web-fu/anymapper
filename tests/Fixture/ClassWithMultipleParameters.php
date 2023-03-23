<?php

declare(strict_types=1);

namespace WebFu\Tests\Fixture;

class ClassWithMultipleParameters
{
    public function __construct(private string $param1, private int $param2)
    {
    }
}
