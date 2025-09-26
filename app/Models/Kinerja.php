<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    protected $table ="kinerja";

    protected $fillable = [
     'wilayah', 'kota' , 'nama' ,'alamat','nomerhp','email','jalur','latitude','longitude','status','program','wilayah','setoran','id_koleks'
 ];
}
