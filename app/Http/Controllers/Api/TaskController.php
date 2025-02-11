<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusEnum;
use App\Enums\TaskPhaseEnum;
use App\Enums\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\rescheduleUsersTaskRequest;
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
use App\Models\User;
use App\Service\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    const ITEMS_PER_PAGE = 10;
    const DEFAULT_PAGE = 1;
    public function __construct(private TaskService $taskService ){}
    public function fetchTasks(Request $request, $projectId) {
        $project  = Project::findOrFail($projectId);

        // filter parameters
        $collaboratorId = isset($request->user_id) && is_numeric($request->user_id) ? intval($request->user_id) : null;
        $taskGroupId = isset($request->task_group_id) && is_numeric($request->task_group_id) ? intval($request->task_group_id) : null;
        $phaseId = isset($request->phase_id) && is_numeric($request->phase_id) ? intval($request->phase_id) : null;

        // paginations parametser
        $page = $request->get('page', self::DEFAULT_PAGE);
        $perPage = $request->get('per_page', self::ITEMS_PER_PAGE);

        $tasks = $this->taskService->fetchTasks(
            projectId : $project->id,
            userId : $collaboratorId,
            phaseId : $phaseId,
            taskGroupId : $taskGroupId
        );

        $paginatedTasks = $tasks->forPage($page, $perPage);

        $totalItems = $tasks->count();
        $totalPages = ceil($totalItems / $perPage);

        return response()->json([
            'tasks' => TaskResource::collection($paginatedTasks),
            'pagination' => [
                'current_page' => intVal($page),
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'per_page' => $perPage
            ],
            'status' => true
        ], 200);
    }



    public function create(TaskRequest $request)  {
        abort_if(!TaskGroup::find($request->task_group_id), 404, 'task group not found');
        abort_if(Task::titleIsAlreadyUseInTheCurrentGroupTask($request->title,$request->task_group_id), 422, 'Title already use in this Task Group');
        $task  = $this->taskService->createTask($request->all());
        return response()->json([
            'message' => "Operation successfully",
            'task' => TaskResource::make($task),
            'status' => true
        ], 200);    }


    function fetchCollaboratorTasks(int $projectId, int $userId)   {
        abort_if(!Project::find($projectId), 404, 'project not found');
        abort_if(!User::find($userId), 404, 'user not found');
        $tasks = $this->taskService->fetchTasks(projectId : $projectId, userId : $userId)
        ->groupBy(fn($task) => $task->taskPhase->name);

        return response()->json([
            'status' => true,
            'tasks' => $tasks,
        ], 200);

    }
    public function update(UpdateTaskRequest $request, $id)  {
        if ($request->title) {
            abort_if(Task::titleIsAlreadyUseInTheCurrentGroupTask($request->title,(int)$request->taskgroup_id), 422, 'Title already use in this Task Group');
        }
        $task  = Task::findOrFail($id);
        $task->update($request->all());
        $task->refresh();
        return response()->json(['message' => "operation successfully", 'task' => TaskResource::make($task),'status' => true], 200);
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


    public function rescheduleUsersTask(rescheduleUsersTaskRequest $request, $projectId, $taskId)  {
        $project = Project::findOrFail($projectId);
        $task = Task::findOrFail($taskId);
        abort_if(!$project->isTheAdministrator(Auth::id(), $project->id), 403, 'You are not authorized');
        abort_if(!$task->taskBelongsToProject($project->id), 403, 'Task not found in this project');
        $date  = strval($request->date);
        $this->taskService->rescheduleUserTask( $date, $taskId);
        $task->refresh();
        return response()->json(['message' => "operation successfully", 'task' => TaskResource::make($task),'status' => true], 200);
    }



}
