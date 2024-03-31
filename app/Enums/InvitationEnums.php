<?php

namespace App\Enums;

enum InvitationEnums: int | string
{
    case STATUS_PENDING = 1;
    case STATUS_REFUSED = 3;
    case STATUS_ACCEPTED = 2;
    case STATUS_CANCELED = 0;
    case TYPE_RECEIVER = 'receiver';
    case TYPE_SENDER = 'sender';
}