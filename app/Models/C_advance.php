<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class c_advance extends Model
{
    protected $table ="c_advance";
    protected $primaryKey = 'id_ca';
    protected $fillable = [
     'id_ca','id_coa','id_buku','	nama_akun','keterangan','qty','nominal','realisasi','pengaju','user_approve','referensi','program','coa_debet','coa_kredit','no_resi','note','id_kantor','user_insert','user_update'
 ];
}
