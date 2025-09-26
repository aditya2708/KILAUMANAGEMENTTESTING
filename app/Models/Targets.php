<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Targets extends Model
{
    protected $table ="targets";
    protected $primaryKey = 'id';
    protected $fillable = [
     'target', 'periode', 'kunjungan', 'transaksi', 'minimal', 'bonus', 'honor', 'id_jenis', 'tanggal', 'status', 'id_kantor', 'jenis_target', 'alasan_tolak', 'user_insert', 'user_approve','user_update'
 ];
}
