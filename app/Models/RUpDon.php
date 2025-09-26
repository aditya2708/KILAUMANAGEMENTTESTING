<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RUpDon extends Model
{
    protected $table ="rupdon";

    protected $fillable = [
     'nama','no_hp', 'alamat', 
     'longitude', 'latitude', 'gambar_donatur', 'id_don'
 ];
}
