<?php

namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Kantor;
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
class AnalisisDetailExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $tgltext1, $blntext1, $thntext1)
    {
        
        $this->tgltext1 = $tgltext1 ;
        $this->blntext1 = $blntext1 ;
        $this->thntext1 = $thntext1 ;
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $tgltext1 = $this->tgltext1;
        $blntext1 = $this->blntext1;
        $thntext1 = $this->thntext1;
        
        $request = $this->request;
        $id = $request->idTai;
        $y = date('Y');
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
        $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        if($request->approv == 2){
            $approve = "approval = '2'";
        }else if($request->approv == 1){
            $approve = "approval = '1'";
        }else{
            $approve = "approval IS NOT NULL";
        }
        
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        // $b1 = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        // $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
    
        // $real = "DATE_FORMAT(transaksi.tanggal,'%m-%Y') >= '$b1' AND DATE_FORMAT(transaksi.tanggal,'%m-%Y') <= '$b2'";
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
        
        $bul1 = '01-'.$b1;
        $bul2 = '01-'.$b2;
        
        $bula1 = date('Y-m', strtotime($bul1));
        $bula2 = date('Y-m', strtotime($bul2));

        $real = "DATE_FORMAT(tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
        
        $jumlah = $request->kondisi != '' ? "transaksi.jumlah = 0" : "transaksi.jumlah > 0";
        
        if($request->bay == ''){
            $bay = "transaksi.pembayaran IS NOT NULL";
        }else if($request->bay == 'cash'){
            $bay = "transaksi.pembayaran != 'noncash'";
        }else if($request->bay == 'noncash'){
            $bay = "transaksi.pembayaran = 'noncash'";
        }
        $prd = $request->plhtgl;
        if($prd == 0){
            $prdHeader = $tgltext1;
        }else if($prd == 1){
            $prdHeader = $blntext1;
        }else if($prd == 2){
            $prdHeader = $thntext1;
        }
        $now = date('Y-m-d');
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        // return($tahun);
        
        // $data = [];
        if($request->plhtgl == 0){
            if($request->analis == 'bank'){
                 $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.id_bank = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'tanggal'){
                $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND DAY(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'tahun'){
                $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND YEAR(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'bulan'){
                $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND MONTH(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'kantor'){
                $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.id_kantor = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'jam'){
                        $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND HOUR(created_at) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'donatur'){
                $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                        ->whereRaw("$tgls AND $bay AND transaksi.id_donatur = '$id'  AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'program'){
                    $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.id_program = '$id'  AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'status'){
                        $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                        ->whereRaw("$tgls AND $bay AND transaksi.status = '$id' AND $approve AND via_input = 'transaksi'")
                        ->get();
            }else if($request->analis == 'petugas'){
                    $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.id_koleks = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'bayar'){
                    $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.pembayaran = '$id' AND $approve AND via_input = 'transaksi'")
                        ->get();
            }else if($request->analis == 'user'){
                    $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->join('users','users.id','=','transaksi.user_insert')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$tgls AND $bay AND transaksi.user_insert = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }
        }else if($request->plhtgl == 1){
            if($request->analis == 'bank'){
                 $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.id_bank = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'tanggal'){
                $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" DAY(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'bulan'){
                $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" MONTH(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'tahun'){
                $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" YEAR(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'kantor'){
                $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.id_kantor = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'jam'){
                        $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" HOUR(created_at) = '$id'  AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'donatur'){
                $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.id_donatur = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'program'){
                    $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.id_program = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'status'){
                        $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                        ->whereRaw(" transaksi.status = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $real")
                        ->get();
            }else if($request->analis == 'petugas'){
                    $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("transaksi.id_koleks = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }else if($request->analis == 'bayar'){
                    $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.pembayaran = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $real")
                        ->get();
            }else if($request->analis == 'user'){
                    $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->join('users','users.id','=','transaksi.user_insert')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$bay AND transaksi.user_insert = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                        ->get();
            }
        }else if($request->plhtgl == 2){
            if($request->analis == 'bank'){
                 $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND transaksi.id_bank = '$id' AND  $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'tanggal'){
                $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND DAY(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
                        
            }else if($request->analis == 'tahun'){
                $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND YEAR(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'bulan'){
                $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND MONTH(tanggal) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'kantor'){
                $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND transaksi.id_kantor = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'jam'){
                        $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND HOUR(created_at) = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'donatur'){
                $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND transaksi.id_donatur = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'program'){
                    $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND transaksi.id_program = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'status'){
                        $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                        ->whereRaw("$year AND transaksi.status = '$id' AND $bay AND $approve AND via_input = 'transaksi'")
                        ->get();
            }else if($request->analis == 'petugas'){
                    $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$year AND transaksi.id_koleks = '$id' AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                        ->get();
            }else if($request->analis == 'bayar'){
                    $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw(" transaksi.pembayaran = '$id' AND $approve AND $bay AND via_input = 'transaksi' AND $year")
                        ->get();
            }else if($request->analis == 'user'){
                    $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->join('users','users.id','=','transaksi.user_insert')
                        // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                        ->whereRaw("$bay AND transaksi.user_insert = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah AND $year")
                        ->get();
            }
        }
        
            return view('ekspor.analisexport',[
                'data' => $data,
                'analis' => $request->analis,
                'toggle' => 'detail',
                'prd' => $prdHeader,
                'kondisi' => $request->kondisi,
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
            ]);
            
    }
}