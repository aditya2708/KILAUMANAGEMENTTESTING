<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\User;
use App\Models\GrupCOA;
use App\Models\JenlapKeuangan;
use App\Models\RumlapKeuangan;
use Auth;
use App\Models\SaldoAw;
use App\Models\Penutupan;
use App\Models\Pengeluaran;
use DataTables;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use App\Models\Transaksi;
use App\Models\Tunjangan;
use App\Models\Jurnal;
use Staudenmeir\LaravelCte;
use App\Models\HapusPengeluaran;
use App\Models\HapusTransaksi;
use Illuminate\Support\Collection;
use Staudenmeir\LaravelCte\Query\Builder as CteBuilder;
use Excel;
use PDF;
use App\Exports\LaporanKeuanganExport;
use App\Exports\LaporanBulananExport;
use App\Exports\DetailLaporanKeuanganExport;

class LaporanKeuanganController extends Controller
{
// ini fungsi yang lama untuk get data laporan bulanan
    public function laporan_bulanan(Request $request){
        
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
                    

           $jenis = JenlapKeuangan::where('aktif','y')->get();
           
        if ($request->ajax()) {
            
            // $kntr = $request->kota;
            $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
            $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
            $currentYear = $p;
            $range = 1; // rentang tahun yang ingin ditampilkan
            $oldestYear = $currentYear - $range;
            $thh = [];
            $blnn = [];
            $filbln = [];  
     
     
            for ($year = $currentYear; $year >= $oldestYear; $year--) {
                $thh[] = $year;
            }
            
            for ($i = 1; $i <= 12; $i++) {
                $bulan = date('m-t', mktime(0, 0, 0, $i, 1));
                $blnn[] = $bulan;
             
            }
        
            $p = [];
            // Tampilkan daftar bulan Desember tahun ini
            for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $p[] = date("m-t", $date) ;
            }
            
            $blnz= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $blnz[] = date("Y-m-t", $date) ;
            }
            
