<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tambahan extends Model
{
    protected $table ="tambahan";

    protected $fillable = [
     'id','unit','no_hp','alamat', 'id_com'
 ];
}
