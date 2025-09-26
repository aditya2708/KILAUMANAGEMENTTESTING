<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpGaji extends Model
{
    protected $table ="up_gaji";
    protected $primaryKey = 'id';
    protected $fillable = [
     'id_gaji','keterangan', 'nominal', 'status', 'tmt', 'id_karyawan', 'bayar', 'tgl_gaji', 'tanggal', 'tgl_bayar', 'user_insert'
 ];
}
