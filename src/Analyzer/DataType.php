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

class DataType
{
    public const BOOL     = 'bool';
    public const INT      = 'int';
    public const FLOAT    = 'float';
    public const STRING   = 'string';
    public const CALLABLE = 'callable';
    public const RESOURCE = 'resource';
    public const ARRAY    = 'array';
    public const OBJECT   = 'object';
}
