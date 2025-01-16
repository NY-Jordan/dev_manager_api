<?php

namespace App\Models;

use App\Enums\ProjectInvitation\InvitationEntityEnums;
use App\Enums\ProjectInvitation\InvitationStatusEnums;
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
        'Uuid',
        'status_id'
    ];



    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function status(){
        return $this->belongsTo(InvitationStatus::class);
    }

    public function userSenders(){
        return $this->belongsTo(User::class,  relation : 'projectInvitationSender', foreignKey: 'sender');
    }

    public function userReceivers(){
        return $this->belongsTo(User::class, relation : 'projectInvitationReceiver', foreignKey: 'receiver' );
    }

    public static  function findByUuid(string $uuid) {
        return self::where('uuid', $uuid)->first();
    }


    public function newInvitation($receiver_id, $project_id, $sender = null, ){
       /*  dd($project_id); */
        $invitation =  $this->create([
            'id',
            'receiver' => $receiver_id,
            'sender' => $sender ? $sender : Auth::id(),
            'project_id' => $project_id,
            'status_id' =>InvitationStatusEnums::STATUS_PENDING
        ]);
        return $invitation;
    }

    function setStatus($status)  {

        $this->attributes['status_id'] = $status;
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
        ->where('status_id',StatusEnum::STATUS_ACTIVE)
        ->first();
        if ($invitation) {
            return true;
        }
        return false;
    }

}
