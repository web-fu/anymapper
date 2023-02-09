<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use function WebFu\Internal\camelcase_to_underscore;
use function WebFu\Internal\get_type;

class ArrayAnalyzer implements AnalyzerInterface
{
    /**
     * @var Track[]
     */
    private array $trackList = [];

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $underscoreName = camelcase_to_underscore((string) $key);
            $source = is_int($key) ? TrackType::NUMERIC_INDEX : TrackType::STRING_INDEX;
            $this->trackList[$underscoreName] = new Track($key, $source, [get_type($value)]);
        }
    }

    /** @return Track[] */
    public function getOutputTrackList(): array
    {
        return $this->trackList;
    }

    /** @return Track[] */
    public function getInputTrackList(): array
    {
        return $this->trackList;
    }

    public function getOutputTrack(string $track): Track|null
    {
        return $this->trackList[$track] ?? null;
    }

    public function getInputTrack(string $track): Track|null
    {
        return $this->trackList[$track] ?? null;
    }
}
