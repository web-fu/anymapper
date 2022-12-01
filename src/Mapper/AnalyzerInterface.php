<?php

declare(strict_types=1);

namespace WebFu\Mapper;

interface AnalyzerInterface
{
    public function getGettablePaths(): array;
    public function getSettablePaths(): array;
}