<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hubungi extends Model
{
    protected $table ="hubungi";

    protected $fillable = [
     'id_hub', 'id_atasan', 'id_karyawan', 'id_jabatan', 'id_kantor', 'pr_jabatan', 'kantor_induk', 'nama_atasan', 'pesan'
    ];
    
    protected $primaryKey = 'id_hub';
}