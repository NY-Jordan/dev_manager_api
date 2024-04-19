<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','status', 'project_id'
    ] ;


    public function project(){
        return $this->morphToMany(Project::class, 'project_id');
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->save();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->save();
    }
}
