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

class TrackType
{
    public const PROPERTY      = 'property';
    public const METHOD        = 'method';
    public const NUMERIC_INDEX = 'numeric index';
    public const STRING_INDEX  = 'string index';
}
