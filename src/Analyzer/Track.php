<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class Track
{
    /** @param string[] $dataTypes */
    public function __construct(
        private string|int $name,
        private TrackType  $source,
        private array      $dataTypes,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    public function getSource(): TrackType
    {
        return $this->source;
    }

    /**
     * @return string[]
     */
    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }
}
