<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectInvitaion extends Model
{
    use HasFactory;
    protected $fillable = [
        'receiver',
        'sender',
        'project_id'
    ];

    public function project(){
        return $this->morphToMany(Project::class, 'project_id');
    }
    public function user(){
        return $this->morphToMany(User::class, ['receiver', 'sender']);
    }
    public function newInvitation($receiver_id, $project_id, $sender = null, ){

        $user  = User::findOrFail($receiver_id);
        $invitation = $this->create([
            'receiver' => $receiver_id,
            'sender' => $sender ? $sender : Auth::id(),
            'project_id' => $project_id
        ]);

        return $invitation;

    }
}
