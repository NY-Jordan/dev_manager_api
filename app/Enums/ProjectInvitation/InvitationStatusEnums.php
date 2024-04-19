<?php

namespace App\Enums\ProjectInvitation;

enum InvitationStatusEnums: int
{
    case STATUS_PENDING = 1;
    case STATUS_REFUSED = 3;
    case STATUS_ACCEPTED = 2;
    case STATUS_CANCELED = 0;

    static function statusIsPending($status) : bool {
        return InvitationStatusEnums::STATUS_PENDING->value === $status;
    }

    static function statusIsRefused($status) : bool {
        return InvitationStatusEnums::STATUS_REFUSED->value === $status;
    }

    static function statusIsAccepted($status) : bool {
        return InvitationStatusEnums::STATUS_ACCEPTED->value === $status;
    }

    static function statusIsCanceled($status) : bool {
        return InvitationStatusEnums::STATUS_CANCELED->value === $status;
    }
}