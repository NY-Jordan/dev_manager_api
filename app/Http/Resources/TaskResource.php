<?php

namespace App\Http\Resources;

use App\Http\Resources\Task\TaskPhaseRessource;
use App\Http\Resources\Task\TaskTypeRessource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'taskgroup_id' => $this->task_group_id,
            'user_id' => $this->user_id,
            'assigned_user' => $this->getUsersTask(),
            'title' => $this->title,
            'breifing' => $this->breifing,
            'details' => $this->details,
            'reminder' => $this->reminder,
            'task_phase' =>TaskPhaseRessource::make($this->resource->taskPhase),
            'type' => TaskTypeRessource::make($this->resource->taskType),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
