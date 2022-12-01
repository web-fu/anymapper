<?php

declare(strict_types=1);

namespace WebFu\Mapper;

interface AnalyzerInterface
{
    public function getGettablePaths(): array;
    public function getGettablePath(string $path): \Reflector;
    public function getSettablePaths(): array;
    public function getSettablePath(string $path): \Reflector;
}