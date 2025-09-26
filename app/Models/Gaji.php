<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table ="gaji";
	protected $primaryKey = 'id_gaji';
    protected $fillable = [
     'id_karyawan','nama','masa_kerja','golongan','id_jabatan','id_kantor',
     'gapok','tj_jabatan','tj_daerah','tj_p_daerah','tj_anak','tj_pasangan','tj_beras','transport',
     'jml_hari','total','created', 'thp', 'bpjs', 'potab', 'ketenagakerjaan', 'kesehatan', 'bonus', 'bokin', 'potdis', 'no_rek', 'nik', 'status_kerja', 'potongan', 'potdll', 'status',
     'user_insert', 'user_update', 'potlaptab', 'arpotkol', 'arrinbon'
 ];
}