<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekdis extends Model
{
    protected $table = "rekdis";

    protected $fillable = [
     'tanggal', 'tgl_gaji', 'id_karyawan', 'nama', 'cek_in', 'keterlambatan', 'cek_out', 'status', 'jumlah', 'id_laporan', 'laporan', 'keterangan', 'stat'
 ];
}
