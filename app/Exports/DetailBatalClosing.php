<?php

namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\HapusTransaksi;
use App\Models\User;
use App\Models\HapusPengeluaran;
use Carbon\Carbon;
use DB;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class DetailBatalClosing implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $b, $t)
    {
        
        $this->b = $b ;
        $this->t = $t ;
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $b = $this->b;
        $t = $this->t;
        $request = $this->request;
        
        $tglclos = $request->tglclos;
        $coa     = $request->coa;
        $bln     = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $bulan   = date('m', strtotime('01-'.$bln));
        $tahun   = date('Y', strtotime('01-'.$bln));
        // $salnow  = SaldoAw::whereDate('bulan', date('Y-m-t'))->first();
        
        $tran   = Transaksi::selectRaw("transaksi.id, 'tran' AS tab, 'Transaksi' AS ket_tab, transaksi.tanggal AS tgl, transaksi.jumlah AS nominal, transaksi.approval AS acc, transaksi.via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, IF(DATE(transaksi.created_at) = DATE(transaksi.updated_at), transaksi.user_insert, transaksi.user_update) AS user_pj, IF(DATE(transaksi.created_at) = DATE(transaksi.updated_at), DATE(transaksi.created_at), '') AS dibuat, IF(DATE(transaksi.created_at) != DATE(transaksi.updated_at), DATE(transaksi.updated_at), '') AS diupdate, '' AS dihapus")
                            ->whereMonth('transaksi.tanggal', $bulan)
                            ->whereYear('transaksi.tanggal', $tahun)
                            ->whereRaw("DATE(transaksi.tanggal) != DATE(transaksi.created_at)")
                            ->where(function($q) use ($tglclos){
                                $q->whereDate('transaksi.created_at', '>=', $tglclos)->orWhereDate('transaksi.updated_at', '>=', $tglclos);
                                })
                            ->where(function($q) use ($coa){
                                $q->where('transaksi.coa_debet', $coa)->orWhere('transaksi.coa_kredit', $coa);
                                })
                            ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'transaksi.coa_debet')
                            ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'transaksi.coa_kredit')
                            ->where('transaksi.approval', 1);
        $peng   = Pengeluaran::selectRaw("pengeluaran.id, 'peng' AS tab, 'Pengeluaran' AS ket_tab, pengeluaran.tgl, pengeluaran.nominal, pengeluaran.acc, pengeluaran.via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, IF(DATE(pengeluaran.created_at) = DATE(pengeluaran.updated_at), pengeluaran.user_input, pengeluaran.user_approve) AS user_pj, IF(DATE(pengeluaran.created_at) = DATE(pengeluaran.updated_at), DATE(pengeluaran.created_at), '') AS dibuat, IF(DATE(pengeluaran.created_at) != DATE(pengeluaran.updated_at), DATE(pengeluaran.updated_at), '') AS diupdate, '' AS dihapus")
                            ->whereMonth('pengeluaran.tgl', $bulan)
                            ->whereYear('pengeluaran.tgl', $tahun)
                            ->whereRaw("DATE(pengeluaran.tgl) != DATE(pengeluaran.created_at)")
                            ->where(function($q) use ($tglclos){
                                $q->whereDate('pengeluaran.created_at', '>=', $tglclos)->orWhereDate('pengeluaran.updated_at', '>=', $tglclos);
                                })
                            ->where(function($q) use ($coa){
                                $q->where('pengeluaran.coa_debet', $coa)->orWhere('pengeluaran.coa_kredit', $coa);
                                })
                            ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'pengeluaran.coa_debet')
                            ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'pengeluaran.coa_kredit')
                            ->where('pengeluaran.acc', 1);
        $h_tran = HapusTransaksi::selectRaw("hapus_transaksi.id, 'h_tran' AS tab, 'Hapus Transaksi' AS ket_tab, hapus_transaksi.tanggal AS tgl, hapus_transaksi.jumlah AS nominal, hapus_transaksi.approval AS acc, hapus_transaksi.via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, hapus_transaksi.user_delete AS user_pj, '' AS dibuat, '' AS diupdate, DATE(hapus_transaksi.created_at) AS dihapus")
                            ->whereMonth('hapus_transaksi.tanggal', $bulan)
                            ->whereYear('hapus_transaksi.tanggal', $tahun)
                            ->whereRaw("DATE(hapus_transaksi.tanggal) != DATE(hapus_transaksi.created_at)")
                            ->where(function($q) use ($tglclos){
                                $q->whereDate('hapus_transaksi.created_at', '>=', $tglclos)->orWhereDate('hapus_transaksi.updated_at', '>=', $tglclos);
                                })
                            ->where(function($q) use ($coa){
                                $q->where('hapus_transaksi.coa_debet', $coa)->orWhere('hapus_transaksi.coa_kredit', $coa);
                                })
                            ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'hapus_transaksi.coa_debet')
                            ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'hapus_transaksi.coa_kredit')
                            ->where('hapus_transaksi.approval', 1);
        $h_peng = HapusPengeluaran::selectRaw("hapus_pengeluaran.id, 'h_peng' AS tab, 'Hapus Pengeluaran' AS ket_tab, hapus_pengeluaran.tgl, hapus_pengeluaran.nominal, hapus_pengeluaran.acc, hapus_pengeluaran.via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, hapus_pengeluaran.user_delete AS user_pj, '' AS dibuat, '' AS diupdate, DATE(hapus_pengeluaran.created_at) AS dihapus")
                            ->whereMonth('hapus_pengeluaran.tgl', $bulan)
                            ->whereYear('hapus_pengeluaran.tgl', $tahun)
                            ->whereRaw("DATE(hapus_pengeluaran.tgl) != DATE(hapus_pengeluaran.created_at)")
                            ->where(function($q) use ($tglclos){
                                $q->whereDate('hapus_pengeluaran.created_at', '>=', $tglclos)->orWhereDate('hapus_pengeluaran.updated_at', '>=', $tglclos);
                                })
                            ->where(function($q) use ($coa){
                                $q->where('hapus_pengeluaran.coa_debet', $coa)->orWhere('hapus_pengeluaran.coa_kredit', $coa);
                                })
                            ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'hapus_pengeluaran.coa_debet')
                            ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'hapus_pengeluaran.coa_kredit')
                            ->where('hapus_pengeluaran.acc', 1); 
                            
        $data = $tran->unionAll($peng)->unionAll($h_tran)->unionAll($h_peng)->orderByRaw('tgl')->get();
        $result = [];
            foreach($data as $i => $val){
                $user = User::find($val->user_pj); 
                $uspj = $user->name;
                $result[] = [
                    'tgl'     => $val->tgl,              
                    'nominal'    => $val->nominal,            
                    'coa_debet'  => $val->coa_debet,            
                    'coa_kredit' => $val->coa_kredit,                
                    'via_input' => $val->via_input,             
                    'user_pj'   => $uspj,             
                    'dibuat'    => $val->dibuat,             
                    'diubah'    => $val->diupdate,             
                    'dihapus' => $val->dihapus
                ];
            }
            return view('ekspor.detailbatalclosing',[
                'data' => $result,
                'priode' => 'Priode Bulan '. $b.'-'.$t,
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
            ]);
            
    }
}