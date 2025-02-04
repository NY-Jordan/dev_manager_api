<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'user_id',
        'schedule_at'
    ];

    public function task(){
        return $this->belongsTo(Task::class, );
    }

    public function users(){
        return $this->belongsTo(User::class);
    }


}
