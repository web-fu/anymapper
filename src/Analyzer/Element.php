<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class Element
{
    public function __construct(
        private readonly string|int    $name,
        private readonly ElementSource $type,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    public function getType(): ElementSource
    {
        return $this->type;
    }
}
