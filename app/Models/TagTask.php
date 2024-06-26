<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagTask extends Model
{
    use HasFactory;

    public function getByTask($taskId){
        return $this->where('task_id', $taskId)->get();
    }
}
