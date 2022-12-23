<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

enum ElementSource
{
    case PROPERTY;
    case METHOD;
    case NUMERIC_INDEX;
    case STRING_INDEX;
}
