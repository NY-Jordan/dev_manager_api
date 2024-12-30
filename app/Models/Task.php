<?php

namespace App\Models;

use App\Enums\TaskPhaseEnum;
use App\Enums\TaskTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'phase',
        'user_id',
        'type'
    ];

    public function taskType(){
        return $this->belongsTo(TaskType::class, 'type');
    }
    public function taskGroup(){
        return $this->belongsTo(related: TaskGroup::class, );
    }

    public function createDailyTask(string $title, string $breifing) : Task {
        $phaseId = TaskPhase::where('name', TaskPhaseEnum::STARTED)->first()->id;
        $typeId = TaskType::where('name', TaskTypeEnum::OWN)->first()->id;

        $task = $this->create([
            'title' => $title,
            'breifing' => $breifing,
            'type' => $typeId,
            'phase' =>$phaseId,
            'user_id' => Auth::id()
        ]);
        return $task;
    }

    function fetchDailyTasks()  :array|Collection  {
        $typeId = TaskType::where('name', TaskTypeEnum::OWN)->first()->id;

        return $this->where('type', $typeId)
        ->where('user_id', Auth::id())
        ->get();
    }

    static function titleIsAlreadyUseInTheCurrentGroupTask(string $title, int $taskgroup_id) : bool {
        $task  = self::where('title', $title)->where('taskgroup_id', $taskgroup_id)->first();
        if ($task) {
            return true;
        }
        return false ;
    }
}
