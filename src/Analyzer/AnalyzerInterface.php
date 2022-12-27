<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /** @return Element[] */
    public function getOutputTrackList(): array;

    /** @return Element[] */
    public function getInputTrackList(): array;

    public function getOutputTrack(string $track): Element|null;
    public function getInputTrack(string $track): Element|null;
}
