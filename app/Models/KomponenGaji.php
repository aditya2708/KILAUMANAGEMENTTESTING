<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenGaji extends Model
{
    protected $table ="komponen_gaji";
    protected $primaryKey = 'id';
    protected $fillable = [
     'id', 'id_skema','id_komponen', 'bisa_edit', 'id_persen', 'id_com', 'aktif'
    ];
}
