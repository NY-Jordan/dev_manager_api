<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getToken(): NewAccessToken
    {
        if ($this->email_verified_at) {
            return $this->createToken("API TOKEN", ['*']);
        }
        return $this->createToken("API TOKEN", ['limited']);
    }

    public static function findByEmail($email) : User|null {
        return User::where('email', $email)->first();
    }

    public static function findByPasswordAndEmail($email, $password) : User|bool {
        $user = User::where('email', $email)->first();
        if(!$user || !Hash::check($password,$user->password)){
            return false;
        }
        return $user;
    }

    public function setEmailVerifiedAt($date = null) : void {
        $this->email_verified_at = $date ? $date : Carbon::now();
    }
}
