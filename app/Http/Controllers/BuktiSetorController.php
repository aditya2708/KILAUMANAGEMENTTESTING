<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\BuktiSetorZakatExport;
use App\Models\Transaksi;
use App\Models\Kantor;
use App\Models\Prosp;
use App\Models\Donatur;
use App\Models\LinkParam;
use App\Models\User;
use App\Models\Program;
use App\Models\Prog;
use App\Models\ProgBSZ;
use App\Models\Bank;
use App\Models\Pengeluaran;
use App\Models\RekapT;
use App\Models\COA;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Tunjangan;
use Carbon\Carbon;
use App\Models\HapusTransaksi;
use DataTables;
use Illuminate\Support\Facades\Http;

use Excel;
use PDF;
use App\Exports\RutinExport;

class BuktiSetorController extends Controller
{
         public function buktisetor_zakat(Request $request)
    {
               

            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
            $petugas = User::where('aktif', 1)->where('id_com', Auth::user()->id_com)->get();
            $progbsz = ProgBSZ::where('aktif','1')->get();
            $kntr = $request->kantor == '' ? [Auth::user()->id_kantor] : $request->kantor;
            $cari = $request->cari;
            $tahun = date('Y');
            $thn = $request->thn == '' ? "YEAR(transaksi.tanggal) =  '$tahun'" : "YEAR(transaksi.tanggal) = '$request->thn'";
            // $bln = $request->bln == '' ? "MONTH(transaksi.tanggal) =  '$bulan'" : "MONTH(transaksi.tanggal) = '$request->bln'";
          
        
            $progs = Prog::whereNotNull('id_bsz')->get();
            // $jenis = $request->jenis_zakat == '' ? "IS NOT NULL" :  '$request->jenis_zakat';
            $jenis = $request->jenis_zakat;
            $bulan = date('m');
            $bln = $request->bln  == '' ? $bulan :$request->bln ;
            if($request->bln == '' ){
            $bulawal =  01 ;
            $bulterkahir =  $request->bln == '' ? date('m') : end($bln) ;
            }else{
            $bulawal =  $request->bln == '' ? date('m') : reset($bln) ;
            $bulterkahir =  $request->bln == '' ? date('m') : end($bln) ;
            }
            
        if($request->ajax()){
            if($jenis == '1'){
                    $data = Transaksi::selectRaw("transaksi.*,donatur.penghasilan,DATE_FORMAT(tanggal, '%Y') as tahun, SUM(jumlah) as jumlah ,
                    SUM(IF(MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <= $bulterkahir AND transaksi.approval = 1 AND $thn, transaksi.jumlah, 0)) AS jumlah1")
                    ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
                    ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <= $bulterkahir AND transaksi.approval = 1")
                    ->whereIn('id_program', $progs->pluck('id_program'))
                    ->whereIn('transaksi.id_kantor', $kntr)
                    ->orderBy('transaksi.tanggal', 'desc')
                    ->groupBy('id_donatur')
                    ->get();
                    
            if($request->tab == 'tab1' && $jenis == '1'){    
                $jumlahTotal = Transaksi::selectRaw("SUM(jumlah) as total_jumlah")
                ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <= $bulterkahir AND transaksi.approval = 1")
                ->where(function($query) use ($request, $cari) {
                    if(isset($request->cari) && $request->cari != ''){
                        $query->where('donatur','LIKE','%'.$cari.'%');
                    }
                })
                ->whereIn('id_program', $progs->pluck('id_program'))
                ->whereIn('transaksi.id_kantor', $kntr)
                ->first();
                
                return $jumlahTotal;
            }
            

            }else{
                    $data = Transaksi::selectRaw("transaksi.*, donatur.penghasilan, DATE_FORMAT(tanggal, '%Y') as tahun, SUM(jumlah) as jumlah,
                    SUM(IF(MONTH(transaksi.tanggal) >= '$bulawal' AND MONTH(transaksi.tanggal) <= '$bulterkahir' AND transaksi.approval = 1 AND $thn, transaksi.jumlah, 0)) AS jumlah1")
                    ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
                    ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= '$bulawal' AND MONTH(transaksi.tanggal) <= '$bulterkahir' AND transaksi.approval = 1")
                    ->whereIn('transaksi.id_kantor', $kntr)
                    ->orderBy('transaksi.tanggal', 'desc')
                    ->groupBy('id_donatur')
                    ->get();
                    
            if($request->tab == 'tab1' && $jenis == '0'){    
                $jumlahTotal = Transaksi::selectRaw("SUM(jumlah) as total_jumlah")
                ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <= $bulterkahir AND transaksi.approval = 1")
                ->where(function($query) use ($request, $cari) {
                    if(isset($request->cari) && $request->cari != ''){
                        $query->where('donatur','LIKE','%'.$cari.'%');
                    }
                })
                ->whereIn('transaksi.id_kantor', $kntr)
                ->first();
                
                return $jumlahTotal;
            }

            
            }
            return DataTables::of($data)
            
            ->make(true);
        }
        return view('bukti-setor.bukti_setor_zakat', compact('kantor','petugas','progbsz')); 
    }
    
    
        public function buktiBy(Request $request, $id){
        $data['ui'] = Transaksi::whereRaw("id_donatur = '$id'")->first();
        return $data;
    }
    
        public function eksbukti(Request $request){
    
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

        
        $jenis = $request->status;
        $ttdz = \App\Models\Profile::first();
        $don = Donatur::whereRaw("id = '$request->id'")->first();
        $petugas = Donatur::whereRaw("id = '$don->id_koleks'")->first();
        $progs = Prog::whereNotNull('id_bsz')->get();
        $id_karyawan = Tunjangan::first();
        if ($id_karyawan->diterima_bsz != '') {
                $ttd = Karyawan::selectRaw("id_karyawan,nama,ttd")->where('id_karyawan', $id_karyawan->diterima_bsz)->first();
            } else {
        $ttd = null; 
        }

       if($request->per == 'bulan' && $jenis == '0'){
        $data = Transaksi::selectRaw("transaksi.*,DATE_FORMAT(tanggal,'%Y') as tahun ")
        ->whereRaw("transaksi.id_donatur = '$request->id' AND $thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir AND transaksi.jumlah > 0 AND transaksi.approval = 1")
        ->get();
        $tot = Transaksi::whereRaw("id_donatur = '$request->id' AND $thn AND  MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir AND transaksi.jumlah > 0 ")->select(DB::raw('SUM(jumlah) as total'))->first();
        $pdf = PDF::loadView('eksportbukti', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'ttd' => $ttd]);
        return $pdf->stream('Bukti Setor Data.pdf');
        
       }else if($request->per == 'tahun' && $jenis == '0'){
            $data = Transaksi::selectRaw("transaksi.*,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(transaksi.jumlah)as jumlah ")
            ->whereRaw("id_donatur = '$request->id' AND $thn AND transaksi.jumlah > 0 AND transaksi.approval = 1 ")->get();
            $tot = Transaksi::whereRaw("id_donatur = '$request->id' AND $thn  AND transaksi.jumlah > 0 AND transaksi.approval = 1 ")->select(DB::raw('SUM(jumlah) as total'))->first();
            $pdf = PDF::loadView('eksportbukti', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Data.pdf');
            
    
       }else if($request->per == 'tahun' && $jenis == '1'){
          
                $data = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                           ->whereRaw("$thn AND transaksi.id_donatur = '$request->id' AND jumlah > 0 AND transaksi.approval = 1 ");
                })
                ->selectRaw("prog_bsz.nama, transaksi.id_program, DATE_FORMAT(transaksi.tanggal, '%Y') as tahun, SUM(transaksi.jumlah) as jumlah")
                ->groupBy('transaksi.id_program', 'tahun', 'prog_bsz.nama')
                ->get();
          
            $tot = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                           ->whereRaw("$thn AND transaksi.id_donatur = '$request->id' AND transaksi.jumlah > 0 AND transaksi.approval = 1 ");
                })->select(DB::raw('SUM(transaksi.jumlah) as total'))->first();
            $bsz = ProgBSZ::get();
            $pdf = PDF::loadView('eksportzakat', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'bsz'=>$bsz,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Zakat.pdf');
       }else if($request->per == 'bulan' && $jenis == '1'){

                $data = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn,$bln,$bulawal,$bulterkahir) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                         ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir AND transaksi.id_donatur = '$request->id' AND jumlah > 0 AND transaksi.approval = 1");
                })
                ->selectRaw("transaksi.id_donatur,prog_bsz.nama, transaksi.id_program, DATE_FORMAT(transaksi.tanggal, '%m') as bulan ,transaksi.tanggal, 
                DATE_FORMAT(transaksi.tanggal, '%Y') as tahun, SUM(transaksi.jumlah) as jumlah")
                ->groupBy('transaksi.id_program')
                ->get();
                
            $tot = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn,$bln,$bulawal,$bulterkahir) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                         ->whereRaw("$thn AND MONTH(transaksi.tanggal) >= $bulawal AND MONTH(transaksi.tanggal) <=  $bulterkahir AND transaksi.id_donatur = '$request->id' AND transaksi.jumlah > 0 AND transaksi.approval = 1  ");
                })
                ->select(DB::raw('SUM(transaksi.jumlah) as total'))->first();
            $bsz = ProgBSZ::get();
            $pdf = PDF::loadView('eksportzakat', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'bsz'=>$bsz,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Zakat.pdf');
       }
    }
    
        public function export(Request $request){
        $tahun = date('Y');
        $bulan = date('m');
        $thn = $request->thn == '' ? $tahun: $request->thn;
        $jenis = $request->jenis_zakat == '1' ? 'Zakat' : 'Non-Zakat' ;
        $bln = $request->bln;
        $kntr = $request->kantor == '' ? [Auth::user()->id_kantor] : $request->kantor;
        $kantor = Kantor::whereIn('id', $kntr)->get();
        $unitArray = $kantor->pluck('unit')->toArray();
        $kntrString = implode(', ', $unitArray);
        $bulawal = 01; 
        $bulterkahir = $bulan ;
        if ($request->bln != '') {
            $bulawal = reset($bln);
            $bulterkahir = end($bln);
        }
        
        
        if($request->tombol == 'xls'){
          
                $response =  Excel::download(new BuktiSetorZakatExport($request),'Bukti Setor '. $jenis .' Kantor '. $kntrString .' Periode Tahun '. $thn .' Bulan '. $bulawal .' Sampai '. $bulterkahir.'.xlsx');
        }else{
          
                $response =  Excel::download(new BuktiSetorZakatExport($request),'Bukti Setor '. $jenis .' Kantor '. $kntrString .' Periode Tahun '. $thn .' Bulan '. $bulawal .' Sampai '. $bulterkahir.'.csv');

        }
        ob_end_clean();
        
        return $response;
    }
    
}
