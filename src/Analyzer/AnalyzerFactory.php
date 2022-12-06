<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

class AnalyzerFactory
{
    public static function create(array|object $subject): AnalyzerInterface
    {
        if (is_object($subject)) {
            return new ClassAnalyzer($subject);
        }

        return new ArrayAnalyzer($subject);
    }
}
