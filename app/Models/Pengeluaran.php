<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table ="pengeluaran";
	protected $primaryKey = 'id';
    protected $fillable = [
     'jenis_transaksi','id_anggaran', 'keterangan', 'saldo_dana', 'qty', 'nominal', 'old_nominal' ,'via_input' , 'pembayaran', 'bank', 'non_cash', 'kantor', 'tgl', 'user_input', 'user approve', 'referensi', 'program', 'kantor', 'coa_debet', 'coa_kredit', 'no_resi','department','acc', 'note' ,'bukti', 'bukti_kegiatan', 'berita_acara', 'hapus_token', 'notif','created_at'
 ];
}