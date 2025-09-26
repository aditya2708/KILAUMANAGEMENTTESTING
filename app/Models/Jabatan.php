<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table ="jabatan";

    protected $fillable = [
     'id','jabatan', 'pr_jabatan', 'tj_jabatan', 'tj_jab_daerah', 'tj_training', 'tj_spv', 'kon_tj_spv', 'id_com'
 ];
}
