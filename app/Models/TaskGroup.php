<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','status'
    ] ;


    public function project(){
        return $this->morphToMany(Project::class, 'project_id');
    }

    public function setNameAttribute($name){
        $this->attributes['name'] = $name;
        $this->save();
    }

    public function setStatusAttribute($status){
        $this->attributes['status'] = $status;
        $this->save();
    }
}
