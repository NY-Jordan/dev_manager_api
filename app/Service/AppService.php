<?php

namespace App\Service;

use App\Enums\TaskTypeEnum;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskUser;
use App\Models\User;

class AppService {

    /**
     * register a new user
     */
    public function getStatistics()  {

        $projects = Project::where('user_id', auth()->id())
            ->orWhereHas('userProject', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        $tasks = Task::where('user_id', auth()->id())
        ->orWhereHas('taskUser', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();


        return [
            'projects' => str_pad($projects->count(), 2, '0', STR_PAD_LEFT),
            'tasks' => str_pad($tasks->count(), 2, '0', STR_PAD_LEFT),
            'links' => '00',
            'notes' => '00',
        ];
    }

    function getUserTasksTracking()  {
        $tasks = collect([]);

        $ownTasks = Task::where('user_id', auth()->id())
        ->whereType(TaskType::whereName(TaskTypeEnum::OWN)->first()->id)
        ->get();

        $assignTasks = TaskUser::whereUserId( auth()->id())->get();

        foreach ($ownTasks as $key => $task) {
            $tasks->push($task);
        }

        foreach ($assignTasks as $key => $userTask) {
            $task = $userTask->task;
            $task->updated_at = $userTask->schedule_at;
            $tasks->push($task);
        }

        $tasks =  $tasks->groupBy(fn($task) => $task->updated_at->format('Y-m-d'));

        $tasks = $tasks->map(fn( $group) => $group->map(fn($task) => TaskResource::make($task)));;

        return $tasks;

    }

    public function getUserCollaborators()  {

        $projects = Project::where('user_id', auth()->id())
            ->orWhereHas('userProject', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        $collaborators = collect([]);

        foreach ($projects as $key => $project) {
            $collaborators = $collaborators->merge($project->getCollaborators())
            ->unique('user.id')
            ->reject(fn($item) => $item->user->id === auth()->id());
        }

        return $collaborators;
    }
}
