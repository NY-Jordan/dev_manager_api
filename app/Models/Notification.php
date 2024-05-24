<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'type', 'user_id', 'created_at', 'updated_at'];

    public  static function  createNotification(string $type, string $message, $user_id) {
        self::create([
            'message' => $message,
            'type' => $type,
            'user_id' => $user_id,
        ]);
    }
}
