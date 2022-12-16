<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class ElementAnalyzer
{
    public function __construct(
        private readonly string      $name,
        private readonly ElementType $type,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ElementType
    {
        return $this->type;
    }
}
