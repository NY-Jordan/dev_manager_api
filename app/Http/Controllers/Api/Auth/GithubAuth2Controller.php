<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserTokenResource;
use App\Models\User;
use App\Service\AuthService;
use App\Service\OtpService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;


class GithubAuth2Controller extends Controller
{
    function __construct(
        private AuthService $authService,
        private OtpService $otpService
        )
    {

    }

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('github')
                         ->stateless()
                         ->redirect()
                         ->getTargetUrl(),
        ]);
    }


    public function handleAuthCallback()
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('github')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }


        /** @var User $user */
        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'email_verified_at' => now(),
                    'github_id' => $socialiteUser->getId(),
                    'name' =>  $socialiteUser->getNickname() ?? $socialiteUser->getName(),
                    'picture' => $socialiteUser->getAvatar(),
                ]
            );

            return UserTokenResource::make($user);
    }
}
