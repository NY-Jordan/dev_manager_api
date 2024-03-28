<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\AuthEnums;
use App\Enums\OtpEnums;
use App\Events\EmailVerificationEvent;
use App\Events\Register;
use App\Events\RegisterEvent;
use App\Exceptions\HttpResponse_if;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserTokenResource;
use App\Jobs\EmailVerificationJob;
use App\Models\Otp;
use App\Models\User;
use App\Service\AuthService;
use App\Service\OtpService;
use Illuminate\Http\Exceptions\HttpResponseException;
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
    * @return  UserTokenResource
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
    * @return UserTokenResource
    */
    public function login(LoginRequest $request)  : UserTokenResource {
        $findUserCredentials = User::findByPasswordAndEmail($request->email, $request->password);
        if(!$findUserCredentials){
            throw new HttpResponseException(response()->json([
                'errors' => 'Bad Credentials',
                'status' => false
                ], 400));
        }
        if(!$findUserCredentials->email_verified_at){
            throw new HttpResponseException(response()->json([
                'errors' => 'email not verified',
                'status' => false
                ], 400));
        }
        return UserTokenResource::make($findUserCredentials);
    }


    /**
    * email user verifiation
    * @param  [string] code
    */
    public  function emailVerification(EmailVerificationRequest $request) : UserTokenResource{
        $otp = Otp::findByUserAndType(Auth::user()->id, OtpEnums::EMAIL_VALIDATION);
        $otpIsRight =  $this->otpService->check($otp,$request->code);
        if(!$otpIsRight){
            throw new HttpResponseException(response()->json([
                'errors' => 'otp is not valid',
                'status' => false
                ], 400));
        }
        $user = User::find(Auth::id());
        $user->setEmailVerifiedAt();
        $user->setStatus(true);
        $otp->setVerified();
        $otp->setExpiredAt();
        return UserTokenResource::make($user);
    }
   
}
