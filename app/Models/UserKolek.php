<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserKolek extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_karyawan','id_jabatan','name', 'email', 'password','kota','level','kolektor','minimal','kunjungan','qty','honor','omset','bonus','target','jenis','diluar',
        'api_token','keuangan','kepegawaian','kolekting','pengaturan','presensi','pr_jabatan','id_kantor','kantor_induk','aktif','shift', 'up_shift', 'tim', 'acc_shift', 'id_shift', 'kotug', 'status_kerja', 
        'id_com', 'level_hc', 'injenreq', 'injamker', 'kon_gaji'
    ];

    protected $guard = "userkolek";
    protected $table = "users";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRouteKeyName(){
        return 'id';
      }

      protected $primaryKey = "id";

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
