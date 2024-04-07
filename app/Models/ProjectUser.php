<?php

namespace App\Models;

use App\Enums\InvitationEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 'project_id'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function addNewUserToProject(ProjectInvitaion $invitation){
        if ($invitation->status === InvitationEnums::STATUS_ACCEPTED) {
            $this->create([
                'user_id'=> $invitation->receiver_id,
                'project_id'=> $invitation->project_id
            ]);
        }
       

    }

    public static function isACollaborator($project_id, $user_id) : bool {
        $user = self::where('user_id', $user_id)->where('id', $project_id)->first();
        $is = !empty($user) ? true : false;
        return $is;
    }

}
