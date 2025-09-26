<?php
 // app/Http/Controllers/MidtransController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Karyawan;
use Auth;

class MidtransController extends Controller
{
    public function handleMidtransCallback(Request $request)
    {
        $notif = $request->all();
        return($notif);
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)->first();
        // Periksa apakah transaksi sukses
        if ($notif['transaction_status'] == 'capture' || $notif['transaction_status'] == 'settlement') {
            if ($notif['payment_type'] == 'bank_transfer') {
                $bank = $notif['va_numbers'][0]['bank']; // Nama bank
                $vaNumber = $notif['va_numbers'][0]['va_number']; // Nomor Virtual Account
            }else{
                $bank = $notif['payment_type'];
            }
            Transaksi::updateOrCreate(
                ['id_transaksi' => $notif['order_id']],
                [
                    'payment_type'      => $bank,
                    'transaction_time'  => $notif['transaction_time'],
                    'status'            => 'Donasi',
                    'pembayaran'        => 'transfer',
                    'jumlah'            => $notif['gross_amount'],
                    'subtot'            => $notif['gross_amount'],
                    'old_jumlah'        => $notif['gross_amount'],
                    'id_bank'           => 11,
                    'tanggal'           => date('YYYY-md-dd'),
                    'donatur'           => $karyawan->nama,
                    'alamat'            => $karyawan->alamat,
                    'id_sumdan'         => 0,
                    'id_program'        => 0,
                    'program'           => 0,
                    'subprogram'        => 0,
                    'keterangan'        => null,
                    'kota'              => Auth::user()->kota,
                    'id_kantor'         => 321213,
                    'kantor_induk'      => 4,
                    'approval'          => 1,
                    'user_insert'       => Auth::user()->id,
                    'user_update'       => Auth::user()->id,
                    'id_pros'           => 0,
                    'akun'              => 'Infaq Shodaqoh Umum',
                    'coa_debet'         => '402.02.001.000',
                    'coa_kredit'        => '101.01.002.013',
                ]
            );
    
            return response()->json(['message' => 'Transaksi berhasil disimpan']);
        }
    
        return response()->json(['message' => 'Transaksi gagal'], 400);
    }

}
