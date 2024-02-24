<?php

namespace App\Models;

use App\Enums\OtpEnums;
use App\Service\OtpService;
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
    public function createOtp($user_id, $type, $expired_at = 24) : Otp {
        (new OtpService)->desactiveAllOtpWithTheSameType($type);
        return $this->create([
            'type' => $type,
            'code' => $this->createOtpCode(),
            'user_id' => $user_id,
            'expired_at' => Carbon::now()->addHours($expired_at)
        ]);
    }

    public static  function findByUserAndType($user_id, OtpEnums $type) : Otp|null {

        
        return Otp::whereUserId($user_id)
            ->where('expired_at', '>', now())
            ->where('type', $type)
            ->where('verified', null)
            ->first();
    }

    public function setVerified($bool = null) : void {
        $this->verified = $bool ? $bool : true;
        $this->save();
    }
    public function setExpiredAt($date = null) : void {
        $this->expired_at = $date ? $date : Carbon::now();
        $this->save();
    }


}