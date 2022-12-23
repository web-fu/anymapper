<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use function WebFu\Internal\camelcase_to_underscore;

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

    /** @return Element[] */
    public function getOutputTrackList(): array
    {
        return $this->getTrackList();
    }

    /** @return Element[] */
    public function getInputTrackList(): array
    {
        return $this->getTrackList();
    }

    /** @return Element[] */
    private function getTrackList(): array
    {
        $trackList = [];
        foreach (array_keys($this->data) as $key) {
            $underscoreName = camelcase_to_underscore((string) $key);
            $keyType = is_int($key) ? ElementSource::NUMERIC_INDEX : ElementSource::STRING_INDEX;
            $trackList[$underscoreName] = new Element($key, $keyType);
        }

        return $trackList;
    }
}
