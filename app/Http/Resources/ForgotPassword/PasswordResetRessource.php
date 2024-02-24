<?php

namespace App\Http\Resources\ForgotPassword;

use App\Enums\TokenTypeEnums;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordResetRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->getToken(TokenTypeEnums::PASSWORD_RESET);
        return [
            'token' => [
                'access_token' => $token->plainTextToken
            ],
            'message' => 'Otp has been sent to the specific email'
        ];
    }
}
