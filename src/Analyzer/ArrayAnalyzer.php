<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use function WebFu\Mapper\camelcase_to_underscore;

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

    /** @return ElementAnalyzer[] */
    public function getOutputTrackList(): array
    {
        return $this->getTrackList();
    }

    /** @return ElementAnalyzer[] */
    public function getInputTrackList(): array
    {
        return $this->getTrackList();
    }

    /** @return ElementAnalyzer[] */
    private function getTrackList(): array
    {
        $trackList = [];
        foreach (array_keys($this->data) as $key) {
            $underscoreName = camelcase_to_underscore((string) $key);
            $keyType = is_int($key) ? ElementType::NUMERIC_INDEX : ElementType::STRING_INDEX;
            $trackList[$underscoreName] = new ElementAnalyzer($key, $keyType);
        }
        return $trackList;
    }
}
