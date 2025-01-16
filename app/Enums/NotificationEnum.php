<?php

namespace App\Enums;

enum NotificationEnum: string
{
    case INVITATION = "INVITATION";
    case INVITATION_CONFIRMATION = "INVITATION_CONFIRMATION";
    case INVITATION_ACCEPTED = "INVITATION_ACCEPTED";
    case INVITATION_REFUSED = "INVITATION_REFUSED";
    case MESSAGE = "MESSAGE";

    public static function invitationTypes(): array {
        return [
            self::INVITATION,
            self::INVITATION_CONFIRMATION,
            self::INVITATION_ACCEPTED,
            self::INVITATION_REFUSED,
        ];
    }
}
