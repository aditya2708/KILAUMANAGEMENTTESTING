<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoAw extends Model
{
    protected $table ="saldo_awal";
	protected $primaryKey = 'id';
    protected $fillable = [
     'coa', 'saldo_awal', 'closing', 'canclos', 'tgl_closing', 'bulan','konak'
 ];
}