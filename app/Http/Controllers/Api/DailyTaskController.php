<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateDailyTaskRequest;
use App\Http\Requests\Task\UpdateDailyTaskRequest;
use App\Http\Resources\Task\DailyTasksResource;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DailyTaskController extends Controller
{
    public function create(CreateDailyTaskRequest $request, Task $task)  {
        $newTask = $task->createDailyTask($request->title, $request->breifing);
        return response()->json(['message' => "operation successfully", 'task' => DailyTasksResource::make($newTask)], 201);
    }

    public function fetch( Task $task)  {

        return DailyTasksResource::collection( $task->fetchDailyTasks());
    }

    public function update(UpdateDailyTaskRequest $request, $id)  {
        $task  = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true, 'task' =>  $task], 200);
    }

}
