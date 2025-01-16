<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
use App\Enums\TaskPhaseEnum;
use App\Enums\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDailyTaskRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\Task\assignTaskRequest;
use App\Http\Requests\Task\TaskFileRequest;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskGroup;
use App\Models\TaskPhase;
use App\Models\TaskType;
use App\Service\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService ){}
    public function fetchTasks(Request $request, $projectId)  {
        $project  = Project::findOrFail($projectId);
        $collaboratorId = $request->user_id ? intval($request->user_id) : null;
        $tasks = $project->getTasks(userId :$collaboratorId)->forPage(1, 6);
        return response()->json(['message' => "operation successfully", 'tasks' => TaskResource::collection($tasks),'status' => true], 200);
    }


    public function create(TaskRequest $request)  {
        abort_if(!TaskGroup::find($request->task_group_id), 404, 'task group not found');
        abort_if(Task::titleIsAlreadyUseInTheCurrentGroupTask($request->title,$request->task_group_id), 422, 'Title already use in this Task Group');
        $typeId = TaskType::where('name', TaskTypeEnum::ASSIGN)->first()->id;
        $data = $request->all();
        $data['phase'] = TaskPhase::where('name', TaskPhaseEnum::BACKLOG)->first()->id;
        $data['user_id'] = Auth::id();
        $data['type'] = $typeId;
        $data['status_id'] = StatusEnum::STATUS_ACTIVE;
        $task = Task::create($data);
        return response()->json(['message' => "operation successfully", 'task' => TaskResource::make($task),'status' => true], 201);
    }


    public function update(UpdateTaskRequest $request, $id)  {
        if ($request->title) {
            abort_if(Task::titleIsAlreadyUseInTheCurrentGroupTask($request->title,$request->taskgroup_id), 422, 'Title already use in this Task Group');
        }
        $task  = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }

    public function delete(Request $request, $id)  {
        $task  = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => "operation successfully", 'task' => TaskResource::make($task), 'status' => true], 200);
    }

    public function attatchFileToTask(TaskFileRequest $request)  {
        abort_if(!TaskGroup::find($request->task_id), 404, 'task  not found');
        TaskFile::create($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true], 201);
    }

    public function detachFileToTask(Request $request, $id)  {
        TaskFile::findOrFail( $id)->delete();
        return response()->json(['message' => "operation successfully", 'status' => true], 201);
    }

    public function updateFileTask(TaskFileRequest $request, $id)  {
        $file  = TaskFile::findOrFail($id);
        $file->update($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true], 201);
    }

    public function getAllFilesTask($id)  {
        $task  = Task::findOrFail($id);
        $files =    TaskFile::where('task_id', $id)->get();
        return response()->json([
            'task' => $id,
            'files' => $files,
            'status' => true],
        201);
    }

    public function assignTask(assignTaskRequest $request, $projectId)  {
        $project = Project::findOrFail($projectId);
        $task = Task::findOrFail($request->task_id);
        abort_if(!$task->taskBelongsToProject($project->id), 403, 'Task not found in this project');
        $this->taskService->assignTask( $task, $request->users);
        $task->refresh();
        return response()->json(['message' => "operation successfully", 'task' => TaskResource::make($task),'status' => true], 200);
    }


}
