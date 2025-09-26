<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpNoLoc extends Model
{
    protected $table ="upnoloc";
    protected $primaryKey = 'id';
    protected $fillable = [
     'nama', 'petugas', 'no_hp', 'alamat', 'latitude', 'longitude', 'id_koleks', 'ket', 'id_don', 'gambar_donatur', 'token', 'user_insert', 'user_update', 'acc', 'jenis'
 ];
}
