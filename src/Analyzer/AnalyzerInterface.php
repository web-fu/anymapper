<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

interface AnalyzerInterface
{
    /** @return string[] */
    public function getGettableNames(): array;
    public function getGettableMethod(string $path): \ReflectionMethod|null;
    /** @return string[] */
    public function getSettableNames(): array;
    public function getSettableMethod(string $path): \ReflectionMethod|null;
}
