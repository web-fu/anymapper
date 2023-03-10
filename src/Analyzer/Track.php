<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use WebFu\Reflection\ReflectionTypeExtended;

class Track
{
    /**
     * @param TrackType::* $source
     */
    public function __construct(
        private string|int $name,
        private string $source,
        private ReflectionTypeExtended $dataTypes,
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

    public function getDataTypes(): ReflectionTypeExtended
    {
        return $this->dataTypes;
    }
}
