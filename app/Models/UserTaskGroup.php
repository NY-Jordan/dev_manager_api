<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTaskGroup extends Model
{
    use HasFactory;
    protected $fillable = [ 
        "user_id",
        "task_group_id",
        "project_id" 
    ];
}
