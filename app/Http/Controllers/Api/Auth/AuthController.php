<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\AuthEnums;
use App\Enums\OtpEnums;
use App\Events\EmailVerificationEvent;
use App\Events\Register;
use App\Events\RegisterEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\User\UserTokenResource;
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
    public function register(RegisterRequest $request) : UserTokenResource
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


    /**
    * login user
    * @param  [string] password
    * @param  [email] email
    * @return [string] message
    */
    public function login(LoginRequest $request)  : UserTokenResource {
        $findUserCredentials = User::findByPasswordAndEmail($request->email, $request->password);
        abort_if(!$findUserCredentials, 400, 'Bad Credentials');
        abort_if(!$findUserCredentials->email_verified_at, 400, 'email not verified');
        return UserTokenResource::make($findUserCredentials);
    }


    /**
    * email user verifiation
    * @param  [string] code
    */
    public  function emailVerification(EmailVerificationRequest $request) : UserTokenResource{
        $otp = Otp::findByUserAndType(Auth::user()->id, OtpEnums::EMAIL_VALIDATION);
        $otpIsRight =  $this->otpService->check($otp,$request->code);
        abort_if(!$otpIsRight, 400, 'otp is not valid');
        $user = User::find(Auth::id());
        $user->setEmailVerifiedAt();
        $user->setStatus(true);
        $otp->setVerified();
        $otp->setExpiredAt();
        return UserTokenResource::make($user);
    }


    
}
