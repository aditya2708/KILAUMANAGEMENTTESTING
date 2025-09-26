<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyaluran extends Model
{
    protected $table ="penyaluran";
	protected $primaryKey = 'id';
    protected $fillable = [
     'nama_pm','jenis_transaksi', 'keterangan', 'saldo_dana', 'qty', 'nominal','via_input' , 'pembayaran', 'bank', 'non_cash', 'kantor', 'tgl_mohon', 'tgl_salur' ,  'user_input', 'user approve', 'referensi', 'program', 'kantor', 'coa_debet', 'coa_kredit', 'via_mohon', 'no_resi','department','acc'
 ];
}