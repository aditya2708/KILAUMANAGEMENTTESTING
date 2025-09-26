<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asnaf extends Model
{
    protected $table ="asnaf";
	protected $primaryKey = 'id';
    protected $fillable = [
     'asnaf', 'aktif'
 ];
}