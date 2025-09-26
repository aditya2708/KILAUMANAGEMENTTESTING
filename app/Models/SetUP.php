<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetUP extends Model
{
    protected $table ="set_up";
    // protected $guarded = [];
    protected $primaryKey = 'id';
    protected $fillable = [
     'id_kantor','id_pembeda','jenis','bayar','tanggal','nominal','user_insert','user_update','user_approve','created_at','updated_at'
 ];
}
  