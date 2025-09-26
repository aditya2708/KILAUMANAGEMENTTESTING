<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HapusPengeluaran extends Model
{
    protected $table ="hapus_pengeluaran";
    protected $fillable = [
     'id','jenis_transaksi', 'keterangan', 'saldo_dana', 'qty', 'nominal', 'via_input' , 'pembayaran', 'bank', 'non_cash', 'kantor', 'tgl', 'user_input', 'user approve', 'user_delete', 'referensi', 'program', 'kantor', 'coa_debet', 'coa_kredit', 'no_resi','department','acc', 'note' ,'bukti', 'hapus_token', 'hapus_alasan', 'notif'
 ];
}