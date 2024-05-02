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

use WebFu\Reflection\ReflectionType;

class Track
{
    /**
     * @param TrackType::* $source
     */
    public function __construct(
        private string|int $name,
        private string $source,
        private ReflectionType $dataTypes,
    ) {
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    /**
     * @return TrackType::*
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public function getDataTypes(): ReflectionType
    {
        return $this->dataTypes;
    }
}
