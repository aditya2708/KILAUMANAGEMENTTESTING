<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapHub extends Model
{
    protected $table ="laphub";
	protected $primaryKey = 'id';
    protected $fillable = [
     'ket', 'tgl_fol', 'pembayaran', 'bukti', 'deskripsi', 'tahap', 'jalur', 'program', 'id_sumdan', 'id_karyawan', 'id_don', 'feedback'
 ];
}