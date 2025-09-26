<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $table ="penerimaan";
	protected $primaryKey = 'id';
    protected $fillable = [
     'coa', 'jenis_transaksi', 'qty', 'nominal', 'pembayaran', 'bank', 'non_cash', 'keterangan', 'kantor', 'tgl', 'user_input'
 ];
}