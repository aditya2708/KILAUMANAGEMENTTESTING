<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackUser extends Model
{
    protected $table ="feed_user";

    protected $fillable = [
     'id_karyawan', 'nama','feedback'
    ];
    
    protected $primaryKey = 'id_feed_user';
}