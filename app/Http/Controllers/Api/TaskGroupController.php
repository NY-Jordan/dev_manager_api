<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskGroupStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskGroup\AttachUserToTaskGroupRequest;
use App\Http\Requests\TaskGroup\CreateTaskGroupRequest;
use App\Http\Requests\TaskGroup\DetachUserFromTaskGroupRequest;
use App\Http\Requests\TaskGroup\UpdateTaskGroupNameRequest;
use App\Http\Requests\TaskGroup\UpdateTaskGroupStatusRequest;
use App\Http\Resources\Task\TaskGroupResources;
use App\Http\Resources\TasksGroupsResource;
use App\Models\Project;
use App\Models\TaskGroup;
use App\Service\TaskGroupService;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskGroupController extends Controller
{
    const ITEMS_PER_PAGE = 5;

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

        TaskGroup::create($request->all());
        $tasksGroup = TaskGroup::where('project_id', $project->id)->paginate(SELF::ITEMS_PER_PAGE);

        return  response()->json(
            [
                'task_groups' => TasksGroupsResource::collection(($tasksGroup)),
                'pagination' => [
                    'total_pages' =>  $tasksGroup->lastPage(),
                    'total_items' => $tasksGroup->total(),
                    'current_page' => $tasksGroup->currentPage()
                ],
                'status' => true
            ], 200);
    }


    public function updateName(UpdateTaskGroupNameRequest $request, $id){
        $taskGroup = TaskGroup::findOrFail($id);
        $project = Project::findOrFail($taskGroup->project_id);
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }
        $taskGroup->setName($request->name);
        return TasksGroupsResource::make($taskGroup);
    }


    public function updateStatus(UpdateTaskGroupStatusRequest $request, $id){
        $taskGroup = TaskGroup::findOrFail($id);
        $project = Project::findOrFail($taskGroup->project_id);
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }
        abort_if(is_null(TaskGroupStatusEnum::tryFrom($request->status)), 404, 'status not found');
        $taskGroup->setStatus($request->status);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function delete(Request $request, $id){
        $taskGroup   = TaskGroup::findOrFail($id);
        $taskGroup->delete();
        return TasksGroupsResource::make($taskGroup);
    }


    public function getByProject(Request $request, $project_id){
        $tasksGroup = TaskGroup::where('project_id', $project_id)->paginate(SELF::ITEMS_PER_PAGE);
        return response()->json(
            [
                'task_groups' => TasksGroupsResource::collection(($tasksGroup)),
                'pagination' => [
                    'total_pages' =>  $tasksGroup->lastPage(),
                    'total_items' => $tasksGroup->total(),
                    'current_page' => $tasksGroup->currentPage()
                ],
                'status' => true
            ], 200);
    }


    public function attachUserToTaskGroup(AttachUserToTaskGroupRequest $request){
        $project = Project::findOrFail($request->project_id);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($project->id, $request->user_id), 403, 'Action unauthorized');
        $userTaskGroup = $this->taskGroupService->attachUserFromTaskGroup($request->task_group_id, $request->user_id, $project->id);
        abort_if(!$userTaskGroup, 400, 'user is already attach on this task group');
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function detachUserFromTaskGroup(DetachUserFromTaskGroupRequest $request){
        $project = Project::findOrFail($request->project_id);
        abort_if(!$this->userService->isAdministratorOrCollaboratorOfTheProject($project->id, $request->user_id), 403, ' Action unauthorized');
        $this->taskGroupService->detachUserFromTaskGroup($request->task_group_id, $request->user_id, $project->id);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }
}
