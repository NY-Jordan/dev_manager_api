<?php

namespace App\Models;

use App\Enums\TaskPhaseEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','status', 'project_id'
    ] ;


    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }



    public function tasks(){
        return $this->hasMany(Task::class, );
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->save();
    }



    public function setStatus($status)
    {
        $this->status = $status;
        $this->save();
    }

    function getTasks(TaskPhaseEnum|null  $phase  = null)  {
        if (!$phase) {
            return $this->tasks();
        }
        return $this->tasks()->where('phase', TaskPhase::findByName($phase->value)?->id);
    }
}
