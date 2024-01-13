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

class AnalyzerFactory
{
    /**
     * @param mixed[]|object $subject
     */
    public static function create(array|object $subject): AnalyzerInterface
    {
        if (is_object($subject)) {
            return new ClassAnalyzer($subject);
        }

        return new ArrayAnalyzer($subject);
    }
}
