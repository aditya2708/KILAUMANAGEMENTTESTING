<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table ="pengaduan";

    protected $fillable = [
     'id_transaksi','kolektor','aduan','kota'
 ];
}
