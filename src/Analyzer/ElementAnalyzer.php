<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class ElementAnalyzer
{
    public function __construct(
        private readonly string|int $name,
        private readonly ElementType $type,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    public function getType(): ElementType
    {
        return $this->type;
    }
}
