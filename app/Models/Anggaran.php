<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $table ="anggaran";
    protected $primaryKey = 'id_anggaran';
    protected $fillable = [
     'id_anggaran','coa','nama_akun','keterangan','anggaran','nominal','dari','relokasi','uang_pengeluaran','total','kantor','jenis',
     'jabatan','referensi','program','tanggal','user_input','user_updated','user_approve','realisasi','alasan','user_reject','acc'
 ];
}
