<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskGroup\AttachUserToTaskGroupRequest;
use App\Http\Requests\TaskGroup\CreateTaskGroupRequest;
use App\Http\Requests\TaskGroup\DetachUserFromTaskGroupRequest;
use App\Http\Requests\TaskGroup\UpdateTaskGroupNameRequest;
use App\Http\Requests\TaskGroup\UpdateTaskGroupStatusRequest;
use App\Http\Resources\Task\TaskGroupResources;
use App\Models\Project;
use App\Models\projectUser;
use App\Models\TaskGroup;
use App\Service\TaskGroupService;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskGroupController extends Controller
{
    public function __construct(
        private TaskGroupService $taskGroupService,
        private UserService $userService
    ){

    }

    public function create(CreateTaskGroupRequest $request){
        $project = Project::findOrFail($request->project_id);
        if ($request->user()->cannot('create', $project)) {
            abort(403);
        }
        $taskGroup = $project->groupTask()->create($request->validated());
        return TaskGroupResources::make($taskGroup);
    }


    public function updateName(UpdateTaskGroupNameRequest $request, $id){
        $taskGroup = TaskGroup::findOrFail($id);
        $project = Project::findOrFail($taskGroup->project_id);
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }
        $taskGroup->setNameAttribute($request->name);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function updateStatus(UpdateTaskGroupStatusRequest $request, $id){
        $taskGroup = TaskGroup::findOrFail($id);
        $project = Project::findOrFail($taskGroup->project_id);
        if ($request->user()->cannot('update', $project)) {
            abort(403); 
        }
        $taskGroup->setStatusAttribute($request->status);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function delete(Request $request, $id){
        TaskGroup::findOrFail($id)->delete();
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function getByProject($project_id){
        $tasksGroup = TaskGroup::where('project_id', $project_id);
        return response()->json(['task group' => $tasksGroup, 'status' => true], 200);
    }


    public function attachUserToTaskGroup(AttachUserToTaskGroupRequest $request){
        $project = Project::findOrFail($request->project_id);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($project->id, $request->user_id), 403, ' Action unauthorized');
        $userTaskGroup = $this->taskGroupService->attachUserFromTaskGroup($request->task_group_id, $project->user_id);
        abort_if(!$userTaskGroup, 400, 'user is already attach on this task group');
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }

    
    public function detachUserFromTaskGroup(DetachUserFromTaskGroupRequest $request){
        $project = Project::findOrFail($request->project_id);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($project->id, $request->user_id), 403, ' Action unauthorized');
        $userTaskGroup = $this->taskGroupService->detachUserFromTaskGroup($request->task_group_id, $project->user_id);
        abort_if(!$userTaskGroup, 400, 'user is already dettached on this task group');
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }
}
