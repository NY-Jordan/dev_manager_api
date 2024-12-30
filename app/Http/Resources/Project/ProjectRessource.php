<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'logo' => $this->resource->logo,
            "user_id" => $this->resource->user_id,
            "collaborators" => $this->resource->getCollaborators()->count(),
            'is_admin' => $this->resource->isTheAdministrator(),
            'tasks' => $this->resource->getTasks()->count(),
            "delivery_at" => $this->resource->delivery_at,
            "created_at" => $this->resource->created_at,
            "updated_at" => $this->resource->updated_at,

        ];
    }
}
