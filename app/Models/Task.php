<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'taskgroup_id',
        'title',
        'breifing',
        'details',
        'status',
        'reminder',
        'phase'
    ];


    static function titleIsAlreadyUseInThisGroupTask(string $title, int $taskgroup_id) : bool {
        $task  = self::where('title', $title)->where('taskgroup_id', $taskgroup_id)->first();
        if ($task) {
            return true;
        }
        return false ;
    }
}
