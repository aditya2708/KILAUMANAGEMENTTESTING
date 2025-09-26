<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    protected $table ="tunjangan";
    public $timestamps = false;
	protected $primaryKey = 'id_tj';
    protected $fillable = [
     'tj_beras', 'jml_beras', 'tj_pasangan', 'tj_anak', 'tj_transport', 'umr', 'persentasi', 'kolektor', 'so', 'spv_kol', 'spv_so', 'potongan', 'tj_plt', 'accu', 'pul', 'lappul', 'id_com','min_anggaran'
 ];
}