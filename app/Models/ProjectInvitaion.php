<?php

namespace App\Models;

use App\Enums\InvitationEnums;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectInvitaion extends Model
{
    use HasFactory, Uuids;
    public $incrementing = false;

    protected $fillable = [
        'receiver',
        'sender',
        'project_id',
        'Uuid'
    ];

    public function project(){
        return $this->morphToMany(Project::class, 'project_id');
    }
    public function user(){
        return $this->morphToMany(User::class, ['receiver', 'sender']);
    }
    public function newInvitation($receiver_id, $project_id, $sender = null, ){
        
        $invitation =  $this->create([
            'receiver' => $receiver_id,
            'sender' => $sender ? $sender : Auth::id(),
            'project_id' => $project_id,
        ]);
        
        return $invitation;

    }

    function setStatus($status)  {

        $this->attributes['status'] = $status;
        $this->save();
    }

    public static function check_if_exist($uuid,$user_id = null, $who = InvitationEnums::TYPE_RECEIVER)  {
        $invitation = self::where('uuid', $uuid)->where($who, !$user_id ? Auth::id() : $user_id)->first();
        $invitation_exist = !empty($invitation) ? $invitation  : false;
       return $invitation_exist;
    }
}
