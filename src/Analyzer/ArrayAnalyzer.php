<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use function WebFu\Internal\camelcase_to_underscore;

class ArrayAnalyzer implements AnalyzerInterface
{
    /**
     * @var Element[]
     */
    private array $trackList = [];

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $underscoreName = camelcase_to_underscore((string) $key);
            $source = is_int($key) ? ElementSource::NUMERIC_INDEX : ElementSource::STRING_INDEX;
            $this->trackList[$underscoreName] = new Element($key, $source, [gettype($value)]);
        }
    }

    /** @return Element[] */
    public function getOutputTrackList(): array
    {
        return $this->trackList;
    }

    /** @return Element[] */
    public function getInputTrackList(): array
    {
        return $this->trackList;
    }

    public function getOutputTrack(string $track): Element|null
    {
        return $this->trackList[$track] ?? null;
    }

    public function getInputTrack(string $track): Element|null
    {
        return $this->trackList[$track] ?? null;
    }
}
