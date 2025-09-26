<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi_Perhari_All extends Model
{
    protected $table ="transaksi_perhari_all";

    protected $fillable = [
     'tanggal', 'name', 'id', 'kota', 'jumlah', 'tdm', 'honor', 'bonus_cap', 'donasi', 't_donasi', 'tutup', 'tutup_x', 'ditarik', 'k_hilang', 'total', 'target', 'kunjungan'
 ];
}
