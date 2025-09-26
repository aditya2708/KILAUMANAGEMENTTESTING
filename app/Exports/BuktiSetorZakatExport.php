<?php
namespace App\Exports;

use Auth;

use App\Models\Transaksi;
use App\Models\Prog;
use App\Models\Tunjangan;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BuktiSetorZakatExport implements  FromView
{
public function __construct($request)
    {
       $this->request = $request;
        return $this;
    }

    public function view(): View{   
        
    $request  = $this->request ;  
    $jenis = $request->jenis_zakat;  
    $kntr =  $request->kantor == '' ? [Auth::user()->id_kantor] : $request->kantor;; 
    $tahun = date('Y');
    $bulan = date('m');
    $thn = $request->thn == '' ? "YEAR(tanggal) =  '$tahun'" : "YEAR(tanggal) = '$request->thn'";
    $bln = $request->bln;
    $bulawal = 01; 
    $bulterkahir = $bulan ;
        if ($request->bln != '') {
            $bulawal = reset($bln);
            $bulterkahir = end($bln);
        }
     $thn1 = $request->thn == '' ? $tahun: $request->thn;
    $progs = Prog::whereNotNull('id_bsz')->get();
    $jul = $request->jenis_zakat == '1' ? 'Zakat' : 'Non-Zakat' ;  
    
        if($jenis == '1'){
            $data = Transaksi::selectRaw("transaksi.*,donatur.penghasilan,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(jumlah) as jumlah ")
            ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
            ->whereRaw(" $thn AND transaksi.approval = 1 AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir")
            ->whereIn('transaksi.id_kantor',$kntr)
             ->orderBy('tanggal', 'desc')
            ->whereIn('id_program',$progs->pluck('id_program'))->groupBy('id_donatur')->get();
            }else{
            $data = Transaksi::selectRaw("transaksi.*,donatur.penghasilan,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(jumlah) as jumlah ")
            ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
            ->whereRaw(" $thn AND transaksi.approval = 1 AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir")
            ->whereIn('transaksi.id_kantor',$kntr)
             ->orderBy('tanggal', 'desc')
            ->groupBy('id_donatur')->get();
            
            }
                    return view('ekspor.buktisetorexport',[
                        'data' => $data,
                        'judul' => 'Bukti Setor'. $jul,
                        'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name,
                        'periode' => 'Periode Tahun '. $thn1 .' Bulan '. $bulawal .' Sampai '. $bulterkahir,
                    ]);

 
        }
    }
