<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgBSZ extends Model
{
    protected $table ="prog_bsz";
	protected $primaryKey = 'id';
    protected $fillable = [
     'nama', 'aktif','keterangan'
 ];
}