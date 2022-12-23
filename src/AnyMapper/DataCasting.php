<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

enum DataCasting
{
    case STRING_TO_INT;
    case STRING_TO_FLOAT;
    case STRING_TO_DATETIME;
    case INT_TO_STRING;
    case FLOAT_TO_STRING;
    case DATETIME_TO_STRING;
}
