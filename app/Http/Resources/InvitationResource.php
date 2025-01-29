<?php

namespace App\Http\Resources;

use App\Http\Resources\Project\ProjectRessource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'project' => ProjectRessource::make($this->project),
            'receiver' => [
                'id' => $this->userReceivers->id,
                'name' => $this->userReceivers->name,
                'picture' => $this->userReceivers->picture,
                'email' => $this->userReceivers->email,
                'email_verified_at' => $this->userSenders->email_verified_at,
                'status_id' => $this->userReceivers->status_id,
                'google_id' => $this->userReceivers->google_id,
                'github_id' => $this->userReceivers->github_id,
                'created_at' => $this->userReceivers->created_at,
                'updated_at' => $this->userReceivers->updated_at,
                'is_admin' => $this->project->isTheAdministrator($this->userReceivers->id),
            ],
            'sender' => [
                'id' => $this->userSenders->id,
                'name' => $this->userSenders->name,
                'picture' => $this->userSenders->picture,
                'email' => $this->userSenders->email,
                'email_verified_at' => $this->userSenders->email_verified_at,
                'status_id' => $this->userSenders->status_id,
                'google_id' => $this->userSenders->google_id,
                'github_id' => $this->userSenders->github_id,
                'created_at' => $this->userSenders->created_at,
                'updated_at' => $this->userSenders->updated_at,
                'is_admin' => $this->project->isTheAdministrator($this->userSenders->id),
            ],
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
