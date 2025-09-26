<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantor extends Model
{
    protected $table ="tambahan";
    
    protected $fillable = [
     'id', 'id_coa','unit', 'level','no_hp','alamat','kantor_induk','tj_daerah','acc_up', 'user_insert', 'user_update', 'id_com', 'id_jabpim', 'id_pimpinan'
 ];
}
