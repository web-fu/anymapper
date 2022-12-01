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

    public function getGettableNames(): array
    {
        return array_keys($this->data);
    }

    public function getGettableMethod(string $path): ?\ReflectionMethod
    {
        if (!array_key_exists($path, $this->data)) {
            return null;
        }
        return new \ReflectionMethod($this, 'getIndex');
    }

    public function getSettableNames(): array
    {
        return array_keys($this->data);
    }

    public function getSettableMethod(string $path): ?\ReflectionMethod
    {
        if (!array_key_exists($path, $this->data)) {
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
