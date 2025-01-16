<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPhase extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status'
    ];

    public function tasks(){
        return $this->hasMany(Task::class, );
    }

    static function findByName(string $name): TaskPhase|null  {
        return self::where('name', $name)->first();
    }

}
