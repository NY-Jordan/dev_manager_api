<?php


namespace App\Service;

use App\Enums\StatusEnum;
use App\Enums\TaskPhaseEnum;
use App\Enums\TaskTypeEnum;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectInvitaion;
use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\TaskPhase;
use App\Models\TaskType;
use App\Models\TaskUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService {
    /**
     *
     *
     * @param int $projectId
     * @param int $taskId
     * @param int[] $users
     *
     * @return void
     */
    public static function assignTask( Task $task, array $users)  {
        TaskUser::whereTaskId($task->id)->delete();
        foreach ($users as $key => $user) {
            TaskUser::create([
                'task_id' => $task->id,
                'user_id' => $user,
                'schedule_at' => now()
            ]);
        }
    }

    function createTask(array $data)  {
        $typeId = TaskType::where('name', TaskTypeEnum::ASSIGN)->first()->id;
        $data['phase'] = TaskPhase::where('name', TaskPhaseEnum::BACKLOG)->first()->id;
        $data['user_id'] = Auth::id();
        $data['type'] = $typeId;
        $data['status_id'] = StatusEnum::STATUS_ACTIVE;
        $task = Task::create($data);
        return $task;
    }

    public function fetchTasks(int $projectId , null|int $userId = null, int|null $taskGroupId = null, int|null $phaseId = null, string|null $assignedDate = null ){
        $project = Project::find($projectId);
        $taskGroups = $project->tasksGroup;
        // filter by task group
        if ($taskGroupId) {
            $taskGroups = $project->tasksGroup->where('id', $taskGroupId);
        }
        $allTasks = collect();
        foreach ($taskGroups as $taskGroup) {
            $tasks = $taskGroup->tasks;
            // filter by user (collaborator)
            if ($userId !== null) {
                $tasks = $this->filterByUser($tasks , $userId);
            }
             // filter by phase
            if ($phaseId !== null) {
                $tasks = $this->filterByPhase($tasks , $phaseId);
            }
            // filter by assigned date
            if ($assignedDate !== null) {
                $tasks = $tasks->filter(function ($task) use ($assignedDate): mixed{
                    return (new Carbon($task->taskUser->first()->schedule_at))->day === (new Carbon($assignedDate))->day;
                })->all();
            }

            $allTasks = $allTasks->merge($tasks);
        }

        return $allTasks->sortBy('created_at');
    }

    private function filterByUser (Collection $tasks, int $userId) {
        return  $tasks->filter(function ($task) use ($userId) {
            if ($task->taskUser) {
                foreach ($task->taskUser as $key => $taskUser) {
                    if ($taskUser->user_id === $userId) {
                        return true;
                    }
                }
                return false;
            }
        });
    }

    private function filterByPhase (Collection $tasks, int $phaseId) {
        return  $tasks->filter(function ($task) use ($phaseId) {
            if ($task->phase === $phaseId) {
                return true;
            }
            return false;
        });
    }

    public function rescheduleUserTask(string $date, int $taskId) {
        $usersTasks = TaskUser::where('task_id', $taskId)->get(); // Collection

        foreach ($usersTasks as $task) {
            $task->schedule_at =  new Carbon($date);
            $task->save();
        }

        return $usersTasks;
    }

    public function rescheduleDailyTask(string $date, int $taskId) {
        $task = Task::findOrFail($taskId); // Collection
        $task->created_at = new Carbon($date);;
        $task->updated_at = new Carbon($date);;
        $task->save();

        return $task->refresh();
    }

}





