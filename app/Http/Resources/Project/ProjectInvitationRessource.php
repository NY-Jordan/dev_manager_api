<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\User\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectInvitationRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'sender' => UserResource::make(User::find($this->resource->sender)),
            'receiver' => UserResource::make(User::find($this->resource->receiver)),
            'project' => ProjectRessource::make(Project::find($this->resource->project_id)),
            'status' => $this->resource->status,
        ];
    }
}
