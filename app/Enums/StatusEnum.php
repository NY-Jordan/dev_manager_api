<?php

namespace App\Enums;

enum StatusEnum: INT
{
    case STATUS_ACTIVE = 2;
    case STATUS_INACTIVE = 1;
    case STATUS_DELETE = 3;
}
