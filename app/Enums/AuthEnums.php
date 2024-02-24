<?php

namespace App\Enums;

enum AuthEnums: string
{
    case OPT_INVALID = "otp is not valid";
    case BAD_CREDENTIALS = "Bad Credentials";
}