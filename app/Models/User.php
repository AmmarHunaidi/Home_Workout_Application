<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\UserDevice;
use App\Models\UserInfo;
use App\Models\Follow;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = "id";
    protected $fillable = [
        'f_name',
        'l_name',
        'email',
        'password',
        'prof_img_url',
        'gender',
        'birth_date',
        'bio',
        'role_id',
        'email_verified_at',
        'deleted_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    //realations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function info()
    {
        return $this->hasMany(UserInfo::class);
    }

    public function followers() //people follow this user
    {
        return $this->hasMany(Follow::class, 'following');
    }

    public function follows() //people this users follow
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function blocks() //people this users follow
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'id');
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class, 'user_id');
    }
    //Accessor
    public function setFNameAttribute($f_name)
    {
        $this->attributes['f_name'] = strtolower($f_name);
    }
    public function setLNameAttribute($l_name)
    {
        $this->attributes['l_name'] = strtolower($l_name);
    }
    public function getFNameAttribute($f_name)
    {
        return ucfirst($f_name);
    }
    public function getLNameAttribute($l_name)
    {
        return ucfirst($l_name);
    }
    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email);
    }
}
