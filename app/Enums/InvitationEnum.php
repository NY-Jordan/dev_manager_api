<?php

namespace App\Enums;

enum InvitationEnum: string
{
    case INVITATION = "INVITATION";
    case CONFIRMATION = "CONFIRMATION";
    case MESSAGE = "MESSAGE";
}