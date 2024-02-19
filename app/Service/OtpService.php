<?php

namespace App\Service;

use App\Enums\OtpEnums;
use App\Models\Otp;
use App\Models\User;
use Exception;

class OtpService {

    /**
     * verify if the opt code is right
     */
    public function check(Otp $otp, $code)  {
        if (!$otp || $otp->verified) {
          return false;
        }
        if ($otp->code === $code) {
            return true;
        }
        return false;
    }
}