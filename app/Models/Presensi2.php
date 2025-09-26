<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi2 extends Model
{
    protected $table ="presensi2";

    protected $fillable = [
     'id_presensi', 'id_karyawan', 'pr_jabatan', 'kantor_induk', 'nama', 'id_request', 'id_req',
     'cek_in','keterlambatan', 'break_out', 'break_in', 'cek_out', 'status', 'lampiran', 'foto', 'foto_out', 'acc', 'acc_out', 'ket', 'latitude', 'longitude', 'acc_shift', 'id_shift'
    ];
    
    protected $primaryKey = 'id_presensi';
}
