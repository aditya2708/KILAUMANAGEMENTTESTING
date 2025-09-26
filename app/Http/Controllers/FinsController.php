<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Donatur;
use App\Models\Program;
use App\Models\Prog;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\User;
use App\Models\GrupCOA;
use App\Models\SaldoAw;
use App\Models\Penutupan;
use App\Models\Penerimaan;
use App\Models\Pengeluaran;
use App\Models\C_advance;
use App\Models\Anggaran;
use App\Models\Transaksi;
use App\Models\SumberDana;
use App\Models\Tunjangan;
use App\Models\Jurnal;

use App\Exports\ResumeanggaranExport;
use App\Exports\KasbankExport;
use App\Exports\AnggaranExport;
use App\Exports\downloadformat;
use App\Exports\DPExport;

use DataTables;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

use Staudenmeir\LaravelCte;



use App\Imports\c_advanceImport;
use App\Imports\AnggaranImport;
// use Maatwebsite\Excel\Facades\Excel;
use Excel;

class FinsController extends Controller
{

     public function KBexport(Request $request)
    {
    
         $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
         $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
         $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
         
        $kntr = $request->kntr != '' ? "id_kantor = $request->kntr" : "id_kantor != ''";  
        // $grup = $request->grup ==  ? ''
        $response = Excel::download(new KasbankExport($kntr,$a), 'kas-bank.xlsx');
        ob_end_clean();
        return $response;
    }
            public function downloadformat(Request $request)
    {

        $response = Excel::download(new downloadformat(), 'DownloadFormatAnggaran.xlsx');
        ob_end_clean();
        return $response;
    }
    
    //         public function import(Request $request)
    // {
    //     return($request);
    //     $file = $request->file('file');
    //     $file->getSize();
    //     $nama = $file->getClientOriginalName();
    //     $file->move('excel',$file->getClientOriginalName());
    //     Excel::import(new c_advanceImport,('/home/kilauindonesia/public_html/kilau/excel/' . $nama));
    //     return view('fins-budget.approve-anggaran');
    // }
    
    public function import(Request $request)
    {

        $file = $request->file('file');
        $nama = $file->getClientOriginalName();
        $file->move('excel', $nama);

        Excel::import(new AnggaranImport, ('/home/kilauindonesia/public_html/kilau/excel/' . $nama));
        \LogActivity::addToLog(Auth::user()->name . ' Mengimport Data Anggaran');

        return redirect('approve-anggaran');
    }
    
    

