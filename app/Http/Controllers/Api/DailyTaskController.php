<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\rescheduleUsersTaskRequest;
use App\Http\Requests\Task\CreateDailyTaskRequest;
use App\Http\Requests\Task\UpdateDailyTaskPhaseRequest;
use App\Http\Requests\Task\UpdateDailyTaskRequest;
use App\Http\Resources\Task\DailyTasksResource;
use App\Models\Task;
use App\Models\TaskPhase;
use App\Service\TaskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DailyTaskController extends Controller
{
    public function __construct(private TaskService $taskService) {

    }
    public function create(CreateDailyTaskRequest $request, Task $task)  {
        $newTask = $task->createDailyTask($request->title, $request->breifing);
        return response()->json(['message' => "operation successfully", 'task' => DailyTasksResource::make($newTask)], 201);
    }

    public function fetch(Request $request, Task $task)  {

        return DailyTasksResource::collection( $task->fetchDailyTasks($request->search, $request->date));
    }


    public function update(UpdateDailyTaskRequest $request, $id)  {
        $task  = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true, 'task' =>   DailyTasksResource::make($task)], 200);
    }

    public function updatePhase(UpdateDailyTaskPhaseRequest $request, $id)  {
        $phase = TaskPhase::findOrFail($request->phaseId);
        $task  = Task::findOrFail($id);
        $task->setPhase($request->phaseId);
        return response()->json(['message' => "operation successfully", 'status' => true, 'task' =>   DailyTasksResource::make($task)], 200);
    }


    public function rescheduleDailyTask(rescheduleUsersTaskRequest $request,  $taskId)  {
        $task = Task::findOrFail($taskId);
        abort_if(!($task->user_id === auth()->id()), 403, 'this task is not yours');
        $date  = strval($request->date);
        $task  = $this->taskService->rescheduleDailyTask( $date, $taskId);
        return response()->json(['message' => "operation successfully", 'task' => DailyTasksResource::make($task),'status' => true], 200);
    }


}
