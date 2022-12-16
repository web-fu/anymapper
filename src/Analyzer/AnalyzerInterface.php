<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /** @return string[] */
    public function getOutputTrackList(): array;

    /** @return string[] */
    public function getInputTrackList(): array;

    /** @return string[] */
    public function getGettableNames(): array;

    /** @return string[] */
    public function getSettableNames(): array;
}
