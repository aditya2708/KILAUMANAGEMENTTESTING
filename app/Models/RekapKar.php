<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RekapKar extends Authenticatable
{
    protected $table ="rekap_kar";
    protected $primaryKey = "id_rekap";
    protected $guard = "rekap_kar";
	protected $guarded = [];

    // protected $fillable = [
    //  'id_gaji','nama', 'pendidikan', 
    //  'status_nikah', 'id_gol','masa_kerja', 'golongan','id_pasangan', 'nm_pasangan', 'tgl_lahir', 'tgl_nikah','nm_anak', 'tgl_lahir_anak', 'status_anak',
    //  'tgl_kerja', 'id_jabatan', 'id_karyawan', 'id_kantor', 'tgl_gaji','id_daerah','tgl_mk', 'tgl_gol', 'status_kerja', 'tj_pas', 'jab_daerah', 'plt', 'user_insert','user_approve','acc',
    //  ];
 



}
