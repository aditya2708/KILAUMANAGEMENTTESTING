<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table ="transaksi";

    protected $fillable = [
     'id_bank','id_transaksi','tanggal','pembayaran','id_koleks', 'id_donatur','program','subprogram','keterangan','bukti', 'bukti2',
     'jumlah', 'old_jumlah', 'status','kota','donatur','kolektor','alamat','alasan','approval', 'created_at', 'id_sumdan', 'id_program', 
     'id_kantor', 'id_pros', 'coa_debet', 'coa_kredit','via_input','qty', 'ket_penerimaan', 'akun','user_insert', 'id_camp', 'hapus_token','notif', 'dp'
 ];
}
