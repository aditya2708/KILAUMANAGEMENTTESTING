<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaGaji extends Model
{
    protected $table ="skema_gaji";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'nama','aktif', 'id_com'
    ];
}
