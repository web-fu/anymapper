<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class Element
{
    /** @param string[] $dataTypes */
    public function __construct(
        private readonly string|int    $name,
        private readonly ElementSource $source,
        private readonly array $dataTypes,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    public function getSource(): ElementSource
    {
        return $this->source;
    }

    /**
     * @return string[]
     */
    public function getDataType(): array
    {
        return $this->dataTypes;
    }
}
