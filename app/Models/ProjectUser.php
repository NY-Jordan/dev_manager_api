<?php

namespace App\Models;

use App\Enums\InvitationEnums;
use App\Enums\ProjectInvitation\InvitationStatusEnums;
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
        if ($invitation->status === InvitationStatusEnums::STATUS_ACCEPTED) {
            $this->create([
                'user_id'=> $invitation->receiver,
                'project_id'=> $invitation->project_id
            ]);
        }
    }

    
    public static function isACollaborator($project_id, $user_id) : bool {
        $user = self::where('user_id', (int)$user_id)->where('project_id', (int)$project_id)->first();
        $is = !empty($user) ? true : false;
        return $is;
    }

}
