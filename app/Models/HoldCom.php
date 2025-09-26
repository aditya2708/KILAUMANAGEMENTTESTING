<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoldCom extends Model
{
    protected $table ="hold_com";
	protected $primaryKey = 'id_hc';
    protected $fillable = [
     'id_karyawan', 'nama_hc', 'user_insert', 'user_update'
 ];
}