<?php

namespace App\Models;

use App\Enums\ProjectInvitation\InvitationEntityEnums;
use App\Enums\StatusEnum;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectInvitaion extends Model
{
    use HasFactory, Uuids;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'receiver',
        'sender',
        'project_id',
        'Uuid'
    ];

    public function project(){
        return $this->morphToMany(Project::class, 'project_id');
    }
    public function sender(){
        return $this->morphToMany(User::class,  'sender');
    }

    public function receiver(){
        return $this->morphToMany(User::class,  'receiver');
    }
    

    public function newInvitation($receiver_id, $project_id, $sender = null, ){
       /*  dd($project_id); */
        $invitation =  $this->create([
            'id',
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

    public static function check_if_exist($uuid, $user_id = null)  {
        $invitation = self::where('uuid', $uuid)->where('receiver', !$user_id ? Auth::id() : $user_id)->first();
        if (!empty($invitation)) {
           return $invitation;
        }
        $_invitation = self::where('uuid', $uuid)->where('sender', !$user_id ? Auth::id() : $user_id)->first();
        if (!empty($_invitation )) {
            return $_invitation;
         }
        return false;
    }

    public static function check_if_user_is_invited($projectId,$userId) {
        $invitation = self::whereReceiver($userId)
        ->whereProjectId($projectId)
        ->where('status',StatusEnum::STATUS_ACTIVE)
        ->first();
        if ($invitation) {
            return true;
        }
        return false;
    }

}
