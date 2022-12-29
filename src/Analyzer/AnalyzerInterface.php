<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /** @return Track[] */
    public function getOutputTrackList(): array;

    /** @return Track[] */
    public function getInputTrackList(): array;

    public function getOutputTrack(string $track): Track|null;
    public function getInputTrack(string $track): Track|null;
}
