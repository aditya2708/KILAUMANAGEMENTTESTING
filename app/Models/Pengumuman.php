<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table ="pengumuman";
	protected $primaryKey = 'id';
	protected $guarded = [];
//     protected $fillable = [
//      	'isi', 'jenis', 'id_kantor', 'tgl_awal', 'tgl_akhir', 'id_com', 'user_insert', 'user_update'
//  ];
}