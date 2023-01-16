<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use WebFu\Resolver\TypeResolver;
use function WebFu\Internal\camelcase_to_underscore;

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
            $typeResolver = new TypeResolver($value);
            $this->trackList[$underscoreName] = new Track($key, $source, $typeResolver->resolve());
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
