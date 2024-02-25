<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'delivery_at'
    ];


    public function getProjectOfUser($id = null, $user_id = null){
        $userId = $user_id ? $user_id : Auth::id();
        if(!$id){
            return $this->where('user_id', $userId)->get();
        }
        return $this->where('id', $id)->where('user_id', $userId)->get();
    }

    public function createNewProject(string $name, $user_id = null,  DateTime $delevry_at = null){
        $project = $this->create([
            'name' => $name,
            'user_id' => $user_id ? $user_id : Auth::id(),
            'delivery_at' => $delevry_at

        ]);
        return $project;
    }

}
