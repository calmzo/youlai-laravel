<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SysUser extends BaseModel implements JWTSubject, AuthenticatableContract,
    AuthorizableContract
{
    use HasFactory, Notifiable, Authenticatable, Authorizable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'issuer' => env('JWT_ISSUER'),
            'userId' => $this->getKey()
        ];
    }

//    public function routeNotificationForEasySms($driver, $notification = null)
//    {
//        return $this->mobile;
//    }

    public function routeNotificationForEasySms($driver, $notification = null)
    {
        return $this->mobile;
    }


    public function roles()
    {
        return $this->belongsToMany(SysRole::class, 'sys_user_role','user_id', 'role_id');
    }

    public function dept()
    {
        return $this->belongsTo(SysDept::class, 'dept_id');
    }

}
