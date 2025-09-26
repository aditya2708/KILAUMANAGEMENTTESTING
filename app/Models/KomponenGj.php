<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenGj extends Model
{
    protected $table ="komponen_gj";
    protected $primaryKey = 'id';
    protected $fillable = [
     'id', 'nama', 'modal', 'grup', 'aktif', 'urutan','id_com'
    ];
}