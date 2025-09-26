<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoDana extends Model
{
    public $timestamps = false;
    protected $table ="saldo_dana";
    protected $primaryKey ="id";

    protected $fillable = [
     'coa_dana', 'coa_expend', 'coa_receipt', 'level', 'nama_coa', 'active', 'parent', 'operasi', 'id_com'
 ];
}