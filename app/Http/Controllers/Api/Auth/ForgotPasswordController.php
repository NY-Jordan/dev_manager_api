<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\OtpEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\ForgotPassword\ForgotPasswordRessource;
use App\Http\Resources\ForgotPassword\PasswordResetRessource;
use App\Jobs\PasswordResetJob;
use App\Models\Otp;
use App\Models\User;
use App\Service\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function __construct(
        Private OtpService $otpService
    )
    {
        
    }

    public function forgotPassword(ForgotPasswordRequest $request){
        $user = User::where('email', $request->email)->where('status', true)->first();
        abort_if(!$user, 400, 'Email Not Found');
        dispatch(new PasswordResetJob($user));
        return PasswordResetRessource::make($user);
    }

    public function updatePassword(UpdatePasswordRequest $request){
        $otp = Otp::findByUserAndType(Auth::user()->id, OtpEnums::RESET_PASSWORD);
        $otpIsRight =  $this->otpService->check($otp,$request->code);
        abort_if(!$otpIsRight, 400, 'otp is not valid');
        $user = User::find(Auth::id());
        $user->setPassword($request->password);
        $otp->setVerified();
        $otp->setExpiredAt();
        return response()->json(['message' => "password updated sucessfully", 'status' => true], 200);
    }
}
