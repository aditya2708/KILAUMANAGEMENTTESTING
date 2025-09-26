<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table ="laporan";

    protected $fillable = [
     'id_laporan', 'id_karyawan', 'pr_jabatan','kantor_induk', 'nama',  'lampiran','ket', 'stat_feed', 'vn', 'sec_vn','link_lap', 'capaian', 'target'
    ];
    
    protected $primaryKey = 'id_laporan';
}
