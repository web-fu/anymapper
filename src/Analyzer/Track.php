<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class Track
{
    /**
     * @param TrackType::* $source
     * @param DataType::*[] $dataTypes
     */
    public function __construct(
        private string|int $name,
        private string $source,
        private array $dataTypes,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    /**
     * @return TrackType::*
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return DataType::*[]
     */
    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }
}
