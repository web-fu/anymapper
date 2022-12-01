<?php

declare(strict_types=1);

namespace WebFu\Mapper;

class ArrayAnalyzer implements AnalyzerInterface
{
    private array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getGettablePaths(): array
    {
        return array_keys($this->data);
    }

    public function getSettablePaths(): array
    {
        return array_keys($this->data);
    }
}
