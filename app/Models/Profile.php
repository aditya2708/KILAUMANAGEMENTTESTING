<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table ="company";
    protected $primaryKey = 'id_com';
    protected $guarded = [];
}
