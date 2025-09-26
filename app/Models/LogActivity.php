<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $table ="log_activity";
	protected $primaryKey = 'id';
    protected $fillable = [
     'pesan', 'url', 'method', 'ip', 'user_agent', 'user_id', 'id_kantor', 'kantor_induk','keterangan','via','id_data','jenis_aksi',
    ];
}