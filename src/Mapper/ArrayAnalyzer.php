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

    public function getGettablePath(string $path): ?\Reflector
    {
        if (!array_keys($this->data)) {
            return null;
        }
        return new \ReflectionMethod($this, 'getIndex');
    }

    public function getSettablePaths(): array
    {
        return array_keys($this->data);
    }

    public function getSettablePath(string $path): ?\Reflector
    {
        if (!array_keys($this->data)) {
            return null;
        }
        return new \ReflectionMethod($this, 'setIndex');
    }

    public function getIndex(string $key): mixed
    {
        return $this->data[$key];
    }

    public function setIndex(string $key, mixed $value)
    {
        $this->data[$key] = $value;
    }
}
