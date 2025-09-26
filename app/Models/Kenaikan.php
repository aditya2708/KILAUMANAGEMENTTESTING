<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kenaikan extends Model
{
    protected $table ="kenaikan";
    protected $primaryKey = "id_naik";
	protected $guarded = [];
//     protected $fillable = [
//      'id_karyawan', 'nama', 'masa_kerja', 'golongan', 'tgl_mk', 'tgl_gol', 'file_sk','status_kerja', 'user_insert','user_approve','acc','jkk','jht','jkm','jpn','kesehatan','no_rek','tgl_sk','id_mentor'
//  ];
}
