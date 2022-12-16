<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class ArrayAnalyzer implements AnalyzerInterface
{
    /**
     * @var mixed[]
     */
    private array $data;

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getGettableNames(): array
    {
        return array_keys($this->data);
    }

    public function getSettableNames(): array
    {
        return array_keys($this->data);
    }

    /** @return string[] */
    public function getOutputTrackList(): array
    {
        return array_combine($this->getGettableNames(), $this->getGettableNames());
    }

    /** @return string[] */
    public function getInputTrackList(): array
    {
        return array_combine($this->getSettableNames(), $this->getSettableNames());
    }

    public function getSettableMethod(string $path): \ReflectionMethod|null
    {
        if (!array_key_exists($path, $this->data)) {
            return null;
        }

        return new \ReflectionMethod($this, 'setIndex');
    }
}
