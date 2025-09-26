<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voting extends Model
{
    protected $table ="voting";
	protected $primaryKey = 'id';
	protected $guarded = [];
//     protected $fillable = [
//      	'isi', 'jenis', 'id_kantor', 'tgl_awal', 'tgl_akhir', 'id_com', 'user_insert', 'user_update'
//  ];
}