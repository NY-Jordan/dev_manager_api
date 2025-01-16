<?php

namespace App\Models;

use DateTime;
use Faker\Core\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'user_id',
        'delivery_at'
    ];

    public function projectInvitation($type = null){
       return  $this->hasMany(ProjectInvitaion::class);
    }

    public function userProject(){
        return $this->hasMany(ProjectUser::class, 'project_id');
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->save();
    }

    public function tasksGroup(){
       return  $this->hasMany(TaskGroup::class);

    }

    public function getCollaborators(null|int $projectId = null){
        $project = $this->find($projectId ? $projectId : $this->id);
        $projectUsers = $project->userProject;
        return $projectUsers;
    }

    public function getInvitations(null|int $projectId = null){

        $project = $this->find(id: $projectId ? $projectId : $this->id);
        $projectInvitations = $project->projectInvitation;
        return $projectInvitations;
    }

    public function getTasks(null|int $projectId = null, null|int $userId = null){
        $project = $this->find($projectId ? $projectId : $this->id);
        $taskGroups = $project->tasksGroup;
        $allTasks = collect();
        foreach ($taskGroups as $taskGroup) {
            $tasks = $taskGroup->tasks;
           if ($userId !== null) {
                $tasks = $tasks->filter(function ($task) use ($userId) {
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
            $allTasks = $allTasks->merge($tasks);
        }

        return $allTasks->sortByDesc('created_at');
    }


    public function getProjectOfUser($id = null, $user_id = null){
        $userId = $user_id ? $user_id : Auth::id();
        if(!$id){
            return $this->where('user_id', $userId)->get();
        }
        return $this->where('id', $id)->where('user_id', $userId)->get();
    }

    public function createNewProject(string $name, UploadedFile|null $logo,$user_id = null,  DateTime $delivery_at = null){

        if ($logo) {
            $filename = 'project'.\random_int(0, 1000000).'.'.$logo->getClientOriginalExtension();
            $path = $logo->storeAs(
                'projects/'.auth()->user()->name,
                $filename,
                'public'
            );
            $fullPath = $url = asset('storage/' . $path);
        }
        $project = $this->create([
            'name' => $name,
            'logo' => $fullPath ?? null ,
            'user_id' => $user_id ? $user_id : Auth::id(),
            'delevry_at' => $delivery_at

        ]);
        return $project;
    }

    public  function isTheAdministrator(int|null $userId = null, $project_id  = null) : bool {
        $user = $this->where('user_id', $userId ? $userId : Auth::id())->where('id', $project_id ?? $this->id)->first();
        $is = !empty($user) ? true : false;
        return $is;
    }

}
