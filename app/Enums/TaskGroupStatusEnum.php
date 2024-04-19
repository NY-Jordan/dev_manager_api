<?php

namespace App\Enums;

enum TaskGroupStatusEnum: int 
{
    case PUBLIC_STATUS = 1;
    case PRIVATE_STATUS = 2;
}