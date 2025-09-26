<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiKaryawan extends Model
{
    protected $table ="mutasi_karyawan";

    protected $guarded = [];
    
    protected $primaryKey = 'id_mutasi';
}
