<?php

namespace App\Models;

use App\Enums\OtpEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'expired_at',
        "verified",
        'user_id'
    ];

     /**
     * create  an opt code
     *  @param  [int] length  the size of otpcode
     */
    public static function createOtpCode(int $length = 5){
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * create opt for the emails validation
     *  @param  [User] user_id
     * @param  [int] expired_at
     */
    public function createOtpEmailsValidation(User $user_id, $expired_at = 24) : Otp {

        return $this->create([
            'type' => OtpEnums::EMAIL_VALIDATION,
            'code' => $this->createOtpCode(),
            'user_id' => $user_id,
            'expired_at' => Carbon::now()->addHours($expired_at)
        ]);
    }

    public static  function findByUserAndType(User $user_id, OtpEnums $type) : Otp {

        return Otp::whereUserId($user_id)
            ->where('expired_at', '>', now())
            ->where('type', $type)->first();
    }
    
    public function setVerified($bool = null) : void {
        $this->verified = $bool ? $bool : true;
    }


}
