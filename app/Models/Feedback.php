<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table ="feedback";

    protected $fillable = [
     'id_jabatan', 'id_kantor', 'id_laporan','id_karyawan', 'id_jabatan', 'pr_jabatan', 'kantor_induk', 'nama_atasan', 'feedback', 'sec_vn', 'vn', 'baca'
    ];
    
    protected $primaryKey = 'id_feedback';
}