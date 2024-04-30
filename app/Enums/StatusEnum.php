<?php

namespace App\Enums;

enum StatusEnum: INT
{
    case STATUS_ACTIVE = 1;
    case STATUS_INACTIVE = 0;
    case STATUS_DELETE = 2;
}