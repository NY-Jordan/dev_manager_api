<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InviteUserOnProjectRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::find($this->resource->receiver);
        return [
            'uuid' => $this->resource->uuid,
            'user' => UserResource::make($user),
            'status' => true
        ];
    }
}