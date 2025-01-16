<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->getToken();
        return [
            'token' => [
                'access_token' => $token->plainTextToken,
                'token'=> $token
            ],
            'user' => UserResource::make($this)
        ];
    }
}
