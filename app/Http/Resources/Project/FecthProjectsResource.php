<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FecthProjectsResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count' => $this->resource->count(),
            'projects' => ProjectRessource::collection($this->resource),
            'status' => true
        ];
    }
}
