<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jurnal extends Model
{
    protected $table ="jurnal";
	protected $primaryKey = 'id_jurnal';
    protected $fillable = [
    'coa_use',
    'nominal_use',
     'jenis_transaksi',
     'keterangan', 
     'nominal_debit',
     'nominal_kredit',
     'user_input',
     'user_update',
     'kantor',
     'coa_debet',
     'coa_kredit',
     'jenis',
     'tanggal',
     'acc',
     'no_resi'
 ];
}