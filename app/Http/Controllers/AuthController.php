<?php

namespace App\Http\Controllers;

use App\Enums\OtpEnums;
use App\Events\EmailVerificationEvent;
use App\Events\Register;
use App\Events\RegisterEvent;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTokenResource;
use App\Jobs\EmailVerificationJob;
use App\Models\Otp;
use App\Models\User;
use App\Service\AuthService;
use App\Service\OtpService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function __construct(
        private AuthService $authService,
        private OtpService $otpService
        )
    {
        
    }

    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] picture
    * @return [string] message
    */
    public function register(RegisterRequest $request)
    {
        $user  = $this->authService->register(
            $request->name, 
            $request->email, 
            $request->picture,
            $request->password, 
        );
        event(new RegisterEvent($user));
        return UserTokenResource::make($user);
        
    }

    function login() {
        return;
    }


    public  function email_verification(EmailVerificationRequest $request){
        $otp = Otp::findByUserAndType(Auth::user()->id, OtpEnums::EMAIL_VALIDATION);
        $otpIsRight =  $this->otpService->check($otp,$request->code);
        abort_if(!$otpIsRight, 400, 'otp is not valid');
        $user = User::find(Auth::id());
        $user->setEmailVerifiedAt();
        $otp->setVerified();
        return UserResource::make($user);
    }


}
