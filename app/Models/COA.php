<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class COA extends Model
{
    protected $table ="coa";
	protected $primaryKey = 'id';
    protected $fillable = [
        'coa', 'nama_coa', 'coa_parent', 'id_parent', 'level', 'parent', 'aktif', 'group', 'saldo_new','konak','id_com'
    ];
 
    public function children()
    {
        return $this->hasMany(COA::class, 'coa_parent');
    }

    public function parent()
    {
        return $this->belongsTo(COA::class, 'coa_parent');
    }
    
    public function getTotalProductsAttribute()
    {
        $total = $this->saldo_new;
        foreach ($this->children as $child) {
            $total += $child->getTotalProductsAttribute();
        }
        return $total;
    }
}