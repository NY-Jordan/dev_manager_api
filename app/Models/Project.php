<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'user_id',
        'delivery_at'
    ];

    public function projectInvitation($type = null){
        $this->belongsTo(ProjectInvitaion::class);
    }

    public function userProject(){
        return $this->morphToMany(projectUser::class, 'project_id');
    }

    public function groupTask(){
        $this->belongsTo(groupTask::class);

    }
    
    public function getProjectOfUser($id = null, $user_id = null){
        $userId = $user_id ? $user_id : Auth::id();
        if(!$id){
            return $this->where('user_id', $userId)->get();
        }
        return $this->where('id', $id)->where('user_id', $userId)->get();
    }

    public function createNewProject(string $name, $user_id = null,  DateTime $delivery_at = null){
        $project = $this->create([
            'name' => $name,
            'user_id' => $user_id ? $user_id : Auth::id(),
            'delevry_at' => $delivery_at

        ]);
        return $project;
    }

    public  function isTheAdministrator($user_id) : bool {
        $user = $this->where('user_id', $user_id)->where('id', $this->project_id)->first();
        $is = !empty($user) ? true : false;
        return $is;
    }

   


}
