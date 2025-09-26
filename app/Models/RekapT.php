<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapT extends Model
{
    protected $table ="rekap_transaksi";
    protected $primaryKey = "id";
    protected $fillable = [
        'id_transaksi', 'akun','alamat', 'approval','bukti', 'bukti2','coa_debet', 'coa_kredit', 'created_at', 'donatur', 'id_bank', 'id_camp','id_donatur',
        'id_kantor', 'id_koleks','id_program', 'id_pros','id_sumdan', 'id_transaksi','jumlah', 'kantor_induk', 'ket_penerimaan', 'keterangan', 'kolektor', 'kota','name','via_input',
        'notif', 'pembayaran','program', 'qty','status', 'subprogram','tanggal', 'user_approve', 'user_insert', 'user_update', 'updated_at'
    ];
}