<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HapusTransaksi extends Model
{
    protected $table ="hapus_transaksi";

    protected $fillable = [
     'id','id_bank','id_transaksi','tanggal','pembayaran','id_koleks', 'id_donatur','program','subprogram','keterangan','bukti', 'bukti2',
     'jumlah','status','kota','donatur','kolektor','alamat','alasan','approval', 'created_at', 'id_sumdan', 'id_program', 
     'id_kantor', 'id_pros', 'coa_debet', 'coa_kredit','via_input','qty', 'ket_penerimaan', 'akun','user_insert', 'user_delete','id_camp', 'hapus_token', 'hapus_alasan','notif'
 ];
}
