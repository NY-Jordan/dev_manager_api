<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserTokenResource;
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
        
        
        return UserTokenResource::make($user);
        
    }
}
