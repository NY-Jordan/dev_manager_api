<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public function InvitationStatus(){
        return $this->hasMany(ProjectInvitaion::class, 'project_id');
    }
}
