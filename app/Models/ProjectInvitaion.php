<?php

namespace App\Models;

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
}
