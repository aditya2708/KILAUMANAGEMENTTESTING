<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapKeluarga extends Model
{
    protected $table ="rekap_keluarga";
    protected $primaryKey = "id_rekkel";
	protected $guarded = [];
    // protected $fillable = [
    //     'id_karyawan', 'id_pasangan','nama', 'status_nikah','no_kk', 'scan_kk','nm_pasangan', 'tgl_lahir', 'tgl_nikah', 'nm_anak', 'tgl_lahir_anak', 'status_anak','user_insert','user_approve','acc',
    // ];
}