     function resumedanapengelola(Request $request)
    {
        $sumber = SumberDana ::all();
        
        // $k = Auth::user()->id_kantor;
        // $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        // if(Auth::user()->level == 'admin'){
        //      $kantor = Kantor::all();
        // }elseif(Auth::user()->level == 'kacab'){
        //     if($kan == null){
        //         $kantor = Kantor::whereRaw("id = $k")->get();
        //     }else{
        //         $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
        //     }
        // }
        
        $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
          $bank = COA::whereRaw("grup = 4 AND id_kantor = '$kz'")->get();
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        
        
        if($request->ajax())
                {
            $sumber = $request->sdana != '' ? "sumber_dana.id_sumber_dana = $request->sdana" : "sumber_dana.id_sumber_dana != ''"; 
            $kntr = $request->kntr != '' ? "transaksi.id_kantor = $request->kntr" : "transaksi.id_kantor != ''";  
            // $jenis = $request->jenis != '' ? "transaksi.pembayaran = $request->jenis" : "transaksi.pembayaran != ''";  
             $jenis = $request->jenis;
             $dpdari = $request->dpdari != '' ? $request->dpdari : 0;
              $dpsampai = $request->dpsampai != '' ? $request->dpsampai : 100;
            // $kntr = $request->kntr != '' ? "transaksi.id_kantor = ''" : "transaksi.id_kantor = '$request->kntr'";
            // $sumber = $request->sdana == '' ? "sumber_dana.id_sumber_dana = '$request->sdana'" : "sumber_dana.id_sumber_dana = ''";
            
            $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
            $tgl_akhir = $request->sampai != '' ? $request->sampai : $tgl_awal;
            
            if ($request->month != '') {
                $tgl = explode('/', $request->month);
                $b = $tgl[0];
                $t = $tgl[1];
            }else{
                $b = date('m');
                $t = date('Y');
            }
            
            if($request->periode == 'hari'){
                if($jenis == 'cash'){
                                $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,transaksi.qty,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran != 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'" )
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else if($jenis == 'noncash'){
                    $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran = 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'  ")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else{
                      $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp,transaksi.jumlah,transaksi.subprogram,
                                  SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                  SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                  COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls
                                 ")
                                ->where('prog.parent', 'n')
                                ->whereRaw("$kntr AND $sumber AND prog.parent = 'n' AND transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }
            }else if($request->periode == 'bulan'){
                  if($jenis == 'cash'){
                                $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,transaksi.qty,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran != 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else if($jenis == 'noncash'){
                    $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran = 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1' ")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else{
                      $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp,transaksi.jumlah,transaksi.subprogram,
                                  SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                  SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                  COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls
                                 ")
                                ->where('prog.parent', 'n')
                                ->whereRaw("$kntr AND $sumber AND prog.parent = 'n' AND transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1' ")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }
            }    
          
            
            
        return DataTables::of($datas)
            // ->addColumn('tot', function($datas){
            //  $dt = '';
            //         $jum = $datas->jumlah ;
            //         // $hitung = COUNT(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval == '1'  , transaksi.jumlah > 0) 
            //         //  $dt = number_format ($d[], 0, ',', '.');  
            //          $dt = number_format ( $jum, 0, ',', '.');
            //          return $dt;
            // })
            
          
            //  ->rawColumns(['tot'])
            ->make(true);
      
      }
    
    //     {
    //          $sumber = $request->sdana != '' ? "sumber_dana.id_sumber_dana = $request->sdana" : "sumber_dana.id_sumber_dana != ''"; 
    //         $kntr = $request->kntr != '' ? "transaksi.id_kantor = $request->kntr" : "transaksi.id_kantor != ''";  
    //         // $jenis = $request->jenis != '' ? "transaksi.pembayaran = $request->jenis" : "transaksi.pembayaran != ''";  
    //          $jenis = $request->jenis;
    //          $dpdari = $request->dpdari != '' ? $request->dpdari : 0;
    //           $dpsampai = $request->dpsampai != '' ? $request->dpsampai : 100;
    //         // $kntr = $request->kntr != '' ? "transaksi.id_kantor = ''" : "transaksi.id_kantor = '$request->kntr'";
    //         // $sumber = $request->sdana == '' ? "sumber_dana.id_sumber_dana = '$request->sdana'" : "sumber_dana.id_sumber_dana = ''";
            
    //         $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
    //         $tgl_akhir = $request->sampai != '' ? $request->sampai : $tgl_awal;
            
    //         if($request->jenis == 'cash'){
    //                         $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
    //                         ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
    //                         ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,transaksi.qty,
    //                         SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
    //                         COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
    //                         // ->where('prog.parent', 'n')
    //                          ->whereRaw("transaksi.pembayaran != 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
    //                         ->groupBy('prog.id_program', 'prog.program')
    //                         ->orderBy('transaksi.id', 'DESC')
    //                         // ->orderBy('jmls', 'ASC')
    //                         ->get();
    //         }else if($request->jenis == 'noncash'){
    //             $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
    //                         ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
    //                         ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,
    //                         SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
    //                         COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
    //                         // ->where('prog.parent', 'n')
    //                          ->whereRaw("transaksi.pembayaran = 'noncash' AND $kntr AND $sumber AND prog.parent = 'n' AND  transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
    //                         // ->where('jmls' >= 0)
    //                         ->groupBy('prog.id_program', 'prog.program')
    //                         ->orderBy('transaksi.id', 'DESC')
    //                         ->get();
    //         }else{
    //               $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
    //                         ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
    //                         ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp,
    //                         SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
    //                         COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
    //                         ->where('prog.parent', 'n')
    //                         ->whereRaw("$kntr AND $sumber AND prog.parent = 'n' AND transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
    //                         ->groupBy('prog.id_program', 'prog.program')
    //                         ->orderBy('transaksi.id', 'DESC')
    //                         ->get();
    //         }


    //     return DataTables::of($datas)
    //         ->addColumn('tot', function($datas){
    //          $dt = '';
    //                  $dt = number_format ($datas->dp/100 * $datas->jumlah, 0, ',', '.');  
    //                  return $dt;
    //         })
    //          ->rawColumns(['tot'])
    //         ->make(true);
      
    //   }
             return view('fins-budget.resume_dana_pengelola',compact('kantor','sumber'));
             
    }
     function danapengelola_export(Request $request)
    {
        if($request->tombol == 'xls'){
            $r = Excel::download(new DPExport($request), 'Resume Dana Pengelola.xlsx');
        }else{
            $r = Excel::download(new DPExport($request), 'Resume Dana Pengelola.csv');
        }
        ob_end_clean();
        return $r;
    }



     function total(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k ")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
        if($request->ajax())
        {
           
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";

            
            $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $waduw = $request->daterange != '' ? "DATE(sub.tanggal) >= '$dari' AND DATE(sub.tanggal) <= '$sampai'" : "DATE(sub.tanggal) >= '$now' AND DATE(sub.tanggal) <= '$now'";
            
           
     if($request->waktu == 'realtime' ){
          
            $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
            ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
                
            $datas = COA::selectRaw("coa.*,tambahan.unit,SUM(sub.jumlah) as debet,SUM(sub.nominal) as kredit , sub.tanggal")
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$stts AND $kntr AND (grup = 3 OR grup = 4) AND parent = 'n' ")
            ->get();
            
        }else if( $request->stts == 'aktif' ){
          
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
                ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
            $datas = saldoaw::selectRaw("saldo_awal.*,coa.id_kantor,coa.nama_coa,coa.coa,tambahan.unit,sub.tanggal")
            ->leftjoin('coa','coa.coa','=','saldo_awal.coa')
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'saldo_awal.coa')
                                   ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$stts AND $kntr AND (grup = 3 OR grup = 4) AND parent = 'n' ")
            ->get();
         
        }else{
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
                ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
            $datas = saldoaw::selectRaw("saldo_awal.*,coa.id_kantor,coa.nama_coa,coa.coa,tambahan.unit,sub.tanggal")
            ->leftjoin('coa','coa.coa','=','saldo_awal.coa')
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'saldo_awal.coa')
                                   ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$kntr AND (grup = 3 OR grup = 4) AND parent = 'n' ")
            ->get();
        }
       
                
        }
             return $datas;
             
    }
    
    
    function totalpengeluaran(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
        if($request->ajax())
         {
           
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";
            $kntr = $request->kntr != '' ? "pengeluaran.kantor = '$request->kntr'" : "pengeluaran.kantor IS NOT NULL";
            // $kntr = $request->kntr == '' ? "kantor = ''" : "kantor = '$request->kntr'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $waduw = $request->daterange != '' ? "DATE(sub.tanggal) >= '$dari' AND DATE(sub.tanggal) <= '$sampai'" : "DATE(sub.tanggal) >= '$now' AND DATE(sub.tanggal) <= '$now'";
            
           
            $datas = Pengeluaran::whereRaw("DATE(pengeluaran.tgl) = '$a' AND $kntr AND acc ='1' " )
            ->get();

           

        }
             return $datas;
             
    }
    
    
     function totalcash(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
        if($request->ajax())
         {
           
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";

            
            $kntr = $request->kntr == '' ? "id_kantor = ''" : "id_kantor = '$request->kntr'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $waduw = $request->daterange != '' ? "DATE(sub.tanggal) >= '$dari' AND DATE(sub.tanggal) <= '$sampai'" : "DATE(sub.tanggal) >= '$now' AND DATE(sub.tanggal) <= '$now'";
            
           
        if($request->kntr == '' ){
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
            ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
                
            $datas = COA::selectRaw("coa.*,tambahan.unit,SUM(sub.jumlah) as debet,SUM(sub.nominal) as kredit , sub.tanggal")
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw(" grup = 3 AND parent = 'n' ")
            ->get();
            
        }else{
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
            ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
                
            $datas = COA::selectRaw("coa.*,tambahan.unit,SUM(sub.jumlah) as debet,SUM(sub.nominal) as kredit , sub.tanggal")
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$kntr AND grup = 3 AND parent = 'n' ")
            ->get();
        }
           

        }
             return $datas;
             
    }
    
         function totalbank(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
        if($request->ajax())
        {
           
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";

            
            $kntr = $request->kntr == '' ? "id_kantor = ''" : "id_kantor = '$request->kntr'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $waduw = $request->daterange != '' ? "DATE(sub.tanggal) >= '$dari' AND DATE(sub.tanggal) <= '$sampai'" : "DATE(sub.tanggal) >= '$now' AND DATE(sub.tanggal) <= '$now'";
            
           
        if($request->kntr == '' ){
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
            ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
                
            $datas = COA::selectRaw("coa.*,tambahan.unit,SUM(sub.jumlah) as debet,SUM(sub.nominal) as kredit , sub.tanggal")
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw(" grup = 4 AND parent = 'n' ")
            ->get();
            
        }else{
              $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
            ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
                
            $datas = COA::selectRaw("coa.*,tambahan.unit,SUM(sub.jumlah) as debet,SUM(sub.nominal) as kredit , sub.tanggal")
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($a,$hari,$bulan,$tahun) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$a'");
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$kntr AND grup = 4 AND parent = 'n' ")
            ->get();
        }
           

        }
                return($datas);
            //  return view('fins-home.kas_bank', compact('bank','kantor'));
             
    }
    
    
         function totalpenerimaan(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
         if($request->ajax())
         {
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";
            $kntr = $request->kntr != '' ? "transaksi.id_kantor = '$request->kntr'" : "transaksi.id_kantor IS NOT NULL";
            // $kntr = $request->kntr == '' ? "kantor = ''" : "kantor = '$request->kntr'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";

           
            $datas = Transaksi::whereRaw("DATE(transaksi.tanggal) = '$a' AND $kntr AND approval ='1' " )
            ->get();

           

        }
             return $datas;
        
             
    }
    
    
    
    
     function totalpengajuan(Request $request)
    {
        
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::all();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        
        if($request->ajax())
        {
           
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            $now = date('Y-m-d');
            if($request->daterange != ''){
                $begin = new DateTime($dari); 
                $end = new DateTime($sampai);
            }else{
                $begin = new DateTime($now); 
                $end = new DateTime($now);
            }
            $cek = $request->waktu;
        
            $a = $request->blns == '' ? Carbon::now()->format('Y-m-d') : $request->blns;
            $hari = Carbon::createFromFormat('Y-m-d', $a)->format('d');
            $bulan = Carbon::createFromFormat('Y-m-d', $a)->format('m');
            $tahun = Carbon::createFromFormat('Y-m-d', $a)->format('Y');
            
            
            $stts = $request->stts == 'aktif' ? "aktif = 'y'" : "aktif = 'n'";

            $kntr = $request->kntr != '' ? "anggaran.kantor = '$request->kntr'" : "anggaran.kantor IS NOT NULL";
            // $kntr = $request->kntr == '' ? "kantor = ''" : "kantor = '$request->kntr'";
            $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $waduw = $request->daterange != '' ? "DATE(sub.tanggal) >= '$dari' AND DATE(sub.tanggal) <= '$sampai'" : "DATE(sub.tanggal) >= '$now' AND DATE(sub.tanggal) <= '$now'";
            
           $datas = [];
                $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name ")
                 ->whereRaw("acc = '1'AND $kntr AND DATE(anggaran.tanggal) = '$a' ")
                //  ->whereRaw("$kntr AND $stts AND  DATE(anggaran.tanggal) = '$a' DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' AND acc = '1' ")
                 ->get();
          
        }
                return($datas);
        
             
    }
    
    
    function kas_bank(Request $request)
    {
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
        $bank = COA::whereRaw("grup LIKE '%4%' AND id_kantor = '$k'")->get();
        
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->where('id_com', Auth::user()->id_com)->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->where('id_com', Auth::user()->id_com)->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR kantor_induk = $kan->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        if($request->ajax())
        {
            $r_foot = $request->foot;
            $r_tgl  = $request->blns != '' ? $request->blns : date('Y-m-d');
            $r_stat = $request->stts;
            $r_wak  = $request->waktu;
            $r_kan  = $request->kntr;
            $tgsal  = date("Y-m-t", strtotime("-1 month", strtotime($r_tgl)));
            $awals  = date('Y-m-01', strtotime($r_tgl));
            $tgwal  = date('d', strtotime($r_tgl));
            if($tgwal == '01'){
                $akhirs = date('Y-m-01', strtotime($r_tgl));
            }else{
                $akhirs = date('Y-m-d', strtotime('-1 day',strtotime($r_tgl)));
            }
            
            $seljurn  = "coa_use AS cd, SUM(nominal_debit) AS debit, SUM(nominal_kredit) AS kredit";
            $seltran  = "coa_debet AS cd, SUM(jumlah) AS debit, 0 AS kredit";
            $selpeng  = "coa_kredit AS cd, 0 AS debit, SUM(nominal) AS kredit";
            
            $selgrup  = "cd";
            
            $getcoa   = COA::whereRaw("(grup LIKE '%4%' OR grup LIKE '%3%') AND id_kantor = $r_kan AND aktif = '$r_stat'");
            $arr_coa  = $getcoa->pluck('coa')->toArray();
            
            if($r_wak == 'realtime'){
                for($x = 0; $x < 2; $x++){
                    $get_jurn   = DB::table('jurnal')
                                ->selectRaw($seljurn)
                                ->where(function($q) use ($r_tgl, $x, $awals, $akhirs) {
                                    if($x == 0){
                                        $q->whereDate('tanggal', $r_tgl);
                                    }else{
                                        $q->whereDate('tanggal', '>=', $awals)->whereDate('tanggal', '<=', $akhirs);
                                    }
                                })
                                ->whereIn('coa_use', $arr_coa)
                                ->groupByRaw($selgrup)
                                ;
                                
                    $get_trans  = DB::table('transaksi')
                                ->selectRaw($seltran)
                                ->where(function($q) use ($r_tgl, $x, $awals, $akhirs) {
                                    if($x == 0){
                                        $q->whereDate('tanggal', $r_tgl);
                                    }else{
                                        $q->whereDate('tanggal', '>=', $awals)->whereDate('tanggal', '<=', $akhirs);
                                    }
                                })
                                ->where('approval', 1)
                                ->whereIn('coa_debet', $arr_coa)
                                ->groupByRaw($selgrup)
                                ;
                                
                    $get_peng   = DB::table('pengeluaran')
                                ->selectRaw("$selpeng")
                                ->where(function($q) use ($r_tgl, $x, $awals, $akhirs) {
                                    if($x == 0){
                                        $q->whereDate('tgl', $r_tgl);
                                    }else{
                                        $q->whereDate('tgl', '>=', $awals)->whereDate('tgl', '<=', $akhirs);
                                    }
                                })
                                ->where('acc', 1)
                                ->whereIn('coa_kredit', $arr_coa)
                                ->groupByRaw($selgrup)
                                
                                ;
                    if($x == 0){
                        $dattab = $get_trans->unionAll($get_peng)->unionAll($get_jurn)->get();
                    }else{
                        $datsal = $get_trans->unionAll($get_peng)->unionAll($get_jurn)->get();
                    }
                }
                
                $dattab->transform(function($i){ return (array)$i; });
                $tabray = $dattab->toArray();
                
                $datsal->transform(function($i){ return (array)$i; });
                $salray = $datsal->toArray();
            }
            
            $listcoa = $getcoa->select('coa.*', 'tambahan.unit')->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')->get();
            $data = [];
            $f_salwal   = 0;
            $f_totdeb   = 0;
            $f_totkre   = 0;
            $f_salkhir  = 0;
            $t_kas      = 0;
            $t_bank     = 0;
            foreach($listcoa as $i => $val){
                $pencoa = Penutupan::where('coa_pen', $val->coa)->latest('tanggal')->first();
                
                if($r_wak == 'realtime'){
                    $getsal = SaldoAw::where('coa', $val->coa)->whereDate('bulan', $tgsal)->first();
                    $tsal   = $getsal != null ? $getsal->saldo_awal : 0;
                    
                    $filtab = array_filter($tabray, function ($item) use($val) {
                        return $item['cd'] == $val->coa; 
                    });
                    $totdeb = array_sum(array_column($filtab, 'debit'));
                    $totkre = array_sum(array_column($filtab, 'kredit'));
                    
                    $filsal = array_filter($salray, function ($item) use($val) {
                        return $item['cd'] == $val->coa; 
                    });
                    $saldeb = array_sum(array_column($filsal, 'debit'));
                    $salkre = array_sum(array_column($filsal, 'kredit'));
                    
                    if($tgwal != '01'){
                        $salwal = $tsal + $saldeb - $salkre;
                    }else{
                        $salwal = $tsal;
                    }
                    
                    $salkhir = $salwal + $totdeb - $totkre;
                }else{
                    $getpen = Penutupan::where('coa_pen', $val->coa)->whereDate('tanggal', $r_tgl)->first();
                    if($getpen != null){
                        $salwal     = $getpen->saldo_awal;
                        $totdeb     = $getpen->debit;
                        $totkre     = $getpen->kredit;
                        $salkhir    = $getpen->saldo_akhir;
                    }else{
                        $salwal     = 0;
                        $totdeb     = 0;
                        $totkre     = 0;
                        $salkhir    = 0;
                    }
                }
                
                if($r_foot != 1){
                    $data[] = [
                                'coa'       => $val->coa,
                                'nama_coa'  => $val->nama_coa, 
                                'awal'      => number_format($salwal, 0, ',', '.' ),
                                'deb'       => number_format($totdeb, 0, ',', '.' ), 
                                'kred'      => number_format($totkre, 0, ',', '.' ), 
                                'tot'       => number_format($salkhir, 0, ',', '.' ),
                                'unit'      => $val->unit,
                                'tgl'       => $pencoa != null ? date('Y-m-d', strtotime($pencoa->tanggal)) : ''
                                ];
                }else{
                    if(in_array(3, [$val->grup])){ $t_kas   += $salkhir; }
                    if(in_array(4, [$val->grup])){ $t_bank  += $salkhir; }
                    $f_salwal   += $salwal;
                    $f_totdeb   += $totdeb;
                    $f_totkre   += $totkre;
                    $f_salkhir  += $salkhir;  
                }
            }
            
            if($r_foot != 1){
                return DataTables::of($data)
                    ->make(true);
            }else{
                $footdat = [
                            'salwal'    => $f_salwal,
                            'totdeb'    => $f_totdeb,
                            'totkre'    => $f_totkre,
                            'salkhir'   => $f_salkhir,
                            't_kas'     => $t_kas,
                            't_bank'    => $t_bank,
                            ];
                return($footdat);
            }
                    
        }
        
        return view('fins-home.kas_bank', compact('bank','kantor'));

    }
    
//      function kas_bank(Request $request)
//   {
            
//             if ($request->daterange != '') {
//                 $tgl = explode(' - ', $request->daterange);
//                 $dari = date('Y-m-d', strtotime($tgl[0]));
//                 $sampai = date('Y-m-d', strtotime($tgl[1]));
//             }
    
//             $now = date('Y-m-d');
            
//             if($request->daterange != ''){
//                 $begin = new DateTime($dari); 
//                 $end = new DateTime($sampai);
//             }else{
//                 $begin = new DateTime($now); 
//                 $end = new DateTime($now);
//             }  
            
//             $waduw = $request->daterange != '' ? "DATE(penutupan.created_at) >= '$dari' AND DATE(penutupan.created_at) <= '$sampai'" : "DATE(penutupan.created_at) >= '$now' AND DATE(penutupan.created_at) <= '$now'";
            
//             $datas = COA::selectRaw("coa.*, penutupan.*, penutupan.created_at as pawon, '$request->pen' as p")->leftJoin('penutupan','penutupan.coa_pen','=','coa.coa')
//             ->whereRaw("id_kantor = '$request->kntr' AND (grup = 3 OR grup = 4) AND parent = 'n' ")->get();

//             for($i = $begin; $i <= $end; $i->modify('+1 day')){
//                 foreach($datas as $d){
//                     $c_tgl = $d->pawon != '' ? date('Y-m-d',strtotime($d->pawon)) : '';
                    
//                     if($i->format("Y-m-d") == $c_tgl){
//                         $saldo_akhir = $d->saldo_akhir;
//                         $saldo_awal = $d->saldo_awal;
//                         $debit = $d->debit;
//                         $kredit = $d->kredit;
//                         $adjustment = $d->adjustment;
//                         $user_input = $d->user_input;
//                         $user_update = $d->user_update;
//                         $k100000 = $d->k100000;
//                         $k75000 = $d->k75000;
//                         $k50000 = $d->k50000;
//                         $k20000 = $d->k20000;
//                         $k10000 = $d->k10000;
//                         $k5000 = $d->k5000;
//                         $k2000 = $d->k2000;
//                         $k1000 = $d->k1000;
//                         $k500 = $d->k500;
//                         $k100 = $d->k100;
//                         $l1000 = $d->l1000;
//                         $l500 = $d->l500;
//                         $l200 = $d->l200;
//                         $l100 = $d->l100;
//                         $l50 = $d->l50;
//                         $l25 = $d->l25;
//                     }else {
//                         $saldo_akhir = '';
//                         $saldo_awal = '';
//                         $debit = '';
//                         $kredit = '';
//                         $adjustment = '';
//                         $user_input = '';
//                         $user_update = '';
//                         $k100000 = '';
//                         $k75000 = '';
//                         $k50000 = '';
//                         $k20000 = '';
//                         $k10000 = '';
//                         $k5000 = '';
//                         $k2000 = '';
//                         $k1000 = '';
//                         $k500 = '';
//                         $k100 = '';
//                         $l1000 = '';
//                         $l500 = '';
//                         $l200 = '';
//                         $l100 = '';
//                         $l50 = '';
//                         $l25 = '';
//                     }
    
//                 $data[]  = ['tanggal' => $i->format("Y-m-d"),
//                             'coa' => $d->coa,
//                             'id_kantor' => $d->id_kantor,
//                             'nama_coa' => $d->nama_coa,
//                             'saldo_akhir' => $saldo_akhir,
//                             'saldo_awal' => $saldo_awal ,
//                             'debit' => $debit,
//                             'kredit' => $kredit,
//                             'adjustment' => $adjustment,
//                             'user_input' => $user_input,
//                             'user_update' => $user_update,
//                             'k100000' =>  $k100000 ,
//                             'k75000' => $k75000,
//                             'k50000' => $k50000,
//                             'k20000' => $k20000,
//                             'k10000' => $k10000,
//                             'k5000' => $k5000,
//                             'k2000' => $k2000,
//                             'k1000' => $k1000,
//                             'k500' => $k500,
//                             'k100' => $k100,
//                             'l1000' => $l1000,
//                             'l500' => $l500,
//                             'l200' => $l200,
//                             'l100' => $l100,
//                             'l50' => $l50,
//                             'l25' => $l25,
//                             'grup' => $d->grup,
//                             'p' => $d->p
//                             ];
//                 }
//             }
           
//             return DataTables::of($datas)
//             // ->addColumn('aksi', function($data){
//             //     // return($data);
//             //     $dat = array($data['grup']);
//             //     if(in_array('4',$dat)){
//             //         $button = '<a href="javascript:void(0)" style="color:#1e0fbe" data-bs-toggle="modal" data-bs-target="#modal_aja" class="getdong" id="'.$data['coa'].'">BO</a>';
//             //     }else{
//             //         $button = '<a href="javascript:void(0)" style="color:#1e0fbe" data-bs-toggle="modal" data-bs-target="#modals" class="getdongs" id="'.$data['coa'].'">CO</a>';
//             //     }
//             //     return $button;
//             // })
//             // ->addColumn('tgl', function($data){
//             //     if($data['p'] == 'tanggal'){
//             //         $tanggal = $data['tanggal'];
//             //     }else if($data['p'] == 'bulan'){
//             //         $tanggal = date('Y-m-t');
//             //     }else if($data['p'] == 'tahun'){
//             //         $last_year_last_month_date = date("Y-12-01", strtotime("-1 year"));
//             //         $end_date = date("Y-12-t", strtotime($last_year_last_month_date));
//             //         $tanggal = $end_date;
//             //     }
                
//             //     return $tanggal;
//             // })
//             ->rawColumns(['aksi'])
//             ->make(true);
//         }
      function saldo_dana(Request $request)
    {
         if($request->ajax()){
        $datas = COA::selectRaw("coa.*, penutupan.*, penutupan.created_at as pawon, '$request->pen' as p ,tambahan.unit")->leftJoin('penutupan','penutupan.coa_pen','=','coa.coa')
            ->leftJoin('tambahan','tambahan.id_coa','=','coa.coa')
            // ->whereRaw("$kntr")
            ->whereRaw("grup = 6 ");
           
            
             return DataTables::of($datas)
            ->make(true);
    }
        return view('fins-home.saldo_dana');

    }
    
    
          function pengajuan_ca(Request $request)
          
           {
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
         $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        $namcoa = COA::whereRaw("grup = 6")->get();
        $jabat = Jabatan::all();
        
        // $dari = $request->dari == '' ? "tanggal != ''" : "tanggal = $request->dari : date('Y-m-d')" ;
        // $sampai = $request->sampai == '' ?"tanggal != ''" :"tanggal = $request->sampai : $dari" ;
        
      
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        
        if($request->ajax())
        {
            
        //  if ($request->stts == '') {
        //     $stts = "anggaran.acc IS NOT NULL";
        // } else if ($request->stts == 2) {
        //     $stts = "anggaran.acc = 2";
        // } else if ($request->stts == 1) {
        //     $stts = "anggaran.acc = 1";
        // } else if ($request->stts == 0) {
        //     $stts = "anggaran.acc = 0";
        // }
        
            // $kntr = $request->kntr == '' ? "id_kantor != 'IS NOT NULL'" : "id_kantor = '$request->kntr'";
            // $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            // $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            // $sampai = $request->sampai != '' ? $request->sampai : $dari;
            
            
            
            // $tgls = $request->daterange != '' ? "DATE(anggaran.created_at) >= '$dari' AND DATE(anggaran.created_at) <= '$sampai'" : "DATE(anggaran.created_at) >= '$now' AND DATE(anggaran.created_at) <= '$now'" ;

            $datas = C_advance::leftJoin('tambahan','tambahan.id','=','c_advance.id_kantor')
                ->selectRaw("c_advance.*, tambahan.unit")
                // ->whereRaw("$kntr AND $stts AND DATE_FORMAT(tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '$sampai'")
                // ->whereRaw("$kntr AND $stts")
                ->get();
                
            return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('apr', function($datas){
                if($datas->acc == 1){
                $button = '<label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                    }elseif($datas->acc == 0){
                    $button = '<label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-ban"></i></label>';
                        }else{
                            $button = '<label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-stream"></i></label>';
                        }
                
                return $button;
            })
            
            ->rawColumns(['apr'])
            ->make(true);
        }
        return view('fins-budget.pengajuan_ca', compact('bank','kantor','namcoa','jabat','saldo'));

    }
          
          
  

      public function pengajuanBy(Request $request, $id){
        $data['ui'] = Anggaran::join('users','users.id', '=', 'anggaran.user_input')->join('tambahan','tambahan.id', '=', 'anggaran.kantor')->selectRaw("users.name, anggaran.*,tambahan.unit,
        SUM(anggaran.anggaran + anggaran.tambahan + anggaran.relokasi) as total
        "
        )->where('anggaran.id_anggaran', $id)->first();
        // $data['ua'] = Anggaran::join('users','users.id', '=', 'anggaran.user_approve')->join('tambahan','tambahan.id', '=', 'anggaran.kantor')->select('users.name', 'anggaran.*','tambahan.unit')->where('anggaran.id_anggaran', $id)->first();
        // $data['ini'] = Anggaran::selectRaw("SUM(anggaran.anggaran + anggaran.tambahan)as total")->first();
        
        return $data;
    }
    
    public function aksipengajuan(Request $request)
    {
        
        // return $request;
        $p = Anggaran::findOrFail($request->id);
    
        // if(Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->keuangan == 'admin'){
        //     if($request->aksi == 'acc' ){
        //         Anggaran::where('id_anggaran', $request->id)->update([
        //             'user_approve' => Auth::user()->id,
        //             'user_updated' => Auth::user()->id,
        //         ]);
        //         \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data Pengajuan Anggaran ' . $p->jenis_transaksi);
        //     }else{
               
        //         Anggaran::where('id_anggaran', $request->id)->update([
        //             'acc' => 0,
        //             'user_reject' => Auth::user()->id,
        //             'alasan' => $request->ket,
        //             'user_updated' => Auth::user()->id,
        //         ]);
        //         \LogActivity::addToLog(Auth::user()->name . ' Rejected Data Pengajuan Anggaran ' . $p->jenis_transaksi);
        //     }
        
        // }else 
        if($request->aksi == 'acc' ){
            Anggaran::where('id_anggaran', $request->id)->update([
                'acc' => 1,
                'user_approve2' => Auth::user()->id,
                'user_updated' => Auth::user()->id,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data Pengajuan Anggaran ' . $p->jenis_transaksi);
        
        }else{
            Anggaran::where('id_anggaran', $request->id)->update([
                'acc' => 0,
                'user_reject' => Auth::user()->id,
                'alasan' => $request->ket,
                'user_updated' => Auth::user()->id,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Rejected Data Pengajuan Anggaran ' . $p->jenis_transaksi);
        }

        
        return response()->json(['success' => 'Data is successfully updated']);
    }
       public function editaggaran(Request $request)
    {
        $p = Anggaran::findOrFail($request->id);
    if($request->jenis == 'edit'){
        Anggaran::where('id_anggaran', $request->id)->update([
                'anggaran' => $request->nominal != '' ? preg_replace("/[^0-9]/", "", $request->nominal) : 0, 
                'keterangan' =>$request->ket,
                'tanggal' => $request->edittgl,

            ]);
                    \LogActivity::addToLog(Auth::user()->name . ' edit Data Pengajuan Anggaran nominal ');
                    return response()->json(['success' => 'Data is successfully updated']);
    }else{
                Anggaran::where('id_anggaran', $request->id)->update([
                'tambahan' => $request->nominal != '' ? preg_replace("/[^0-9]/", "", $request->nominal) : 0,
                 'keterangan' => $request->ket,
            ]);
    \LogActivity::addToLog(Auth::user()->name . ' edit Data Pengajuan Anggaran nominal ');

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
}
   public function update_waktupemgajuan(Request $request)
    {
 
     Tunjangan::where('id_tj', 1)->update([
            'min_anggaran' => $request->hari,
               
            ]);
           \LogActivity::addToLog(Auth::user()->name . ' Mengubah Data Minimal Waktu Anggaran, Oleh '. $request->id);
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    
    
    public function editspengajuan(Request $request)
    {
          if($request->jeniss == 'relokasi'){
                Anggaran::where('id_anggaran', $request->id)->update([
                'ket_rek' => $request->ket,
                'relokasi' => $request->nominal!= '' ? preg_replace("/[^0-9]/", "", $request->nominal) : 0
            ]);
            
             }else if ($request->jeniss == 'edit'){
                Anggaran::where('id_anggaran', $request->id)->update([
                'anggaran' => $request->nominal != '' ? preg_replace("/[^0-9]/", "", $request->nominal) : 0,
                 'keterangan' => $request->ket,
            ]);
            }else{
                 Anggaran::where('id_anggaran', $request->id)->update([
                    'tambahan' => $request->nominal != '' ? preg_replace("/[^0-9]/", "", $request->nominal) : 0,
                 'keterangan' => $request->ket,
                 ]);
            }
        if($request->jeniss == 'relokasi'){
             Anggaran::where('id_anggaran', $request->z)->update([
                'relokasi' => $request->nominal!= '' ? - preg_replace("/[^0-9]/", "", $request->nominal) : 0
                ]);
        }
    
    
            \LogActivity::addToLog(Auth::user()->name . ' Mengubah Data Pengajuan Anggaran, dengan id'. $request->id);
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    
    
    
     public function acc_all(Request $request)
    {
        $id_com = Auth::user()->id_com;
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $status = DB::table('request')->select('status')->distinct()->get();
        
        // if($request->tglrange != '') {
        //         $tgl = explode(' - ', $request->tglrange);
        //         $dari = date('Y-m-d', strtotime($tgl[0]));
        //         $sampai = date('Y-m-d', strtotime($tgl[1]));
        //     }

            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(anggaran.created_at) >= '$dari' AND DATE(anggaran.created_at) <= '$sampai'" : "DATE(anggaran.created_at) IS NOT NULL AND DATE(anggaran.created_at) IS NOT NULL" ;
            $jabat = $request->jabatan != '' ? "request.id_jabatan = '$request->jabatan'" : "request.id_jabatan IS NOT NULL";
            $kntr = $request->kntr != '' ? "kantor = '$request->kntr'" : "kantor IS NOT NULL";
            // $stts = $request->status != '' ? "anggaran.status = '$request->status'" : "anggaran.status IS NOT NULL";
            $kett = $request->kett != '' ? "anggaran.acc = '$request->kett'" : "anggaran.acc IS NOT NULL";
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            
            // $kntr = Auth::user()->id_kantor;
            // $k = Kantor::where('kantor_induk', $kntr)->first();
        
        
          {
        $id_com = Auth::user()->id_com;
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $status = DB::table('request')->select('status')->distinct()->get();
        
        // if($request->tglrange != '') {
        //         $tgl = explode(' - ', $request->tglrange);
        //         $dari = date('Y-m-d', strtotime($tgl[0]));
        //         $sampai = date('Y-m-d', strtotime($tgl[1]));
        //     }

            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(anggaran.created_at) >= '$dari' AND DATE(anggaran.created_at) <= '$sampai'" : "DATE(anggaran.created_at) IS NOT NULL AND DATE(anggaran.created_at) IS NOT NULL" ;
            $jabat = $request->jabatan != '' ? "request.id_jabatan = '$request->jabatan'" : "request.id_jabatan IS NOT NULL";
            $kntr = $request->kntr != '' ? "kantor = '$request->kntr'" : "kantor IS NOT NULL";
            // $stts = $request->status != '' ? "anggaran.status = '$request->status'" : "anggaran.status IS NOT NULL";
            $kett = $request->kett != '' ? "anggaran.acc = '$request->kett'" : "anggaran.acc IS NOT NULL";
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            
            $darib = $request->darib != '' ? $request->darib : date('Y-m');
            $sampaib = $request->sampaib != '' ? $request->sampaib : $darib;
            $periode = $request->periode;
            // $kntr = Auth::user()->id_kantor;
            // $k = Kantor::where('kantor_induk', $kntr)->first();
        
            if($periode == 'harian' || $periode == '' ){
        if(Auth::user()->keuangan == 'keuangan pusat' ){
            
         Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '$sampai' AND acc = 2")
         ->update([               
                'user_approve' => Auth::user()->id,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );
                }else{
            Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '$sampai' AND acc = 2")
             ->update([  
                'acc' => 1,
                'user_approve2' => Auth::user()->id,
                'user_updated' => Auth::user()->id,
                ]);
            \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );

        }
        }else 
        if($periode == 'bulan'){
            if(Auth::user()->keuangan == 'keuangan pusat' ){
         Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m') >= '$darib' AND DATE_FORMAT(tanggal,'%Y-%m') <= '$sampaib' AND acc = 2")
         ->update([               
                'user_approve' => Auth::user()->id,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );
            
                }else{
            Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m') >= '$darib' AND DATE_FORMAT(tanggal,'%Y-%m') <= '$sampaib' AND acc = 2")
             ->update([  
                'acc' => 1,
                'user_approve2' => Auth::user()->id,
                'user_updated' => Auth::user()->id,
                ]);
            \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );

            }
            
    
        }
        
        // else if($periode == 'tahun'){
        //      $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
        //          ->leftjoin('users','users.id','=','anggaran.user_input')
        //          ->selectRaw("anggaran.*, tambahan.unit,users.name ")
        //          ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y') >= '$darit' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampait' ")
        //          ->get();
        // }
        
        
        

        return response()->json(['success' => 'Data is successfully updated']);

    }
        
        
        //  if(Auth::user()->keuangan == '' ){
            
        //  Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '$sampai' AND acc = 2")
        //  ->update([               
        //         'user_approve' => Auth::user()->id,
        //     ]);
        //     \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );
            
        //  }else{
        //     Anggaran::whereRaw("$kntr AND DATE_FORMAT(tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(tanggal,'%Y-%m-%d') <= '$sampai' AND acc = 2")
        //      ->update([  
        //         'acc' => 1,
        //         'user_approve2' => Auth::user()->id,
        //         'user_updated' => Auth::user()->id,
        //         ]);
        //     \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengajuan Anggaran ' );

        // }
        
        return response()->json(['success' => 'Data is successfully updated']);

    }
    
    

        public function min_waktu_pengajuan(){
        $data = Tunjangan::where('id_com',Auth::user()->id_com)->first();
        return $data;
    }
           
           
        function approve_anggaran(Request $request)
     
           {
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        $namcoa = COA::whereRaw("grup = 6")->get();
        $jabat = Jabatan::all();
            
        $wkt = Tunjangan::where('min_anggaran',Auth::user()->id_com)->first();    
        
        // if(Auth::user()->level == 'admin'){
        //      $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        // }elseif(Auth::user()->level == 'kacab'){
        //     if($kan == null){
        //         $kantor = Kantor::whereRaw("id = $k")->get();
        //     }else{
        //         $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
        //     }
        // }
        
         $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
        
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->get();
            }
        }
        
        if($request->ajax())
        {
          
        if ($request->stts == '') {
            $stts = "anggaran.acc IS NOT NULL";
        } else if ($request->stts == 2) {
            $stts = "anggaran.acc = 2";
        } else if ($request->stts == 1) {
            $stts = "anggaran.acc = 1";
        } else if ($request->stts == 0) {
            $stts = "anggaran.acc = 0";
        }
         
        $now = date('Y-m-d');
           $kntr = $request->kntr == '' ? "id_kantor != 'IS NOT NULL'" : "kantor = '$request->kntr'";
            // $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            $darib = $request->darib != '' ? $request->darib : date('Y-m');
            $sampaib = $request->sampaib != '' ? $request->sampaib : $dari;
            $darit = $request->darit != '' ? $request->darit : date('Y');
            $sampait = $request->sampait != '' ? $request->sampai : $dari;
            $periode = $request->periode;
              
        if($periode == 'harian' || $periode == '' ){
             $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
                 ->get();
            // $realisasi = Pengeluaran::whereRaw("id = '$datas->id_anggaran'")->get();     
        }else if($periode == 'bulan'){
          $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name ")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m') >= '$darib' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m') <= '$sampaib' ")
                 ->get();
                
        }else if($periode == 'tahun'){
             $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name ")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y') >= '$darit' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampait' ")
                 ->get();
        }
         
    // if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
    //          $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
    //              ->leftjoin('users','users.id','=','anggaran.user_input')
    //              ->selectRaw("anggaran.*, tambahan.unit,users.name")
    //              ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
    //              ->get();
    //         // $realisasi = Pengeluaran::whereRaw("id = '$datas->id_anggaran'")->get();     
    //     }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
    //       $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
    //              ->leftjoin('users','users.id','=','anggaran.user_input')
    //              ->selectRaw("anggaran.*, tambahan.unit,users.name ")
    //              ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
    //              ->get();
                
    //     }
         
        //   $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
        //          ->leftjoin('users','users.id','=','anggaran.user_input')
        //          ->selectRaw("anggaran.*, tambahan.unit,users.name ")
        //          ->whereRaw("$kntr AND $stts AND  $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
        //          ->get();
            
   
     
            return DataTables::of($datas )
             ->addIndexColumn()
            ->addColumn('apr', function($datas){
                if($datas->acc == 1){
                $button = '<label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                    }elseif($datas->acc == 0){
                    $button = '<label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-ban"></i></label>';
                        }else{
                            $button = '<label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-stream"></i></label>';
                        }
                
                return $button;
            })
            
             ->addColumn('agr', function($datas){
                $jml = number_format($datas->anggaran, 0, ',', '.');
                return $jml;
            })
            
             ->addColumn('rlk', function($datas){
                $jml = number_format($datas->relokasi, 0, ',', '.');
                return $jml;
            })
             ->addColumn('tmbh', function($datas){
                $jml = number_format($datas->tambahan, 0, ',', '.');
                return $jml;
            })
            ->addColumn('real', function($datas){
                 $realisasi = Pengeluaran::whereRaw("id_anggaran = $datas->id_anggaran AND acc = '1'")->sum('nominal');
                $jml = number_format($realisasi, 0, ',', '.');
                return $jml;
            })
          ->addColumn('tot', function($datas){
             $dt = '';
              if($datas -> tambahan != ''){
               $dt = number_format($datas->anggaran + $datas->tambahan, 0, ',', '.');  
              }else if($datas -> relokasi != ''){
             $dt = $datas->anggaran + $datas->relokasi ;

              }
                  $dt = number_format($datas->anggaran + $datas->tambahan + $datas->relokasi, 0, ',', '.' );
                return $dt;
            })
            
            
           
            ->addColumn('app', function($datas){
                $ss = '';
                if( $datas -> user_approve != ''){
                    $ss = User::select('users.name')->where('id',$datas->user_approve )->first()->name;
                }else{
                    $ss = '';
                }
                return $ss;
            })
            
              ->addColumn('app2', function($datas){
                $ss = '';
                if( $datas -> user_approve2 != ''){
                    $ss = User::select('users.name')->where('id',$datas->user_approve2 )->first()->name;
                }else{
                    $ss = '';
                }
                return $ss;
            })
           
             ->addColumn('urej', function($datas){
                $sa = '';
                if( $datas -> user_reject != ''){
                    $sa = User::select('users.name')->where('id',$datas->user_reject )->first()->name;
                }else{
                    $ss = '';
                }
                return $sa;
            })
            
            
            ->rawColumns(['apr','tot','app','app2','urej','tmbh','rlk','agr'])
            ->make(true);
        }
        return view('fins-budget.approve_anggaran', compact('bank','kantor','namcoa','jabat','saldo'));

    }
        public function getsemuacoa(){
        $coa_parent = COA::where('grup','like','%2%')->orWhere('grup','like','%9%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->nama_coa.'-'.$val->coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                "anggaran" =>$val->anggaran,
                "tambahan" =>$val->tambahan,
                "total" =>$val->tambahan +$val->anggaran,
            ];
        }
        return response()->json($h1);
    }
    

        public function getreal(Request $request){
        
        // $jt = $request->level1;
        $id_kan = $request->kantor;
        
        // $data = Pengeluaran::selectRaw("SUM('id_anggaran') as real")->get();
        $data = Pengeluaran::selectRaw('sum(nominal)as semua')->whereRaw("kantor = '$id_kan'")->get();

        // foreach($coa_parent as $key => $val){
        //     $h1[] = [
        //         "text" => $val->parent.'- '.$val->nama_coa.'- '.$val->coa,
        //         "coa" => $val->coa,
        //         "id" => $val->coa,
        //         "parent" => $val->parent,
        //         "nama_coa" => $val->nama_coa,
        //         "anggaran" =>$val->anggaran,
        //         "tambahan" =>$val->tambahan,
        //         "total" =>$val->tambahan +$val->anggaran,
        //     ];
        // }
         return $data;
        // return response()->json($h1);
    }
    
            public function getjumrealisasi(Request $request){
        
        $id = $request->a;
        $data = Pengeluaran::selectRaw('sum(nominal)as semua')->whereRaw("id_anggaran = '$id'")->get();

    
         return $data;
    }

        public function getcoauntukrelokasi(){
        $tgl_akhir = date('Y-m');
        $k = Auth::user()->id_kantor;
        $coa_parent = COA::leftjoin('anggaran','anggaran.coa','=','coa.coa')
         ->selectRaw("anggaran.*,coa.*")->whereRaw("DATE_FORMAT(anggaran.tanggal,'%Y-%m') = '$tgl_akhir' AND anggaran.kantor = '$k' AND anggaran.acc = '1' ")->orderBy('anggaran.tanggal', 'DESC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                "acc" => $val->acc,
                "tanggal" => $val->tanggal,
                "keterangan" => $val->keterangan,
                "id_anggaran" =>$val->id_anggaran,
                "anggaran" =>$val->anggaran,
                "tambahan" =>$val->tambahan,
                "total" =>$val->tambahan + $val->anggaran + $val->relokasi,
            ];
        }
        return response()->json($h1);
    }



        public function post_anggaran(Request $request){
        
             foreach($request->arr as $val){
                

                $tgl_now= $val['tgl_now'];
                $tgl_now= $val['tgl_now'];
                $saldo_dana= $val['saldo_dana'];
                $saldo = $val['saldo'];
                $jabatan = $val['jabatan'];
                $realisasi = $val['nominal']== '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
                $jenis = $val['jenis'];
                $kantor = $val['kantor'];
                $keterangan = $val['keterangan'];
                $referensi = $val['referensi'];
                $nominal = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);

                
                $input = $request->all();
                $input['tanggal'] = $tgl_now;
                $input['coa'] = $saldo_dana;
                $input['nama_akun'] = $saldo;
                $input['jabatan'] = $jabatan;
                $input['realisasi'] = $realisasi;
                $input['jenis'] = $jenis;
                $input['anggaran'] = $nominal;
                $input['keterangan'] = $keterangan;
                $input['referensi'] = $referensi;
                $input['acc'] = 2;
                $input['kantor'] = $kantor ;
                $input['user_input'] = Auth::user()->id;

                Anggaran::create($input);
            }
        
            return response()->json(['success' => 'Data is successfully added']);
    }

     public function PAexport(Request $request){
         
        if ($request->stts == '') {
            $stts = "anggaran.acc IS NOT NULL";
        } else if ($request->stts == 2) {
            $stts = "anggaran.acc = 2";
        } else if ($request->stts == 1) {
            $stts = "anggaran.acc = 1";
        } else if ($request->stts == 0) {
            $stts = "anggaran.acc = 0";
        }
         
        $now = date('Y-m-d');
        $kntr = $request->kntr == '' ? "id_kantor != 'IS NOT NULL'" : "kantor = '$request->kntr'";
        // $grup = $request->grup == '' ? "grup = 3 AND grup = 4" : "grup = '$request->grup'";
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        $darib = $request->darib != '' ? $request->darib : date('Y-m');
        $sampaib = $request->sampaib != '' ? $request->sampaib : $dari;
        $darit = $request->darit != '' ? $request->darit : date('Y');
        $sampait = $request->sampait != '' ? $request->sampai : $dari;
        $periode = $request->periodenya;
        if($request->tombol == 'xls'){
            $response = Excel::download(new AnggaranExport($stts,$now,$kntr,$dari,$sampai,$darib,$sampaib,$darit,$sampait,$periode), 'pengajuan-anggaran.xlsx');
        }else{
            $response = Excel::download(new AnggaranExport($stts,$now,$kntr,$dari,$sampai,$darib,$sampaib,$darit,$sampait,$periode), 'pengajuan-anggaran.csv');
        }
    
        ob_end_clean();
        return $response;
    }
    
    
     function resume_anggaran(Request $request)
        {
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        $namcoa = COA::whereRaw("grup = 6")->get();
        $jabat = Jabatan::all();
            
        $wkt = Tunjangan::where('min_anggaran',Auth::user()->id_com)->first();    
        if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
          
                $kantor = Kantor::whereRaw("id = $k")->get();
        }
        
        
        
        
        if($request->ajax())
        {
        // $wewe = date('Y-m', strtotime($request->dari));
        // $hari = date('Y-m-d');
        // $now = date('Y');
        // $now1 = date('m');
        // $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        // $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
       
            $kntr = $request->kntr == '' ? "anggaran.kantor != 'IS NOT NULL'" : "anggaran.kantor = '$request->kntr'";
           
            $years =  $request->years == '' ? date('Y') : $request->years;
            // $month = $request->month  == '' ?  date('Y-m') : date('Y-m', strtotime(str_replace('/','-',$request->month)));
        if($request->daterange != ''){
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
            $sampai = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
            
            // // Menangani format tanggal yang benar
            // $dari = DateTime::createFromFormat('d-m-Y', $tgl[0])->format('Y-m-d');
            // $sampai = DateTime::createFromFormat('d-m-Y', $tgl[1])->format('Y-m-d');
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }    
            
        if ($request->month != '') {
            $tgl = explode('/', $request->month);
            $b = $tgl[0];
            $t = $tgl[1];
        }else{
            $b = date('m');
            $t = date('Y');
        }
        $tahuns = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
      
            $din = "anggaran.acc = '1'";
        if($request->periode == 'tahun'){
             $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                     ->leftjoin('users','users.id','=','anggaran.user_input')
                   ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
                     ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
                     SUM(IF(YEAR(anggaran.tanggal) = '$years' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
                     SUM(IF(YEAR(anggaran.tanggal) = '$years' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
                     SUM(IF(YEAR(anggaran.tanggal) = '$years' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
                     SUM(IF(YEAR(pengeluaran.tgl) = '$years' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
                     SUM(IF(YEAR(anggaran.tanggal) = '$years' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot 
                     ") 
                     ->whereRaw("$din AND $kntr AND YEAR(anggaran.tanggal) = '$years'")
                     ->groupBy('anggaran.coa','anggaran.nama_akun')
                     ->get();
        }else if($request->periode == 'bulan'){
              $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                     ->leftjoin('users','users.id','=','anggaran.user_input')
                    ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
                     ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
                     SUM(IF(MONTH(anggaran.tanggal) = '$b' AND YEAR(anggaran.tanggal) = '$t' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
                     SUM(IF(MONTH(anggaran.tanggal) = '$b' AND YEAR(anggaran.tanggal) = '$t' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
                     SUM(IF(MONTH(anggaran.tanggal) = '$b' AND YEAR(anggaran.tanggal) = '$t' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
                     SUM(IF(MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$t' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
                     SUM(IF(MONTH(anggaran.tanggal) = '$b' AND YEAR(anggaran.tanggal) = '$t' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot 
                     ") 
                     
                     ->whereRaw("$din AND $kntr AND MONTH(anggaran.tanggal) = '$b' AND YEAR(anggaran.tanggal) = '$t' ")
                    ->groupBy('anggaran.coa','anggaran.nama_akun')
                     ->get();
        }else{
              $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                     ->leftjoin('users','users.id','=','anggaran.user_input')
                     ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
                     ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
                     SUM(IF(DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot ,
                    SUM(IF(DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) / SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi , 0))  * 100  AS persen
                     ") 
                //   SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.realisasi * 100 / (anggaran.anggaran + anggaran.tambahan + anggaran.relokasi), 0)) AS persen, 
                //      SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi - anggaran.realisasi , 0)) AS sisa
                        // ->selectRaw("anggaran.*, tambahan.unit,users.name")
                     ->whereRaw("$din AND $kntr AND DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai'")
                     ->groupBy('anggaran.coa','anggaran.nama_akun')
                     ->get();
        }
          
        //   foreach($datas as $val){
        //     $wew =  $val->tambahan + $val->anggaran + $val->relokasi;
        //     $rek =  $val->realisasi;
        //     $ss[] = [
        //         "tanggal" => $val->coa,
        //         "coa" => $val->coa,
        //         "nama_akun" => $val->nama_akun,
        //         "anggaran" =>$val->anggaran,
        //         "relokasi" =>$val->relokasi,
        //         "tambahan" =>$val->tambahan,
        //         "tot" =>$val->tambahan + $val->anggaran + $val->relokasi ,
        //         'realisasi'=>$val->realisasi,
        //         "persen"=>round(( $wew / $rek) * 100,1),
        //         'sisa'=> $val->tambahan + $val->anggaran + $val->relokasi - $val->realisasi,
        //         'program'=>$val->program,
        //         'jabatan'=>$val->jabatan,
        //         'unit'=> $val->unit,
        //     ];
        // }
          
          
         
        //   $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
        //          ->leftjoin('users','users.id','=','anggaran.user_input')
        //          ->selectRaw("anggaran.*, tambahan.unit,users.name  
        
        //  SUM(anggaran.anggaran + anggaran.tambahan + anggaran.relokasi) as tot , 
                //  SUM(anggaran.anggaran + anggaran.tambahan + anggaran.relokasi / anggaran.realisasi * 100) as persen ,
                //  SUM(anggaran.anggaran + anggaran.tambahan + anggaran.relokasi - anggaran.realisasi) as sisa 
                //")
        //          ->whereRaw("$kntr AND $stts AND  $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
        //          ->get();
            
            return DataTables::of($datas)
          ->addIndexColumn()
        //   ->addColumn('tot', function($datas){
        //      $dt = '';
        //          $wew = $datas::selectRaw("anggaran.coa,SUM(anggaran.anggaran + anggaran.tambahan + anggaran.relokasi) as tot");
        //         //  SUM($datas->anggaran + $datas->tambahan + $datas->relokasi);
        //           $dt = $wew;
        //         return $dt;
        //     })
             ->addColumn('persen', function($datas){
                  $crot = 0 ;
                //  if($datas->realisasi > 0){
                //     $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
                //     $rek = $datas->realisasi;
                //   $crot = round(( $wew / $wew  ) * 100,1);
                //  }else{
                //      $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
                //     $rek = $datas->realisasi;
                //   $crot = round(( $rek  / $wew ) * 100,1);
                //  }
                  
                  
                  
                return $crot;
                 
            })
             ->addColumn('sisa', function($datas){
                 $crot = 0 ;
                //  if(-$datas->relokasi > $datas->realisasi){
                //       $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
                //   $rek = $datas->realisasi;
                //   $crot =  number_format( $wew - $rek );
                //   }else if (-$datas->realisasi = $datas->relokasi){
                //     $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
                //   $rek = $datas->realisasi;
                //   $crot =  number_format( $wew );
                //   }else{
                //   $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
                //   $rek = $datas->realisasi;
                //   $crot =  number_format( $wew - $rek);
                //  }
                return $crot;
            })
            ->rawColumns(['sisa','persen'])
            ->make(true);
        }
        
        return view('fins-budget.resume_anggaran', compact('bank','kantor','namcoa','jabat','saldo'));

    }
    
    public function raexport(Request $request)
    {
        if($request->periode == 'tahun'){
        $dari =  date('Y', strtotime($request->dari)) != '' ? date('Y', strtotime($request->dari)) : date('Y');
        $sampai = date('Y', strtotime($request->sampai)) != '' ?  date('Y', strtotime($request->sampai)) : $dari;

        }else if($request->periode == 'bulan'){
        $dari = date('m', strtotime($request->dari))  != '' ? date('m', strtotime($request->dari)) : date('m');
        $sampai = date('m', strtotime($request->sampai)) != '' ? date('m', strtotime($request->sampai)) : $dari;
        }else{
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
        }
            
         $k = Auth::user()->id_kantor;
         $kntr = $request->kntr == '' ? "anggaran.kantor != 'IS NOT NULL'" : "anggaran.kantor = '$request->kntr'";
         $periode = $request->periode;
        if($request->tombol == 'xls'){
             $response = Excel::download(new ResumeanggaranExport($kntr,$dari,$sampai,$periode), 'Resume-Anggaran.xlsx');
        }else{
             $response = Excel::download(new ResumeanggaranExport($kntr,$dari,$sampai,$periode), 'Resume-Anggaran.xlsx');
        }
    
        ob_end_clean();
        return $response;
    }

    public function resumeBy(Request $request){
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        $data = transaksi::leftjoin('users','users.id', '=', 'transaksi.user_insert')->leftjoin('prog','prog.id_program','=','transaksi.id_program')
        ->leftjoin('tambahan','tambahan.id', '=', 'transaksi.id_kantor')->selectRaw("users.name, transaksi.*")
        ->whereRaw("transaksi.id_program = '$request->id_prog' AND transaksi.tanggal >= '$dari' AND transaksi.tanggal <= '$sampai' AND transaksi.approval = '1' ")
        ->get();
    
        return DataTables::of($data)
            // ->addIndexColumn()
            // ->addColumn('real', function($datas){
            //     $dt = ''; 
            //     $wew = $data::selectRaw("SUM(prog.dp * transaksi.jumlah ) as tot");
            //     $dt = $wew;
            //     // $jml = number_format($realisasi, 0, ',', '.');
            //     return $dt;
            // })
            // ->rawColumns(['tot'])
            
            ->addColumn('tot', function($data){
             $dt = '';
                     $dt = number_format ($data->dp * $data->jumlah / 100, 0, ',', '.');  
                     return $dt;
            })
             ->rawColumns(['tot'])
            
            ->make(true);

        // return $data;
    }
    
    
        public function transaksiBy(Request $request, $id){
        $data = transaksi::join('users','users.id', '=', 'transaksi.user_insert')->leftjoin('prog','prog.id_program','=','transaksi.id_program')
        ->join('tambahan','tambahan.id', '=', 'transaksi.id_kantor')->selectRaw("users.name, transaksi.*")
        ->whereRaw("transaksi.id = '$id'")
        ->get();
        return $data;
    }


        public function editdp (Request $request){
        $idt = $request->idt;
        $idtrans = $request->idtrans;
        $dpanyar = $request->dpbarunya;
        $pilihan = $request->pilihan;
        $idp = $request->idp;
        $bulan = date('Y-m'); 
        $tahun = date('Y'); 
        // Pilihan 1 = Transaksi ini Saja
        // Pilihan 2 = Di Bulan ini Dan seterusnya
        // Pilihan 3 = Di Tahun ini Dan seterusnya
        if($pilihan = '1'){
              Transaksi::whereRaw("id = '$idt'")->update([
                'dp' => $dpanyar,
            ]);
            // \LogActivity::addToLog(Auth::user()->name . ' Edit persen DP ID transaksi' . $idtrans);
        }else if($pilihan = '2'){
              Transaksi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m')  = '$bulan' AND dp = $idp")->update([
                'dp' => $dpanyar,
            ]);
            
              Prog::whereRaw("id_program = '$idp'")->update([
                'dp' => $dpanyar,
            ]);
            // \LogActivity::addToLog(Auth::user()->name . ' Edit persen DP ID transaksi' . $idtrans. 'Di' . $bulan . 'Seterusnya');
        }else if($pilihan = '3'){
             Transaksi::whereRaw("DATE_FORMAT(tanggal, '%Y'  )  = '$bulan' AND dp = $idp ")->update([
                'dp' => $dpanyar,
            ]);
            
              Prog::whereRaw("id_program = '$idp'")->update([
                'dp' => $dpanyar,
            ]);
            // \LogActivity::addToLog(Auth::user()->name . ' Edit persen DP ID transaksi' . $idtrans. 'Di' . $tahun . 'Seterusnya');
        }
          
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function editdptabelprogdp (Request $request){
        $idprog = $request->idprognya;
        $dpanyar = $request->dpbaru;
            Prog::whereRaw("id_program= '$idprog'")->update([
                'dp' => $dpanyar,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Edit persen DP ID Program' . $idtrans);
        return response()->json(['success' => 'Data is successfully updated']);
    }

        public function getsemuaprogram(){
        $program = prog::get();
        foreach($program as $key => $val){
            $h1[] = [
                "text" => $val->id_program.'-'.$val->program.'-'.$val->dp,
                "id" => $val->id_program,
                "program" => $val->program,
            ];
        }
        return response()->json($h1);
    }

        public function jumtrans(Request $request){
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
        $data = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
        ->selectRaw("transaksi.id_program,transaksi.subprogram,prog.dp,SUM(transaksi.jumlah)as tot")
        ->whereRaw("transaksi.tanggal >= '$dari' AND transaksi.tanggal  <= '$sampai' AND transaksi.id_program = '$request->idnya' AND transaksi.approval = '1'")
        ->groupBy('transaksi.id_program','prog.dp') ->get();
        return $data;
    }




    }
    
    
      

