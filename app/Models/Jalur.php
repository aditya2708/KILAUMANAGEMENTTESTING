<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jalur extends Model
{
    protected $table ="jalur";
	protected $primaryKey = 'id_jalur';
    protected $fillable = [
     'id_kantor',
     'nama_jalur', 
     'kota',
     'id_agen',
     'id_spv'
 ];
}