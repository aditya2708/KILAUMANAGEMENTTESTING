<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapFol extends Model
{
    protected $table ="lap_folup";
	protected $primaryKey = 'id';
    protected $fillable = [
     'ket', 'tgl_fol', 'pembayaran', 'bukti', 'deskripsi', 'tahap', 'jalur', 'program', 'id_sumdan', 'id_peg', 'id_karyawan', 'id_don', 'status','id_prog','id_pros','jenis','carfol', 'created_at'
 ];
}