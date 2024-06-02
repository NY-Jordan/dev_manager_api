<?php

namespace App\Models;

use App\Enums\NotificationEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'user_id' ,'notifiable_contentt_id', 'created_at', 'updated_at', 'status'];

    public  static function  createNotification(NotificationEnum $type, $user_id, $contentId) {
        return self::create([
            'type' => $type,
            'user_id' => $user_id,
            'notifiable_contentt_id' => $contentId,
            'status' => StatusEnum::STATUS_ACTIVE
        ]);
    }
}
