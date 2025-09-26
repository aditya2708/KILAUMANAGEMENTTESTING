<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSSO extends Model
{
    protected $table ="users_sso";
	protected $primaryKey = 'id';
	protected $guarded = [];
}