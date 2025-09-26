<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
{
    protected $table ="todo";
	protected $primaryKey = 'id_todo';
    protected $fillable = [
     'id_rows', 'name_rows', 'desc_rows', 'stat_rows'
 ];
}