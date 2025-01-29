<?php

namespace App\Http\Resources;

use App\Enums\TaskPhaseEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksGroupsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "status" => $this->resource->status,
            'backlog' => $this->resource->getTasks()->count(),
            'started' => $this->resource->getTasks(TaskPhaseEnum::STARTED)->count(),
            'in_review' => $this->resource->getTasks(TaskPhaseEnum::IN_REVIEW)->count(),
            'done' => $this->resource->getTasks(TaskPhaseEnum::DONE)->count(),
            "project_id" => $this->resource->project_id,
            "created_at" => $this->resource->created_at,
            "updated_at" => $this->resource->updated_at
        ];
    }
}