        $blnjnr = $blnz[12];
        $blnfeb = $blnz[11];
        $blnmar = $blnz[10];
        $blnapr = $blnz[9];
        $blnmei = $blnz[8];
        $blnjun = $blnz[7];
        $blnjul = $blnz[6];
        $blnags = $blnz[5];
        $blnsep = $blnz[4];
        $blnokt = $blnz[3];
        $blnnov = $blnz[2];
        $blndes = $blnz[1];
    
       
            $blnzz= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $blnzz[] = date("m", $date) ;
            }
            
            $filun= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December" .$currentYear);
                $filun[] = date("Y-m",$date) ;
            }
            
            $fillalu= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December" .$oldestYear);
                $fillalu[] = date("Y-m",$date) ;
            }
            
            $d = $thh;
            // $baru = SaldoAw::selectRaw("DISTINCT(bulan)")->whereYear('bulan',$d[0])->latest();
            // $ngitung = count($baru->get());
            
            // if($ngitung > 0){
            //     $l = date('m', strtotime($baru->first()->bulan));
            // }else{
            //     $l = '12';
            // }
            
        $mon = $request->bln == '' ? date('m') : $request->bln;  
        $blnkurang = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$mon.'-'.$currentYear)));
        $blnkurangthnlalu = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$mon.'-'.$oldestYear)));
        
        $via = $request->via;
        $multinya = $request->mulbul ; 
        $wowd = $request->jenisnya;
        $dummy =  date('m') ;
   
        
        $teto = DB::table('rumlap_keuangan')->whereRaw("id_jenlap =  $wowd AND aktif = 'y'")->get();
        $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
      
        foreach($tet as $s){
             $a[] = [
                        'saldo' =>  preg_split("/[\+\-]/" ,$s->rumus),
                        'rumusnya' =>  preg_split("/[\+\-\s]/" ,$s->rumus),
                        'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                    ];
        }    
        
        // untuk ambil saldonya
        for($i = 0; $i < count($a); $i++){
          $saldo[] = $a[$i]['saldo'];
        }
        
         for($i = 0; $i < count($saldo); $i++){
          $s1[] = $saldo[$i];
        }
        

        
        // untuk ambil rumusnya
        for($i = 0; $i < count($a); $i++){
          $rms[] = $a[$i]['rumusnya'];
        }
        
         for($i = 0; $i < count($rms); $i++){
          $getrumus[] = $rms[$i];
        }
        
        
        //untuk ambil data operatornya
        for($i = 0; $i < count($a); $i++){
          $tnd[] = $a[$i]['tanda'];
        }
        
         for($i = 0; $i < count($tnd); $i++){
          $ttbesar[] = $tnd[$i];
        }
       
              


   if($via == '0'){


// ini fungsi yang lama untuk get data laporan yang realtime 


            // $union = DB::table('transaksi')
            //             ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
            //             ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
            //             ->unionAll(DB::table('pengeluaran')
            //                     ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
            //                     ->whereRaw("MONTH(tgl) = '01' AND YEAR(tgl) = '$currentYear' AND acc = 1"))
            //             ->unionAll(DB::table('jurnal')
            //                     ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
            //                     ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND acc >= 1"))
            //             ->unionAll(DB::table('transaksi')
            //                     ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
            //                     ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
            //                     ->whereRaw("MONTH(transaksi.tanggal) = '01' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
            //                     )
            //             ;
            
// $cteTransaksithn = DB::table('transaksi')
//     ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
//     ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') > '$filun[11]' AND DATE_FORMAT(tanggal,'%Y-%m') < '$filun[0]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
//     // ->whereRaw("coa_debet IN ('$combinedArray')")    
//     // ->orWhereRaw("coa_kredit IN ('$combinedArray')")   

//     ;

// $ctePengeluaranthn = DB::table('pengeluaran')
//     ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
//     ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') > '$filun[11]' AND DATE_FORMAT(tgl,'%Y-%m') < '$filun[0]' AND acc = 1 ")
//     // ->whereRaw("coa_debet IN ('$combinedArray')")    
//     // ->orWhereRaw("coa_kredit IN ('$combinedArray')")   

//     ;

// $cteJurnalthn = DB::table('jurnal')
//     ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
//     ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') > '$filun[11]' AND DATE_FORMAT(tanggal,'%Y-%m') < '$filun[0]' AND acc = 1 ")

//     ;


// $cteProgthn = DB::table('transaksi')
//     ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
//       ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
//       0 as nominal_debit, 0 as nominal_kredit, 
//       transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
//     ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[11]' AND transaksi.approval = 1
//      AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");




// // // Gabungkan query union
// $unionthn = $cteTransaksithn->unionAll($ctePengeluaranthn)->unionAll($cteJurnalthn)->unionAll($cteProgthn);

//                 $jan = DB::table('b as t')
//                         ->selectRaw("root, t.coa,t.nama_coa, SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s ")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                             //  ->whereYear('sub.tanggal', $currentYear)
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
//                                 ;
//                         })
//                         ->groupBy('root')
//                         ->get(); 
                        
             
            
                
//                 $jan2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnjnr) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnjnr'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get();
                
//                 $janx = DB::table('b as t')
//                         ->selectRaw("root, t.coa,t.nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
//                                 ;
//                         })
//                         ->groupBy('root')
//                         ->get(); 
           
//             $januari = [];
//             $b = 0;
//             foreach($jan as $i => $val){
                
//                 if($jan2[$i]->coa == $val->coa ){
//                     $b = $jan2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
                
//                 if($janx[$i]->coa == $val->coa){
//                     $deb = $janx[$i]->debit + $val->debit;
//                     $kre = $janx[$i]->kredit + $val->kredit;
//                 }
//                         $januari[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,


//                         ];
                    
//           }


//       $feb = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnz,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                  ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
             
//                 $feb2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnfeb) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnfeb'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get();
                
             
           
           
//                   $febx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//             $febuari = [];
//             $b = 0;
//             foreach($feb as $i => $val){
//                 if($feb2[$i]->coa == $val->coa & $s1){
//                     $b = $feb2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                 if($febx[$i]->coa == $val->coa){
//                     $deb = $febx[$i]->debit + $val->debit;
//                     $kre = $febx[$i]->kredit + $val->kredit;
//                 }
                
//                         $febuari[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
         
         



         
//             $mar = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                    
                    
//                 $mar2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnmar) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnmar'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get();    
                        
             
           
//                 $marx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
           
//             $maret = [];
//             $b = 0;
//             foreach($mar as $i => $val){
//                 if($mar2[$i]->coa == $val->coa & $s1){
//                     $b = $mar2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($marx[$i]->coa == $val->coa){
//                     $deb = $marx[$i]->debit + $val->debit;
//                     $kre = $marx[$i]->kredit + $val->kredit;
//                 }
//                         $maret[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
         
//                 $apr = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                  $apr2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnapr) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnapr'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get();    
               
            
                
//                   $aprx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                
//             $april = [];
//             $b = 0;
//             foreach($apr as $i => $val){
//                 if($apr2[$i]->coa == $val->coa & $s1){
//                     $b = $apr2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($aprx[$i]->coa == $val->coa){
//                     $deb = $aprx[$i]->debit + $val->debit;
//                     $kre = $aprx[$i]->kredit + $val->kredit;
//                 }
//                         $april[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
//                 $mei = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                  $mei2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnmei) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnmei'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
                
              
           
//                   $meix = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                
//             $meii = [];
//             $b = 0;
//             foreach($mei as $i => $val){
//                 if($mei2[$i]->coa == $val->coa & $s1){
//                     $b = $mei2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                 if($meix[$i]->coa == $val->coa){
//                     $deb = $meix[$i]->debit + $val->debit;
//                     $kre = $meix[$i]->kredit + $val->kredit;
//                 }
//                         $meii[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,


//                         ];
                    
//           }
         
//                 $jun = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                   $jun2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnjun) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnjun'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
                
             
                        
//                 $junx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                
                
//             $juni = [];
//             $b = 0;
//             foreach($jun as $i => $val){
//                 if($jun2[$i]->coa == $val->coa & $s1){
//                     $b = $jun2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                 if($junx[$i]->coa == $val->coa){
//                     $deb = $junx[$i]->debit + $val->debit;
//                     $kre = $junx[$i]->kredit + $val->kredit;
//                 }
                
//                         $juni[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
//                 $jul = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                 $jul2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnjul) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnjul'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
               
              
           
//                 $julx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                
           
//             $juli = [];
//             $b = 0;
//             foreach($jul as $i => $val){
//                 if($jul2[$i]->coa == $val->coa & $s1){
//                     $b = $jul2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($julx[$i]->coa == $val->coa){
//                     $deb = $julx[$i]->debit + $val->debit;
//                     $kre = $julx[$i]->kredit + $val->kredit;
//                 }
//                         $juli[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
//                 $ags = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                  $ags2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnags) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnags'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
               
               
                        
//                   $agsx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get();         
           
//             $agustus = [];
//             $b = 0;
//             foreach($ags as $i => $val){
//                 if($ags2[$i]->coa == $val->coa & $s1){
//                     $b = $ags2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($agsx[$i]->coa == $val->coa){
//                     $deb = $agsx[$i]->debit + $val->debit;
//                     $kre = $agsx[$i]->kredit + $val->kredit;
//                 }
//                         $agustus[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
//                 $sep = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                               ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                  $sep2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnsep) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnsep'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
                
               
                        
//                 $sepx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get();   
                        
//             $september = [];
//             $b = 0;
//             foreach($sep as $i => $val){
//                 if($sep2[$i]->coa == $val->coa & $s1){
//                     $b = $sep2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($sepx[$i]->coa == $val->coa){
//                     $deb = $sepx[$i]->debit + $val->debit;
//                     $kre = $sepx[$i]->kredit + $val->kredit;
//                 }
//                         $september[] = [
//                             'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
//                 $okt = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//               $okt2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnokt) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnokt'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
               
           
//                 $oktx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
//                                 ;
//                         })
//                         ->groupBy('root')
//                         ->get();   
                        
//             $oktober = [];
//             $b = 0;
//             foreach($okt as $i => $val){
//                 if($okt2[$i]->coa == $val->coa & $s1){
//                     $b = $okt2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                  if($oktx[$i]->coa == $val->coa){
//                     $deb = $oktx[$i]->debit + $val->debit;
//                     $kre = $oktx[$i]->kredit + $val->kredit;
//                 }
//                         $oktober[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
         
         
//                 $nov = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//                 $nov2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blnnov) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blnnov'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
              
            
            
//                  $novx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
//                                 ;
//                         })
//                         ->groupBy('root')
//                         ->get(); 
                        
//             $november = [];
//             $b = 0;
//             foreach($nov as $i => $val){
//                 if($nov2[$i]->coa == $val->coa & $s1){
//                     $b = $nov2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                 if($novx[$i]->coa == $val->coa){
//                     $deb = $novx[$i]->debit + $val->debit;
//                     $kre = $novx[$i]->kredit + $val->kredit;
//                 }
//                         $november[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
//                         ];
                    
//           }
         
//                 $des = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        
//               $des2 = DB::table('coa as t')
//                 ->leftJoin('saldo_awal', function ($join) use ($blndes) {
//                     $join->on('saldo_awal.coa', '=', 't.coa')
//                         ->whereRaw("saldo_awal.bulan = '$blndes'");
//                 })
//                 ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
//                 ->get(); 
               

//                   $desx = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$unionthn->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
//                                 ;
//                         })
//                         ->groupBy('root')
//                         ->get(); 
//             $desember = [];
//             $b = 0;
//             foreach($des as $i => $val){
//                 if($des2[$i]->coa == $val->coa & $s1){
//                     $b = $des2[$i]->saldo_awal;
//                 }else{
//                     $b = 0;
//                 }
//                 if($desx[$i]->coa == $val->coa){
//                     $deb = $desx[$i]->debit + $val->debit;
//                     $kre = $desx[$i]->kredit + $val->kredit;
//                 }
//                         $desember[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

//                         ];
                    
//           }
    


                  

$cteTransaksi1 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    
    ;

$ctePengeluaran1 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '01' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    ;

$cteJurnal1 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;


$cteProg1 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '01' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");




// // Gabungkan query union
$union1 = $cteTransaksi1->unionAll($ctePengeluaran1)->unionAll($cteJurnal1)->unionAll($cteProg1);





$cteTransaksi2 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran2 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '02' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal2 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '02' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg2 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '02' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union2 = $cteTransaksi2->unionAll($ctePengeluaran2)->unionAll($cteJurnal2)->unionAll($cteProg2);






$cteTransaksi3 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '03' AND YEAR(tanggal) = '$currentYear'AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran3 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '03' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal3 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '03' AND YEAR(tanggal) = '$currentYear'  AND acc = 1 ")
    ;

$cteProg3 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '03' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union3 = $cteTransaksi3->unionAll($ctePengeluaran3)->unionAll($cteJurnal3)->unionAll($cteProg3);






$cteTransaksi4 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '04' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran4 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '04' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal4 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '04' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg4 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '04' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union4 = $cteTransaksi4->unionAll($ctePengeluaran4)->unionAll($cteJurnal4)->unionAll($cteProg4);




$cteTransaksi5 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '05' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran5 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '05' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal5 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '05' AND YEAR(tanggal) = '$currentYear'  AND acc = 1 ")
    ;

$cteProg5 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '05' AND YEAR(transaksi.tanggal) = '$currentYear'AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union5 = $cteTransaksi5->unionAll($ctePengeluaran5)->unionAll($cteJurnal5)->unionAll($cteProg5);




$cteTransaksi6 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '06' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran6 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '06' AND YEAR(tgl) = '$currentYear'AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal6 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '06' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg6 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '06' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union6 = $cteTransaksi6->unionAll($ctePengeluaran6)->unionAll($cteJurnal6)->unionAll($cteProg6);




$cteTransaksi7 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '07' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran7 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '07' AND YEAR(tgl) = '$currentYear'AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal7 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '01' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg7 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '07' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union7 = $cteTransaksi7->unionAll($ctePengeluaran7)->unionAll($cteJurnal7)->unionAll($cteProg7);





$cteTransaksi8 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '08' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran8 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '08' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal8 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '08' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg8 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '08' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union8 = $cteTransaksi8->unionAll($ctePengeluaran8)->unionAll($cteJurnal8)->unionAll($cteProg8);




$cteTransaksi9 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '09' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran9 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '09' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    ;

$cteJurnal9 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '09' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg9 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '09' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union9 = $cteTransaksi9->unionAll($ctePengeluaran9)->unionAll($cteJurnal9)->unionAll($cteProg9);




$cteTransaksi10 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '10' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran10 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '10' AND YEAR(tgl) = '$currentYear'AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal10 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '10' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg10 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '10' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union10 = $cteTransaksi10->unionAll($ctePengeluaran10)->unionAll($cteJurnal10)->unionAll($cteProg10);





$cteTransaksi11 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '11' AND YEAR(tanggal) = '$currentYear'AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran11 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '11' AND YEAR(tgl) = '$currentYear' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal11 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '11' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg11 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '11' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union11 = $cteTransaksi11->unionAll($ctePengeluaran11)->unionAll($cteJurnal11)->unionAll($cteProg11);


$cteTransaksi12 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '12' AND YEAR(tanggal) = '$currentYear' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran12 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tgl) = '12' AND YEAR(tgl) = '$currentYear'AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal12 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("MONTH(tanggal) = '12' AND YEAR(tanggal) = '$currentYear' AND acc = 1 ")
    ;

$cteProg12 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("MONTH(transaksi.tanggal) = '12' AND YEAR(transaksi.tanggal) = '$currentYear' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union12 = $cteTransaksi12->unionAll($ctePengeluaran12)->unionAll($cteJurnal12)->unionAll($cteProg12);

         
                //  $jan = COA::leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $janx = COA::leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();
         
         
         
                $jan = DB::table('b as t')
                        ->selectRaw("root, t.coa,t.nama_coa, SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s ")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
                        
             
            
                
                $jan2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnjnr) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnjnr'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get();
                
                
              
                $janx = DB::table('b as t')
                        ->selectRaw("root, t.coa,t.nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
           
            $januari = [];
            $b = 0;
            foreach($jan as $i => $val){
                
                if($jan2[$i]->coa == $val->coa ){
                    $b = $jan2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                
                if($janx[$i]->coa == $val->coa){
                    $deb = $janx[$i]->debit + $val->debit;
                    $kre = $janx[$i]->kredit + $val->kredit;
                }
                        $januari[] = [
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            // 'neraca_s' => ($b + $val->debit) - $val->kredit + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }


                // $feb = COA::leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $febx = COA::leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();


                $feb = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnz,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                //  ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
             
                $feb2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnfeb) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnfeb'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get();
                
             
                // $feb2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz,$blnzz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //                     ->whereRaw("t2.bulan= '$blnz[11]' ");
                //                 //  ->whereMonth('t2.bulan', 1)
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
           
           
                  $febx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
            $febuari = [];
            $b = 0;
            foreach($feb as $i => $val){
                if($feb2[$i]->coa == $val->coa & $s1){
                    $b = $feb2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($febx[$i]->coa == $val->coa){
                    $deb = $febx[$i]->debit + $val->debit;
                    $kre = $febx[$i]->kredit + $val->kredit;
                }
                
                        $febuari[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
         
         

                // $mar = COA::leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $marx = COA::leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();    

         
            $mar = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            //     ->whereYear('sub.tanggal', $currentYear)
                            //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
                            //     ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                    
                    
                $mar2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnmar) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnmar'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get();    
                        
                // $mar2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //                   ->whereRaw("t2.bulan= '$blnz[10]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[9])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
           
                $marx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
            $maret = [];
            $b = 0;
            foreach($mar as $i => $val){
                if($mar2[$i]->coa == $val->coa & $s1){
                    $b = $mar2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($marx[$i]->coa == $val->coa){
                    $deb = $marx[$i]->debit + $val->debit;
                    $kre = $marx[$i]->kredit + $val->kredit;
                }
                        $maret[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
                // $apr = COA::leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $aprx = COA::leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();    
         
         
                $apr = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            //     ->whereYear('sub.tanggal', $currentYear)
                            //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
                            //     ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                 $apr2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnapr) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnapr'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get();    
               
                // $apr2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //                  ->whereRaw("t2.bulan= '$blnz[9]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[8])
                //         })
                            
                //         ->groupBy('root')
                        // ->get(); 
                
                  $aprx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
            $april = [];
            $b = 0;
            foreach($apr as $i => $val){
                if($apr2[$i]->coa == $val->coa & $s1){
                    $b = $apr2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($aprx[$i]->coa == $val->coa){
                    $deb = $aprx[$i]->debit + $val->debit;
                    $kre = $aprx[$i]->kredit + $val->kredit;
                }
                        $april[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
            //  $mei = COA::leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
            //                 $join->on('sub.coa_debet' ,'=', 'coa')
            //                     ;
            //             })
            //         ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
            //         ->groupBy('coa') ->get();
                
            //      $meix = COA::leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
            //                 $join->on('sub.coa_kredit' ,'=', 'coa')
            //                     ;})
            //         ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
            //         ->groupBy('coa') ->get();    
         
                $mei = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                 $mei2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnmei) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnmei'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
                
                // $mei2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //             ->whereYear('t2.bulan', $currentYear)
                //             ->whereRaw("t2.bulan= '$blnz[8]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[7])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
           
                  $meix = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
            $meii = [];
            $b = 0;
            foreach($mei as $i => $val){
                if($mei2[$i]->coa == $val->coa & $s1){
                    $b = $mei2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($meix[$i]->coa == $val->coa){
                    $deb = $meix[$i]->debit + $val->debit;
                    $kre = $meix[$i]->kredit + $val->kredit;
                }
                        $meii[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,


                        ];
                    
          }
         
                // $jun = COA::leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $junx = COA::leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();    
         
                $jun = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                  $jun2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnjun) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnjun'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
                
                // $jun2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                 //  ->whereMonth('t2.bulan', $blnz[6])
                //               ->whereYear('t2.bulan', $currentYear)
                //               ->whereRaw("t2.bulan= '$blnz[7]' ");
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
                        
                $junx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                
            $juni = [];
            $b = 0;
            foreach($jun as $i => $val){
                if($jun2[$i]->coa == $val->coa & $s1){
                    $b = $jun2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($junx[$i]->coa == $val->coa){
                    $deb = $junx[$i]->debit + $val->debit;
                    $kre = $junx[$i]->kredit + $val->kredit;
                }
                
                        $juni[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
                // $jul = COA::leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $julx = COA::leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get();    
         
                $jul = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            //     ->whereYear('sub.tanggal', $currentYear)
                            //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
                            //     ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $jul2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnjul) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnjul'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
               
                // $jul2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //               ->whereRaw("t2.bulan= '$blnz[6]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[5])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
           
                $julx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
           
            $juli = [];
            $b = 0;
            foreach($jul as $i => $val){
                if($jul2[$i]->coa == $val->coa & $s1){
                    $b = $jul2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($julx[$i]->coa == $val->coa){
                    $deb = $julx[$i]->debit + $val->debit;
                    $kre = $julx[$i]->kredit + $val->kredit;
                }
                        $juli[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
                 $ags = COA::leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_debet' ,'=', 'coa')
                                ;
                        })
                    ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                    ->groupBy('coa') ->get();
                
                 $agsx = COA::leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_kredit' ,'=', 'coa')
                                ;})
                    ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                    ->groupBy('coa') ->get();  
         
                $ags = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            //     ->whereYear('sub.tanggal', $currentYear)
                            //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
                            //     ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                 $ags2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnags) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnags'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
               
                // $ags2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //                     ->whereRaw("t2.bulan= '$blnz[5]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[4])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
                        
                  $agsx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get();         
           
            $agustus = [];
            $b = 0;
            foreach($ags as $i => $val){
                if($ags2[$i]->coa == $val->coa & $s1){
                    $b = $ags2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($agsx[$i]->coa == $val->coa){
                    $deb = $agsx[$i]->debit + $val->debit;
                    $kre = $agsx[$i]->kredit + $val->kredit;
                }
                        $agustus[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
          
                // $sep = COA::leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $sepx = COA::leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get(); 
         
                $sep = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            //     ->whereYear('sub.tanggal', $currentYear)
                            //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
                            //     ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                 $sep2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnsep) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnsep'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
                
                // $sep2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                 //  ->whereMonth('t2.bulan', $blnz[3])
                //                  ->whereYear('t2.bulan', $currentYear)
                //                   ->whereRaw("t2.bulan= '$blnz[4]' ");
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
                        
                $sepx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get();   
                        
            $september = [];
            $b = 0;
            foreach($sep as $i => $val){
                if($sep2[$i]->coa == $val->coa & $s1){
                    $b = $sep2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($sepx[$i]->coa == $val->coa){
                    $deb = $sepx[$i]->debit + $val->debit;
                    $kre = $sepx[$i]->kredit + $val->kredit;
                }
                        $september[] = [
                            'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                          
                            

                        ];
                    
          }
         
         
                // $okt = COA::leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $oktx = COA::leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get(); 
         
                $okt = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
              $okt2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnokt) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnokt'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
               
                // $okt2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //              ->whereRaw("t2.bulan= '$blnz[3]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[2])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
           
           
                $oktx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
                                // ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get();   
                        
            $oktober = [];
            $b = 0;
            foreach($okt as $i => $val){
                if($okt2[$i]->coa == $val->coa & $s1){
                    $b = $okt2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($oktx[$i]->coa == $val->coa){
                    $deb = $oktx[$i]->debit + $val->debit;
                    $kre = $oktx[$i]->kredit + $val->kredit;
                }
                        $oktober[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
         
                $nov = COA::leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_debet' ,'=', 'coa')
                                ;
                        })
                    ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                    ->groupBy('coa') ->get();
                
                 $novx = COA::leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                            $join->on('sub.coa_kredit' ,'=', 'coa')
                                ;})
                    ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                    ->groupBy('coa') ->get(); 
         
                $nov = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $nov2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blnnov) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blnnov'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
              
                // $nov2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //              ->whereRaw("t2.bulan= '$blnz[2]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[1])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
                        
                 $novx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
                                // ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
                        
            $november = [];
            $b = 0;
            foreach($nov as $i => $val){
                if($nov2[$i]->coa == $val->coa & $s1){
                    $b = $nov2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($novx[$i]->coa == $val->coa){
                    $deb = $novx[$i]->debit + $val->debit;
                    $kre = $novx[$i]->kredit + $val->kredit;
                }
                        $november[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                        ];
                    
          }
         
                // $des = COA::leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_debet' ,'=', 'coa')
                //                 ;
                //         })
                //     ->selectRaw("coa,nama_coa,SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                //     ->groupBy('coa') ->get();
                
                //  $desx = COA::leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz,$s1) {
                //             $join->on('sub.coa_kredit' ,'=', 'coa')
                //                 ;})
                //     ->selectRaw("coa,nama_coa, SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit")
                //     ->groupBy('coa') ->get(); 
         
         
                $des = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                //   ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
              $des2 = DB::table('coa as t')
                ->leftJoin('saldo_awal', function ($join) use ($blndes) {
                    $join->on('saldo_awal.coa', '=', 't.coa')
                        ->whereRaw("saldo_awal.bulan = '$blndes'");
                })
                ->selectRaw('t.coa, COALESCE(saldo_awal.saldo_awal, 0) as saldo_awal')
                ->get(); 
               
                // $des2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                //         ->withRecursiveExpression('b', $query)
                //         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                  ->whereYear('t2.bulan', $currentYear)
                //                   ->whereRaw("t2.bulan= '$blnz[1]' ");
                //                 //  ->whereMonth('t2.bulan', $blnz[1])
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
                        
                  $desx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereYear('sub.tanggal', $currentYear)
                                // ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
                                // ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
            $desember = [];
            $b = 0;
            foreach($des as $i => $val){
                if($des2[$i]->coa == $val->coa & $s1){
                    $b = $des2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($desx[$i]->coa == $val->coa){
                    $deb = $desx[$i]->debit + $val->debit;
                    $kre = $desx[$i]->kredit + $val->kredit;
                }
                        $desember[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
         
         
       
                  
                  

     


        }



            return DataTables::of($teto)
          ->addColumn('coah', function($teto){
                if($teto->rumus == '0' && $teto->level == '1' ){  
                    $c = '<b>'. $tet->nama. '</b>';
                }else if($teto->rumus != '0' && $teto->level == '2' && $teto->nama == 'Penyaluran' || $teto->nama ==  'Penerimaan' ){
                    $c = '&nbsp; <b>'. $teto->nama. '</b>';
                }else if($teto->level == '1' && $teto->rumus != '0'){
                  $c = '<b>'. $teto->nama. '</b>';
                }else {
                      $c = '&nbsp;&nbsp;'.$teto->nama;
                }
                return $c;
            })
          ->addColumn('saldo1', function($s1)  use ($d,$teto,$p,$via,$wowd,&$inArray,$blnkurang,$dummy,&$ttbesar,&$tetos){
               
  if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = SaldoAw::select('saldo_awal.saldo_awal')->whereRaw("YEAR(bulan) = '$d[0]' ")->whereIn('coa',$cc)->get();
        $filteredData = collect($tetos)->whereIn('coa', $cc)->pluck('saldo_awal');   
          
             
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            
        return ($total);      
             
             
          
            
              }else if ($via == '0'){
                  
         $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();

    
          foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
                $tetos = SaldoAw::select('saldo_awal.saldo_awal')->whereRaw("bulan = '$blnkurang' ")->whereIn('coa',$cc)->get();
                $filteredData = collect($tetos)->whereIn('coa', $cc)->pluck('saldo_awal');   

            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
             
             if($dummy == '01'){
            return $totals;
            }else{
            return $total;         
        }
     
        
  }
       
            })
          ->addColumn('saldo2', function($s1)  use ($d,$teto,$p,$via,$wowd,&$tahunlalu,$blnkurangthnlalu,$dummy,&$ttbesar){


      if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

      
             
        $tetos = SaldoAw::select('saldo_awal.saldo_awal')->whereRaw("YEAR(bulan) = '$d[1]' ")->whereIn('coa',$cc)->get();
        $filteredData = collect($tetos)->whereIn('coa', $cc)->pluck('saldo_awal');    
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
                $total += $value;
            } 
            
            return  $total ;
            
      }else if ($via == '0'){
        
        //  $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
      
        //     foreach($tet as $s){
        //         $aa[] = [
        //              'saldo' =>  preg_split("/[\+\-]/",$s->rumus),
        //             ];
            
             
        // }    
        

        // for($i = 0; $i < count($aa); $i++){
        //   $saldoo[] = $aa[$i]['saldo'];
        // //   $saldo2[] = $b[$i]['saldo2'];
        // }
        // $results = DB::table('rumlap_keuangan')
        //   ->selectRaw("rumus,nama")
        // //   ->whereIn('rumus', $s1)
        //     ->where(function ($query) use ($s1) {
        //         foreach ($s1 as $value) {
        //             $query->orWhere('rumus', '=', $value);
        //         }
        //     })
        //     ->get();
        
        

        // foreach($results as $s){
        //      $bb = [
        //             'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
        //             // 'sd' =>   preg_split("/[\+\-][\ ]/",$s->rumus),
        //             // 'sd' =>   preg_split('/[+\-][\ ]+/', $s->rumus, -1, PREG_SPLIT_NO_EMPTY),
                   
        //             ];
        // }   
        //           $cc = $bb['sd'];
        
        
        //  foreach($results as $s){
        //      $tt = [
        //             'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
        //             ];
        // }   
        //           $ttbesar = $tt['tanda'];
        
        // $filteredData = collect($tahunlalu)->whereIn('coa', $cc)->pluck('neraca_s');
        
        // $total = 0;
        // $totals = 0;
        // for($i = 0; $i < count($filteredData); $i++){
        //     $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
        //     $total += $value;
        //     }
            
            
          $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();


    
          foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
            $tetos = SaldoAw::select('saldo_awal.saldo_awal')->whereRaw("bulan = '$blnkurangthnlalu' ")->whereIn('coa',$cc)->get();
                $filteredData = collect($tetos)->whereIn('coa', $cc)->pluck('saldo_awal');   
          
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
        
        
      if($dummy == '01'){
            return $totals;
            }else{
            return $total;         
        }
        

  }
        
            })
          ->addColumn('saldo3', function($s1)  use ($d,$teto,$p,$via,$wowd,&$januari,&$ttbesar){
  if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();

    
          foreach($results as $s){
             $bb = [
                    // 'sd' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.* ")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[11]' ")
                    ->whereIn('coa', $cc)
                    ->get();
             
             
         foreach($results as $s){
            //   $stringElement = strval($s);
             $tt = [
                    'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                    ];
        }   
                  $ttbesar = $tt['tanda'];     
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
               
            } 
      return $total;
  }else if ($via == '0'){
      
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];

        $filteredData = collect($januari)->whereIn('coa', $cc)->pluck('neraca_s');
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            
        return ($total);         
        
  }
        
        
        
        
        
            })    
          ->addColumn('saldo4', function($s1)  use ($d,$teto,$p,$via,$wowd,&$febuari,&$ttbesar){

  if($via == '1'){
         $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[10]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
  } else if ($via == '0'){
        

        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  

        $filteredData = collect($febuari)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
         }) 
          ->addColumn('saldo5', function($s1)  use ($d,$teto,$p,$via,$wowd,&$maret,&$ttbesar){
   
 if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[9]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
         foreach($results as $s){
            //   $stringElement = strval($s);
             $tt = [
                    'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                    ];
        }   
                  $ttbesar = $tt['tanda'];     
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            
            } 
             
            return  $total ;
 } else if ($via == '0'){
        
          

        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        $filteredData = collect($maret)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
        
         })  
          ->addColumn('saldo6', function($s1)  use ($d,$teto,$p,$via,$wowd,&$april,&$ttbesar){
            if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[8]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
          foreach($results as $s){
            //   $stringElement = strval($s);
             $tt = [
                    'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                    ];
        }   
                  $ttbesar = $tt['tanda'];     
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
            
            
            return  $total ;
            } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        

        
        $filteredData = collect($april)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
        
         })  

          ->addColumn('saldo7', function($s1)  use ($d,$teto,$p,$via,$wowd,&$meii,&$ttbesar){
        if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[7]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
          foreach($results as $s){
            //   $stringElement = strval($s);
             $tt = [
                    'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                    ];
        }   
                  $ttbesar = $tt['tanda'];     
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
        } else if ($via == '0'){
        

        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
    
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        $filteredData = collect($meii)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
  
         })
          ->addColumn('saldo8', function($s1)  use ($d,$teto,$p,$via,$wowd,&$juni,&$ttbesar){
    if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[6]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();

             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
          } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        $filteredData = collect($juni)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
            })
          ->addColumn('saldo9', function($s1)  use ($d,$teto,$p,$via,$wowd,&$juli,&$ttbesar){
    if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.* ")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[5]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
  
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
            
    } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
    
        $filteredData = collect($juli)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
         })
          ->addColumn('saldo10', function($s1)  use ($d,$teto,$p,$via,$wowd,&$agustus,&$ttbesar){
    if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[4]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
    } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        

        $filteredData = collect($agustus)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
            })
          ->addColumn('saldo11', function($s1)  use ($d,$teto,$p,$via,$wowd,&$september,&$ttbesar){
     if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.* ")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[3]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
        
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
     } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        
        $filteredData = collect($september)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
            
            })
          ->addColumn('saldo12', function($s1)  use ($d,$teto,$p,$via,$wowd,&$oktober,&$ttbesar){
  if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[2]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
  } else if ($via == '0'){
       
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        $filteredData = collect($oktober)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
            })
          ->addColumn('saldo13', function($s1)  use ($d,$teto,$p,$via,$wowd,&$november,&$ttbesar){
    if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[1]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
            
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
    } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        
        $filteredData = collect($november)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
  
            })
          ->addColumn('saldo14', function($s1)  use ($d,$teto,$p,$via,$wowd,&$desember,&$ttbesar){
    if($via == '1'){
      $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
            
            
        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m-%d') = '$p[0]' ")
                    ->whereIn('t.coa', $cc)
                    ->get();
             
             
          $filteredData = collect($tetos)->pluck('saldo_awal');     
             
            $total = 0;
            for($i = 0; $i < count($filteredData); $i++){
                $total += $ttbesar[$i].$filteredData[$i] ;
            } 
             
            return  $total ;
    } else if ($via == '0'){
        
        $results = DB::table('rumlap_keuangan')
          ->selectRaw("rumus,nama")
        //   ->whereIn('rumus', $s1)
            ->where(function ($query) use ($s1) {
                foreach ($s1 as $value) {
                    $query->orWhere('rumus', '=', $value);
                }
            })
            ->get();
        
        

        foreach($results as $s){
             $bb = [
                    'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
        
        
        $filteredData = collect($desember)->whereIn('coa', $cc)->pluck('neraca_s');
        
        $total = 0;
        $totals = 0;
        for($i = 0; $i < count($filteredData); $i++){
            $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
            $total += $value;
            }
            

        return $total;         
        
  }
            })
            ->rawColumns(['coah','saldo1','saldo2','saldo3','saldo4','saldo5','saldo6','saldo7','saldo8','saldo9','saldo10','saldo11','saldo12','saldo13','saldo14'])
            ->make(true);
        }
        
        return view ('fins-laporan.laporan_bulanan',compact('jenis'));
    }
    
    public function laporan_bulanan_tabel(Request $request){
        $p = $request->tahun == '' ? date('Y') : $request->tahun;
        $m = $request->tahun == '' ? date('Y') : $request->tahun;


        $currentYear = $p;
        $currentMonth = $m;
        $range = 1; // rentang tahun yang ingin ditampilkan
        $oldestYear = $currentYear - $range;
        
        $data = [];
        
        for ($year = $currentYear; $year >= $oldestYear; $year--) {
            $data['tahun'][] = $year;
            $data['th'][] = substr($year, -2);
        }
        
        
        for ($i = 1; $i <= 12; $i++) {
            $bulan = date('M', mktime(0, 0, 0, $i, 1));
            $data['bulan'][] = $bulan;
        }

        
        return $oldestYear;
    }
    
    public function laporan_keuangan(Request $request){
       
     
        $query = DB::table('rumlap_keuangan as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('rumlap_keuangan as t0')
                            ->select('t3.*', 't0.root')
                            ->join('rumlap_keuangan as t3', 't3.id', '=', 't0.id')
                    );
                    
            $querys = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );   
                    
                    
         $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();  

        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
      $jenis = JenlapKeuangan::where('aktif','y')->get();
        
      
        
        
        if ($request->ajax()) {
             
           
            $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
         
        
           
         
          
          
      
            $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
            $currentYear = $p;
            $range = 1; // rentang tahun yang ingin ditampilkan
            $oldestYear = $currentYear - $range;
            
            $wowd = $request->jenis;
            $via = $request->via;
            $multinya = $request->mulbul ; 
            $blnkurangbanyakbgt = [];
            $blnkuranglahunlalubanyakbgt = [] ;
            
           
         
            $tet =  date('m') ; 
            $dummy = [$tet,$tet]; // untuk multi default multi bulan
            $mon = $request->bln == '' ? date('m') : $request->bln;  
            $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
            $bulawal = $multinya == '0' ? date('m') : reset($monbanyak) ;
            $bulterkahir = $multinya == '0' ? date('m') : end($monbanyak) ;
            $blnterakhirkurang = $multinya == '0' ? $request->bln : end($monbanyak) - 01 ;


          
             $filun= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December" .$currentYear);
                $filun[] = date("Y-m",$date) ;
            }

             $thnlalu= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December" .$oldestYear);
                $thnlalu[] = date("Y-m",$date) ;
            }
       


              
              
                    // $bul1 = $mon;
                    // $bul2 = $mon;
                
                    $bul1bnk = reset($monbanyak) ;
                    $bul2bnk = end($monbanyak);
             
                
                
                 $tet =  date('m') ; 
                 
             // bulan yang dikurang 
                    $bulkur1 =  date("Y-m-t", strtotime('-1 month', strtotime($p.'-'.$mon.'-01')));
                    // $bulkur1 = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$mon.'-'.$p)));
                    // $bulkurthnlalu1 = date("m-t", strtotime("-1 month", strtotime(date('Y').'-'.$mon.'-01')));
                    
                
                    $bulkurthnlalu1 = date("m-t", strtotime("-1 month ", strtotime($oldestYear.'-'.$mon.'-01')));
                    
                
                
                    
                $bulta = date("Y-m",strtotime($p.'-'.$mon));
                $blnkurangbanyakbgtthnllalu = [];
                $blnkurangbanyakbgt = [];
                $bnyak = [];
                $blnbanyakbgtthnllalu = [];
                     foreach ($monbanyak as $date) {
                            $bnyakbet = date("Y-m",strtotime('01-'.$date.'-'.$p));;
                            $blnkurangbanyak = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$p)));
                            $blnkurangbanyakthnlalu = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$oldestYear)));
                            $blnbanyakthnlalu = date("Y-m", strtotime('01-'.$date.'-'.$oldestYear));
                        if ($blnkurangbanyak !== false) {
                            $blnkurangbanyakbgt[] = $blnkurangbanyak;
                        }
                        if ($bnyakbet !== false) {
                            $bnyak[] = $bnyakbet;
                        }
                        if ($blnkurangbanyakthnlalu !== false) {
                            $blnkurangbanyakbgtthnllalu[] = $blnkurangbanyakthnlalu;
                        }
                        if ($blnbanyakthnlalu !== false) {
                            $blnbanyakbgtthnllalu[] = $blnbanyakthnlalu;
                        }
                            }
                
            $tahun = date('Y', strtotime("01-".$p));
            $tahunlalu = date('Y', strtotime("01-".$oldestYear));
            

            $d = [];
            for ($year = $currentYear; $year >= $oldestYear; $year--) {
                $d[] = $year;
            }
         
         
            $baru = SaldoAw::selectRaw("DISTINCT(bulan)")->whereYear('bulan',$d[0])->latest();
            
            $ngitung = count($baru->get());
            // return($baru->first()->bulan);
            
            if($ngitung > 0){
                $l = date('m', strtotime($baru->first()->bulan));
            }else{
                $l = '12';
            }
   
      
   
        $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
      
        foreach($tet as $s){
             $a[] = [
                        // 'indik' => $s->indikator,
                        'saldo' =>  preg_split("/[\+\-\s]/" ,$s->rumus),
                    ];
        }    

        foreach($a as $s){
             $b[] = [
                        'saldo' =>  $s,
                        
                    ];
        }    
    
      

        for($i = 0; $i < count($a); $i++){
          $saldo[] = $a[$i]['saldo'];
        //   $saldo2[] = $b[$i]['saldo2'];
        }
        
         for($i = 0; $i < count($saldo); $i++){
          $s1[] = $saldo[$i];
   
        }
        
        
  $filunDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$p)),date("Y-m",strtotime('01-'.$mon.'-'.$p))] : $bnyak ;
  $thnlaluDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear)),date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear))] : $blnbanyakbgtthnllalu ;


    $combinedArray = [];
    foreach ($s1 as $subArray) {
        $combinedArray = array_merge($combinedArray, $subArray);
    }


    // $combinedArray = array_filter($combinedArray);
    $combinedArrayString = "'" . implode("','", $combinedArray) . "'";
    $coaValues = collect($combinedArray)->flatten()->unique()->implode("','");
        $hasilz = [];
        $hasil1 = [];
        $hasil2 = [];
        $hasil3 = [];
          $zzz = [];
          $aaa = [];
          $aaaa = [];
          $aaaaa = [];
         $teto = DB::table('rumlap_keuangan')
            ->whereRaw("id_jenlap = $wowd AND aktif = 'y'")
         ->get();
         
          foreach($teto as $s){
                     $zzz[] = [
                                'rumus' =>  $s->rumus,
                            ];
                }    
            
                        foreach($zzz as $val){
                            $hasilz[] = 
                                preg_split("/[\+\-\s]/" ,$val['rumus']) ;
                                ;
                        }
       
        // $coanya = preg_split("/[\+\-\s]/" ,$hasilz) ;
        $data = COA::whereIn('coa', $hasilz)->get();
          foreach($data as $s){
                     $aa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aa as $val){
                            $hasil[] = 
                                 $val['id']
                                ;
                        }
       
        // if(count($hasil) > 0 ){
        $data1 = COA::whereIn('id_parent', $hasil)->get();
             foreach($data1 as $s){
                     $aaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaa as $val){
                            $hasil1[] = 
                                 $val['id']
                                ;
                        }
        // }else if (count($hasil1) > 0){
          $data2 = COA::whereIn('id_parent', $hasil1)->get();
          foreach($data2 as $s){
                     $aaaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaaa as $val){
                            $hasil2[] = 
                                 $val['id']
                                ;
                        }
        // }else if(count($hasil2) > 0){
            $data3 = COA::whereIn('id_parent', $hasil2)->get();
            foreach($data3 as $s){
                     $aaaaa[] = [
                                'coa' =>  $s->coa,
                            ];
                }    
            
                        foreach($aaaaa as $val){
                            $hasil3[] = 
                                 $val['coa']
                                ;
                        }
        
        
        $mergedArray = array_merge($hasil,$hasil1, $hasil2, $hasil3);





          if($via == '0'){
              
                $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                        ->whereRaw("MONTH(tanggal) = '$mon' AND YEAR(tanggal) = '$p' AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tgl) = '$mon' AND YEAR(tgl) = '$p' AND acc = 1"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tanggal) = '$mon' AND YEAR(tanggal) = '$p' AND acc = 1"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                ->whereRaw("MONTH(transaksi.tanggal) = '$mon' AND YEAR(transaksi.tanggal) = '$p' AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
                                )
                        ;
              
           
              
                // $cteTransaksi = DB::table('transaksi')
                //     ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                //     >where(function ($query) use ($filunDates) {
                //             foreach ($filunDates as $date) {
                //     $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                //         }
                //     })
                //     ->whereRaw("approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                
                //     ;
                

                // $ctePengeluaran = DB::table('pengeluaran')
                //     ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                //      ->where(function ($query) use ($filunDates) {
                //             foreach ($filunDates as $date) {
                //     $query->WhereRaw("DATE_FORMAT(tgl, '%Y-%m') = '$date'");
                //         }
                //     })
                //      ->whereRaw("acc = 1")    
                //     ;
                
               
                  
                
                // $cteJurnal = DB::table('jurnal')
                //     ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                //      ->where(function ($query) use ($filunDates) {
                //             foreach ($filunDates as $date) {
                //     $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                //         }
                //     })
                //     ->whereRaw("acc = 1")    
                //     ;
                    
              
                
                // $cteProg = DB::table('transaksi')
                //     ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                //       ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
                //       0 as nominal_debit, 0 as nominal_kredit, 
                //       transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                //       ->where(function ($query) use ($filunDates) {
                //             foreach ($filunDates as $date) {
                //     $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                //         }
                //     })
                //     ->whereRaw("transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
                  
                //   ;
                   
                // $union = $cteTransaksi->unionAll($ctePengeluaran)->unionAll($cteJurnal)->unionAll($cteProg);
             


            $cteTransaksithn = DB::table('transaksi')
                    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                     ->where(function ($query) use ($thnlaluDates) {
                            foreach ($thnlaluDates as $date) {
                    $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                        }
                    })
                    ->whereRaw("approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                    
                    // ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') >= '$thnlalu[12]' AND DATE_FORMAT(tanggal,'%Y-%m') <= '$thnlalu[0]' AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                    ;
                

                $ctePengeluaranthn = DB::table('pengeluaran')
                    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                      ->where(function ($query) use ($thnlaluDates) {
                            foreach ($thnlaluDates as $date) {
                    $query->WhereRaw("DATE_FORMAT(tgl, '%Y-%m') = '$date'");
                        }
                    })
                     ->whereRaw("acc = 1")   
                    // ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') >= '$thnlalu[12]' AND DATE_FORMAT(tgl,'%Y-%m') <= '$thnlalu[0]' AND acc = 1 ")
                    ;
                
               
                  
                
                $cteJurnalthn = DB::table('jurnal')
                    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                    ->where(function ($query) use ($thnlaluDates) {
                            foreach ($thnlaluDates as $date) {
                    $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                        }
                    })
                    ->whereRaw("acc = 1")    
                    // ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') >= '$thnlalu[12]' AND DATE_FORMAT(tanggal,'%Y-%m') <= '$thnlalu[0]'  AND acc = 1  ")
                  
                    ;
                    
              
                
                $cteProgthn = DB::table('transaksi')
                    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
                      0 as nominal_debit, 0 as nominal_kredit, 
                      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                     ->where(function ($query) use ($thnlaluDates) {
                            foreach ($thnlaluDates as $date) {
                    $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                        }
                    })
                    ->whereRaw("transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
                  ;
                   
             

                $unionthlalu = $cteTransaksithn->unionAll($ctePengeluaranthn)->unionAll($cteJurnalthn)->unionAll($cteProgthn);            
  
  
  
                  
  
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
          
            $saldo1 = DB::table('b as t')
                ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level, t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                ->withRecursiveExpression('b', $query)
                ->leftjoin(DB::raw("({$union->toSql()}) as sub"), function ($join) use ($currentYear, $mon, $multinya, $bnyak) {
                    $join->on('sub.coa_debet', '=', 't.coa')
                        ->where(function ($query) use ($multinya, $currentYear, $mon, $bnyak) {
                            if (!empty($bnyak) && count($bnyak) > 0) {
                                if (count($bnyak) > 1) {
                                    $query->whereIn(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), function ($query) use ($bnyak) {
                                        foreach ($bnyak as $tgl) {
                                            $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))
                                                ->where(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $tgl);
                                        }
                                    });
                                } else {
                                    $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))
                                        ->where(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $bnyak[0]);
                                }
                            } else {
                                // Handle single date case (if needed)
                                $query->whereMonth('sub.tanggal', $mon)
                                    ->whereYear('sub.tanggal', $currentYear)
                                    ->where('acc', 1);
                            }
                        });
                })
                ->groupBy('root')
                ->get();

                        
                    $saldo2 = DB::table('b as t')
                    ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                    ->withRecursiveExpression('b', $query)
                    ->leftjoin('saldo_awal as t2', function ($join) use ($currentYear, $multinya, $bulkur1, $blnkurangbanyakbgt, $bnyak, $mon) {
                        $join->on('t2.coa', '=', 't.coa')
                            ->where(function ($query) use ($multinya, $currentYear, $bulkur1, $blnkurangbanyakbgt, $bnyak, $mon) {
                                if (!empty($bnyak) && count($bnyak) > 0) {
                                    if ($multinya == "1") {
                                        $query->whereIn(DB::raw("DATE_FORMAT(t2.bulan, '%Y-%m')"), $bnyak);
                                    } else {
                                        $query
                                            ->whereMonth('t2.bulan', $mon)
                                            ->whereYear('t2.bulan', $currentYear);
                                    }
                                } else {
                                    // Handle single date case (if needed)
                                    $query->whereRaw("t2.bulan = '$bulkur1'");
                                }
                            });
                    })
                    ->groupBy('root')
                    ->get();

           
                $saldox = DB::table('b as t')
                ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level, t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                ->withRecursiveExpression('b', $query)
                ->leftjoin(DB::raw("({$union->toSql()}) as sub"), function ($join) use ($currentYear, $mon, $multinya, $bnyak) {
                    $join->on('sub.coa_kredit', '=', 't.coa')
                        ->where(function ($query) use ($multinya, $currentYear, $mon, $bnyak) {
                            if (!empty($bnyak) && count($bnyak) > 0) {
                                if (count($bnyak) > 1) {
                                    $query->whereIn(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), function ($query) use ($bnyak) {
                                        foreach ($bnyak as $tgl) {
                                            $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))
                                                ->where(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $tgl);
                                        }
                                    });
                                } else {
                                    $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))
                                        ->where(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $bnyak[0]);
                                }
                            } else {
                                // Handle single date case (if needed)
                                $query->whereMonth('sub.tanggal', $mon)
                                    ->whereYear('sub.tanggal', $currentYear)
                                    ->where('acc', 1);
                            }
                        });
                })
                ->groupBy('root')
                ->get();
                        
           
                        
            $inArray = [];
            $a = 0;
            foreach($saldo1 as $i => $val){
                if($saldo2[$i]->coa == $val->coa ){
                    $a = $saldo2[$i]->saldo_awal ;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                        $inArray[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'id_parent' => $val->id_parent,
                            'nama_coa' => $val->nama_coa,
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'saldo_awal' => $a,
                            'neraca_s' => ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                          
                           
                        ];
                    
          }
         $saldothnlalu = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$unionthlalu->toSql()}) as sub"),function($join) use ($oldestYear,$mon,$multinya,$blnbanyakbgtthnllalu,$via) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            ->where(function ($query) use ($multinya,$oldestYear,$blnbanyakbgtthnllalu,$mon,$via) {
                                if ($multinya == "1") {
                                    $query->whereIn(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), function ($query) use ($blnbanyakbgtthnllalu) {
                                             foreach ($blnbanyakbgtthnllalu as $tgl) {
                                    $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))->orWhere(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $tgl);
                                            }
                                        });

                                 }else if($multinya == "0" ) {
                                        $query->whereMonth('sub.tanggal', $mon)
                                        ->whereYear('sub.tanggal', $oldestYear)
                                        ->where('acc', 1);
                            }
                            });
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldothnlalu2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($oldestYear,$multinya,$bulkurthnlalu1,$blnkurangbanyakbgtthnllalu,$via,$blnbanyakbgtthnllalu) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->where(function ($query) use ($multinya,$oldestYear,$bulkurthnlalu1,$blnkurangbanyakbgtthnllalu,$via,$blnbanyakbgtthnllalu) {
                                if ($multinya ==  "1") {
                                    $query->whereIn(DB::raw("DATE_FORMAT(t2.bulan, '%Y-%m')"), $blnbanyakbgtthnllalu);
                                        // $query->whereIn(DB::raw('t2.bulan'), $blnkurangbanyakbgtthnllalu);
                                 }else if($multinya == "0" ) {
                                        $query->whereRaw("DATE_FORMAT(t2.bulan, '%m-%t') = '$bulkurthnlalu1'")
                                         ->whereYear('t2.bulan', $oldestYear);
                            }
                            });
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                $saldothnlalux = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$unionthlalu->toSql()}) as sub"),function($join) use ($multinya,$oldestYear,$blnbanyakbgtthnllalu,$mon,$via) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                            ->where(function ($query) use ($multinya,$oldestYear,$blnbanyakbgtthnllalu,$mon,$via) {
                                if ($multinya == "1") {
                                    $query->whereIn(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), function ($query) use ($blnbanyakbgtthnllalu) {
                                             foreach ($blnbanyakbgtthnllalu as $tgl) {
                                    $query->select(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"))->orWhere(DB::raw("DATE_FORMAT(sub.tanggal,'%Y-%m')"), '=', $tgl);
                                            }
                                        });
                                 }else if($multinya == "0" ) {
                                        $query->whereMonth('sub.tanggal', $mon)
                                        ->whereYear('sub.tanggal', $oldestYear)
                                        ->where('acc', 1);
                            }
                            });
                        })
                            
                        ->groupBy('root')
                        ->get(); 
            $tahunlalu = [];
            $b = 0;
            foreach($saldothnlalu as $i => $val){
                if($saldothnlalu2[$i]->coa == $val->coa){
                    $b = $saldothnlalu2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($saldothnlalux[$i]->coa == $val->coa){
                    $deb = $saldothnlalux[$i]->debit + $val->debit;
                    $kre = $saldothnlalux[$i]->kredit + $val->kredit;
                }
                        $tahunlalu[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                          

                        ];
                    
          }
         




          }else{
            $inArray = DB::table('b as t')
            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent,bulan")
            ->withRecursiveExpression('b', $querys)
            ->leftjoin('saldo_awal as t2',function($join) use ($multinya,$bulterkahir,$mon,$d) {
                $join->on('t2.coa' ,'=', 't.coa')
            ->where(function ($querys) use ($multinya,$bulterkahir,$mon,$d) {
                    if ($multinya == "1") {
                            $querys->whereRaw("YEAR(bulan) = '$d[0]' AND DATE_FORMAT(bulan,'%m') = $bulterkahir ");
                                // $query->whereIn(DB::raw('t2.bulan'), $blnkurangbanyakbgt);
                     }else if($multinya == "0" ) {
                            $querys->whereRaw("YEAR(bulan) = '$d[0]' AND DATE_FORMAT(bulan,'%m') = $mon ");
                            // ->whereRaw("t2.bulan = '$bulkur1'");
                    }
                });
            })
                            
                        ->groupBy('root')
                        ->get();  
                        
            $tahunlalu = DB::table('b as t')
            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent,bulan")
            ->withRecursiveExpression('b', $querys)
            ->leftjoin('saldo_awal as t2',function($join) use ($multinya,$bulterkahir,$mon,$d) {
                $join->on('t2.coa' ,'=', 't.coa')
            ->where(function ($querys) use ($multinya,$bulterkahir,$mon,$d) {
                    if ($multinya == "1") {
                            $querys->whereRaw("YEAR(bulan) = '$d[1]' AND DATE_FORMAT(bulan,'%m') = $bulterkahir ");
                                // $query->whereIn(DB::raw('t2.bulan'), $blnkurangbanyakbgt);
                     }else if($multinya == "0" ) {
                            $querys->whereRaw("YEAR(bulan) = '$d[1]' AND DATE_FORMAT(bulan,'%m') = $mon ");
                            // ->whereRaw("t2.bulan = '$bulkur1'");
                    }
                });
            })
                            
                        ->groupBy('root')
                        ->get();              
          }
      
            $data = DataTables::of($teto)
            ->addIndexColumn()
            ->addColumn('coah', function($teto){
                if($teto->rumus == '0' && $teto->level == '1' ){  
                    $c = '<b>'. $tet->nama. '</b>';
                }else if($teto->rumus != '0' && $teto->level == '2' && $teto->nama == 'Penyaluran' || $teto->nama ==  'Penerimaan' ){
                    $c = '&nbsp; <b>'. $teto->nama. '</b>';
                }else if($teto->level == '1' && $teto->rumus != '0'){
                  $c = '<b>'. $teto->nama. '</b>';
                }else {
                      $c = '&nbsp;&nbsp;'.$teto->nama;
                }
                return $c;
            })
            
    
        ->addColumn('saldo1', function($s1)  use ($d,$teto,$a ,&$via ,&$saldo,&$blnskrng,&$blnkurang,&$inArray,&$wowd,&$tahunlalu,&$mon,$multinya,$bulterkahir,&$mergedArray){
            //dari sini yang di pake 
          if($via == '1'){
                 $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
              
                    foreach($tet as $s){
                        $aa[] = [
                             'saldo' =>  preg_split("/[\+\-]/",$s->rumus),
                            ];
                    
                     
                }    
                
        
                for($i = 0; $i < count($aa); $i++){
                  $saldoo[] = $aa[$i]['saldo'];
                //   $saldo2[] = $b[$i]['saldo2'];
                }
                $results = DB::table('rumlap_keuangan')
                  ->selectRaw("rumus,nama")
                    ->where(function ($query) use ($s1) {
                        foreach ($s1 as $value) {
                            $query->orWhere('rumus', '=', $value);
                        }
                    })
                    ->get();
                
                
        
                foreach($results as $s){
                     $bb = [
                            'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                            ];
                }   
                          $cc = $bb['sd'];
                
                
                 foreach($results as $s){
                     $tt = [
                            'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                            ];
                }   
                          $ttbesar = $tt['tanda'];
                $filteredData = collect($inArray)->whereIn('coa', $cc)->pluck('saldo_awal');
                
              
        
                $total = 0;
                for($i = 0; $i < count($filteredData); $i++){
                    
                    $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
                    $total += $value;
                        // $total += $ttbesar[$i].$filteredData[$i];
                    }
                
               
            
                return $total;   
           }else if ($via == '0'){
                 $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
                    foreach($tet as $s){
                        $aa[] = [
                             'saldo' =>  preg_split("/[\+\-]/",$s->rumus),
                            ];
                    
                     
                }    
                
        
                for($i = 0; $i < count($aa); $i++){
                  $saldoo[] = $aa[$i]['saldo'];
                //   $saldo2[] = $b[$i]['saldo2'];
                }
                $results = DB::table('rumlap_keuangan')
                  ->selectRaw("rumus,nama")
                    ->where(function ($query) use ($s1) {
                        foreach ($s1 as $value) {
                            $query->orWhere('rumus', '=', $value);
                        }
                    })
                    ->get();
                
                
        
                foreach($results as $s){
                     $bb = [
                            'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                            ];
                }   
                          $cc = $bb['sd'];
                
                
                 foreach($results as $s){
                     $tt = [
                            'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                            ];
                }   
                          $ttbesar = $tt['tanda'];
                $filteredData = collect($inArray)->whereIn('coa', $cc)->pluck('neraca_s');
                
        
                $total = 0;
                for($i = 0; $i < count($filteredData); $i++){
                    
                        $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
                        $total += $value;
                            // $total += $ttbesar[$i].$filteredData[$i];
                        }
                    
                   
                    return $total;     
                  }
        })
            
            
        ->addColumn('saldo2', function($s1)  use ($blnskrng,$blnkurang,$teto,$d,$wowd,$via,&$tahunlalu,&$mon,$multinya,$bulterkahir){
            
                if($via == '1'){
               
                
                         $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
                  
                        foreach($tet as $s){
                            $aa[] = [
                                 'saldo' =>  preg_split("/[\+\-]/",$s->rumus),
                                ];
                        
                         
                    }    
                    
            
                    for($i = 0; $i < count($aa); $i++){
                      $saldoo[] = $aa[$i]['saldo'];
                    //   $saldo2[] = $b[$i]['saldo2'];
                    }
                    $results = DB::table('rumlap_keuangan')
                      ->selectRaw("rumus,nama")
                    //   ->whereIn('rumus', $s1)
                        ->where(function ($query) use ($s1) {
                            foreach ($s1 as $value) {
                                $query->orWhere('rumus', '=', $value);
                            }
                        })
                        ->get();
                    
                    
            
                    foreach($results as $s){
                         $bb = [
                                'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                                ];
                    }   
                              $cc = $bb['sd'];
                    
                    
                     foreach($results as $s){
                         $tt = [
                                'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                                ];
                    }   
                              $ttbesar = $tt['tanda'];
                    $filteredData = collect($tahunlalu)->whereIn('coa', $cc)->pluck('saldo_awal');
                    
                  
            
                    $total = 0;
                    for($i = 0; $i < count($filteredData); $i++){
                        
                        $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
                        $total += $value;
                            // $total += $ttbesar[$i].$filteredData[$i];
                        }
                    
                   
                
                    return $total;   
                        
                   
              }else if ($via == '0'){
                    
                     $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
                  
                        foreach($tet as $s){
                            $aa[] = [
                                 'saldo' =>  preg_split("/[\+\-]/",$s->rumus),
                                ];
                        
                         
                    }    
                    
            
                    for($i = 0; $i < count($aa); $i++){
                      $saldoo[] = $aa[$i]['saldo'];
                    //   $saldo2[] = $b[$i]['saldo2'];
                    }
                    $results = DB::table('rumlap_keuangan')
                      ->selectRaw("rumus,nama")
                    //   ->whereIn('rumus', $s1)
                        ->where(function ($query) use ($s1) {
                            foreach ($s1 as $value) {
                                $query->orWhere('rumus', '=', $value);
                            }
                        })
                        ->get();
                    
                    
            
                    foreach($results as $s){
                         $bb = [
                                'sd' =>  preg_split("/[\ ][\+-][\ ]+/",$s->rumus),
                                ];
                    }   
                              $cc = $bb['sd'];
                    
                    
                     foreach($results as $s){
                         $tt = [
                                'tanda' =>  preg_split("/[\d. ]+/" ,$s->rumus),
                                ];
                    }   
                              $ttbesar = $tt['tanda'];
                    $filteredData = collect($tahunlalu)->whereIn('coa', $cc)->pluck('neraca_s');
                    
            
                    $total = 0;
                    for($i = 0; $i < count($filteredData); $i++){
                        
                        $value = ($ttbesar[$i] === '-') ? -$filteredData[$i] : $filteredData[$i];
                        $total += $value;
                            // $total += $ttbesar[$i].$filteredData[$i];
                        }
                    
                   
                
                    return $total;     
            
                        
                 
                    
              }
            
            })
            ->rawColumns(['coah','saldo1','saldo2'])
            ->make(true);
            
            if($request->tombol == 'xls'){
                $response =  Excel::download(new LaporanKeuanganExport($data->getData()->data, $request->title, $p), $request->title . ' Tahun '. $p .'.xlsx');
                ob_end_clean();
                return $response;
            }else if($request->tombol == 'csv'){
                $response =  Excel::download(new LaporanKeuanganExport($data->getData()->data, $request->title, $p), $request->title . ' Tahun '. $p .'.csv');
                ob_end_clean();
                return $response;
            }
            
            return $data;
        }
        
        return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    }
    
    public function ekspor(Request $request){
        
    }
    
    public function eksporbulanan(Request $request){
            
     
        $tahun = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $jenis = $request->jenis;
        $title = $request->jenis == 1 ? 'Perubahan Dana' : $request->jenis == 2 ? 'Posisi Keuangan' : 'Arus Kas' ;
        $via = $request->via;

  
        if($request->tombol == 'xls'){
            $response =  Excel::download(new LaporanBulananExport( $tahun, $jenis,$via,$title), 'Laporan Bulanan '.$title.'.xlsx');

        }else{
            $response =  Excel::download(new LaporanBulananExport( $tahun, $jenis,$via,$title), 'Laporan Bulanan '.$title.'.csv');
        }
        ob_end_clean();
        
        return $response;
    }
    
    public function detail_keuangan(Request $request){
       $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
       
         $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
         $currentYear = $p;
         $range = 1; // rentang tahun yang ingin ditampilkan
         $oldestYear = $currentYear - $range;
         $wowd = $request->jenis;
         $via = $request->via;
         $multinya = $request->mulbul ; 
         $tet =  date('m') ; 
         $dummy = [$tet,$tet]; // untuk multi default multi bulan
         $mon = $request->bln == '' ? date('m') : $request->bln;  
         $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
         $latestData = end($monbanyak);
         $b = $multinya = '0' ? $mon : $latestData ;
        $inbulan = date("Y-m-t", strtotime('01-'.$mon.'-'.$currentYear));
        
        $cek = SaldoAw::whereDate('bulan', $inbulan)->first();
         $blnkurangbanyakbgt = [];
                $bnyak = [];
                $blnbanyakbgtthnllalu = [];
                     foreach ($monbanyak as $date) {
                            $bnyakbet = date("Y-m",strtotime('01-'.$date.'-'.$p));;
                            $blnkurangbanyak = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$p)));
                            $blnkurangbanyakthnlalu = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$oldestYear)));
                            $blnbanyakthnlalu = date("Y-m", strtotime('01-'.$date.'-'.$oldestYear));
                        if ($blnkurangbanyak !== false) {
                            $blnkurangbanyakbgt[] = $blnkurangbanyak;
                        }
                        if ($bnyakbet !== false) {
                            $bnyak[] = $bnyakbet;
                        }
                        if ($blnkurangbanyakthnlalu !== false) {
                            $blnkurangbanyakbgtthnllalu[] = $blnkurangbanyakthnlalu;
                        }
                        if ($blnbanyakthnlalu !== false) {
                            $blnbanyakbgtthnllalu[] = $blnbanyakthnlalu;
                        }
                            }
       
        $filunDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$p)),date("Y-m",strtotime('01-'.$mon.'-'.$p))] : $bnyak ;
        $thnlaluDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear)),date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear))] : $blnbanyakbgtthnllalu ;
        $bulkur1 =  date("Y-m-t", strtotime('-1 month', strtotime($p.'-'.$mon.'-01')));


    if ($request->ajax()) {
        $hasil1 = [];
        $hasil2 = [];
        $hasil3 = [];
          $aaa = [];
          $aaaa = [];
          $aaaaa = [];
        $coanya = preg_split("/[\+\-\s]/" ,$request->data['rumus']) ;
        $data = COA::whereIn('coa', $coanya)->get();
          foreach($data as $s){
                     $aa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aa as $val){
                            $hasil[] = 
                                 $val['id']
                                ;
                        }
       
     
        $data1 = COA::whereIn('id_parent', $hasil)->get();
             foreach($data1 as $s){
                     $aaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaa as $val){
                            $hasil1[] = 
                                 $val['id']
                                ;
                        }
   
          $data2 = COA::whereIn('id_parent', $hasil1)->get();
          foreach($data2 as $s){
                     $aaaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaaa as $val){
                            $hasil2[] = 
                                 $val['id']
                                ;
                        }

            $data3 = COA::whereIn('id_parent', $hasil2)->get();
            foreach($data3 as $s){
                     $aaaaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaaaa as $val){
                            $hasil3[] = 
                                 $val['id']
                                ;
                        }
        
        
        $mergedArray = array_merge($hasil,$hasil1, $hasil2, $hasil3);
       
        
         $cteTransaksi = DB::table('transaksi')
                    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                   
                    ->WhereRaw("DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$p' ")
                    ->whereRaw("approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                    ;
                

                $ctePengeluaran = DB::table('pengeluaran')
                    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                    ->WhereRaw("DATE_FORMAT(tgl, '%m') = '$b' AND DATE_FORMAT(tgl, '%Y') = '$p' ")
                     ->whereRaw("acc = 1")
                    ;
                
               
                  
                $cteJurnal = DB::table('jurnal')
                    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                    ->WhereRaw("DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$p' ")
                    ->whereRaw("acc = 1")
                    ;
                    
              
                
                $cteProg = DB::table('transaksi')
                    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
                      0 as nominal_debit, 0 as nominal_kredit, 
                      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                    ->whereRaw("transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0 AND DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$p'")
                   
                  ;
                   
                $union = $cteTransaksi->unionAll($ctePengeluaran)->unionAll($cteJurnal)->unionAll($cteProg);
    
     $saldo1 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($currentYear,$mon,$multinya,$filunDates,$bnyak,$b,$p) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                               ->WhereRaw("DATE_FORMAT(sub.tanggal, '%m') = '$b' AND DATE_FORMAT(sub.tanggal, '%Y') = '$p' ")
                                  ->where('acc', 1)
                                ;
                            
                          
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$multinya,$bulkur1,$blnkurangbanyakbgt) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->where(function ($query) use ($multinya,$currentYear,$bulkur1,$blnkurangbanyakbgt) {
                                if ($multinya == "1") {
                                        $query->whereIn(DB::raw('t2.bulan'), $blnkurangbanyakbgt);
                                 }else if($multinya == "0" ) {
                                        $query
                                        ->whereRaw("t2.bulan = '$bulkur1'");
                            }
                            });
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($multinya,$currentYear,$mon,$bnyak,$filunDates,$b,$p) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                 ->WhereRaw("DATE_FORMAT(sub.tanggal, '%m') = '$b' AND DATE_FORMAT(sub.tanggal, '%Y') = '$p' ")
                                 ->where('acc', 1)
                                ;
                            
                          
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent, IF(SUM(t2.closing) IS NOT NULL, SUM(t2.closing), 0) as closing, IF(COUNT(t2.closing) IS NOT NULL, COUNT(t2.closing), 0) as conclos")
                            ->withRecursiveExpression('b', $query)
                            ->leftjoin('saldo_awal as t2',function($join) use ($inbulan) {
                                $join->on('t2.coa' ,'=', 't.coa')
                                    ->whereDate('t2.bulan', $inbulan);
                            })
                            ->groupBy('root')
                            ->get(); 
                }
                        
            $inArray = [];
            $a = 0;
            foreach($saldo1 as $i => $val){
                if($cek != null){
                    $closed = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->saldo_awal : 0;
                    $closing = $saldo3[$i]->coa == $val->coa ? ($saldo3[$i]->closing > 0 && $saldo3[$i]->closing == $saldo3[$i]->conclos ? 1 : 0) : 0;
                }else{
                    $closed = 0;
                    $closing = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa ){
                    $a = $saldo2[$i]->saldo_awal ;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                        $inArray[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'saldo_awal' => 0,
                            'id_parent' => $val->id_parent,
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'neraca_saldo' => substr($val->coa, 0, 1) == 4 ? $a - $deb + $kre : ($a + $deb) - $kre,
                            'neraca_s' => ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'closed' => $closed ,
                            // 'neraca_s' => ($a + $val->debit) - $val->kredit + $val->debit_s - $val->kredit_s,
                        ];
                    
          }
        //   if(count($datas) > 0){
              
        //     $filteredData1 = collect($inArray)->whereIn('coa', $semcoa); 
        //   }else {
        //      $filteredData1 = collect($inArray)->whereIn('coa', $coanya); 
        //   }
            $filteredData = collect($inArray)->whereIn('id_parent', $mergedArray); 
            // $finaldata  = array_merge($filteredData, $filteredData);
            return DataTables::of($filteredData)

            ->make(true);
        }
        
        return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    }
    
    public function detail_keuangan2(Request $request){
            $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
       
         $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
         $currentYear = $p;
         $range = 1; // rentang tahun yang ingin ditampilkan
         $oldestYear = $currentYear - $range;
         $wowd = $request->jenis;
         $via = $request->via;
         $multinya = $request->mulbul ; 
         $tet =  date('m') ; 
         $dummy = [$tet,$tet]; // untuk multi default multi bulan
         $mon = $request->bln == '' ? date('m') : $request->bln;  
         $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
         $latestData = end($monbanyak);
         $b = $multinya = '0' ? $mon : $latestData ;
         $inbulan = date("Y-m-t", strtotime('01-'.$mon.'-'.$oldestYear));
        
        $cek = SaldoAw::whereDate('bulan', $inbulan)->first();
         $blnkurangbanyakbgt = [];
                $bnyak = [];
                $blnbanyakbgtthnllalu = [];
                     foreach ($monbanyak as $date) {
                            $bnyakbet = date("Y-m",strtotime('01-'.$date.'-'.$p));;
                            $blnkurangbanyak = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$p)));
                            $blnkurangbanyakthnlalu = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$date.'-'.$oldestYear)));
                            $blnbanyakthnlalu = date("Y-m", strtotime('01-'.$date.'-'.$oldestYear));
                        if ($blnkurangbanyak !== false) {
                            $blnkurangbanyakbgt[] = $blnkurangbanyak;
                        }
                        if ($bnyakbet !== false) {
                            $bnyak[] = $bnyakbet;
                        }
                        if ($blnkurangbanyakthnlalu !== false) {
                            $blnkurangbanyakbgtthnllalu[] = $blnkurangbanyakthnlalu;
                        }
                        if ($blnbanyakthnlalu !== false) {
                            $blnbanyakbgtthnllalu[] = $blnbanyakthnlalu;
                        }
                            }
       
        $filunDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$p)),date("Y-m",strtotime('01-'.$mon.'-'.$p))] : $bnyak ;
        $thnlaluDates = $multinya == "0" ? [date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear)),date("Y-m",strtotime('01-'.$mon.'-'.$oldestYear))] : $blnbanyakbgtthnllalu ;
        $bulkur1 =  date("Y-m-t", strtotime('-1 month', strtotime($oldestYear.'-'.$mon.'-01')));

     if ($request->ajax()) {
        $hasil1 = [];
        $hasil2 = [];
        $hasil3 = [];
          $aaa = [];
          $aaaa = [];
          $aaaaa = [];
        $coanya = preg_split("/[\+\-\s]/" ,$request->data['rumus']) ;
        $data = COA::whereIn('coa', $coanya)->get();
          foreach($data as $s){
                     $aa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aa as $val){
                            $hasil[] = 
                                 $val['id']
                                ;
                        }
       
        // if(count($hasil) > 0 ){
        $data1 = COA::whereIn('id_parent', $hasil)->get();
             foreach($data1 as $s){
                     $aaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaa as $val){
                            $hasil1[] = 
                                 $val['id']
                                ;
                        }
        // }else if (count($hasil1) > 0){
          $data2 = COA::whereIn('id_parent', $hasil1)->get();
          foreach($data2 as $s){
                     $aaaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaaa as $val){
                            $hasil2[] = 
                                 $val['id']
                                ;
                        }
        // }else if(count($hasil2) > 0){
            $data3 = COA::whereIn('id_parent', $hasil2)->get();
            foreach($data3 as $s){
                     $aaaaa[] = [
                                'id' =>  $s->id,
                            ];
                }    
            
                        foreach($aaaaa as $val){
                            $hasil3[] = 
                                 $val['id']
                                ;
                        }
        
        
        $mergedArray = array_merge($hasil,$hasil1, $hasil2, $hasil3);
        $cteTransaksi = DB::table('transaksi')
                    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                    // ->where(function ($query) use ($filunDates) {
                    //         foreach ($filunDates as $date) {
                    // $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                    //     }
                    // })
                    ->WhereRaw("DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$oldestYear' ")
                    ->whereRaw("approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                    ;
                

                $ctePengeluaran = DB::table('pengeluaran')
                    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                    //  ->where(function ($query) use ($filunDates) {
                    //         foreach ($filunDates as $date) {
                    // $query->WhereRaw("DATE_FORMAT(tgl, '%Y-%m') = '$date'");
                    //     }
                    // })
                    ->WhereRaw("DATE_FORMAT(tgl, '%m') = '$b' AND DATE_FORMAT(tgl, '%Y') = '$oldestYear' ")
                     ->whereRaw("acc = 1")
                    ;
                
               
                  
                $cteJurnal = DB::table('jurnal')
                    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                    //  ->where(function ($query) use ($filunDates) {
                    //         foreach ($filunDates as $date) {
                    // $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                    //     }
                    // })
                    ->WhereRaw("DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$oldestYear' ")
                    ->whereRaw("acc = 1")
                    ;
                    
              
                
                $cteProg = DB::table('transaksi')
                    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
                      0 as nominal_debit, 0 as nominal_kredit, 
                      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                    //   ->where(function ($query) use ($filunDates) {
                    //         foreach ($filunDates as $date) {
                    // $query->WhereRaw("DATE_FORMAT(tanggal, '%Y-%m') = '$date'");
                    //     }
                    // })
                    ->whereRaw("transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0 AND DATE_FORMAT(tanggal, '%m') = '$b' AND DATE_FORMAT(tanggal, '%Y') = '$oldestYear'")
                   
                  ;
                   
                $union = $cteTransaksi->unionAll($ctePengeluaran)->unionAll($cteJurnal)->unionAll($cteProg);
    
     $saldo1 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($currentYear,$mon,$multinya,$filunDates,$bnyak,$b,$oldestYear) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                            
                                // ->where(function ($query) use ($filunDates) {
                                // foreach ($filunDates as $date) {
                                // $query->WhereRaw("DATE_FORMAT(sub.tanggal, '%Y-%m') = '$date'")
                                //   ->where('acc', 1);
                                //     }   
                                // })
                               ->WhereRaw("DATE_FORMAT(sub.tanggal, '%m') = '$b' AND DATE_FORMAT(sub.tanggal, '%Y') = '$oldestYear' ")
                                  ->where('acc', 1)
                                ;
                            
                          
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$multinya,$bulkur1,$blnkurangbanyakbgt) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->where(function ($query) use ($multinya,$currentYear,$bulkur1,$blnkurangbanyakbgt) {
                                if ($multinya == "1") {
                                        $query->whereIn(DB::raw('t2.bulan'), $blnkurangbanyakbgt);
                                 }else if($multinya == "0" ) {
                                        $query
                                        ->whereRaw("t2.bulan = '$bulkur1'");
                            }
                            });
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($multinya,$currentYear,$mon,$bnyak,$filunDates,$b,$oldestYear) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->where(function ($query) use ($filunDates) {
                                // foreach ($filunDates as $date) {
                                // $query->WhereRaw("DATE_FORMAT(sub.tanggal, '%Y-%m') = '$date'")
                                //   ->where('acc', 1);
                                //     }   
                                // })
                                 ->WhereRaw("DATE_FORMAT(sub.tanggal, '%m') = '$b' AND DATE_FORMAT(sub.tanggal, '%Y') = '$oldestYear' ")
                                 ->where('acc', 1)
                                ;
                            
                          
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent, IF(SUM(t2.closing) IS NOT NULL, SUM(t2.closing), 0) as closing, IF(COUNT(t2.closing) IS NOT NULL, COUNT(t2.closing), 0) as conclos")
                            ->withRecursiveExpression('b', $query)
                            ->leftjoin('saldo_awal as t2',function($join) use ($inbulan) {
                                $join->on('t2.coa' ,'=', 't.coa')
                                    ->whereDate('t2.bulan', $inbulan);
                            })
                            ->groupBy('root')
                            ->get(); 
                }
                        
            $inArray = [];
            $a = 0;
            foreach($saldo1 as $i => $val){
                if($cek != null){
                    $closed = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->saldo_awal : 0;
                    $closing = $saldo3[$i]->coa == $val->coa ? ($saldo3[$i]->closing > 0 && $saldo3[$i]->closing == $saldo3[$i]->conclos ? 1 : 0) : 0;
                }else{
                    $closed = 0;
                    $closing = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa ){
                    $a = $saldo2[$i]->saldo_awal ;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                        $inArray[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'saldo_awal' => 0,
                            'id_parent' => $val->id_parent,
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'neraca_saldo' => substr($val->coa, 0, 1) == 4 ? $a - $deb + $kre : ($a + $deb) - $kre,
                            'neraca_s' => ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'closed' => $closed ,
                        ];
                    
          }
       
            $filteredData = collect($inArray)->whereIn('id_parent', $mergedArray); 
            return DataTables::of($filteredData)

            ->make(true);
        }
        
        return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    }
    
    public function detail_debet(Request $request){
        $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $mon =  $request->bln == '' ? date('m') : $request->bln; 
        $tet =  date('m') ; 
        $dummy = [$tet,$tet];
        $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
        $latestData = end($monbanyak);
        $multinya = $request->mulbul ; 
        $b = $multinya = '0' ? $mon : $latestData ;

        $tahunt = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$p'";
        $tahunp = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$p'";
        $coa = $request->coa;
        if($request->ajax()){

                $prog = DB::table('prog')
                        ->selectRaw("id_program")
                        ->where('coa1', $request->coa)
                        ->get();
                $hasil = [];
                foreach($prog as $val){
                    $hasil[] = $val->id_program;
                }

                $pengeluaran = DB::table('pengeluaran')->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$p' as t, '$b' as b, nominal as debit")
                                ->where('coa_debet', $request->coa)->whereRaw("$tahunp AND acc = 1");
                $transaksi = DB::table('transaksi')
                        ->selectRaw(" transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$p' as t, '$b' as b, transaksi.jumlah as debit ")
                        ->where('coa_debet', $request->coa)
                        ->unionAll($pengeluaran)
                        ->whereRaw("$tahunt AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");    
                $data = $transaksi->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        } 
    }

    public function detail_kredit(Request $request){
       
        $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $mon =  $request->bln == '' ? date('m') : $request->bln; 
        $tet =  date('m') ; 
        $dummy = [$tet,$tet];
        $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
        $latestData = end($monbanyak);
        $multinya = $request->mulbul ; 
        $b = $multinya = '0' ? $mon : $latestData ;

        $tahunt = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$p'";
        $tahunp = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$p'";
        $coa = $request->coa;
        if($request->ajax()){

                $prog = DB::table('prog')
                        ->selectRaw("id_program")
                        ->where('coa1', $request->coa)
                        ->get();
                $hasil = [];
                foreach($prog as $val){
                    $hasil[] = $val->id_program;
                }

                $pengeluaran = DB::table('pengeluaran')->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$p' as t, '$b' as b, nominal as kredit")
                                ->where('coa_kredit', $request->coa)->whereRaw("$tahunp AND acc = 1");
                $transaksi = DB::table('transaksi')
                        ->selectRaw(" transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$p' as t, '$b' as b, transaksi.jumlah as kredit ")
                        ->where('coa_kredit', $request->coa)
                        ->unionAll($pengeluaran)
                        ->whereRaw("$tahunt AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");    
                $data = $transaksi->get();
                $result = $data[0]->kredit + $data[1]->kredit ;
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        } 
    }

    public function detail_debet_sebelumnya(Request $request){
        $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $currentYear = $p;
        $range = 1; // rentang tahun yang ingin ditampilkan
        $oldestYear = $currentYear - $range;
        $mon =  $request->bln == '' ? date('m') : $request->bln; 
        $tet =  date('m') ; 
        $dummy = [$tet,$tet];
        $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
        $latestData = end($monbanyak);
        $multinya = $request->mulbul ; 
        
        $b = $multinya = '0' ? $mon : $latestData ;

        $tahunt = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$oldestYear'";
        $tahunp = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$oldestYear'";
        $coa = $request->coa;
        if($request->ajax()){

                $prog = DB::table('prog')
                        ->selectRaw("id_program")
                        ->where('coa1', $request->coa)
                        ->get();
                $hasil = [];
                foreach($prog as $val){
                    $hasil[] = $val->id_program;
                }

                $pengeluaran = DB::table('pengeluaran')->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$p' as t, '$b' as b, nominal as debit")
                                ->where('coa_debet', $request->coa)->whereRaw("$tahunp AND acc = 1");
                $transaksi = DB::table('transaksi')
                        ->selectRaw(" transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$p' as t, '$b' as b, transaksi.jumlah as debit ")
                        ->where('coa_debet', $request->coa)
                        ->unionAll($pengeluaran)
                        ->whereRaw("$tahunt AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");    
                $data = $transaksi->get();
                // dd($data);
                // $result = $data[0]->debit + $data[1]->debit ;
                
            $res = DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
                
            if($request->tombol == 'xls'){
                $r = Excel::download(new DetailLaporanKeuanganExport($res->getData()->data, $request), 'Detail Debet Transaksi ' . $request->title . ' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.xls');
                ob_end_clean();
                return $r;
            }else if($request->tombol == 'csv'){
                $r = Excel::download(new DetailLaporanKeuanganExport($res->getData()->data, $request), 'Detail Debet Transaksi ' . $request->title . ' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.csv');
                ob_end_clean();
                return $r;
            }
            return $res;
        } 
    }
    
    public function detail_kredit_sebelumnya(Request $request){
        $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $currentYear = $p;
        $range = 1; // rentang tahun yang ingin ditampilkan
        $oldestYear = $currentYear - $range;
        $mon =  $request->bln == '' ? date('m') : $request->bln; 
        $tet =  date('m') ; 
        $dummy = [$tet,$tet];
        $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
        $latestData = end($monbanyak);
        $multinya = $request->mulbul ; 
        
        $b = $multinya = '0' ? $mon : $latestData ;

        $tahunt = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$oldestYear'";
        $tahunp = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$oldestYear'";
        $coa = $request->coa;
        if($request->ajax()){

                $prog = DB::table('prog')
                        ->selectRaw("id_program")
                        ->where('coa1', $request->coa)
                        ->get();
                $hasil = [];
                foreach($prog as $val){
                    $hasil[] = $val->id_program;
                }

                $pengeluaran = DB::table('pengeluaran')->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$p' as t, '$b' as b, nominal as kredit")
                                ->where('coa_kredit', $request->coa)->whereRaw("$tahunp AND acc = 1");
                $transaksi = DB::table('transaksi')
                        ->selectRaw(" transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$p' as t, '$b' as b, transaksi.jumlah as kredit ")
                        ->where('coa_kredit', $request->coa)
                        ->unionAll($pengeluaran)
                        ->whereRaw("$tahunt AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");    
                $data = $transaksi->get();
                // return($request);
            $res = DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
                
            if($request->tombol == 'xls'){
                $r = Excel::download(new DetailLaporanKeuanganExport($res->getData()->data, $request), 'Detail Kredit Transaksi ' . $request->title . ' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.xls');
                ob_end_clean();
                return $r;
            }else if($request->tombol == 'csv'){
                $r = Excel::download(new DetailLaporanKeuanganExport($res->getData()->data, $request), 'Detail Kredit Transaksi ' . $request->title . ' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.csv');
                ob_end_clean();
                return $r;
            }
            return $res;
        } 
    }
    
    // public function detail_ekspor(Request $request){
    //         if($request->tombol == 'xls'){
    //             $mon =  $request->bln == '' ? date('m') : $request->bln;
    //             $tet =  date('m') ; 
    //             $dummy = [$tet,$tet];
    //             $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
    //             $latestData = end($monbanyak);
    //             $multinya = $request->mulbul ; 
    //             $b = $multinya = '0' ? $mon : $latestData ;
                
    //             $r = Excel::download(new DetailLaporanKeuanganExport($request), 'Detail Transaksi ' .$request->title.' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.xls');
    //             ob_end_clean();
    //             return $r;
    //         }else{
    //             $r = Excel::download(new DetailLaporanKeuanganExport($request), 'Detail Transaksi ' .$request->title.' COA '.$request->nama_coa.' Mutasi '.' Bulan '.$b.' Tahun '.$request->tahuns.'.csv');
    //             ob_end_clean();
    //             return $r;
    //         }
    // }
}




   


   