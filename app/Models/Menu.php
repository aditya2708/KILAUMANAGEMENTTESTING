<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table ="sidebar";
	protected $primaryKey = 'id';
    protected $fillable = [
        'menu', 'id_parent', 'sort', 'link','id_user', 'id_com'
    ];
    
    public function childs() {
        return $this->hasMany('App\Models\Menu','id_parent','id') ;
    }
}