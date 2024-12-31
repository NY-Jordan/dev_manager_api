<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyTasksResource extends JsonResource
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
            'taskgroup_id' => $this->taskgroup_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'breifing' => $this->breifing,
            'details' => $this->details,
            'reminder' => $this->reminder,
            'phase' =>TaskPhaseRessource::make($this->resource->taskPhase),
            'type' => TaskTypeRessource::make($this->resource->taskType),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
