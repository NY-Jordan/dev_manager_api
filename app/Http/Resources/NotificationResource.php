<?php

namespace App\Http\Resources;

use App\Enums\NotificationEnum;
use App\Models\ProjectInvitaion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typeEnum = NotificationEnum::tryFrom($this->type);
        if ($typeEnum && in_array($typeEnum, NotificationEnum::invitationTypes())) {
            return [
                'id' => $this->id,
                'type' => $this->type,
                'user_id' => $this->user_id,
                'content' => InvitationResource::make(ProjectInvitaion::findByUuid($this->notifiable_contentt_id))
            ];
        }
        return [];
    }
}
