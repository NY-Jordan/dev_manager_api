<?php

namespace App\Http\Controllers;

use App\Events\EmailVerificationEvent;
use App\Events\Register;
use App\Events\RegisterEvent;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserTokenResource;
use App\Jobs\EmailVerificationJob;
use App\Service\AuthService;

class AuthController extends Controller
{
    function __construct(
        private AuthService $authService
        )
    {
        
    }
    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
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
}
