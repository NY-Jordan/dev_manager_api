<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskPhaseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\Task\TaskFileRequest;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskGroup;
use App\Models\TaskPhase;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(TaskRequest $request)  {
        abort_if(!TaskGroup::find($request->taskgroup_id), 404, 'task group not found');
        abort_if(Task::titleIsAlreadyUseInThisGroupTask($request->title,$request->taskgroup_id), 422, 'Title already use in this Task Group');
        $data = $request->all();
        $data['phase'] = TaskPhase::where('name', TaskPhaseEnum::BACKLOG)->first()->id;
        Task::create($data);
        return response()->json(['message' => "operation successfully", 'status' => true], 201);
    }

    public function update(UpdateTaskRequest $request, $id)  {
        if ($request->title) {
            abort_if(Task::titleIsAlreadyUseInThisGroupTask($request->title,$request->taskgroup_id), 422, 'Title already use in this Task Group');
        }
        $task  = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }

    public function delete(Request $request, $id)  {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
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


}
