<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prospek extends Model
{
    protected $table ="prospek";

    protected $fillable = [
     'nama', 'id_don', 'nama', 'petugas', 'old_id_sumdan', 'old_program', 'old_statprog', 'id_sumdan', 'program', 'statprog', 'id_koleks', 'id_so'
 ];
}
