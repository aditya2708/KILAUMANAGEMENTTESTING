<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Userkolektor extends Authenticatable
{

   use Notifiable;

    protected $table ="kolektor";

    protected $fillable = [
      'kota', 'nama', 'no_hp', 'email', 'username', 'password', 'alamat','level'
 ];

 protected $primaryKey = "id_kolektor";

 protected $hidden = [
  'password'
];

 public function getRouteKeyName(){
  return 'nama';
}

public function setPasswordAttribute($val)
{
     return $this->attributes['password'] = bcrypt($val);
}
}
