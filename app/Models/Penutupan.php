<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penutupan extends Model
{
    protected $table ="penutupan";
	protected $primaryKey = 'id_pen';
    protected $fillable = [
        'coa_pen', 'tanggal', 'nama_akun', 'saldo_awal', 'saldo_akhir', 'debit', 'kredit', 'adjustment', 'k100000', 'k75000', 'k50000', 'k20000', 
        'k10000', 'k5000', 'k2000', 'k1000', 'k500', 'k100', 'l1000', 'l500', 'l200', 'l100', 'l50', 'l25', 'user_input', 'user_update', 'saldo_fisik'
 ];
}