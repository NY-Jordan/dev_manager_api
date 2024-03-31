<?php

namespace App\Models;

use App\Enums\InvitationEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projectUser extends Model
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

}
