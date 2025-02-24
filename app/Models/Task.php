<?php

namespace App\Models;

use App\Enums\StatusEnum;
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
        'task_group_id',
        'title',
        'breifing',
        'details',
        'status',
        'reminder',
        'phase',
        'user_id',
        'type',
        'status_id'
    ];

    public function taskType(){
        return $this->belongsTo(TaskType::class, 'type');
    }

    public function ticket(){
        return $this->hasOne(Ticket::class, 'task_id');
    }
    public function taskUser(){
        return $this->hasMany(TaskUser::class, 'task_id');
    }

    public function taskPhase(){
        return $this->belongsTo(TaskPhase::class, 'phase');
    }
    public function taskGroup(){
        return $this->belongsTo(related: TaskGroup::class, );
    }

    public function getUsersTask() {
        $taskUsers = TaskUser::where('task_id', $this->id)->get();
        $result = collect([]);
        foreach ($taskUsers as $key => $taskUser) {
            $user = User::find($taskUser->user_id);
            if ($user) {
                $user->schedule_at = $taskUser->schedule_at;
                $result[] = $user;
            }

        }
        return $result;
    }

    public function createDailyTask(string $title, string $breifing) : Task {
        $phaseId = TaskPhase::where('name', TaskPhaseEnum::STARTED)->first()->id;
        $typeId = TaskType::where('name', TaskTypeEnum::OWN)->first()->id;

        $task = $this->create([
            'title' => $title,
            'breifing' => $breifing,
            'type' => $typeId,
            'phase' =>$phaseId,
            'user_id' => Auth::id(),
            'status_id' => StatusEnum::STATUS_ACTIVE
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

    static function titleIsAlreadyUseInTheCurrentGroupTask(string $title, int $task_group_id) : bool {
        $task  = self::where('title', $title)->where('task_group_id', $task_group_id)->first();
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

    function taskBelongsToProject($projectId) : bool {
        $project =   Project::findOrFail($projectId);
        if ($this->taskGroup) {
            $projectFound = $this->taskGroup->project;
            if ($projectFound->id === $projectId) {
                return true;
            }
            return false;
        }
        return false;
    }
}
