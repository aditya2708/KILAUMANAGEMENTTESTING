<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prosp extends Model
{
    protected $table ="prosp";

    protected $fillable = [
     'id_don', 'id_sumdan', 'id_prog', 'id_peg', 'ket', 'tgl_fol', 'status', 'durasi', 'statprog', 'id_koleks', 'id_so', 'konprog', 'tokpros', 'carfol'
 ];
}
