<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /**
     * @return Track[]
     */
    public function getOutputTrackList(): array;

    /**
     * @return Track[]
     */
    public function getInputTrackList(): array;

    public function getOutputTrack(string $track): Track|null;

    public function getInputTrack(string $track): Track|null;
}
