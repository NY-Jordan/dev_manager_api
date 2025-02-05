<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\StatusEnum;
use App\Enums\TokenTypeEnums;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
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
        'picture',
        'email_verified_at',
        'google_id',
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

    /**
     * The channels the user receives notification broadcasts on.
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.'.$this->id;
    }

    public function projectInvitation(){
        return $this->belongsTo(ProjectInvitaion::class);
    }

    public function projectInvitationSender(){
        return $this->hasMany(ProjectInvitaion::class, 'sender');
    }

    public function projectInvitationReceiver(){
        return $this->hasMany(ProjectInvitaion::class, 'receiver');
    }

    public function getProjectInvitation(int $userId){
       return  $this->projectInvitation()->whereReceiver($userId)->first();
    }

    public function userProject(){
        return $this->morphToMany(ProjectUser::class, 'user_id');
    }

    public function taskUser(){
        return $this->hasMany(TaskUser::class,'user_id');
    }

    public function getToken($type = null): NewAccessToken
    {
        if ($this->email_verified_at) {
            return $this->createToken("API TOKEN", ['*']);
        }
        if ($type === TokenTypeEnums::PASSWORD_RESET) {
            return $this->createToken("API TOKEN", ['password_reset'], Carbon::now()->addMinutes(5));
        }
        return $this->createToken("API TOKEN", ['limited']);
    }

    public static function findByEmail($email) : User|null {
        return User::where('email', $email)->first();
    }

    public static function findByPasswordAndEmail($email, $password) : User|bool {
        $user = User::where('email', $email)->where('status_id', StatusEnum::STATUS_ACTIVE)->first();
        if(!$user || !Hash::check($password,$user->password)){
            return false;
        }
        return $user;
    }

    public function setEmailVerifiedAt($date = null) : void {
        $this->email_verified_at = $date ? $date : Carbon::now();
        $this->save();
    }
    public function setStatus(bool $value){
        $this->status = $value;
        $this->save();
    }

    public static function search($key){
        return self::where('name', 'like','%'.$key.'%')
        ->orWhere('email', 'like','%'.$key.'%')
        ->get()
        ->except(Auth::id());
    }

    public function setPassword($password){
        $this->password = Hash::make($password);
        $this->save();
    }
}
