<?php

namespace App\Models;

use App\Enums\TaskPhaseEnum;
use App\Enums\TaskTypeEnum;
use Carbon\Carbon;
use Date;
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

    public function taskPhase(){
        return $this->belongsTo(TaskPhase::class, 'phase');
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

    function fetchDailyTasks(null|string $search = null, null|string $date = null)  :array|Collection  {
        $typeId = TaskType::where('name', TaskTypeEnum::OWN)->first()->id;

        $builder =  $this->where('type', $typeId)
        ->where('user_id', Auth::id());

        if ($date) {
            $builder->whereDate('created_at', Carbon::parse($date)->toDateString());
        } else {
            $builder->whereDate('created_at', now()->toDateString());
        }

        if ($search) {
            $builder->where('title','like', "%$search%");
        }

        return $builder->get();
    }

    static function titleIsAlreadyUseInTheCurrentGroupTask(string $title, int $taskgroup_id) : bool {
        $task  = self::where('title', $title)->where('taskgroup_id', $taskgroup_id)->first();
        if ($task) {
            return true;
        }
        return false ;
    }

    public function setPhase($phaseId)
    {
        $this->phase = $phaseId;
        $this->save();
    }
}
