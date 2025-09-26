<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserSSOAuth extends Authenticatable
{
    use Notifiable;

    protected $guard = "usersso";
    protected $table = "users_sso";
    
    protected $fillable = [
        'nama','email','token'
    ];

    protected $hidden = [
        'password',
    ];

    public function getRouteKeyName(){
        return 'id';
      }

    protected $primaryKey = "id";
}
