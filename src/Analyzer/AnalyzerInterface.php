<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /** @return ElementAnalyzer[] */
    public function getOutputTrackList(): array;

    /** @return ElementAnalyzer[] */
    public function getInputTrackList(): array;

    /** @return string[] */
    public function getGettableNames(): array;

    /** @return string[] */
    public function getSettableNames(): array;
}
