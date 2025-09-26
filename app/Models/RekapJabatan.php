<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapJabatan extends Model
{
    protected $table ="rekap_jabatan";
    protected $primaryKey = "id_rekjab";
	protected $guarded = [];
    // protected $fillable = [
    //     'id_karyawan', 'nama', 'id_jabatan', 'id_spv', 'tgl_jab', 'file','user_insert','user_approve','acc','plt','jab_daerah',
    // ];
}
