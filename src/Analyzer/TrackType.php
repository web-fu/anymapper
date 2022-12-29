<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

enum TrackType
{
    case PROPERTY;
    case METHOD;
    case NUMERIC_INDEX;
    case STRING_INDEX;
}
