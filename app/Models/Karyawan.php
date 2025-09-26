<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    protected $table ="karyawan";
    // protected $guard = "karyawan";

    protected $guarded = [];
 
 public function getRouteKeyName(){
    return 'id_karyawans';
  }

  protected $hidden = [
    'password'
];

protected $primaryKey = "id_karyawans";

}
