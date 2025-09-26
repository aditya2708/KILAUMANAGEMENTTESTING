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
use App\Models\Program;
use App\Exports\JurnalExport;
use App\Exports\SaldoAwalExport;
use App\Exports\BukuBesarExport;
use Excel;

use App\Exports\HarianExport;
use App\Exports\PenutupanExport;

class AkuntasiController extends Controller
{
    
    // public function index(Request $request){
    //     $group = GrupCOA::all();
        
    //     return view ('akuntasi.index', compact('group'));
    // }
 
     
    public function saldo_awal(Request $request){
        $lev = COA::select('level')->distinct()->get();
        if ($request->ajax()) {
             
            $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
            $lvl = $request->lvl == '' ? "IS NOT NULL" : "= '$request->lvl'";
            $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            // $terbaru = SaldoAw::whereRaw("MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun'")->get();
            // $ngecek = count($terbaru);
            
            $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
          
            // $now = Carbon::now()->format('m-Y') ;
            
            if($request->blns == ''){
                
                
                $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor,0 as closing")
                        ->withRecursiveExpression('b', $query)
                        ->whereRaw("t.level $lvl AND t.parent $pr")
                        ->groupBy('root');
                    
            }else{
                 
                $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
                $ngecek = count($terbaru);
                
                if($ngecek > 0){
                    $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing")
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->select('t2.saldo_awal')
                                ->whereMonth('t2.bulan', $bulan)
                                ->whereYear('t2.bulan', $tahun)
                                ;
                        })
                        ->whereRaw("t.parent $lvl AND t.parent $pr")
                        ->groupBy('root');
                    
                }else{
                    $saldo = DB::table('b as t')
                    ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
                    ->withRecursiveExpression('b', $query)
                    ->whereRaw("t.level $lvl AND t.parent $pr")
                    ->groupBy('root');
                }
                
            }
            
            $data = $saldo->get();
            
            // return DataTables::of($data)
            // ->addColumn('kantor', function ($data) {
            //     $p = Kantor::where('id', $data->id_kantor)->first();
            //     if($p == null ){
            //         $jml = '';
            //     }else{
            //         $jml = $p->unit;
            //     }
            //     return $jml;
            // })
            // ->rawColumns(['kantor'])
            // ->make(true);
            
        }
        
        return view ('akuntasi.saldo_awal', compact('lev'));
    }
    
// public function saldo_awal_ah(Request $request){
    //     // return($request);
       
    //     $data = COA::all();
    //     $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
    //     $lvl = $request->level == '' ? "IS NOT NULL" : "= '$request->level'";
    //     $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
    //     $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
    //     $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
    //     $query = DB::table('coa as t1')
    //             ->select('t1.*', 't1.id as root')
                
    //             ->unionAll(
    //                 DB::table('b as t0')
    //                     ->select('t3.*', 't0.root')
    //                     ->join('coa as t3', 't3.id_parent', '=', 't0.id')
    //             );
            
    //     if($request->blns == ''){
            
                
    //         $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor,0 as closing")
    //                     ->withRecursiveExpression('b', $query)
    //                     ->groupBy('root');
                    
    //     }else{
                 
    //         $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
    //         $ngecek = count($terbaru);
                
    //         if($ngecek > 0){
    //             $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing")
    //                     ->withRecursiveExpression('b', $query)
                        
    //                     ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
    //                         $join->on('t2.coa' ,'=', 't.coa')
    //                             ->select('t2.saldo_awal')
    //                             ->whereMonth('t2.bulan', $bulan)
    //                             ->whereYear('t2.bulan', $tahun)
    //                             ;
    //                     })
    //                     ->groupBy('root');
                    
    //         }else{
    //             $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
    //                     ->withRecursiveExpression('b', $query)
    //                     ->groupBy('root');
    //         }
                
    //     }
            
    //     $data = $saldo->get();
        
        
        
    //     foreach($data as $key => $val){
    //         $kntrs = Kantor::where('id', $val->id_kantor);
            
    //         $inArray[] = [
    //             "id" => $val->id,
    //             "coa" => $val->coa,
    //             "nama_coa" => $val->nama_coa,
    //             "saldo_awal" => $val->saldo_awal,
    //             "id_parent" => $val->id_parent,
    //             "level" => $val->level,
    //             "id_kantor" => count($kntrs->get()) > 0 ? ucfirst($kntrs->first()->unit) : '' ,
    //             "parent" => $val->parent,
    //             "tanggals" => $val->tanggals
    //         ];
    //     }
        
    //     $filRay = array_filter($inArray, function ($p) use ($request) {
    //         // $grup = explode(",",$p['grup']);
    //         $fillvl = $request->level == '' ? $p['level'] != 'haha' : $p['level'] == $request->level;
    //         $filcoa = $request->coa == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->coa;
    //         return $fillvl && $filcoa;
    //     });
        
    //     $inArray = array_values($filRay);
        
        
    //     $arid = array_column($inArray, 'id');
        
    
    //     foreach ($inArray as $key => $obj) {
    //         if (!in_array($obj['id_parent'], $arid)) {
    //             $inArray[$key]['id_parent'] = '';
    //         }
    //     }
        
    //     return $inArray;
    // } //belum di ubah ubah
    
    
    // public function saldo_awal_ah(Request $request){

    //     $data = COA::all();
    //     $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
    //     $lvl = $request->lvl == '' ? "IS NOT NULL" : "= '$request->lvl'";
    //     $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
    //     $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
    //     $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
    //     $query = DB::table('coa as t1')
    //             ->select('t1.*', 't1.id as root')
                
    //             ->unionAll(
    //                 DB::table('b as t0')
    //                     ->select('t3.*', 't0.root')
    //                     ->join('coa as t3', 't3.id_parent', '=', 't0.id')
    //             );
            
    //     if($request->blns == ''){
            
                
    //         $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor,0 as closing")
    //                     ->withRecursiveExpression('b', $query)
    //                     ->groupBy('root');
                    
    //     }else{
                 
    //         $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
    //         $ngecek = count($terbaru);
                
    //         if($ngecek > 0){
    //             $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing")
    //                     ->withRecursiveExpression('b', $query)
                        
    //                     ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
    //                         $join->on('t2.coa' ,'=', 't.coa')
    //                             ->select('t2.saldo_awal')
    //                             ->whereMonth('t2.bulan', $bulan)
    //                             ->whereYear('t2.bulan', $tahun)
    //                             ;
    //                     })
    //                     ->groupBy('root');
                    
    //         }else{
    //             $saldo = DB::table('b as t')
    //                     ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
    //                     ->withRecursiveExpression('b', $query)
    //                     ->groupBy('root');
    //         }
                
    //     }
            
    //     $data = $saldo->get();
        
   
        
        
    //     foreach($data as $key => $val){
    //         $kntrs = Kantor::where('id', $val->id_kantor);
            
    //         $inArray[] = [
    //             "id" => $val->id,
    //             "coa" => $val->coa,
    //             "nama_coa" => $val->nama_coa,
    //             "saldo_awal" => $val->saldo_awal,
    //             "id_parent" => $val->id_parent,
    //             "level" => $val->level,
    //             "id_kantor" => count($kntrs->get()) > 0 ? ucfirst($kntrs->first()->unit) : '' ,
    //             "parent" => $val->parent,
    //             "tanggals" => $val->tanggals
    //         ];
    //     }
        
       
        
    //     $filRay = array_filter($inArray, function ($p) use ($request) {
    //         // $grup = explode(",",$p['grup']);
    //         $fillvl = $request->level == '' ? $p['level'] != 'haha' : $p['level'] == $request->level;
    //         $filcoa = $request->coa == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->coa;
    //         return $fillvl && $filcoa;
    //     });
        
    //     $inArray = array_values($filRay);
        
    //     $arid = array_column($inArray, 'id');
        
    //     $inArrayCopy = $inArray; 
        
    //     foreach ($inArrayCopy as $key => $obj) {
    //         if (!in_array($obj['id_parent'], $arid)) {
    //             $inArray[$key]['id_parent'] = '';
    //         }
    //     }
    //     return $inArray;
    // }
    
    
     public function saldo_awal_ah(Request $request){
        $data = COA::all();
        $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        $lvl = $request->lvl == '' ? "IS NOT NULL" : "= '$request->lvl'";
        $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
        $query = DB::table('coa as t1')
                ->select('t1.*', 't1.id as root')
                
                ->unionAll(
                    DB::table('b as t0')
                        ->select('t3.*', 't0.root')
                        ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                );
            
          if ($request->ajax()) {
              
 if($request->blns == ''){
            
                
            $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor,0 as closing")
                        ->withRecursiveExpression('b', $query)
                        ->groupBy('root');
                    
        }else{
                 
            $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
            $ngecek = count($terbaru);
            
            
                if ($ngecek > 0) {
                    $data = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t.id, t.level, t.nama_coa, IF(SUM(COALESCE(t2.saldo_awal, 0)), SUM(t2.saldo_awal), 0) as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, IF(SUM(COALESCE(t2.closing, 0)), '$bln', 0) as closing")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2', function ($join) use ($bulan, $tahun, $bln) {
                            $join->on('t2.coa', '=', 't.coa')
                                ->whereRaw("DATE_FORMAT(t2.bulan, '%m-%Y') = '$bln'");
                        })
                        ->groupBy('root')
                        ->get();
                } else {
                    $data = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t.id, t.level, t.nama_coa, 0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
                        ->withRecursiveExpression('b', $query)
                        ->groupBy('root')
                        ->get();
                }
            
            
                
            // if($ngecek > 0){
                
                
            //     $data = DB::table('b as t')
            //         ->selectRaw("root, t.coa, t.id_parent, t2.id, t.level, t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, t2.closing")
            //         ->withRecursiveExpression('b', $query)
            //         ->leftJoin('saldo_awal as t2', function ($join) use ($bln, $bulan, $tahun) {
            //             $join->on('t2.coa', '=', 't.coa')
            //                 ->where(function ($query) use ($bln, $bulan, $tahun) {
            //                     $query->whereRaw("DATE_FORMAT(t2.bulan, '%m-%Y') = '$bln'");
            //                 });
            //         })
            //         ->groupBy('root')
            //         ->get();


                
            //     // $data = DB::table('b as t')
            //     //         ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing")
            //     //         ->withRecursiveExpression('b', $query)
                        
            //     //         ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun,$bln) {
            //     //             $join->on('t2.coa' ,'=', 't.coa')
            //     //                 ->select('t2.saldo_awal')
            //     //                 ->whereRaw("DATE_FORMAT(t2.bulan, '%m-%Y') = '$bln'")
            //     //                 // ->whereMonth('t2.bulan', $bulan)
            //     //                 // ->whereYear('t2.bulan', $tahun)
            //     //                 ;
            //     //         })
            //     //         ->groupBy('root')
            //     //         ->get();
                    
            // }else{
            //     $data = DB::table('b as t')
            //             ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
            //             ->withRecursiveExpression('b', $query)
            //             ->groupBy('root')
            //             ->get();
            // }
                
        }
 
        foreach($data as $key => $val){
            $kntrs = Kantor::where('id', $val->id_kantor);
            
            $inArray[] = [
                "id" => $val->id,
                "coa" => $val->coa,
                "nama_coa" => $val->nama_coa,
                "saldo_awal" => $val->saldo_awal,
                "id_parent" => $val->id_parent,
                "level" => $val->level,
                "id_kantor" => count($kntrs->get()) > 0 ? ucfirst($kntrs->first()->unit) : '' ,
                "parent" => $val->parent,
                "tanggals" => $val->tanggals
            ];
        }
        
        $filRay = array_filter($inArray, function ($p) use ($request) {
            // $grup = explode(",",$p['grup']);
            $fillvl = $request->level == '' ? $p['level'] != 'haha' : $p['level'] == $request->level;
            $filcoa = $request->coa == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->coa;
            return $fillvl && $filcoa;
        });
        
        $inArray = array_values($filRay);
        
        $arid = array_column($inArray, 'id');
        
        $inArrayCopy = $inArray; 
        
        foreach ($inArrayCopy as $key => $obj) {
            if (!in_array($obj['id_parent'], $arid)) {
                $inArray[$key]['id_parent'] = '';
            }
        }    
            $datas = $inArray;
            return($datas);
            }
            
            
            // return DataTables::of($datas)
          
            // ->make(true);
            
            
        return view ('akuntasi.saldo_awal', compact('lev'));
            
       
      
    }  //untuk datatable


    public function saldo_awal_export(Request $request){
            $lev = COA::select('level')->distinct()->get();
             
            $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
            $lvl = $request->lvl == '' ? "IS NOT NULL" : "= '$request->lvl'";
            $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $terbaru = SaldoAw::whereRaw("MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun'")->get();
            $ngecek = count($terbaru);
            
            $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
                if($request->tombol == 'xls'){
                    $r = Excel::download(new SaldoAwalExport($bln,$lvl,$pr,$bulan,$tahun,$query), 'saldo_awal.xls');
                    ob_end_clean();
                    return $r;
                }else{
                    $r = Excel::download(new SaldoAwalExport($bln,$lvl,$pr,$bulan,$tahun,$query), 'saldo_awal.csv');
                    ob_end_clean();
                    return $r;
                }
           
    }    
    public function saldo_first(Request $request){
        $terbaru = SaldoAw::all('created_at')->max('created_at')->toArray();
        $tgl = date('Y-m-d',strtotime($terbaru['formatted']));
        // $data = SaldoAw::select('saldo_awal.id', 'coa.id as coa_coy', 'coa.nama_coa','coa.coa_parent','saldo_awal.created_at','saldo_awal.coa','saldo_awal.saldo_awal','coa.parent','coa.id_parent','coa.level','tambahan.unit as id_kantor')->leftJoin('coa','coa.coa','=','saldo_awal.coa')->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')->whereRaw(" DATE(saldo_awal.created_at) = '$tgl'")->get();
        
        
        $query = DB::table('coa as t1')
                ->select('t1.*', 't1.id as root')
                
                ->unionAll(
                    DB::table('b as t0')
                        ->select('t3.*', 't0.root')
                        ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                );
                
                
        $tree = DB::table('b as t')
                ->selectRaw("root, t2.coa, t.id_parent, t2.id, t.id as coa_coy ,t.nama_coa,  SUM(t2.saldo_awal) as saldo_awal, t2.created_at, tambahan.unit as id_kantor, t.parent")
                ->withRecursiveExpression('b', $query)
                ->join('saldo_awal as t2', 't2.coa', '=', 't.coa')
                ->leftJoin('tambahan','tambahan.id','=','t.id_kantor')
                ->whereRaw(" DATE(t2.created_at) = '$tgl'")
                ->groupBy('root')
                ->get();
                
        // return($tree);
                
        return $tree;
    }
    
    public function getsaldoaw(Request $request){

        // $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        // $lvl = $request->lvl == '' ? "IS NOT NULL" : "= '$request->lvl'";
        // $pr = $request->coa == '' ? "IS NOT NULL" : "= '$request->coa'";
        // $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        // $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        // $query = DB::table('coa as t1')
        //         ->select('t1.*', 't1.id as root')
                
        //         ->unionAll(
        //             DB::table('b as t0')
        //                 ->select('t3.*', 't0.root')
        //                 ->join('coa as t3', 't3.id_parent', '=', 't0.id')
        //         );
                
                
        // if($request->blns == ''){
        //     $tree = DB::table('b as t')
        //             ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  SUM(t.konak) as saldo_awal, '$bln' as tanggals, t.parent, 0 as closing")
        //             ->withRecursiveExpression('b', $query)
        //             // ->whereRaw("t.level $lvl AND t.parent $pr")
        //             ->groupBy('root')
        //             ->get();
        // }else{
                
        //         $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
        //         $ngecek = count($terbaru);
                
        //         if($ngecek > 0){
        //             $tree = DB::table('b as t')
        //                 ->selectRaw("root, t.coa, t.id_parent, t2.id, t.level ,t.nama_coa,  SUM(t2.konak) as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing as closing")
        //                 ->withRecursiveExpression('b', $query)
                        
        //                 ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
        //                     $join->on('t2.coa' ,'=', 't.coa')
        //                         ->select('t2.saldo_awal')
        //                         ->whereMonth('t2.bulan', $bulan)
        //                         ->whereYear('t2.bulan', $tahun);
        //                 })
        //                 // ->whereRaw("t.parent $lvl AND t.parent $pr")
        //                 ->groupBy('root')
        //                 ->get();
                    
        //         }else{
        //             $tree = DB::table('b as t')
        //             ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor 0 as closing")
        //             ->withRecursiveExpression('b', $query)
        //             // ->whereRaw("t.level $lvl AND t.parent $pr")
        //             ->groupBy('root')
        //             ->get();
        //         }
                
                
        //         // $tree = DB::table('b as t')
        //         //     ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  SUM(IF( MONTH(t2.created_at) = '$bulan' AND YEAR(t2.created_at) = '$tahun' , konak, 0)) AS saldo_awal, '$bln' as tanggals, t.id_kantor, t.parent")
        //         //     ->withRecursiveExpression('b', $query)
        //         //     ->leftJoin('saldo_awal as t2', 't2.coa', '=', 't.coa')
        //         //     // ->whereRaw("t.parent $lvl AND t.parent $pr")
        //         //     ->groupBy('root')
        //         //     ->get();
        // }
        // // return($tree);
        
        // $a = [];
        // foreach($tree as $keys => $t) 
        // {
        //     $a[] = [
        //             'coa' => $t->coa,
        //             'id' => $t->id,
        //             'saldo_awal' => $t->saldo_awal
        //         ];
        // }
        
        // $key_title = [];
        
        // foreach ($a as $key => $data) {
        //     if ($data['coa'] == $request->coax) {
        //         $key_title = [
        //             'saldo_awal' => $data['saldo_awal'] == null ? 0 : $data['saldo_awal'] ,
        //             'id' => $data['id'] != null ?  $data['id'] : null, 
        //             'coah' => $request->coax,
        //         ];
        //     }
        // }
        
        // return $key_title;
        
        $inbulan = $request->blns == '' ? date('Y-m-t') : date("Y-m-t", strtotime('01-'.$request->blns));
        $sal = SaldoAw::where('coa', $request->coax)->whereDate('bulan', $inbulan)->first();
        
        if($sal != null){
            $datasal = [
                        'saldo_awal' => $sal->saldo_awal,
                        'id' => $sal->id,
                        'coah' => $sal->coa
                        ];
        }else{
            $datasal = [
                        'saldo_awal' => 0,
                        'id' => 0,
                        'coah' => $request->coax
                        ];
        }
        
                return($datasal);
    }
    
    function update_saldo(Request $request)
    {
        // dd($request->blnform);
        $inbulan = date("Y-m-t", strtotime('01-'.$request->blnform));
        // return($request);
        if($request->idna > 0){
            $p = SaldoAw::where('id', $request->idna)->update(['saldo_awal' =>  preg_replace("/[^0-9]/", "", $request->sa)]);
            
            return response()->json(['success' => 'berhasil']);
            // if($p > 0){
            //     return response()->json(['success' => 'Data Added successfully.', 'data' => $p]);
            // }else{
            //     return response()->json(['failed' => 'Data Added unsuccessful.', 'data' => $p]);
            // }
            
        }else{
            $p = new SaldoAw;
            $p->coa = $request->coax;
            $p->saldo_awal = preg_replace("/[^0-9]/", "", $request->sa);
            $p->closing = 0;
            $p->bulan = $inbulan;
            
            // return($p);
            $p->save();
            
            return response()->json(['success' => 'berhasil']);
        }
    }
    
    // private function calculate($parent_id, $amount) {
    //     $calculation = new Calculation();
    //     $calculation->parent_id = $parent_id;
    //     $calculation->amount = $amount;
    //     $calculation->save();
    
    //     $sub_calculations = Calculation::where('parent_id', $parent_id)->get();
    
    //     if ($sub_calculations->count() > 0) {
    //         foreach ($sub_calculations as $sub_calculation) {
    //             $this->calculate($sub_calculation->id, $amount * $sub_calculation->factor);
    //         }
    //     }
    // }
    
    
    public function trial_balance(Request $request){
        $this->COA = new \App\Models\COA;
        
        $ngecek = 0; 
        $lev = COA::select('level')->distinct()->get();
        
        if ($request->ajax()) {
             
            // $a = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
            // // $lvl = $request->lvl == '' ? "'vnfvkfvnkfn'" : "'$request->lvl'";
            // // $pr = $request->coa == '' ? "'dsdsdsdsd'" : "'$request->coa'"; 
            
            // $time = strtotime('01-'.$a);
            
            
            // $bln = date("m-Y", strtotime("-1 month", $time));
            // // return($bln);
            
            // $b = Carbon::createFromFormat('m-Y', $bln)->format('m');
            // $t = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            // $bulan = Carbon::createFromFormat('m-Y', $a)->format('m');
            // $tahun = Carbon::createFromFormat('m-Y', $a)->format('Y');
            
            $bln = $request->blns == '' ? date('m-Y') : $request->blns;
            $bulan = date('m', strtotime('01-'.$bln));
            $tahun = date('Y', strtotime('01-'.$bln));;
            
            $bln2 = date("m-Y", strtotime("-1 month", strtotime('01-'.$bln)));
            $bulan2 = date('m', strtotime('01-'.$bln2));
            $tahun2 = date('Y', strtotime('01-'.$bln2));
            
            $inbulan = date("Y-m-t", strtotime('01-'.$bln));
            $inbulan2 = date("Y-m-t", strtotime('01-'.$bln2));
        
        
            $cek = SaldoAw::whereDate('bulan', $inbulan)->first();
                            
            $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                )
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa1, prog.coa2, 0 as jumlah, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                        )
                        ;
            
            // $un = DB::table('transaksi')
            //                     ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
            //                     ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                
            //             ->unionAll(DB::table('transaksi')
            //                     ->selectRaw("prog.coa1, prog.coa2, 0 as jumlah, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
            //                     ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
            //             )
            //             ;
                
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
            
            $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
                            $join
                                ->on('sub.coa_debet' ,'=', 't.coa')
                                // ->on(function($j){
                                //     $j->on('sub.coa_debet' ,'=', 't.coa')->orOn('sub.coa_kredit' ,'=', 't.coa');
                                // })
                                ->whereMonth('sub.tanggal', $bulan)
                                ->whereYear('sub.tanggal', $tahun)
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                            
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($inbulan2) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereDate('t2.bulan', $inbulan2);
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
            
            foreach($saldo as $i => $val){
                
                if($cek != null){
                    $closed = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->saldo_awal : 0;
                    $closing = $saldo3[$i]->coa == $val->coa ? ($saldo3[$i]->closing > 0 && $saldo3[$i]->closing == $saldo3[$i]->conclos ? 1 : 0) : 0;
                }else{
                    $closed = 0;
                    $closing = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                $grup = explode(",",$val->grup);
                $aws = $request->grup;
                
                $coba1 = $request->lvl != '' ? $val->level == $request->lvl : $val->level != null ;
                $coba2 = $request->coa != '' ? $val->parent == $request->coa : $val->parent != null ;
                $coba3 = $request->grup != '' ? array_intersect($grup, $aws) : $val->grup != null;
                
                
                    if($coba1 && $coba2 && $coba3){
                        $inArray[] = [
                            'root' => $val->root,
                            'id' => $val->id,
                            'parent' => $val->parent,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'id_parent' => $val->id_parent,
                            'level' => $val->level,
                            'saldo_awal' => $a, 
                            'debit' => $val->debit == null ? 0 : $val->debit ,
                            'kredit' => $val->kredit == null ? 0 : $val->kredit,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'neraca_saldo' => ($a + $val->debit) - $val->kredit,
                            'neraca_s' => ($a + $val->debit) - $val->kredit + $val->debit_s - $val->kredit_s,
                            'closing' => $closing ,
                            'closed' => $closed,
                        ];
                    }
                
            }
            
            $data = $inArray;
            
            return DataTables::of($data)
            ->addColumn('enak_coa', function ($transaksi) {
                
                if ($transaksi['level'] == 4) {
                    $c = '&nbsp;&nbsp;&nbsp;&nbsp;'.$transaksi['nama_coa'];
                }else if($transaksi['level'] == 3){
                    $c = '&nbsp;&nbsp&nbsp;'.$transaksi['nama_coa'];
                }else if($transaksi['level'] == 2){
                    $c = '&nbsp;&nbsp;'.$transaksi['nama_coa'];
                }else{
                    $c = $transaksi['nama_coa'];
                }
            
                return $c;
            })
            ->rawColumns(['enak_coa'])
            ->make(true);
            
        }

        $grup = GrupCoa::all();
        
        return view ('akuntasi.trial_balance', compact('ngecek', 'lev', 'grup'));
    }
    
    public function tombol_closing(Request $request){
        $a = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        
        $time = strtotime('t-'.$a);
        
        $bln = date("m-Y", strtotime("-1 month", $time));
        
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        // return($bulan);
            
        $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND closing = '1'")->get();
        $ngecek = count($terbaru);
        
        return $ngecek;
    }
    
    public function makeParentChildRelations(&$inArray, &$outArray, $currentParentId = 0) {
        if(!is_array($inArray)) {
            return;
        }
    
        if(!is_array($outArray)) {
            return;
        }
    
        foreach($inArray as $key => $tuple) {
            if($tuple['id_parent'] == $currentParentId) {
                $tuple['children'] = array();
                $this->makeParentChildRelations($inArray, $tuple['children'], $tuple['id']);
                $outArray[] = $tuple;   
            }
        }
    }
    
    public function trial_footer(Request $request){
        
        if ($request->ajax()) {
            
            $a = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
            
            $time = strtotime('01-'.$a);
            
        
            $bln = date("m-Y", strtotime("-1 month", $time));
            // return($bln);
            
            $b = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $t = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $bulan = Carbon::createFromFormat('m-Y', $a)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $a)->format('Y');
            
            $query = DB::table('coa as t1')
                        ->select('t1.*', 't1.id as root')
                            
                        ->unionAll(
                            DB::table('b as t0')
                                ->select('t3.*', 't0.root')
                                ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                        );
            
            if($request->blns == ''){
                        
                $saldo = DB::table('b as t')
                        ->selectRaw("root, t.id, t.coa, t.nama_coa, SUM(t.konak) as total")
                        ->withRecursiveExpression('b', $query)
                        ->groupBy('root')
                        ->get();
                    
            }else{
                $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$b' AND YEAR(bulan) = '$t' AND (closing = '1' OR closing = '0')")->get();
                $ngecek = count($terbaru);
                
                if($ngecek > 0){
                    
                    $saldo = DB::table('b as t')
                        ->selectRaw("root, t.id, t2.coa, t.nama_coa, SUM(t2.konak) as total")
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($b, $t) {
                                $join->on('t2.coa' ,'=', 't.coa')
                                    ->select('t2.saldo_awal')
                                    ->whereMonth('t2.bulan', $b)
                                    ->whereYear('t2.bulan', $t);
                            })
                        ->groupBy('root')
                        ->get();
                }else{
                    $saldo = DB::table('b as t')
                        ->selectRaw("root, t.id, t.coa, t.nama_coa, 0 as total")
                        ->withRecursiveExpression('b', $query)
                        ->groupBy('root')
                        ->get();
                }
                    
            }
        
            $kewajiban = [];
            $sd = [];
            $asset = [];
                     
            foreach($saldo as $d){
                if($d->nama_coa == "Kewajiban"){ $kewajiban[] = $d->total; }
                if($d->nama_coa == "Saldo Dana"){ $sd[] = $d->total; }
                if($d->nama_coa == "Aset"){ $asset[] = $d->total; }
            }
            
            $a = count($asset) > 0 ? $asset[0] : 0;
            $b = count($kewajiban) > 0 ? $kewajiban[0] : 0;
            $c = count($sd) > 0 ? $sd[0] : 0;
            
            $data['saldo_awal_debit'] = $a;
            $data['saldo_awal_kredit'] = $b + $c;
            
            $data['saldo_akhir_debit'] = $a;
            $data['saldo_akhir_kredit'] = $b + $c;
                
            $data['penyesuain'] = Jurnal::selectRaw("SUM(nominal_debit) as debit, SUM(nominal_kredit) as kredit")->whereRaw("MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'")->first();
            $data['mutasi_debit'] = Transaksi::selectRaw("SUM(IF(MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'mutasi', jumlah,0)) AS debit ")->join('coa','coa.coa','=','coa_debet')->first()->debit;
            $data['mutasi_kredit'] = Transaksi::selectRaw("SUM(IF(MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'mutasi', jumlah,0)) AS kredit ")->join('coa','coa.coa','=','coa_kredit')->first()->kredit;
        }
        
        return $data;
    }
    
    public function postClosing(Request $request){
        // return($request);
        // dd('block');
        
        $bln = $request->blns == '' ? date('m-Y') : $request->blns;
        $bulan = date('m', strtotime('01-'.$bln));
        $tahun = date('Y', strtotime('01-'.$bln));;
        
        $bln2 = date("m-Y", strtotime("-1 month", strtotime('01-'.$bln)));
        $bulan2 = date('m', strtotime('01-'.$bln2));
        $tahun2 = date('Y', strtotime('01-'.$bln2));
        
        $inbulan = date("Y-m-t", strtotime('01-'.$bln));
        
        
        $cek = SaldoAw::whereMonth('bulan', $bulan)
                            ->whereYear('bulan', $tahun)
                            ->first();
                            
        $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc")
                        ->unionAll(DB::table('pengeluaran')
                                        ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc")
                        );
                
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
                     
                
                $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereMonth('sub.tanggal', $bulan)
                                ->whereYear('sub.tanggal', $tahun)
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t2.saldo_awal, t.parent")
                            
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($bulan2, $tahun2) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereMonth('t2.bulan', $bulan2)
                                ->whereYear('t2.bulan', $tahun2);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t2.saldo_awal, t.parent, t2.id AS id_sal, t2.closing")
                                
                            ->withRecursiveExpression('b', $query)
                            
                            ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
                                $join->on('t2.coa' ,'=', 't.coa')
                                    ->whereMonth('t2.bulan', $bulan)
                                    ->whereYear('t2.bulan', $tahun);
                            })
                                
                            ->groupBy('root')
                            ->get(); 
                }        
                        
            $saldoak = [];
            foreach($saldo as $i => $val){
            
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                $id_sal = 0;
                if($cek != null){
                    if($saldo3[$i]->coa == $val->coa){
                        $id_sal = $saldo3[$i]->id_sal > 0 ? $saldo3[$i]->id_sal : 0;
                        $closing = $saldo3[$i]->closing > 0 ? $saldo3[$i]->closing : 0;
                    }else{
                        $id_sal = 0;
                        $closing = 0;
                    }
                }
                
                if($val->parent == 'n'){
                    $saldoak = $a + $val->debit - $val->kredit + $val->debit_s - $val->kredit_s;
                }else{
                    $saldoak = 0;
                }
                
                $input['coa'] = $val->coa;
                $input['saldo_awal'] = $saldoak;
                $input['closing'] = 1;
                $input['canclos'] = 0;
                $input['tgl_closing'] = date('Y-m-d');
                $input['bulan'] = $inbulan;
                    
            // dd($bln, $bln2, $inbulan);
            
                if($cek != null && $id_sal > 0) {
                    SaldoAw::where('id',$id_sal)->update($input);
                }else{
                    SaldoAw::create($input);
                }
                
            }
            
        return response()->json(['success' => 'Data Added successfully.']); 
        // dd($saldoak);
        
        
        // $coa = COA::where('aktif', 'y')->get();
        // foreach($coa as $c){
        //     $saldoak = SaldoAw::whereMonth('bulan', $bulan)
        //                     ->whereYear('bulan', $tahun)
        //                     ->where('coa',$c->coa)
        //                     ->first();
                            
        //     if($c->parent == 'y' ){
        //         $o = 0;
        //     }else{
                
        //     $jurnal = Jurnal::selectRaw("SUM(nominal_debit) as debit, SUM(nominal_kredit) as kredit")
        //                     ->whereMonth('tanggal', $bulan)
        //                     ->whereYear('tanggal', $tahun)
        //                     ->where('coa_use',$c->coa)
        //                     ->first();
            
                            
        //     $saldo = SaldoAw::whereMonth('bulan', $bulan2)
        //                     ->whereYear('bulan', $tahun2)
        //                     ->where('coa',$c->coa)
        //                     ->first();
            
        //     $debjur = $jurnal == null ? 0 : $jurnal->debit;
        //     $krejur = $jurnal == null ? 0 : $jurnal->kredit;                
        //     $sal = $saldo == null ? 0 : $saldo->saldo_awal;
            
        //     $deb = Transaksi::where('coa_debet',$c->coa)
        //                         ->whereMonth('tanggal', $bulan)
        //                         ->whereYear('tanggal', $tahun)
        //                         ->sum('jumlah');
                                
        //     $kre = Pengeluaran::where('coa_kredit',$c->coa)
        //                         ->whereMonth('tgl', $bulan)
        //                         ->whereYear('tgl', $tahun)
        //                         ->sum('nominal');
            
        //         $o = ($sal + $deb) - $kre + $debjur - $krejur;
        //     }
            
        //     $input['coa'] = $c->coa;
        //     $input['saldo_awal'] = $o;
        //     $input['closing'] = 1;
        //     $input['bulan'] = $inbulan;
                
        // // dd($bln, $bln2, $inbulan);
        
        //     if($saldoak != null) {
        //         SaldoAw::whereMonth('bulan', $bulan)
        //                     ->whereYear('bulan', $tahun)
        //                     ->where('coa',$c->coa)
        //                     ->update($input);
        //     }else{
        //         SaldoAw::create($input);
        //     }
        // }
        
        // dd($inbulan);
        
    }
    
    public function batalClosing(Request $request){
        
        $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        
        $saldo = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
        
        // return($saldo);
        
        foreach($saldo as $s){
            // $input['coa'] = $s->coa;
            // $input['saldo_awal'] = $s->saldo_awal;
            // $input['closing'] = 0;
            
            SaldoAw::where('id', $s->id)->update(['closing' => 0]);
        }
        
        return response()->json(['success' => 'Data Added successfully.']); 
    }
    
    public function trial_datax(Request $request){
        $data = COA::all();

        $inArray = [];
        foreach($data as $key => $val){
            $dot = COA::where('id', $val->id_parent)->first();
            
            $saldopar = COA::select(\DB::raw("SUM(IF((id_parent = $val->id OR id_parent = 0),saldo_new,0)) AS par, SUM(IF(id = $val->id,saldo_new,0)) AS chil"))->get();
            // $ezze = COA::where('coa',$val->coa)->sum('saldo_new');
            // dd($ezze);
            // $saldopar = SaldoAw::select(\DB::raw("SUM(IF(coa IN (SELECT coa FROM coa WHERE id_parent = '$val->id'), saldo_awal, 0)) AS salpar, SUM(IF(coa IN (SELECT coa FROM coa WHERE id = '$val->id'), saldo_awal, 0)) AS salanak"))->first();
            
            
            $inArray[]=[
                'id' => $val->id,
                'coa' => $val->coa,
                'nama_coa' => $val->nama_coa,
                'id_parent' => $val->id_parent,
                'coa_parent' => $val->id_parent != 0 ? $dot->coa : 0,
                'level' => $val->level,
                // 'saldo_awal' => $ezze,
                'saldo_awal' => $val->parent == 'y' ? $saldopar[0]->par : $saldopar[0]->chil,
                // 'saldo_awal' => COA::join('saldo_awal','coa.coa','=','saldo_awal.coa')->select(\DB::raw("saldo_awal.saldo_awal, SUM(IF(coa.id = $val->id_parent,saldo_awal.saldo_awal,0)) AS uang"))->groupBy('saldo_awal.saldo_awal')->first()->uang,
                'db_mutasi' => Pengeluaran::where('coa_debet',$val->coa)->where('via_input','mutasi')->sum('nominal'),
                'db_kredit' => Pengeluaran::where('coa_kredit',$val->coa)->where('via_input','mutasi')->sum('nominal'),
            ];
        }
        // return($saldopar);
        
        return $inArray;
    }
    
    
    function penutupan(Request $request)
    {
        $k = Auth::user()->id_kantor;
        $com = Auth::user()->id_com;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        
        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_com = '$com'")->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k AND id_com = '$com'")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id) AND id_com = '$com'")->get();
            }
        }
        
        if($request->ajax())
        {
            $buku = $request->buk;
            
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
            
            
            if($buku != ''){
                if($buku == 'kas'){
                    $buk = "grup = 3";
                }else{
                    $buk = "grup = 4";
                }
            }else{
                $buk = "(grup = 3 OR grup = 4)";
            }
            
            $data = '';
            $ehey = [];
            
            $akun = $request->akun == '' ? "coa.coa IS NOT NULL" : "coa.coa = '$request->akun'";
            
            // return($buk);
            
            $waduw = $request->daterange != '' ? "DATE(penutupan.created_at) >= '$dari' AND DATE(penutupan.created_at) <= '$sampai'" : "DATE(penutupan.created_at) >= '$now' AND DATE(penutupan.created_at) <= '$now'";
            
            $datas = COA::selectRaw("coa.*, penutupan.*, penutupan.created_at as pawon, '$request->pen' as p")->leftJoin('penutupan','penutupan.coa_pen','=','coa.coa')->whereRaw("id_kantor = '$request->kans' AND $buk AND parent = 'n' AND $akun ")->groupBy('coa.coa')->get();
            
            
            // return($datas);
            // return(count($data));
            
            for($i = $begin; $i <= $end; $i->modify('+1 day')){
                foreach($datas as $d){
                    $c_tgl = $d->pawon != '' ? date('Y-m-d',strtotime($d->pawon)) : '';
                    
                    if($i->format("Y-m-d") == $c_tgl){
                        // $tanggal = $c_tgl;
                        $cek = 'ada';
                        $saldo_akhir = $d->saldo_akhir;
                        $saldo_awal = $d->saldo_awal;
                        $debit = $d->debit;
                        $kredit = $d->kredit;
                        $adjustment = $d->adjustment;
                        $user_input = $d->user_input;
                        $user_update = $d->user_update;
                        $k100000 = $d->k100000;
                        $k75000 = $d->k75000;
                        $k50000 = $d->k50000;
                        $k20000 = $d->k20000;
                        $k10000 = $d->k10000;
                        $k5000 = $d->k5000;
                        $k2000 = $d->k2000;
                        $k1000 = $d->k1000;
                        $k500 = $d->k500;
                        $k100 = $d->k100;
                        $l1000 = $d->l1000;
                        $l500 = $d->l500;
                        $l200 = $d->l200;
                        $l100 = $d->l100;
                        $l50 = $d->l50;
                        $l25 = $d->l25;
                    }else {
                        // $tanggal = $i->format("Y-m-d");
                        $cek = 'gada';
                        $saldo_akhir = 0;
                        $saldo_awal = 0;
                        $debit = 0;
                        $kredit = 0;
                        $adjustment = 0;
                        $user_input = '';
                        $user_update = '';
                        $k100000 = '';
                        $k75000 = '';
                        $k50000 = '';
                        $k20000 = '';
                        $k10000 = '';
                        $k5000 = '';
                        $k2000 = '';
                        $k1000 = '';
                        // $k500 = '';
                        // $k100 = '';
                        $l1000 = '';
                        $l500 = '';
                        $l200 = '';
                        $l100 = '';
                        // $l50 = '';
                        // $l25 = '';
                        }
    
                $ehey[]  = ['tanggal' => $i->format("Y-m-d"),
                            'coa' => $d->coa,
                            'id_kantor' => $d->id_kantor,
                            'nama_coa' => $d->nama_coa,
                            'saldo_akhir' => $saldo_akhir,
                            'saldo_awal' => $saldo_awal ,
                            'debit' => $debit,
                            'kredit' => $kredit,
                            'adjustment' => $adjustment,
                            'user_input' => $user_input,
                            'user_update' => $user_update,
                            'k100000' =>  $k100000 ,
                            'k75000' => $k75000,
                            'k50000' => $k50000,
                            'k20000' => $k20000,
                            'k10000' => $k10000,
                            'k5000' => $k5000,
                            'k2000' => $k2000,
                            'k1000' => $k1000,
                            // 'k500' => $k500,
                            // 'k100' => $k100,
                            'l1000' => $l1000,
                            'l500' => $l500,
                            'l200' => $l200,
                            'l100' => $l100,
                            // 'l50' => $l50,
                            // 'l25' => $l25,
                            'grup' => $d->grup,
                            'p' => $d->p,
                            'cek' => $cek
                        ];
                }
            }
            
            $data = $ehey;
            
            return DataTables::of($data)
            ->addColumn('aksi', function($data){
                // return($data);
                $dat = array($data['grup']);
                if(in_array('4',$dat)){
                    $button = '<a href="javascript:void(0)" style="color:#1e0fbe" data-bs-toggle="modal" data-bs-target="#modal_aja" id="'.$data['coa'].'">BO</a>';
                }else{
                    $button = '<a href="javascript:void(0)" style="color:#1e0fbe" data-bs-toggle="modal" data-bs-target="#modals"  id="'.$data['coa'].'">CO</a>';
                }
                return $button;
            })
            ->addColumn('tgl', function($data){
                if($data['p'] == 'tanggal'){
                    $tanggal = $data['tanggal'];
                }else if($data['p'] == 'bulan'){
                    $tanggal = date('Y-m-t');
                }else if($data['p'] == 'tahun'){
                    $last_year_last_month_date = date("Y-12-01", strtotime("-1 year"));
                    $end_date = date("Y-12-t", strtotime($last_year_last_month_date));
                    $tanggal = $end_date;
                }
                
                return $tanggal;
            })
            ->rawColumns(['aksi','tgl'])
            ->make(true);
        }
        return view('fins.penutupan', compact('bank','kantor'));

    }
    
    public function penutupan_ekspor(Request $request){
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }
    
        $bulan = date('m');
        $tahun = date('Y');
        
        if($request->pen == 'tanggal'){
            $j_period =  'tanggal-'.$dari.'-s.d-'.$sampai;
            $k_period =  'Tanggal '.$dari.' s.d '.$sampai;
        }else if($request->periode == 'bulan'){
            $j_period =  'bulan-'.$bulan.'-'.$tahun;
            $k_period =  'Bulan '.$bulan.' Tahun '.$tahun;
        }else if($request->periode == 'tahun'){
            $j_period =  'tahun-'.$tahun;
            $k_period =  'Tahun '.$tahun;
        }
        
        // if($request->tombol == 'xlsx'){
            $response =  Excel::download(new PenutupanExport($request, $k_period), 'penutupan-'.$j_period.'.xlsx');
        // }else{
        //     $response =  Excel::download(new PenerimaanExport( $dari, $sampai, $darib, $sampaib, $via, $unit, $stts, $viewnya, $periode, $k_period), 'penerimaan-'.$j_period.'.csv');
        // }
        ob_end_clean();
        
        return $response;
    }
    
    public function cari_akun_penutupan(Request $request){
        $data = COA::whereRaw("(grup = 4 OR grup = 3) AND id_kantor = '$request->kans'")->get();
        return $data;
    }
    
    function caribank(Request $request, $id)
    {
        $roar = $request->uch == '' ? date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))) :  date('Y-m-d', strtotime('-1 day', strtotime($request->uch)));
        // return($id);
        $tgl_for = $request->uch;
        $data['bank'] = COA::where('coa',$id)->first();
        $data['user'] = User::where('id', Auth::user()->id)->first();
        
        if(in_array('4',array($data['bank']->grup))){
            $data['ok'] = 'BO';
        }else{
            $data['ok'] = 'CO';
        }
        
        $d = date('d');
        $b = date('m');
        $t = date('Y');
        
        $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $bulan = date('m', strtotime($waktu));
        $tahun = date('Y', strtotime($waktu));
        
        $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        // $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $sampai = $roar;
        // $sampai1 = date('Y-m-d');
        $sampai1 = $request->uch;
        
        $mbuh = $request->uch == '' ? date('Y-m-d') : $request->uch;
        
        $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $id)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
                
        $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$id'")->get();
        $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$id'")->get();
        
        // return($peng);
                
        $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = '$mbuh' AND approval = 1 AND coa_debet = '$id'")->get();
        $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = '$mbuh' AND acc = 1 AND coa_kredit = '$id'")->get();
        
        $jurnal = Jurnal::selectRaw("SUM(nominal_use) as nominal")->whereRaw("DATE(tanggal) = '$tgl_for' AND acc = 1 AND coa_use = '$id'")->get();
        
        if($request->uch == date('Y-m-01')){
            $saldo_awal = $saldo[0]->saldo;
        }else{
            $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal;
        }
        
        $wow = $saldo_awal + $trans1[0]->jumlah - $peng1[0]->nominal;
        
        $data['saldo_awal'] = $saldo_awal;
        $data['penerimaan'] = $trans1[0]->jumlah;
        $data['pengeluaran'] = $peng1[0]->nominal;
        $data['saldo_akhir'] = $wow;
        $data['jurnal'] = $jurnal[0]->nominal;
        
        // return($request);
        
        return json_encode($data);
    }
    
    function carikantor(Request $request, $id)
    {
        $data['kantor'] = Kantor::where('id_coa',$id)->first();
        $data['user'] = User::where('id', Auth::user()->id)->first();
        
        return json_encode($data);
    }
    
    function updatepen(Request $request){
        // return($request);
        
        return $request;
    }
    
    function tutupin(Request $request){
        // return($request);
        $input = $request->all();
        if($request->tipe == 'BO'){
            if($request->period == 'tgl'){
                $tgl = $request->tanggal_bo != '' ?  $request->tanggal_bo  : date('Y-m-d');
            }else if($request->period == 'bln'){
                if($request->bulan_bo != ''){ 
                    $hari_ini = date($request->bulan_bo.'-d');
                    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
                    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
                    $tgl = $tgl_terakhir;
                }else{ 
                    $hari_ini = date('Y-m-d');
                    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
                    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
                    $tgl = $tgl_terakhir;
                }
            }else if($request->period == 'thn'){
                if($request->tahun_bo != ''){
                    $last_year_last_month_date = date($request->tahun_bo.'-12-01');
                    $end_date = date($request->tahun_bo.'-12-t', strtotime($last_year_last_month_date));
                    $tgl = $end_date;
                }else{
                    $last_year_last_month_date = date("Y-12-01");
                    $end_date = date("Y-12-t", strtotime($last_year_last_month_date));
                    $tgl = $end_date;
                }
            }
            
            $cariakun = COA::where('coa',$request->bank_bo)->first();
            $input['coa_pen'] = $request->bank_bo;
            $input['tanggal'] = $tgl;
            $input['nama_akun'] = $cariakun->nama_coa;
            $input['saldo_awal'] = $request->saldo_awal_bo != '' ? $request->saldo_awal_bo : 0;
            $input['saldo_akhir'] = $request->saldo_akhir_bo != '' ? $request->saldo_akhir_bo : 0;
            $input['debit'] = $request->penerimaan_bo != '' ? $request->penerimaan_bo : 0;
            $input['kredit'] = $request->pengeluaran_bo != '' ? $request->pengeluaran_bo : 0;
            $input['adjustment'] = $request->penyesuaian_bo != '' ? $request->penyesuaian_bo : 0;
            $input['k100000'] = $request->k100000 != '' ? preg_replace("/[^0-9]/", "", $request->k100000) : 0;
            $input['k75000'] = $request->k75000 != '' ? preg_replace("/[^0-9]/", "", $request->k75000) : 0;
            $input['k50000'] = $request->k50000 != '' ? preg_replace("/[^0-9]/", "", $request->k50000) : 0;
            $input['k20000'] = $request->k20000 != '' ? preg_replace("/[^0-9]/", "", $request->k20000) : 0;
            $input['k10000'] = $request->k10000 != '' ? preg_replace("/[^0-9]/", "", $request->k10000) : 0;
            $input['k5000'] = $request->k5000 != '' ? preg_replace("/[^0-9]/", "", $request->k5000) : 0;
            $input['k2000'] = $request->k2000 != '' ? preg_replace("/[^0-9]/", "", $request->k2000) : 0;
            $input['k1000'] = $request->k1000 != '' ? preg_replace("/[^0-9]/", "", $request->k1000) : 0;
            $input['k500'] = $request->k500 != '' ? preg_replace("/[^0-9]/", "", $request->k500) : 0;
            $input['k100'] = $request->k100 != '' ? preg_replace("/[^0-9]/", "", $request->k100) : 0;
            $input['l1000'] = $request->l1000 != '' ? preg_replace("/[^0-9]/", "", $request->l1000) : 0;
            $input['l500'] = $request->l500 != '' ? preg_replace("/[^0-9]/", "", $request->l500) : 0;
            $input['l200'] = $request->l200 != '' ? preg_replace("/[^0-9]/", "", $request->l200) : 0;
            $input['l100'] = $request->l100 != '' ? preg_replace("/[^0-9]/", "", $request->l100) : 0;
            $input['l50'] = $request->l50 != '' ? preg_replace("/[^0-9]/", "", $request->l50) : 0;
            $input['l25'] = $request->l25 != '' ? preg_replace("/[^0-9]/", "", $request->l25) : 0;
            $input['saldo_fisik'] = $request->s_fisik_hide != '' ? preg_replace("/[^0-9]/", "", $request->s_fisik_hide) : 0;
            $input['user_input'] = $request->nama_bo_hide != '' ? $request->nama_bo_hide : NULL;
            $input['user_update'] =  $request->nama_bo_hide != '' ? $request->nama_bo_hide : NULL;
            
            
            
            // return($input);
            Penutupan::create($input);
            \LogActivity::addToLog(Auth::user()->name.' Melakukan Penutupan pada COA '.$request->bank_bo);
            
        }else{
            // return($request->kntr_co);
            if($request->periods == 'tgl'){
                $tgl = $request->tanggal_co != '' ?  $request->tanggal_co  : date('Y-m-d');
            }else if($request->periods == 'bln'){
                if($request->bulan_co != ''){ 
                    $hari_ini = date($request->bulan_co.'-d');
                    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
                    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
                    $tgl = $tgl_terakhir;
                }else{ 
                    $hari_ini = date('Y-m-d');
                    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
                    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
                    $tgl = $tgl_terakhir;
                }
            }else if($request->periods == 'thn'){
                if($request->tahun_co != ''){
                    $last_year_last_month_date = date($request->tahun_co.'-12-01');
                    $end_date = date($request->tahun_co.'-12-t', strtotime($last_year_last_month_date));
                    $tgl = $end_date;
                }else{
                    $last_year_last_month_date = date("Y-12-01");
                    $end_date = date("Y-12-t", strtotime($last_year_last_month_date));
                    $tgl = $end_date;
                }
            }
            
            $cariakun = COA::where('coa',$request->kntr_co)->first();
            $input['coa_pen'] = $cariakun->coa;
            $input['tanggal'] = $tgl;
            $input['nama_akun'] = $cariakun->nama_coa;
            $input['saldo_awal'] = $request->saldo_awal_co != '' ? $request->saldo_awal_co : 0;
            $input['saldo_akhir'] = $request->saldo_akhir_co != '' ? $request->saldo_akhir_co : 0;
            $input['debit'] = $request->penerimaan_co != '' ? $request->penerimaan_co : 0;
            $input['kredit'] = $request->pengeluaran_co != '' ? $request->pengeluaran_co : 0;
            $input['adjustment'] = $request->penyesuaian_co != '' ? $request->penyesuaian_co : 0;
            $input['k100000'] = $request->inputk1 != '' ? $request->inputk1 : 0;
            $input['k75000'] = $request->inputk2 != '' ? $request->inputk2 : 0;
            $input['k50000'] = $request->inputk3 != '' ? $request->inputk3 : 0;
            $input['k20000'] = $request->inputk4 != '' ? $request->inputk4 : 0;
            $input['k10000'] = $request->inputk5 != '' ? $request->inputk5 : 0;
            $input['k5000'] = $request->inputk6 != '' ? $request->inputk6 : 0;
            $input['k2000'] = $request->inputk7 != '' ? $request->inputk7 : 0;
            $input['k1000'] = $request->inputk8 != '' ? $request->inputk8 : 0;
            $input['k500'] = $request->inputk9 != '' ? $request->inputk9 : 0;
            $input['k100'] = $request->inputk10 != '' ? $request->inputk10 : 0;
            $input['l1000'] = $request->inputl1 != '' ? $request->inputl1 : 0;
            $input['l500'] = $request->inputl2 != '' ? $request->inputl2 : 0;
            $input['l200'] = $request->inputl3 != '' ? $request->inputl3 : 0;
            $input['l100'] = $request->inputl4 != '' ? $request->inputl4 : 0;
            $input['l50'] = $request->inputl5 != '' ? $request->inputl5 : 0;
            $input['l25'] = $request->inputl6 != '' ? $request->inputl6 : 0;
            $input['saldo_fisik'] = $request->s_fisik_hide != '' ? preg_replace("/[^0-9]/", "", $request->s_fisik_hide) : 0;
            $input['user_input'] = $request->nama_co_hide != '' ? $request->nama_co_hide : NULL;
            $input['user_update'] =  $request->nama_co_hide != '' ? $request->nama_co_hide : NULL;
            
            
            
            // return($input);
            
            
            Penutupan::create($input);
            \LogActivity::addToLog(Auth::user()->name.' Melakukan Penutupan pada COA '.$cariakun->id_coa);
        }
        // return back();
        
        
        
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    function bukuharian(Request $request){
         
    
        $kan = Auth::user()->id_kantor;
        $user_insert = User::selectRaw('id, name')->where('id_com',Auth::user()->id_com)->where('aktif', 1)->get();
        $user_approve = User::selectRaw('users.id, users.name')->join('transaksi','transaksi.user_approve','=','users.id')->where('users.id_com',Auth::user()->id_com)->where('users.aktif', 1)->distinct()->get();
        $program = User::selectRaw('id, name')->where('id_com',Auth::user()->id_com)->where('aktif', 1)->get();
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
        if(Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat' ){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->keuangan == 'kacab' || Auth::user()->keuangan == 'keuangan cabang' ){
            if($k == null){
                $kantor = Kantor::where('id', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $kan OR id = $k->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        if ($request->ajax()) {
             
            $r_bayar    = $request->pembayaran != '' ? $request->pembayaran : [];
            $r_kan      = $request->kota;
            $r_car      = $request->cartext;
            $r_thn      = $request->year;
            $r_via      = $request->via;
            $r_view     = $request->view != '' ? $request->view : [];
            $r_plhtgl   = $request->plhtgl;
            $r_buku     = $request->buku;
            $r_jentran  = $request->jenis_transaksi;
            $r_stat     = $request->stts;
            $r_prog     = $request->prog;
            $r_uin      = $request->user_insert;
            $r_uap      = $request->user_approve;
            $r_grup     = $request->groupby;
            
            $nom_jurn   = $request->dari_nominal != '' ? " nominal_use >= '$request->dari_nominal' AND nominal_use <= '$request->sampai_nominal'" : "nominal_use > 0";
            $nom_tran   = $request->dari_nominal != '' ? " transaksi.jumlah >= '$request->dari_nominal' AND transaksi.jumlah <= '$request->sampai_nominal'" : "transaksi.jumlah > 0";
            $nom_peng   = $request->dari_nominal != '' ? " pengeluaran.nominal >= '$request->dari_nominal' AND pengeluaran.nominal <= '$request->sampai_nominal'" : "pengeluaran.nominal > 0";
            $bd_jurn    = $request->backdate == '' ? "DATE(jurnal.tanggal) IS NOT NULL AND DATE(jurnal.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(jurnal.tanggal) != DATE(jurnal.created_at) " : "DATE(jurnal.tanggal) = DATE(jurnal.created_at)");
            $bd_tran    = $request->backdate == '' ? "DATE(transaksi.tanggal) IS NOT NULL AND DATE(transaksi.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(transaksi.tanggal) != DATE(transaksi.created_at) " : "DATE(transaksi.tanggal) = DATE(transaksi.created_at)");
            $bd_peng    = $request->backdate == '' ? "DATE(pengeluaran.tgl) IS NOT NULL AND DATE(pengeluaran.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(pengeluaran.tgl) != DATE(pengeluaran.created_at) " : "DATE(pengeluaran.tgl) = DATE(pengeluaran.created_at)");

            $dp_only    = !in_array('0', $r_view) && in_array('1', $r_view) ? 1 : 0;
            
            $f_bayar    = [];
            if($r_bayar != []){    
                $pc     = [];
                $pnc    = [];
                $pb     = [];
                $pm     = [];
                if(in_array('cash', $r_bayar)){
                    $pc  = ['dijemput', 'teller', 'cash'];
                }
                if(in_array('noncash', $r_bayar)){
                    $pnc = ['noncash'];
                }
                if(in_array('bank', $r_bayar)){
                    $pb  = ['transfer', 'bank'];
                }
                if(in_array('mutasi', $r_bayar)){
                    $pm  = ['mutasi'];
                }
                $f_bayar = array_merge($pc, $pnc, $pb, $pm);
            }
                
            if($r_kan == 'all_kan'){
                $inbuku = 0; 
            }else{
                if($r_buku == 'all_kas'){
                    $inbuku = 0; 
                }else if($r_buku == 'all_bank'){
                    $inbuku = 0; 
                }else{
                    $inbuku = 1; 
                }
            }
            
                if ($request->daterange != '') {
                    $tg     = explode(' s.d. ', $request->daterange);
                    $awal   = date('Y-m-d', strtotime($tg[0]));
                    $akhir  = date('Y-m-d', strtotime($tg[1]));
                }else{
                    $awal   = date('Y-m-d');
                    $akhir  = date('Y-m-d');
                }
            
                
                // if ($request->month != '') {
                //     $tg     = explode('-', $request->month);
                //     $thn    = date($tg[0]);
                //     $bln    = date($tg[1]);
                // }else{
                //     $thn    = date('Y');
                //     $bln    = date('m');
                // }
                
                
                $bln    = $request->month != '' ? $request->month : date('Y-m');
                $tobln  = $request->tomonth != '' ? $request->tomonth : $bln;
                $bawal   = date('Y-m-01', strtotime($bln.'-'.'01'));
                $bakhir  = date('Y-m-t', strtotime($tobln.'-'.'01'));
                
                
            if($r_plhtgl == 0){
                $awals  = date('Y-m-01', strtotime($awal));
                $tgsal  = date("Y-m-t", strtotime("-1 month", strtotime($awals)));
                $tgwal  = date('d', strtotime($awal));
                if($tgwal == '01'){
                    $akhirs = date('Y-m-01', strtotime($awal));
                }else{
                    $akhirs = date('Y-m-d', strtotime('-1 day',strtotime($awal)));
                }
                
            }else if($r_plhtgl == 1){
                $tgsal  = date("Y-m-t", strtotime("-1 month", strtotime($bln.'-'.'01')));
                $awals  = date('Y-m-01', strtotime($bln.'-'.'01'));
                $akhirs = date('Y-m-01', strtotime($bln.'-'.'01'));
            }else if($r_plhtgl == 2){
                $tgsal  = date("Y-m-t", strtotime("-1 month", strtotime($r_thn.'-01-01')));
                $awals  = date('Y-m-01', strtotime($r_thn.'-01-01'));
                $akhirs = date('Y-m-01', strtotime($r_thn.'-01-01'));
            }      
            
            // if(Auth::user()->id == 6){
            //     dd($tgsal, $awals, $akhirs);
            // }
            for($x = 0; $x < ($dp_only == 1 ? 1 : 2); $x++){
                if($x == 0){
                    $seljurn  = $r_grup != null ? 
                                "IF('$r_grup' >= 1, IF('$r_grup' > 1, DATE_FORMAT(tanggal, '%Y'), DATE_FORMAT(tanggal, '%Y-%m')), tanggal) AS tgdat, coa_use AS cd, coa_use AS ck, jenis_transaksi AS jentran, 0 AS dp, SUM(nominal_debit) AS debit, SUM(nominal_kredit) AS kredit, 0 as jumlah"
                                : "tanggal AS tgdat, 'jurnal' as via, id_jurnal AS id, coa_use AS cd, coa_use AS ck, jenis_transaksi AS jentran, 0 AS dp, keterangan AS ket, nominal_debit AS debit, nominal_kredit AS kredit, 0 as jumlah, '' AS id_tran, acc as status, '' AS urut"
                                ;
                    $seltran  = $r_grup != null ? 
                                "IF('$r_grup' >= 1, IF('$r_grup' > 1, DATE_FORMAT(tanggal, '%Y'), DATE_FORMAT(tanggal, '%Y-%m')), tanggal) AS tgdat, IF($inbuku = 0, 0, coa_debet) AS cd, coa_kredit AS ck, akun AS jentran, 0 AS dp, SUM(jumlah) AS debit, 0 AS kredit, 0 as jumlah"
                                : "tanggal AS tgdat, 'transaksi' as via, id, coa_debet AS cd, coa_kredit AS ck, akun AS jentran, 0 AS dp, ket_penerimaan AS ket, jumlah AS debit, 0 AS kredit, 0 as jumlah, id_transaksi AS id_tran, transaksi.approval as status, 1 AS urut"
                                ;
                                
                    $selpeng  = $r_grup != null ? 
                                "IF('$r_grup' >= 1, IF('$r_grup' > 1, DATE_FORMAT(tgl, '%Y'), DATE_FORMAT(tgl, '%Y-%m')), tgl) AS tgdat, IF($inbuku = 0, 0, coa_kredit) AS cd, coa_debet AS ck, jenis_transaksi AS jentran, 0 AS dp, 0 AS debit, SUM(nominal) AS kredit, 0 AS jumlah"
                                : "tgl AS tgdat, 'pengeluaran' as via, id, coa_kredit AS cd, coa_debet AS ck, jenis_transaksi AS jentran, 0 AS dp, keterangan AS ket, 0 AS debit, nominal AS kredit, 0 as jumlah, no_resi AS id_tran, acc as status, '' AS urut"
                                ;
                    
                    $seldp1   = $r_grup != null ? 
                                "IF('$r_grup' >= 1, IF('$r_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) AS tgdat, IF($inbuku = 0, 0, transaksi.coa_debet) AS cd, prog.coa1 AS ck, coa.nama_coa AS jentran, 1 AS dp, SUM(transaksi.jumlah * transaksi.dp / 100) AS debit, 0 AS kredit, 0 as jumlah"
                                : "transaksi.tanggal AS tgdat, 'transaksi' as via, transaksi.id, transaksi.coa_debet AS cd, prog.coa1 AS ck, coa.nama_coa AS jentran, 1 AS dp, transaksi.ket_penerimaan AS ket, (transaksi.jumlah * transaksi.dp / 100) AS debit, 0 AS kredit, 0 as jumlah, transaksi.id_transaksi AS id_tran, transaksi.approval as status, 3 AS urut"
                                ; 
                                
                    $seldp2   = $r_grup != null ? 
                                "IF('$r_grup' >= 1, IF('$r_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) AS tgdat, IF($inbuku = 0, 0, transaksi.coa_debet) AS cd, prog.coa2 AS ck, coa.nama_coa AS jentran, 1 AS dp, 0 AS debit, SUM(transaksi.jumlah * transaksi.dp / 100) AS kredit, 0 as jumlah"
                                : "transaksi.tanggal AS tgdat, 'transaksi' as via, transaksi.id, transaksi.coa_debet AS cd, prog.coa2 AS ck, coa.nama_coa AS jentran, 1 AS dp, transaksi.ket_penerimaan AS ket, 0 AS debit, (transaksi.jumlah * transaksi.dp / 100) AS kredit, 0 as jumlah, transaksi.id_transaksi AS id_tran, transaksi.approval as status, 2 AS urut"
                                ; 
                    
        
                    $selgrup  = $r_grup != null ?  
                                "tgdat, cd, ck, jentran, dp"
                                : "tgdat, via, id, cd, ck, jentran, dp, ket, debit, kredit, jumlah, id_tran, status"
                                ;
                    
                    if($r_car != ''){
                        $selhave = $r_grup != null ?
                                    "tgdat LIKE '%$r_car%' OR cd LIKE '%$r_car%' OR ck LIKE '%$r_car%' OR jentran LIKE '%$r_car%'"
                                    : "tgdat LIKE '%$r_car%' OR cd LIKE '%$r_car%' OR ck LIKE '%$r_car%' OR jentran LIKE '%$r_car%' OR ket LIKE '%$r_car%' OR debit LIKE '%$r_car%' OR kredit LIKE '%$r_car%'"
                                    ;
                    }else{
                        $selhave = "jentran != 'haha'";
                    }
                    
                    $selorder = $r_grup != null ?  
                                "tgdat DESC"
                                : "tgdat DESC, ket, urut"
                                ;
                }else{
                    $seljurn  = "'jurnal' as via, SUM(nominal_debit) AS debit, SUM(nominal_kredit) AS kredit";
                    
                    $seltran  = "'transaksi' as via, SUM(jumlah) AS debit, 0 AS kredit";
                                
                    $selpeng  = "'pengeluaran' as via, 0 AS debit, SUM(nominal) AS kredit";
                    
                    // $seldp1   = "'transaksi' as via, SUM(transaksi.jumlah * transaksi.dp / 100) AS debit, 0 AS kredit";
                    
                    // $seldp2   = "'transaksi' as via, 0 AS debit, SUM(transaksi.jumlah * transaksi.dp / 100) AS kredit";
                    
                    $selgrup  = "via";
                    $selhave  = "via != 'haha'";
                }  
                
                
                $bper = COA::whereRaw("grup LIKE '%5%'")->pluck('coa')->toArray();
                $inper = in_array($r_buku, $bper) ? 1 : 0;
                
                if($x == 0 || $r_plhtgl == 0){
                    $get_jurn  = DB::table('jurnal')
                                ->selectRaw($seljurn)
                                ->where(function($q) use ($r_plhtgl, $awal, $akhir, $bawal, $bakhir, $x, $awals, $akhirs, $r_thn) {
                                    if($x == 0){
                                        if($r_plhtgl == 0){
                                            $q->whereDate('tanggal', '>=', $awal)->whereDate('tanggal', '<=', $akhir);
                                        }else if($r_plhtgl == 1){
                                            $q->whereDate('tanggal', '>=', $bawal)->whereDate('tanggal', '<=', $bakhir);
                                        }else if($r_plhtgl == 2){
                                            $q->whereYear('tanggal', $r_thn);
                                        }
                                    }else{
                                        $q->whereDate('tanggal', '>=', $awals)->whereDate('tanggal', '<=', $akhirs);
                                    }
                                })
                                ->where(function($q) use ($r_buku, $r_kan) {
                                    if($r_buku == 'all_kas'){
                                        $q->whereIn('coa_use', function($que) {
                                            $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                        });
                                    }else if($r_buku == 'all_bank'){
                                        $q->whereIn('coa_use', function($que) {
                                            $que->select('id_coa')->from('bank');
                                        });
                                    }else if($r_buku == 'all_noncash'){
                                        $q->whereIn('coa_use', function($que) {
                                            $que->select('coa')->from('coa')->whereRaw("grup LIKE '%5%'");
                                        });
                                    }else if($r_buku == 'all_buk'){
                                        $q->where('coa_use', '!=', 'haha');
                                    }
                                    else if($r_buku != ''){ $q->where('coa_use', $r_buku); }
                                    else{ $q->where('coa_use', 'haha'); }
                                })
                                ->where(function($q) use ($r_kan, $inper) {
                                    if($r_kan == ''){
                                        $q->where('kantor', 'haha');
                                    }else if($r_kan != 'all_kan' && $inper == 1){
                                        $q->where('kantor', $r_kan);
                                    }
                                })
                                ->where(function($q) use ($r_jentran) {
                                    if($r_jentran != ''){ $q->where('coa_use', $r_jentran); }
                                })
                                ->where(function($q) use ($r_stat) {
                                    if($r_stat != ''){ $q->where('acc', $r_stat); }
                                })
                                ->where(function($q) use ($r_uin) {
                                    if($r_uin != ''){ $q->where('user_input', $r_uin); }
                                })
                                ->where(function($q) use ($r_uap) {
                                    if($r_uap != ''){ $q->where('user_update', $r_uap); }
                                })
                                // ->where(function($q) use ($r_prog) {
                                //     if($r_prog != ''){ $q->where('id_program', $r_prog); }
                                // })
                                ->where(function($q) use ($r_via) {
                                    if($r_via != ''){ $q->whereIn('via_input', $r_via); }
                                })
                                ->whereRaw("$nom_jurn AND $bd_jurn")
                                ->groupByRaw($selgrup)
                                ->havingRaw($selhave)
                                ;
                    $get_trans  = DB::table('transaksi')
                                ->selectRaw($seltran)
                                ->where(function($q) use ($r_plhtgl, $awal, $akhir, $bawal, $bakhir, $x, $awals, $akhirs, $r_thn) {
                                    if($x == 0){
                                        if($r_plhtgl == 0){
                                            $q->whereDate('tanggal', '>=', $awal)->whereDate('tanggal', '<=', $akhir);
                                        }else if($r_plhtgl == 1){
                                            $q->whereDate('tanggal', '>=', $bawal)->whereDate('tanggal', '<=', $bakhir);
                                        }else if($r_plhtgl == 2){
                                            $q->whereYear('tanggal', $r_thn);
                                        }
                                    }else{
                                        $q->whereDate('tanggal', '>=', $awals)->whereDate('tanggal', '<=', $akhirs);
                                    }
                                })
                                ->where(function($q) use ($r_buku, $r_kan) {
                                    if($r_buku == 'all_kas'){
                                        $q->whereIn('coa_debet', function($que) {
                                            $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                        });
                                    }else if($r_buku == 'all_bank'){
                                        $q->whereIn('coa_debet', function($que) {
                                            $que->select('id_coa')->from('bank');
                                        });
                                    }else if($r_buku == 'all_noncash'){
                                        $q->whereIn('coa_debet', function($que) {
                                            $que->select('coa')->from('coa')->whereRaw("grup LIKE '%5%'");
                                        });
                                    }else if($r_buku == 'all_buk'){
                                        $q->where('coa_debet', '!=', 'haha');
                                    }
                                    else if($r_buku != ''){ $q->where('coa_debet', $r_buku); }
                                    else{ $q->where('coa_debet', 'haha'); }
                                })
                                ->where(function($q) use ($r_kan, $inper) {
                                    if($r_kan == ''){
                                        $q->where('id_kantor', 'haha');
                                    }else if($r_kan != 'all_kan' && $inper == 1){
                                        $q->where('id_kantor', $r_kan);
                                    }
                                })
                                ->where(function($q) use ($r_jentran) {
                                    if($r_jentran != ''){ $q->where('coa_kredit', $r_jentran); }
                                })
                                ->where(function($q) use ($r_stat) {
                                    if($r_stat != ''){ $q->where('approval', $r_stat); }
                                })
                                ->where(function($q) use ($r_uin) {
                                    if($r_uin != ''){ $q->where('user_insert', $r_uin); }
                                })
                                ->where(function($q) use ($r_uap) {
                                    if($r_uap != ''){ $q->where('user_approve', $r_uap); }
                                })
                                ->where(function($q) use ($r_prog) {
                                    if($r_prog != ''){ $q->where('id_program', $r_prog); }
                                })
                                ->where(function($q) use ($r_via) {
                                    if($r_via != ''){ $q->whereIn('via_input', $r_via); }
                                })
                                ->where(function($q) use ($r_bayar, $f_bayar) {
                                    if($r_bayar != []){ $q->whereIn('pembayaran', $f_bayar); }
                                })
                                ->whereRaw("$nom_tran AND $bd_tran")
                                ->groupByRaw($selgrup)
                                ->havingRaw($selhave)
                                ;
                                
                    $get_peng   = DB::table('pengeluaran')
                                ->selectRaw("$selpeng")
                                ->where(function($q) use ($r_plhtgl, $awal, $akhir, $bawal, $bakhir, $x, $awals, $akhirs, $r_thn) {
                                    if($x == 0){
                                        if($r_plhtgl == 0){
                                            $q->whereDate('tgl', '>=', $awal)->whereDate('tgl', '<=', $akhir);
                                        }else if($r_plhtgl == 1){
                                            $q->whereDate('tgl', '>=', $bawal)->whereDate('tgl', '<=', $bakhir);
                                        }else if($r_plhtgl == 2){
                                            $q->whereYear('tgl', $r_thn);
                                        }
                                    }else{
                                        $q->whereDate('tgl', '>=', $awals)->whereDate('tgl', '<=', $akhirs);
                                    }
                                })
                                ->where(function($q) use ($r_buku, $r_kan) {
                                    if($r_buku == 'all_kas'){
                                        $q->whereIn('coa_kredit', function($que) {
                                            $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                        });
                                    }else if($r_buku == 'all_bank'){
                                        $q->whereIn('coa_kredit', function($que) {
                                            $que->select('id_coa')->from('bank');
                                        });
                                    }else if($r_buku == 'all_noncash'){
                                        $q->whereIn('coa_kredit', function($que) {
                                            $que->select('coa')->from('coa')->whereRaw("grup LIKE '%5%'");
                                        });
                                    }else if($r_buku == 'all_buk'){
                                        $q->where('coa_kredit', '!=', 'haha');
                                    }
                                    else if($r_buku != ''){ $q->where('coa_kredit', $r_buku); }
                                    else{ $q->where('coa_kredit', 'haha'); }
                                })
                                ->where(function($q) use ($r_kan, $inper) {
                                    if($r_kan == ''){
                                        $q->where('kantor', 'haha');
                                    }else if($r_kan != 'all_kan' && $inper == 1){
                                        $q->where('kantor', $r_kan);
                                    }
                                })
                                ->where(function($q) use ($r_jentran) {
                                    if($r_jentran != ''){ $q->where('coa_debet', $r_jentran); }
                                })
                                ->where(function($q) use ($r_stat) {
                                    if($r_stat != ''){ $q->where('acc', $r_stat); }
                                })
                                ->where(function($q) use ($r_uin) {
                                    if($r_uin != ''){ $q->where('user_input', $r_uin); }
                                })
                                ->where(function($q) use ($r_uap) {
                                    if($r_uap != ''){ $q->where('user_approve', $r_uap); }
                                })
                                ->where(function($q) use ($r_prog) {
                                    if($r_prog != ''){ $q->where('program', $r_prog); }
                                })
                                ->where(function($q) use ($r_via) {
                                    if($r_via != ''){ $q->whereIn('via_input', $r_via); }
                                })
                                ->where(function($q) use ($r_bayar, $f_bayar) {
                                    if($r_bayar != []){ $q->whereIn('pembayaran', $f_bayar); }
                                })
                                ->whereRaw("$nom_peng AND $bd_peng")
                                ->groupByRaw($selgrup)
                                ->havingRaw($selhave)
                                ;
                }            
                if($x == 0){ 
                    if(in_array('1', $r_view)){
                    $get_dp1    = DB::table('transaksi')
                                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                                ->leftJoin('coa','coa.coa','=','prog.coa1')
                                ->selectRaw($seldp1)
                                ->where(function($q) use ($r_plhtgl, $awal, $akhir, $bawal, $bakhir, $r_thn) {
                                    if($r_plhtgl == 0){
                                        $q->whereDate('transaksi.tanggal', '>=', $awal)->whereDate('transaksi.tanggal', '<=', $akhir);
                                    }else if($r_plhtgl == 1){
                                        $q->whereDate('transaksi.tanggal', '>=', $bawal)->whereDate('transaksi.tanggal', '<=', $bakhir);
                                    }else if($r_plhtgl == 2){
                                        $q->whereYear('transaksi.tanggal', $r_thn);
                                    }
                                })
                                ->where(function($q) use ($r_buku, $r_kan) {
                                    if($r_buku == 'all_kas'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                        });
                                    }else if($r_buku == 'all_bank'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('id_coa')->from('bank');
                                        });
                                    }else if($r_buku == 'all_noncash'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('coa')->from('coa')->whereRaw("grup LIKE '%5%'");
                                        });
                                    }else if($r_buku == 'all_buk'){
                                        $q->where('transaksi.coa_debet', '!=', 'haha');
                                    }
                                    else if($r_buku != ''){ $q->where('transaksi.coa_debet', $r_buku); }
                                    else{ $q->where('transaksi.coa_debet', 'haha'); }
                                })
                                ->where(function($q) use ($r_kan, $inper) {
                                    if($r_kan == ''){
                                        $q->where('transaksi.id_kantor', 'haha');
                                    }else if($r_kan != 'all_kan' && $inper == 1){
                                        $q->where('transaksi.id_kantor', $r_kan);
                                    }
                                })
                                ->where(function($q) use ($r_jentran) {
                                    if($r_jentran != ''){ $q->where('prog.coa1', $r_jentran); }
                                })
                                ->where(function($q) use ($r_stat) {
                                    if($r_stat != ''){ $q->where('transaksi.approval', $r_stat); }
                                })
                                ->where(function($q) use ($r_uin) {
                                    if($r_uin != ''){ $q->where('transaksi.user_insert', $r_uin); }
                                })
                                ->where(function($q) use ($r_uap) {
                                    if($r_uap != ''){ $q->where('transaksi.user_approve', $r_uap); }
                                })
                                ->where(function($q) use ($r_prog) {
                                    if($r_prog != ''){ $q->where('transaksi.id_program', $r_prog); }
                                })
                                ->where(function($q) use ($r_via) {
                                    if($r_via != ''){ $q->whereIn('transaksi.via_input', $r_via); }
                                })
                                ->where(function($q) use ($r_bayar, $f_bayar) {
                                    if($r_bayar != []){ $q->whereIn('transaksi.pembayaran', $f_bayar); }
                                })
                                ->whereRaw("$nom_tran AND $bd_tran AND via_input = 'transaksi' AND pembayaran != 'noncash'")
                                ->groupByRaw($selgrup)
                                ->havingRaw($selhave)
                                ;
                    
                    $get_dp2    = DB::table('transaksi')
                                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                                ->leftJoin('coa','coa.coa','=','prog.coa2')
                                ->selectRaw($seldp2)
                                ->where(function($q) use ($r_plhtgl, $awal, $akhir, $bawal, $bakhir, $r_thn) {
                                    if($r_plhtgl == 0){
                                        $q->whereDate('transaksi.tanggal', '>=', $awal)->whereDate('transaksi.tanggal', '<=', $akhir);
                                    }else if($r_plhtgl == 1){
                                        $q->whereDate('transaksi.tanggal', '>=', $bawal)->whereDate('transaksi.tanggal', '<=', $bakhir);
                                    }else if($r_plhtgl == 2){
                                        $q->whereYear('transaksi.tanggal', $r_thn);
                                    }
                                })
                                ->where(function($q) use ($r_buku, $r_kan) {
                                    if($r_buku == 'all_kas'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                        });
                                    }else if($r_buku == 'all_bank'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('id_coa')->from('bank');
                                        });
                                    }else if($r_buku == 'all_noncash'){
                                        $q->whereIn('transaksi.coa_debet', function($que) {
                                            $que->select('coa')->from('coa')->whereRaw("grup LIKE '%5%'");
                                        });
                                    }else if($r_buku == 'all_buk'){
                                        $q->where('transaksi.coa_debet', '!=', 'haha');
                                    }
                                    else if($r_buku != ''){ $q->where('transaksi.coa_debet', $r_buku); }
                                    else{ $q->where('transaksi.coa_debet', 'haha'); }
                                })
                                ->where(function($q) use ($r_kan, $inper) {
                                    if($r_kan == ''){
                                        $q->where('transaksi.id_kantor', 'haha');
                                    }else if($r_kan != 'all_kan' && $inper == 1){
                                        $q->where('transaksi.id_kantor', $r_kan);
                                    }
                                })
                                ->where(function($q) use ($r_jentran) {
                                    if($r_jentran != ''){ $q->where('prog.coa2', $r_jentran); }
                                })
                                ->where(function($q) use ($r_stat) {
                                    if($r_stat != ''){ $q->where('transaksi.approval', $r_stat); }
                                })
                                ->where(function($q) use ($r_uin) {
                                    if($r_uin != ''){ $q->where('transaksi.user_insert', $r_uin); }
                                })
                                ->where(function($q) use ($r_uap) {
                                    if($r_uap != ''){ $q->where('transaksi.user_approve', $r_uap); }
                                })
                                ->where(function($q) use ($r_prog) {
                                    if($r_prog != ''){ $q->where('transaksi.id_program', $r_prog); }
                                })
                                ->where(function($q) use ($r_via) {
                                    if($r_via != ''){ $q->whereIn('transaksi.via_input', $r_via); }
                                })
                                ->where(function($q) use ($r_bayar, $f_bayar) {
                                    if($r_bayar != []){ $q->whereIn('transaksi.pembayaran', $f_bayar); }
                                })
                                ->whereRaw("$nom_tran AND $bd_tran AND via_input = 'transaksi' AND pembayaran != 'noncash'")
                                ->groupByRaw($selgrup)
                                ->havingRaw($selhave)
                                ;
                    }
                                
                    if(in_array('0', $r_view) && in_array('1', $r_view)){
                        $trans = $get_trans->unionAll($get_dp1)->unionAll($get_dp2)->unionAll($get_peng)->unionAll($get_jurn)
                                ->orderByRaw($selorder)->get(); 
                    }else if($dp_only == 1){
                        $trans = $get_dp1->unionAll($get_dp2)
                                ->orderByRaw($selorder)->get(); 
                    }else{
                        $trans = $get_trans->unionAll($get_peng)->unionAll($get_jurn)
                                ->orderByRaw($selorder)->get();                
                    }
                }else{
                    if($r_buku == 'all_kas'){
                        $getsal = SaldoAw::whereDate('bulan', $tgsal)
                                            ->whereIn('coa', function($que) {
                                                $que->select('id_coa')->from('tambahan')->where('id_com', Auth::user()->id_com);
                                            })->sum('saldo_awal');
                        $tsal   = $getsal != null ? $getsal : 0;
                    }else if($r_buku == 'all_bank'){
                        $getsal = SaldoAw::whereDate('bulan', $tgsal)
                                            ->whereIn('coa', function($que) {
                                                $que->select('id_coa')->from('bank');
                                            })->sum('saldo_awal');
                        $tsal   = $getsal != null ? $getsal : 0;
                    }else{
                        $getsal = SaldoAw::where('coa', $r_buku)->whereDate('bulan', $tgsal)->first();
                        $tsal   = $getsal != null ? $getsal->saldo_awal : 0;
                    }
                    
                    if($r_plhtgl == 0 && $tgwal != '01'){
                        $getdeb = $get_trans->first();
                        $getkre = $get_peng->first();
                        
                        $tdeb   = $getdeb != null ? $getdeb->debit : 0;
                        $tkre   = $getkre != null ? $getkre->kredit : 0;
                        $salwal = $tsal + $tdeb - $tkre;
                    }else{
                        $salwal = $tsal;
                    }
                }
            }
            $dat = [];
            $sal = [];
            $totdeb = 0;
            $totkre = 0;
            
            // $j   = $dp_only == 1 ? 0 : 1;
            // $con = $dp_only == 1 ? count($trans) : count($trans)+1;
            
            // if($dp_only != 1){
            //     $dat[0]  = [
            //                 'no' => 0,
            //                 'tanggal' => null,
            //                 'coa' => null,
            //                 'jentran' => 'Saldo Awal :',
            //                 'debit' => 0,
            //                 'kredit' => 0,
            //                 'id_tran' => null,
            //                 'ket' => null,
            //                 'saldo' => $salwal,
            //                 'id' => null,
            //                 'via' => null,
            //                 'dp' => null,
            //                 'status' => null,
            //             ];
            // }
            
            // for($j; $j < $con; $j++){
                
            //     $i = $dp_only == 1 ? $j : $j-1; 
                
               
            //         if($i == 0){
            //             $sal[$i] = ($dp_only == 1 ? 0 : $salwal) + $trans[$i]->debit - $trans[$i]->kredit;
            //         }else{
            //             $sal[$i] = $sal[$i-1] + $trans[$i]->debit - $trans[$i]->kredit;
            //         }
                
            //     if($r_grup == null){
            //         $ket = $trans[$i]->dp == 1 ? '#DP '.$trans[$i]->ket : $trans[$i]->ket;
            //     }
                
            //     if($request->tab == 'tab1'){
            //         $totdeb += $trans[$i]->debit;
            //         $totkre += $trans[$i]->kredit;
            //     }
            //     $dat[$j]  = [
            //                 'no' => $j,
            //                 // 'jumlahs' =>  $iya[$i]->dp ? 0 : $t[$i],
            //                 'tanggal' => $trans[$i]->tanggal,
            //                 'coa' =>$trans[$i]->ck,
            //                 'jentran' => $trans[$i]->jentran,
            //                 'debit' => $trans[$i]->debit,
            //                 'kredit' => $trans[$i]->kredit,
            //                 'id_tran' => $r_grup != null ? null : $trans[$i]->id_tran,
            //                 'ket' =>  $r_grup != null ? null : $ket,
            //                 'saldo' =>  $r_grup != null ? null :  $sal[$i],
            //                 'id' => $r_grup != null ? null : $trans[$i]->id,
            //                 'via' => $r_grup != null ? null : $trans[$i]->via,
            //                 'dp' => $r_grup != null ? null : $trans[$i]->dp,
            //                 'status' => $r_grup != null ? null : $trans[$i]->status,
            //             ];
            // }
            
            for($i=0; $i < count($trans); $i++){
               
                    if($i == 0){
                        $sal[$i] = ($dp_only == 1 ? 0 : $salwal) + $trans[$i]->debit - $trans[$i]->kredit;
                    }else{
                        $sal[$i] = $sal[$i-1] + $trans[$i]->debit - $trans[$i]->kredit;
                    }
                
                if($r_grup == null){
                    $ket = $trans[$i]->dp == 1 ? '#DP '.$trans[$i]->ket : $trans[$i]->ket;
                }
                
                if($request->tab == 'tab1'){
                    $totdeb += $trans[$i]->debit;
                    $totkre += $trans[$i]->kredit;
                }
                $dat[]  = [
                            'tanggal' => $trans[$i]->tgdat,
                            'coa' =>$trans[$i]->ck,
                            'jentran' => $trans[$i]->jentran,
                            'debit' => $trans[$i]->debit,
                            'kredit' => $trans[$i]->kredit,
                            'id_tran' => $r_grup != null ? null : $trans[$i]->id_tran,
                            'ket' =>  $r_grup != null ? null : $ket,
                            'saldo' =>  $r_grup != null ? null :  $sal[$i],
                            'id' => $r_grup != null ? null : $trans[$i]->id,
                            'via' => $r_grup != null ? null : $trans[$i]->via,
                            'dp' => $r_grup != null ? null : $trans[$i]->dp,
                            'status' => $r_grup != null ? null : $trans[$i]->status,
                        ];
            }
            if($request->tab == 'tab1'){
                $k = [
                    'debit'     => $totdeb,
                    'kredit'    => $totkre,
                    'salwal'    => $dp_only == 1 ? 0 : $salwal,
                    'salakh'    => $dp_only == 1 ? 0 : $salwal + $totdeb - $totkre,
                    'dp'        => $dp_only,
                    'inper'     => $inper
                ];
                
                return $k;
            }else{
                return DataTables::of($dat)
                ->addIndexColumn()
                ->make(true);
            }
            
        
        }
        
        return view('akuntasi.buku_harian', compact('kantor','user_insert','user_approve'));
    }
    function buku_harian_export(Request $request){
        $r_thn      = $request->year;
        $r_plhtgl   = $request->plhtgl;
        $r_buku     = $request->buku;
        
        if ($request->daterange != '') {
            $tg     = explode(' s.d. ', $request->daterange);
            $awal   = date('Y-m-d', strtotime($tg[0]));
            $akhir  = date('Y-m-d', strtotime($tg[1]));
        }else{
            $awal   = date('Y-m-d');
            $akhir  = date('Y-m-d');
        }
    
        $bln    = $request->month != '' ? $request->month : date('Y-m');
        $tobln  = $request->tomonth != '' ? $request->tomonth : $bln;
        
       if($r_plhtgl == 0){
           $periode = '_tgl_'.$awal.'_s.d_'.$akhir;
       }else if($r_plhtgl == 1){
           $periode = $request->rmonth != null ? '_bln_'.$bln.'_s.d_'.$tobln : '_bln_'.$bln;
       }else{
           $periode = '_thn_'.$r_thn;
       } 
                
        // $periode = $r_plhtgl == 0 ? '_tgl_'.$awal.'_s.d_'.$akhir : '_bln_'.$bln.'_'.$thn;
        
        if($request->tombol == 'xls'){
            $r = Excel::download(new HarianExport($request), 'Buku_harian'.$periode.'_'.$r_buku.'.xlsx');
        }else{
            $r = Excel::download(new HarianExport($request), 'Buku_harian'.$periode.'_'.$r_buku.'.csv');
        }
        ob_end_clean();
        return $r;
    }
    
    function caribuku(Request $request, $id){
        
            if($id == 'all_kan'){
                $buk = COA::whereRaw("grup LIKE '%5%'")->get();
                $h1[0] = [
                        "text" => 'Semua Buku',
                        "coa" => 'Semua',
                        "id" => 'all_buk',
                        "parent" => 'n',
                        "nama_coa" => 'Buku',
                    ];
                $h1[1] = [
                        "text" => 'Semua Kas',
                        "coa" => 'Semua',
                        "id" => 'all_kas',
                        "parent" => 'n',
                        "nama_coa" => 'Kas',
                    ];
                $h1[2] = [
                        "text" => 'Semua Bank',
                        "coa" => 'Semua',
                        "id" => 'all_bank',
                        "parent" => 'n',
                        "nama_coa" => 'Bank',
                    ];
                $h1[3] = [
                        "text" => 'Semua Persediaan',
                        "coa" => 'Semua',
                        "id" => 'all_noncash',
                        "parent" => 'n',
                        "nama_coa" => 'Persediaan',
                    ];
                    
                foreach ($buk as $key => $val) {
                    $h1[$key + 4] = [
                        "text" => $val->nama_coa,
                        "coa" => $val->coa,
                        "id" => $val->coa,
                        "parent" => $val->parent,
                        "nama_coa" => $val->nama_coa,
                    ];
                }
            }else{
                $kan = Kantor::findOrFail($id);
                $buk = COA::whereRaw("(grup LIKE '%4%' OR grup LIKE '%3%') AND id_kantor = $id OR grup LIKE '%5%'")->get();
                
                foreach ($buk as $key => $val) {
                    $h1[] = [
                        "text" => $val->nama_coa,
                        "coa" => $val->coa,
                        "id" => $val->coa,
                        "parent" => $val->parent,
                        "nama_coa" => $val->nama_coa,
                    ];
                }
            }
        
        // }else{
        // $prog = COA::whereRaw("(grup = 4 OR grup = 3) AND id_kantor = $id OR grup = 5")->get();
        // foreach ($prog as $key => $val) {
        //     $h1[] = [
        //         "text" => $val->nama_coa,
        //         "coa" => $val->coa,
        //         "id" => $val->coa,
        //         "parent" => $val->parent,
        //         "nama_coa" => $val->nama_coa,
        //     ];
        // }
        // }
        return response()->json($h1);
    }
    
    function bukuhariandash(Request $request){
        
        $now = date('Y-m-d'); 
         if ($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari11 = date('Y-m-d', strtotime($tgl[0])) == date('Y-m-01', strtotime($tgl[0])) ? date('Y-m-d', strtotime($tgl[0])) : date('Y-m-01', strtotime($tgl[0]))  ;
            $sampai11 = date('Y-m-d', strtotime('-1 day',strtotime($tgl[0]) ));
        }
        $now1 = date('Y-m-1');
        $sampai111 = date('Y-m-d', strtotime('-1 day', strtotime($now)));
        $tgls1 =  $request->daterange != '' ? "DATE(tanggal) >= '$dari11' AND DATE(tanggal) <= '$sampai11'" : "DATE(tanggal) >= '$now1' AND DATE(tanggal) <= '$sampai111'";
        $tglz1 = $request->daterange != '' ? "DATE(tgl) >= '$dari11' AND DATE(tgl) <= '$sampai11'" : "DATE(tgl) >= '$now1' AND DATE(tgl) <= '$sampai111'"; 
          
        if ($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
            
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
        $tglz = $request->daterange != '' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE(tgl) >= '$now' AND DATE(tgl) <= '$now'";
        
        if ($request->month != '') {
            $tgl = explode('-', $request->month);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $t = date('Y');
            $b = date('m');
        }
        $tahuns = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
        $tahunz = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$t'";
        
        if ($request->stts == '') {
            $stts = "transaksi.approval IS NOT NULL";
            $sttz = "acc != 0";
        } else if ($request->stts == 2) {
            $stts = "transaksi.approval = 2";
            $sttz = "acc = 2";
        } else if ($request->stts == 1) {
            $stts = "transaksi.approval = 1";
            $sttz = "acc = 1";
        } else if ($request->stts == 0) {
            $stts = "transaksi.approval = 0";
            $sttz = "acc = 0";
        }
        
        $p = $request->view;
        $p = is_array($p) ? '' : $p;
        $via = $request->via;
        $b = $request->input;
        
        $kntrr = $request->kota == '' ? "transaksi.id_kantor IS NOT NULL" : "transaksi.id_kantor = $request->kota";
        $kntr = $request->kota == '' ? "pengeluaran.kantor IS NOT NULL" : "pengeluaran.kantor = $request->kota";
        
        $buku1 = $request->buku == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_debet = '$request->buku'";
        $buku2 = $request->buku == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.coa_kredit = '$request->buku'";
        
        $nominals = $request->dari_nominal != '' ? " transaksi.jumlah >= '$request->dari_nominal' AND transaksi.jumlah  <= '$request->sampai_nominal'" : "transaksi.jumlah IS NOT NULL";
        $nominalz = $request->dari_nominal != '' ? " nominal >= '$request->dari_nominal' AND nominal  <= '$request->sampai_nominal'" : "nominal IS NOT NULL";
        
        $jentran1 = $request->jenis_transaksi == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_kredit = '$request->jenis_transaksi'";
        $jentran11 = $request->jenis_transaksi == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_kredit = '$request->jenis_transaksi'";
        $jentran2 = $request->jenis_transaksi == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.coa_kredit = '$request->jenis_transaksi'";
        
        $prog = $request->prog == '' ? "transaksi.coa_debet IS NOT NULL" : "id_program = '$request->prog'";
        $progs = $request->prog == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.program = '$request->prog'";
        
        $backdates1 = $request->backdate == '' ? "DATE(pengeluaran.tgl) IS NOT NULL AND DATE(pengeluaran.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(pengeluaran.tgl) != DATE(pengeluaran.created_at) ": "DATE(pengeluaran.tgl) = DATE(pengeluaran.created_at)" );
        $backdates = $request->backdate == '' ? "DATE(transaksi.tanggal) IS NOT NULL AND DATE(transaksi.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(transaksi.tanggal) != DATE(transaksi.created_at) ": "DATE(transaksi.tanggal) = DATE(transaksi.created_at)" );
        
        $user_inserts = $request->user_insert == '' ? "transaksi.user_insert IS NOT NULL": "transaksi.user_insert = '$request->user_insert'" ;
        $user_insertz = $request->user_insert == '' ? "pengeluaran.user_input IS NOT NULL": "pengeluaran.user_input = '$request->user_insert'" ;
        
        $user_approves = $request->user_approve == '' ? "transaksi.user_approve IS NOT NULL": "transaksi.user_approve = '$request->user_approve'" ;
        $user_approvez = $request->user_approve == '' ? "pengeluaran.user_approve IS NOT NULL": "pengeluaran.user_approve = '$request->user_approve'" ;
        $currentYear = date('Y');
        $coabuk = $request->buku == '' ? "coa IS NOT NULL" :  "coa = '$request->buku'";
        
        $awa = [];
        
        $kk = $request->kota == '' ? Auth::user()->id_kantor : $request->kota;
        
        $p = $request->input;
        $currentYear = date('Y');
        if($request->plhtgl == 0){
            if($request->via != ''){
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk  AND YEAR(bulan) = $currentYear")->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit, SUM(nominal) as kredit, '0' as jumlah")
                        ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })->whereRaw("$tglz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->whereIn('via_input', $request->via);
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')
                ->selectRaw("$aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")
                ->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tgls AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->whereIn('via_input', $request->via);
            }else{
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit,  SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
               ->whereRaw("$tglz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->where('via_input', '!=', null );
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw(" $aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tgls AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->where('via_input', '!=', null );
            }
        }else if($request->plhtgl == 1){
            if($request->via != ''){
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit, SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahunz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->whereIn('via_input', $request->via);
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw("$aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahuns AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->whereIn('via_input', $request->via);
            }else{
                // $coa = COA::selectRaw("konak as aw")->whereRaw("(grup = 3 OR grup = 4) AND $coabuk AND id_kantor = '$kk'")->get();
                // $aw = $coa[0]->aw;
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit,  SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahunz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->where('via_input', '!=', null );
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw(" $aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahuns AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves AND $backdates")->where('via_input', '!=', null );
            }
        }
        
        $t = $transaksi->get();
        
        
        $data = [];
        $debit = $t[0]->debit == null ? 0 : $t[0]->debit;
        $jumlah = $t[0]->jumlah == null ? 0 : $t[0]->jumlah;
        $kredit = $t[1]->kredit == null ? 0 : $t[1]->kredit;
        $saldo = $t[0]->saldo == null ? 0 : $t[0]->saldo;
        $data = [
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldo,
            'jumlah' => $saldo + $debit - $kredit,
        ];
                
        return $data;
    }
    
    function bukuhariandashsawal(Request $request){
        
         
        if ($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0])) == date('Y-m-01', strtotime($tgl[0])) ? date('Y-m-d', strtotime($tgl[0])) : date('Y-m-01', strtotime($tgl[0]))  ;
            $sampai = date('Y-m-d', strtotime('-1 day',strtotime($tgl[0]) ));
        }
        $now = date('Y-m-d');
        $now1 = date('Y-m-1');
        $sampai1 = date('Y-m-d', strtotime('-1 day', strtotime($now)));
        $tgls =  $request->daterange != '' ? "DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai'" : "DATE(tanggal) >= '$now1' AND DATE(tanggal) <= '$sampai1'";
        $tglz = $request->daterange != '' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE(tgl) >= '$now1' AND DATE(tgl) <= '$sampai1'";
        if ($request->stts == '') {
            $stts = "transaksi.approval IS NOT NULL";
            $sttz = "pengeluaran.acc IS NOT NULL";
        } else if ($request->stts == 2) {
            $stts = "transaksi.approval = 2";
            $sttz = "pengeluaran.acc = 2";
        } else if ($request->stts == 1) {
            $stts = "transaksi.approval = 1";
            $sttz = "pengeluaran.acc = 1";
        } else if ($request->stts == 0) {
            $stts = "transaksi.approval = 0";
            $sttz = "pengeluaran.acc = 0";
        }
        
        if ($request->month != '') {
            $tgl = explode('-', $request->month);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $t = date('Y');
            $b = date('m');
        }
           $p = $request->view;
        $p = is_array($p) ? '' : $p;
        $via = $request->via;
        $b = $request->input;
        
        $tahunz = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$t'";
          $tahuns = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
        $kntrr = $request->kota == '' ? "transaksi.id_kantor IS NOT NULL" : "transaksi.id_kantor = $request->kota";
        $kntr = $request->kota == '' ? "pengeluaran.kantor IS NOT NULL" : "pengeluaran.kantor = $request->kota";
        
        $buku1 = $request->buku == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_debet = '$request->buku'";
        $buku2 = $request->buku == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.coa_kredit = '$request->buku'";
        
        $nominals = $request->dari_nominal != '' ? " transaksi.jumlah >= '$request->dari_nominal' AND transaksi.jumlah  <= '$request->sampai_nominal'" : "transaksi.jumlah IS NOT NULL";
        $nominalz = $request->dari_nominal != '' ? " nominal >= '$request->dari_nominal' AND nominal  <= '$request->sampai_nominal'" : "nominal IS NOT NULL";
        
        $jentran1 = $request->jenis_transaksi == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_kredit = '$request->jenis_transaksi'";
        $jentran11 = $request->jenis_transaksi == '' ? "transaksi.coa_debet IS NOT NULL" : "transaksi.coa_kredit = '$request->jenis_transaksi'";
        $jentran2 = $request->jenis_transaksi == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.coa_kredit = '$request->jenis_transaksi'";
        
        $prog = $request->prog == '' ? "transaksi.coa_debet IS NOT NULL" : "id_program = '$request->prog'";
        $progs = $request->prog == '' ? "pengeluaran.coa_kredit IS NOT NULL" :  "pengeluaran.program = '$request->prog'";
        
        $backdates1 = $request->backdate == '' ? "DATE(pengeluaran.tgl) IS NOT NULL AND DATE(pengeluaran.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(pengeluaran.tgl) != DATE(pengeluaran.created_at) ": "DATE(pengeluaran.tgl) = DATE(pengeluaran.created_at)" );
        $backdates = $request->backdate == '' ? "DATE(transaksi.tanggal) IS NOT NULL AND DATE(transaksi.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(transaksi.tanggal) != DATE(transaksi.created_at) ": "DATE(transaksi.tanggal) = DATE(transaksi.created_at)" );
        
        $user_inserts = $request->user_insert == '' ? "transaksi.user_insert IS NOT NULL": "transaksi.user_insert = '$request->user_insert'" ;
        $user_insertz = $request->user_insert == '' ? "pengeluaran.user_input IS NOT NULL": "pengeluaran.user_input = '$request->user_insert'" ;
        
        $user_approves = $request->user_approve == '' ? "transaksi.user_approve IS NOT NULL": "transaksi.user_approve = '$request->user_approve'" ;
        $user_approvez = $request->user_approve == '' ? "pengeluaran.user_approve IS NOT NULL": "pengeluaran.user_approve = '$request->user_approve'" ;
        $currentYear = date('Y');
        
        $coabuk = $request->buku == '' ? "coa IS NOT NULL" :  "coa = '$request->buku'";
        
        $awa = [];
        
        $kk = $request->kota == '' ? Auth::user()->id_kantor : $request->kota;
        
        $p = $request->input;
        $currentYear = date('Y');
        if($request->plhtgl == 0){
            if($request->via != ''){
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit, SUM(nominal) as kredit, '0' as jumlah")
                        ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                        ->whereRaw("$tglz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez")->whereIn('via_input', $request->via);
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')
                ->selectRaw("$aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")
                ->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tgls AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->whereIn('via_input', $request->via);
            }else{
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit,  SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tglz AND $sttz  AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez")->where('via_input', '!=', null );
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw(" $aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tgls AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->where('via_input', '!=', null );
            }
        }else if($request->plhtgl == 1){
            if($request->via != ''){
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit, SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahunz AND $sttz AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->whereIn('via_input', $request->via);
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw("$aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahuns AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->whereIn('via_input', $request->via);
            }else{
                // $coa = COA::selectRaw("konak as aw")->whereRaw("(grup = 3 OR grup = 4) AND $coabuk AND id_kantor = '$kk'")->get();
                // $aw = $coa[0]->aw;
                $coa = DB::table("saldo_awal")->selectRaw('saldo_awal as aw')->whereRaw(" $coabuk ")->orderBy('id','desc')->first();
                $aw = $coa->aw;
                $alay = DB::table('pengeluaran')->selectRaw("'0' as saldo, '0' as debit,  SUM(nominal) as kredit, '0' as jumlah")
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('jenis_transaksi','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahunz AND $sttz AND $buku2 AND $nominalz AND $jentran2 AND $progs AND $user_insertz AND $user_approvez  AND $backdates1")->where('via_input', '!=', null );
                $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw(" $aw as saldo, SUM(transaksi.jumlah) as debit, '0' as kredit, '0' as jumlah")->unionAll($alay)
                ->where(function($query) use ($p) {
                            if($p != ''){
                                $query->where('akun','LIKE','%'.$p.'%');
                            }
                        })
                ->whereRaw("$tahuns AND $stts AND jumlah > 0 AND $buku1 AND $nominals AND $jentran1 AND $prog AND $user_inserts AND $user_approves  AND $backdates")->where('via_input', '!=', null );
            }
        }
        
        $t = $transaksi->get();
        
        
        $data = [];
        $debit = $t[0]->debit == null ? 0 : $t[0]->debit;
        $jumlah = $t[0]->jumlah == null ? 0 : $t[0]->jumlah;
        $kredit = $t[1]->kredit == null ? 0 : $t[1]->kredit;
        $saldo = $t[0]->saldo == null ? 0 : $t[0]->saldo;
        
        $data = [
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldo ,
            'jumlah' => $saldo + $debit - $kredit,
        ];
                
        return $data;
    }
    
    function buku_harian_by(Request $request){
        
        if($request->via == 'pengeluaran'){
            $transaksi['d'] = DB::table('pengeluaran')->leftJoin('transaksi','transaksi.hapus_token','=','pengeluaran.hapus_token')->selectRaw("pengeluaran.id, pengeluaran.via_input, pengeluaran.pembayaran as via_bayar, pengeluaran.coa_debet as coa_debett, pengeluaran.saldo_dana, pengeluaran.tgl, pengeluaran.coa_debet,pengeluaran.coa_kredit, pengeluaran.jenis_transaksi, pengeluaran.keterangan, pengeluaran.nominal,  pengeluaran.no_resi, pengeluaran.bukti, pengeluaran.acc, pengeluaran.note, pengeluaran.user_input, pengeluaran.user_approve, 'pengeluaran' as via ,DATE_Format(pengeluaran.created_at,'%Y-%m-%d') as created_at,pengeluaran.kantor as id_kantor,pengeluaran.jenis_transaksi as namakun ")->whereRaw("pengeluaran.id = '$request->id'")->first();
        // $transaksi['d'] = DB::table('pengeluaran')->selectRaw("id, via_input, pembayaran as via_bayar, coa_debet as coa_debett, saldo_dana, pengeluaran.tgl, coa_kredit, jenis_transaksi, keterangan, nominal,  no_resi, bukti, acc, note, user_input, user_approve, 'pengeluaran' as via ,DATE_Format(created_at,'%Y-%m-%d') as created_at ,'' as id_kantor")->whereRaw("id = '$request->id'")->first();
        // $transaksi['p'] = COA::select('nama_coa')->where('coa', $transaksi['d']->coa_kredit)->first()->name;
        }else if($request->via == 'transaksi'){
            // $transaksi['d'] = DB::table('transaksi')->selectRaw("id, via_input, pembayaran as via_bayar, coa_kredit as coa_debett, '' as saldo_dana, transaksi.tanggal as tgl, coa_debet, akun as jenis_transaksi, transaksi.ket_penerimaan as keterangan, transaksi.jumlah as nominal, transaksi.id_transaksi, bukti, approval as acc, keterangan as note, user_insert as user_input, user_approve, 'transaksi' as via ,DATE_Format(created_at,'%Y-%m-%d') as created_at, akun as pengirim,transaksi.hapus_token,coa_kredit,id_kantor")->whereRaw("id = '$request->id'")->first();
            $transaksi['d'] = DB::table('transaksi')->leftJoin('pengeluaran','pengeluaran.hapus_token','=','transaksi.hapus_token')->selectRaw("transaksi.id, transaksi.via_input, transaksi.pembayaran as via_bayar, transaksi.coa_kredit as coa_debett, '' as saldo_dana, transaksi.tanggal as tgl, transaksi.coa_debet, transaksi.akun as jenis_transaksi, transaksi.ket_penerimaan as keterangan, transaksi.jumlah as nominal, transaksi.id_transaksi, transaksi.bukti, transaksi.approval as acc, transaksi.keterangan as note, transaksi.user_insert as user_input, transaksi.user_approve, 'transaksi' as via ,DATE_Format(transaksi.created_at,'%Y-%m-%d') as created_at, transaksi.akun as pengirim,transaksi.hapus_token,transaksi.coa_kredit,pengeluaran.kantor as id_kantor,akun,pengeluaran.jenis_transaksi as namakun")->whereRaw("transaksi.id = '$request->id'")->first();

        }
        
         if($request->via == 'pengeluaran'){
        $transaksi['p'] = COA::selectRaw("nama_coa,coa")->where('coa', $transaksi['d']->coa_kredit)->first();
        $transaksi['z'] = COA::selectRaw("nama_coa,coa")->where('coa', $transaksi['d']->coa_debet)->first();
        }else if($request->via == 'transaksi'){
        $transaksi['z'] = COA::selectRaw("nama_coa,coa")->where('coa', $transaksi['d']->coa_debet)->first();
        $transaksi['p'] = COA::selectRaw("nama_coa,coa")->where('coa', $transaksi['d']->coa_kredit)->first();
        }
        $transaksi['ui'] = User::select('name')->where('id', $transaksi['d']->user_input)->first()->name;
        $transaksi['ui'] = User::select('name')->where('id', $transaksi['d']->user_input)->first()->name;
        $transaksi['ua'] = User::select('name')->where('id', $transaksi['d']->user_approve)->first();
        $transaksi['sd'] = COA::where('grup', 'like', '%6%')->where('parent', 'like', '%n%')->orderBy('coa', 'ASC')->get();
        
        return $transaksi;
    }
    
    function buku_harian_hapus(Request $request){
        // return($request);
        if($request->via == 'pengeluaran'){
            $aw = Pengeluaran::find($request->id);
            $input['id'] = $aw->id;
            $input['jenis_transaksi'] = $aw->jenis_transaksi;
            $input['keterangan'] = $aw->keterangan;
            $input['qty'] = $aw->qty;
            $input['nominal'] = $aw->nominal;
            $input['user_input'] = $aw->user_input;
            $input['user_approve'] = $aw->user_approve;
            $input['referensi'] = $aw->referensi;
            $input['saldo_dana'] = $aw->saldo_dana;
            $input['program'] = $aw->program;
            $input['kantor'] = $aw->kantor;
            $input['coa_debet'] = $aw->coa_debet;
            $input['coa_kredit'] = $aw->coa_kredit;
            $input['tgl'] = $aw->tgl;
            $input['pembayaran'] = $aw->pembayaran;
            $input['bank'] = $aw->bank;
            $input['non_cash'] = $aw->non_cash;
            $input['department'] = $aw->department;
            $input['no_resi'] = $aw->no_resi;
            $input['acc'] = $aw->acc;
            $input['via_input'] = $aw->via_input;
            $input['bukti'] = $aw->bukti;
            $input['note'] = $aw->note;
            $input['notif'] = $aw->notif;
            $input['hapus_token'] = $aw->hapus_token;
            $input['hapus_alasan'] = $request->alasan;
            $input['user_delete'] = Auth::user()->id;
                
            $data = HapusPengeluaran::create($input);
            
            if($aw->via_input == 'mutasi'){
                $aw2 = Transaksi::where('hapus_token', $aw->hapus_token)->whereDate('tanggal', $aw->tgl)->first();
                    $input2['id'] = $aw2->id;
                    $input2['id_bank'] = $aw2->id_bank;
                    $input2['id_transaksi'] = $aw2->id_transaksi;
                    $input2['tanggal'] = $aw2->tanggal;
                    $input2['kolektor'] = $aw2->kolektor;
                    $input2['donatur'] = $aw2->donatur;
                    $input2['alamat'] = $aw2->alamat;
                    $input2['pembayaran'] = $aw2->pembayaran;
                    $input2['id_koleks'] = $aw2->id_koleks;
                    $input2['id_donatur'] = $aw2->id_donatur;
                    $input2['id_sumdan'] = $aw2->id_sumdan;
                    $input2['id_program'] = $aw2->id_program;
                    $input2['program'] = $aw2->program;
                    $input2['subprogram'] = $aw2->subprogram;
                    $input2['keterangan'] = $aw2->keterangan;
                    $input2['bukti'] = $aw2->bukti;
                    $input2['bukti2'] = $aw2->bukti2;
                    $input2['jumlah'] = $aw2->jumlah;
                    $input2['subtot'] = $aw2->subtot;
                    
                    $input2['status'] = $aw2->status;
                    $input2['kota'] = $aw2->kota;
                    $input2['id_kantor'] = $aw2->id_kantor;
                    $input2['kantor_induk'] = $aw2->kantor_induk;
                    
                    $input2['approval'] = $aw2->approval;
                    $input2['alasan'] = $aw2->alasan;
                    $input2['user_insert'] = $aw2->user_insert;
                    $input2['user_update'] = $aw2->user_update;
                    
                    $input2['user_approve'] = $aw2->user_approve;
                    $input2['id_pros'] = $aw2->id_pros;
                    $input2['via_input'] = $aw2->via_input;
                    $input2['akun'] = $aw2->akun;
                    
                    $input2['qty'] = $aw2->qty;
                    $input2['ket_penerimaan'] = $aw2->ket_penerimaan;
                    $input2['coa_debet'] = $aw2->coa_debet;
                    $input2['coa_kredit'] = $aw2->coa_kredit;
                    
                    $input2['id_camp'] = $aw2->id_camp;
                    $input2['hapus_token'] = $aw2->hapus_token;
                    $input2['notif'] = $aw2->notif;
                    
                    $input2['hapus_alasan'] = $request->alasan;
                    $input2['user_delete'] = Auth::user()->id;
                $data2 = HapusPengeluaran::create($input2);
                $aw2->delete();
            }
            
            $aw->delete();
            
        }else if($request->via == 'transaksi'){
            $aw = Transaksi::find($request->id);
            $input['id'] = $aw->id;
            $input['id_bank'] = $aw->id_bank;
            $input['id_transaksi'] = $aw->id_transaksi;
            $input['tanggal'] = $aw->tanggal;
            $input['kolektor'] = $aw->kolektor;
            $input['donatur'] = $aw->donatur;
            $input['alamat'] = $aw->alamat;
            $input['pembayaran'] = $aw->pembayaran;
            $input['id_koleks'] = $aw->id_koleks;
            $input['id_donatur'] = $aw->id_donatur;
            $input['id_sumdan'] = $aw->id_sumdan;
            $input['id_program'] = $aw->id_program;
            $input['program'] = $aw->program;
            $input['subprogram'] = $aw->subprogram;
            $input['keterangan'] = $aw->keterangan;
            $input['bukti'] = $aw->bukti;
            $input['bukti2'] = $aw->bukti2;
            $input['jumlah'] = $aw->jumlah;
            $input['subtot'] = $aw->subtot;
            
            $input['status'] = $aw->status;
            $input['kota'] = $aw->kota;
            $input['id_kantor'] = $aw->id_kantor;
            $input['kantor_induk'] = $aw->kantor_induk;
            
            $input['approval'] = $aw->approval;
            $input['alasan'] = $aw->alasan;
            $input['user_insert'] = $aw->user_insert;
            $input['user_update'] = $aw->user_update;
            
            $input['user_approve'] = $aw->user_approve;
            $input['id_pros'] = $aw->id_pros;
            $input['via_input'] = $aw->via_input;
            $input['akun'] = $aw->akun;
            
            $input['qty'] = $aw->qty;
            $input['ket_penerimaan'] = $aw->ket_penerimaan;
            $input['coa_debet'] = $aw->coa_debet;
            $input['coa_kredit'] = $aw->coa_kredit;
            
            $input['id_camp'] = $aw->id_camp;
            $input['hapus_token'] = $aw->hapus_token;
            $input['notif'] = $aw->notif;
            
            $input['hapus_alasan'] = $request->alasan;
            $input['user_delete'] = Auth::user()->id;
            
            $data = HapusTransaksi::create($input);
            
            if($aw->via_input == 'mutasi'){
                $aw2 = Pengeluaran::where('hapus_token', $aw->hapus_token)->whereDate('tgl', $aw->tanggal)->first();
                    $input2['id'] = $aw2->id;
                    $input2['jenis_transaksi'] = $aw2->jenis_transaksi;
                    $input2['keterangan'] = $aw2->keterangan;
                    $input2['qty'] = $aw2->qty;
                    $input2['nominal'] = $aw2->nominal;
                    $input2['user_input'] = $aw2->user_input;
                    $input2['user_approve'] = $aw2->user_approve;
                    $input2['referensi'] = $aw2->referensi;
                    $input2['saldo_dana'] = $aw2->saldo_dana;
                    $input2['program'] = $aw2->program;
                    $input2['kantor'] = $aw2->kantor;
                    $input2['coa_debet'] = $aw2->coa_debet;
                    $input2['coa_kredit'] = $aw2->coa_kredit;
                    $input2['tgl'] = $aw2->tgl;
                    $input2['pembayaran'] = $aw2->pembayaran;
                    $input2['bank'] = $aw2->bank;
                    $input2['non_cash'] = $aw2->non_cash;
                    $input2['department'] = $aw2->department;
                    $input2['no_resi'] = $aw2->no_resi;
                    $input2['acc'] = $aw2->acc;
                    $input2['via_input'] = $aw2->via_input;
                    $input2['bukti'] = $aw2->bukti;
                    $input2['note'] = $aw2->note;
                    $input2['notif'] = $aw2->notif;
                    $input2['hapus_token'] = $aw2->hapus_token;
                    $input2['hapus_alasan'] = $request->alasan;
                    $input2['user_delete'] = Auth::user()->id;
                $data2 = HapusPengeluaran::create($input2);
                $aw2->delete();
            }
            
            $aw->delete();
            
            // $cari = Transaksi::find($request->id);
            // $ahha = Transaksi::selectRaw("SUM(jumlah) as subtot")->whereRaw("id_transaksi = '$cari->id_transaksi' AND approval != '0'")->groupBy(id_transaksi)->get();
            
            // Transaksi::where('id', $request->id)->update(['subtot' => $ahha[0]->subtot]);
        }
        
        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data, dengan id'. $request->id);
        return response()->json(['success' => 'Data is successfully deleted']);
    }
    
    function buku_harian_acc(Request $request){
        if($request->via == 'pengeluaran'){
            if($request->acc == 1){
                $aw = Pengeluaran::where('id', $request->id)->update(['acc' => $request->acc]);
                $p = 'Menerima Pengeluaran';
            }else{
                $aw = Pengeluaran::where('id', $request->id)->update(['acc' => $request->acc, note => $request->alasan]);
                $p = 'Menolak Pengeluaran';
            }
        }else if($request->via == 'transaksi'){
            
            if($request->acc == 1){
                $aw = Transaksi::where('id', $request->id)->update(['approval' => $request->acc]);
                $p = 'Menerima Transaksi';
            }else{
                $aw = Transaksi::where('id', $request->id)->update(['approval' => $request->acc, alasan => $request->alasan]);
                $p = 'Menolak Transaksi';
            }
            
        }
        
        \LogActivity::addToLog(Auth::user()->name . ' '.$p.', dengan id'. $request->id);
        return response()->json(['success' => 'Data is successfully']);
    }
    
    public function buku_harian_edit(Request $request){
        // dd($request->via);
        if($request->via == 'pengeluaran'){
            $cari = Pengeluaran::find($request->id);
            
            if(!empty($request->edbukti)){
                if ($request->hasFile('edbukti')) {
                    $image = $request->file('edbukti');
                    if ($image->isValid()) {
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'bukti';
                        $image->move($upload_path, $image_name);
                        $input['bukti'] = $image_name;
                        if($cari->via_input == 'mutasi'){
                            $input2['bukti'] = $image_name;
                        }
                    }
                }
            }else{
                $input['bukti'] = $request->old_bukti;
                if($cari->via_input == 'mutasi'){
                    $input2['bukti'] = $request->old_buktie;
                }
            }
            $input['old_nominal'] = $cari->nominal == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari->nominal ;
            $input['nominal'] = preg_replace("/[^0-9]/", "", $request->nominal);
            $input['keterangan'] = $request->ket;
            $input['tgl'] = $request->edtgl;
            $input['pembayaran'] = $request->bayar_edit == '' ? $cari->pembayaran : $request->bayar_edit;
            
            if($cari->via_input == 'pengeluaran' && $cari->coa_debet != $request->jen_edit 
            // && Auth::user()->id == 6
            )
            {
                $coa = COA::where('coa', $request->jen_edit)->first();
                $coakc = COA::where('coa', $request->debet)->first();
                $getcoa =  $request->jen_edit == '' ? $coakc->nama_coa : $coa->nama_coa;
                $getcoadeb =  $request->jen_edit != '' ? $request->jen_edit : $coakc->coa;
                $input['jenis_transaksi'] = $getcoa;
                $input['coa_debet'] = $getcoadeb;
            }
            
            if($cari->via_input == 'mutasi'){
                $cari2 = Transaksi::where('hapus_token', $cari->hapus_token)->whereDate('tanggal', $cari->tgl)->first();
                $input2['old_jumlah'] = $cari2->jumlah == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari2->jumlah ;
                $input2['jumlah'] = preg_replace("/[^0-9]/", "", $request->nominal);
                $input2['keterangan'] = $request->ket;
                
                // $input2['coa_debet'] = $request->jen_edit;
                // $input2['ket_penerimaan'] = 'Mutasi Dari '.$request->jen_edit .' ke '.$request->nama_coa;
                
                $input2['tanggal'] = $request->edtgl;
                $cari2->update($input2);
            }
            
            $cari->update($input);
            
        }else if($request->via == 'transaksi'){
            $cari = Transaksi::find($request->id);
            
            if(!empty($request->edbukti)){
                if ($request->hasFile('edbukti')) {
                    $image = $request->file('edbukti');
                    if ($image->isValid()) {
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'gambarUpload';
                        $image->move($upload_path, $image_name);
                        $input['bukti'] = $image_name;
                        if($cari->via_input == 'mutasi'){
                            $input2['bukti'] = $image_name;
                        }
                    }
                }
            }else{
                $input['bukti'] = $request->old_bukti;
                if($cari->via_input == 'mutasi'){
                    $input2['bukti'] = $request->old_buktie;
                }
            }
            $input['old_jumlah'] = $cari->jumlah == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari->jumlah ;
            $input['jumlah'] = preg_replace("/[^0-9]/", "", $request->nominal);
            $input['keterangan'] = $request->ket;
            $input['tanggal'] = $request->edtgl;
            
            if($cari->via_input == 'mutasi'){
                $cari2 = Pengeluaran::where('hapus_token', $cari->hapus_token)->whereDate('tgl', $cari->tanggal)->first();
                $input2['old_nominal'] = $cari2->nominal == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari2->nominal ;
                $input2['nominal'] = preg_replace("/[^0-9]/", "", $request->nominal);
                $input2['keterangan'] = $request->ket;
                $input2['tgl'] = $request->edtgl;
                $input2['pembayaran'] = $request->bayar_edit;
                $cari2->update($input2);
            }
            
            if($cari->via_input == 'transaksi'){
                if($request->edtgl != $cari->tanggal){
                    $id_trans = $cari->id_donatur.date('dmY', strtotime($request->edtgl)).$cari->id_koleks;
                    $input['id_transaksi'] = $id_trans;
                }
                $ahha = Transaksi::selectRaw("id_transaksi, SUM(jumlah) as subtot")->whereRaw("id_transaksi = '$cari->id_transaksi' AND approval != '0'")->groupBy('id_transaksi')->get();
                // dd($cari);
                Transaksi::where('id_transaksi', $cari->id_transaksi)->update(['subtot' => $ahha[0]->subtot]);
            }
            $cari->update($input);
        }
        
        
            
        // }else if($request->via == 'transaksi'){
        //     $cari = Pengeluaran::find($request->id);
        //         Transaksi::where('id', $request->id)->update([
        //             'keterangan' => $request->ket,
        //             'jumlah' => $request->nominal,
        //             'old_jumlah' => $cari->jumlah
        //         ]);
            
        //     $cari = Transaksi::find($request->id);
        //     $ahha = Transaksi::selectRaw("SUM(jumlah) as subtot")->whereRaw("id_transaksi = '$cari->id_transaksi' AND approval != '0'")->groupBy(id_transaksi)->get();
            
        //     Transaksi::where('id_transaksi', $cari->id_transaksi)->update(['subtot' => $ahha[0]->subtot]);
        // }
        
        \LogActivity::addToLog(Auth::user()->name . ' Edit Data, dengan id'. $request->id);
        return response()->json(['success' => 'Data is successfully']);
    }
    
    
     public function buku_harian_edit_hfm(Request $request){

       

    //     $keyMapping = [
    //         'jen_edit' => 'coa_debet',
    //         'peng_edit'=> 'coa_kredit',
    //         'ket' => 'keterangan',
    //         'nominal' => 'nominal',
    //         'edtgl' => 'tgl',
    //         'edbukti'=> 'bukti',
    //     ];
    //     $perbedaan = [];
    //     foreach ($keyMapping as $kunciRequest => $kunciCari) {
    //         $nilaiRequest = $request->all()[$kunciRequest];
    //         $nilaiCari = $cari[$kunciCari];
        
    //         if ($nilaiRequest !== $nilaiCari && $nilaiRequest !== null) {
    //             $perbedaan[$kunciRequest] = [
    //                 'lama' => $nilaiCari,
    //                 'baru' => $nilaiRequest,
    //             ];
    //         }
    //     }
    // $perbedaan = array_filter($perbedaan);
    // $perbedaanString = '';
    //     foreach ($perbedaan as $kunci => $nilai) {
    //         $perbedaanString .= "$kunci: Lama = {$nilai['lama']}, Baru = {$nilai['baru']}\n";
    //     }

    // $perbedaanString = rtrim($perbedaanString);
   
    //   \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data, dengan id'. $request->id,$perbedaanString,$request->via);



        if($request->via == 'pengeluaran'){
            $cari = Pengeluaran::find($request->id);
            if(!empty($request->edbukti)){
                if ($request->hasFile('edbukti')) {
                    $image = $request->file('edbukti');
                    if ($image->isValid()) {
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'bukti';
                        $image->move($upload_path, $image_name);
                        $input['bukti'] = $image_name;
                        if($cari->via_input == 'mutasi'){
                            $input2['bukti'] = $image_name;
                        }
                    }
                }
            }else{
                $input['bukti'] = $request->old_bukti;
                if($cari->via_input == 'mutasi'){
                    $input2['bukti'] = $request->old_buktie;
                }
            }
            
            $coa = COA::where('coa', $request->jen_edit)->first();
            $coajens = COA::where('coa', $request->peng_edit)->first();
            
            $coakc = COA::where('coa', $request->debet)->first();
            $getcoa = $coa->nama_coa;
            $getcoanam = $coajens->nama_coa;
            
            $getcoadeb =  $request->jen_edit ;
            $getcoakred =  $request->peng_edit; 
                
            $input['old_nominal'] = $cari->nominal == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari->nominal ;
            $input['nominal'] = preg_replace("/[^0-9]/", "", $request->nominal);
            $input['keterangan'] = $request->ket;
            $input['tgl'] = $request->edtgl;
            $input['pembayaran'] = $request->bayar_edit == '' ? $cari->pembayaran : $request->bayar_edit;
            $input['jenis_transaksi'] = $getcoa;
            $input['coa_debet'] = $getcoadeb;
            $input['coa_kredit'] = $getcoakred;
            $input['keterangan'] = $request->ket ;
            if($cari->via_input == 'pengeluaran' && $cari->coa_debet != $request->jen_edit )
            {
                
                // $coa = COA::where('coa', $request->jen_edit)->first();
                // $coakc = COA::where('coa', $request->debet)->first();
                // $getcoa =  $request->jen_edit == '' ? $coakc->nama_coa : $coa->nama_coa;
                // $getcoadeb =  $request->jen_edit != '' ? $request->jen_edit : $coakc->coa;
                // $input['jenis_transaksi'] = $getcoa;
                // $input['coa_debet'] = $getcoadeb;
                
                $coa = COA::where('coa', $request->jen_edit)->first();
                $coakc = COA::where('coa', $request->debet)->first();
                $getcoa = $coa->nama_coa;
                $coajens = COA::where('coa', $request->peng_edit)->first();
                $getcoanam = $coajens->nama_coa;
                
                $getcoadeb =  $request->jen_edit ;
                $getcoakred =  $request->peng_edit ;
                $input['jenis_transaksi'] = $getcoa;
                $input['coa_debet'] = $getcoadeb;
                $input['coa_kredit'] = $getcoakred;
                $input['keterangan'] = $request->ket ;
            }
            
            if($cari->via_input == 'mutasi'){
                $coa = COA::where('coa', $request->peng_edit)->first();
                $cari2 = Transaksi::where('hapus_token', $cari->hapus_token)->whereDate('tanggal', $cari->tgl)->first();
                $input2['old_jumlah'] = $cari2->jumlah == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari2->jumlah ;
                $input2['jumlah'] = preg_replace("/[^0-9]/", "", $request->nominal);
                $input2['ket_penerimaan'] = $request->ket;
                $input2['coa_debet'] = $request->jen_edit;
                $input2['coa_kredit'] = $request->peng_edit;
                $input2['akun'] = $coa->nama_coa;
                // $input2['coa_debet'] = $request->jen_edit;
                // $input2['ket_penerimaan'] = 'Mutasi Dari '.$request->jen_edit .' ke '.$request->nama_coa;
                
                $input2['tanggal'] = $request->edtgl;
                $cari2->update($input2);
            }
            
           
            
            $keyMapping = [
                    'jen_edit' => 'coa_debet',
                    'peng_edit'=> 'coa_kredit',
                    'ket' => 'keterangan',
                    'nominal' => 'nominal',
                    'edtgl' => 'tgl',
                    'edbukti'=> 'bukti',
                ];
                $perbedaan = [];
                foreach ($keyMapping as $kunciRequest => $kunciCari) {
                    $nilaiRequest = $request->all()[$kunciRequest];
                    $nilaiCari = $cari[$kunciCari];
                
                    if ($nilaiRequest !== $nilaiCari && $nilaiRequest !== null) {
                        $perbedaan[$kunciRequest] = [
                            'lama' => $nilaiCari,
                            'baru' => $nilaiRequest,
                        ];
                    }
                }
            $perbedaan = array_filter($perbedaan);
            $perbedaanString = '';
                foreach ($perbedaan as $kunci => $nilai) {
                    $perbedaanString .= "$kunci: Lama = {$nilai['lama']}, Baru = {$nilai['baru']}\n";
                }
        
            $perbedaanString = rtrim($perbedaanString);
    
                \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Buku Harian , dengan id'. $request->id,$perbedaanString,$request->via,'update',$request->id);
        $cari->update($input);

        }else if($request->via == 'transaksi'){
            $cari = Transaksi::find($request->id);
            
            if(!empty($request->edbukti)){
                if ($request->hasFile('edbukti')) {
                    $image = $request->file('edbukti');
                    if ($image->isValid()) {
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'gambarUpload';
                        $image->move($upload_path, $image_name);
                        $input['bukti'] = $image_name;
                        if($cari->via_input == 'mutasi'){
                            $input2['bukti'] = $image_name;
                        }
                    }
                }
            }else{
                $input['bukti'] = $request->old_bukti;
                if($cari->via_input == 'mutasi'){
                    $input2['bukti'] = $request->old_buktie;
                }
            }
            $input['old_jumlah'] = $cari->jumlah == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari->jumlah ;
            $input['jumlah'] = preg_replace("/[^0-9]/", "", $request->nominal);
            $input['tanggal'] = $request->edtgl;
            // $input['keterangan'] = $request->ket;
            $coa = COA::where('coa', $request->peng_edit)->first();
            $coakc = COA::where('coa', $request->debet)->first();
            $getcoa =  $request->jen_edit == '' ? $coakc->nama_coa : $coa->nama_coa;
            $getcoadeb =  $request->jen_edit;
            $getcoakred =  $request->peng_edit ;
            $input['akun'] = $getcoa;
            $input['coa_debet'] = $getcoadeb;
            $input['coa_kredit'] = $getcoakred;
            $input['ket_penerimaan'] = $request->ket ;
            
            
            if($cari->via_input == 'mutasi'){
                $cari2 = Pengeluaran::where('hapus_token', $cari->hapus_token)->whereDate('tgl', $cari->tanggal)->first();
                $getcoadeb =  $request->jen_edit != '' ? $request->jen_edit : $coakc->coa;
                $getcoakred =  $request->peng_edit;
                $input2['old_nominal'] = $cari2->nominal == preg_replace("/[^0-9]/", "", $request->nominal) ? 0 : $cari2->nominal ;
                $input2['nominal'] = preg_replace("/[^0-9]/", "", $request->nominal);
                $input2['tgl'] = $request->edtgl;
                $coa = COA::where('coa', $request->jen_edit)->first();
                $getcoa = $coa->nama_coa;
                $input2['pembayaran'] = $request->bayar_edit == '' ? $cari2->pembayaran : $request->bayar_edit ;
                $input2['jenis_transaksi'] = $getcoa;
                $input2['coa_debet'] = $getcoadeb;
                $input2['coa_kredit'] = $getcoakred;
                $input2['keterangan'] =  $request->ket;
                $cari2->update($input2);
            }
            
            if($cari->via_input == 'transaksi'){
                if($request->edtgl != $cari->tanggal){
                    $id_trans = $cari->id_donatur.date('dmY', strtotime($request->edtgl)).$cari->id_koleks;
                    $input['id_transaksi'] = $id_trans;
                }
                $ahha = Transaksi::selectRaw("id_transaksi, SUM(jumlah) as subtot")->whereRaw("id_transaksi = '$cari->id_transaksi' AND approval != '0'")->groupBy('id_transaksi')->get();
                // dd($cari);
                Transaksi::where('id_transaksi', $cari->id_transaksi)->update(['subtot' => $ahha[0]->subtot]);
            }
            
  if($cari->via_input == 'mutasi'){
      
                  $keyMapping = [
                        'jen_edit' => 'coa_debet',
                        'peng_edit'=> 'coa_kredit',
                        'ket' => 'ket_penerimaan',
                        'nominal' => 'jumlah',
                        'edtgl' => 'tanggal',
                        'edbukti'=> 'bukti',
                    ];
          
  }else{
          $keyMapping = [
                        'jen_edit' => 'coa_debet',
                        'peng_edit'=> 'coa_kredit',
                        'ket' => 'keterangan',
                        'nominal' => 'jumlah',
                        'edtgl' => 'tanggal',
                        'edbukti'=> 'bukti',
                    ];
  }
                 
                    $perbedaan = [];
                    foreach ($keyMapping as $kunciRequest => $kunciCari) {
                        $nilaiRequest = $request->all()[$kunciRequest];
                        $nilaiCari = $cari[$kunciCari];
                    
                        if ($nilaiRequest !== $nilaiCari && $nilaiRequest != null && $nilaiRequest != '') {
                            $perbedaan[$kunciRequest] = [
                                'lama' => $nilaiCari,
                                'baru' => $nilaiRequest,
                            ];
                        }
                    }
                $perbedaan = array_filter($perbedaan);
                $perbedaanString = '';
                    foreach ($perbedaan as $kunci => $nilai) {
                        $perbedaanString .= "$kunci: Lama = {$nilai['lama']}, Baru = {$nilai['baru']}\n";
                    }
            
                $perbedaanStrings = rtrim($perbedaanString);
                
                \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Buku Harian , dengan id'. $request->id,$perbedaanStrings,$request->via,'update',$request->id);
            $cari->update($input);


        }
        
        return response()->json(['success' => 'Data is successfully']);
    }
    
    
    public function bukubesar(Request $request){
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com',Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
            $duar = Auth::user()->id_kantor;
            if($k == null){
                $kantor = Kantor::where('id', Auth::user()->id_kantor)->where('id_com',Auth::user()->id_com)->get();
            }else{
                $kantor = Kantor::whereRaw("(id = '$duar' OR id = '$k->id')")->where('id_com',Auth::user()->id_com)->get();
            }
        }
        $coa = COA::all();
        
        if ($request->ajax()) {
            
            if ($request->daterange != '') {
                $tgl = explode(' s/d ', $request->daterange);
                // return($tgl);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }else{
                $hari_ini = date('Y-m-d');
                $dari = date('Y-m-d', strtotime($hari_ini));
                $sampai = date('Y-m-d', strtotime($hari_ini));
            }
            
            if ($request->month != '') {
                $tgl = explode('-', $request->month);
                $t = date($tgl[0]);
                $b = date($tgl[1]);
            }else if($request->years != '')
            {
                $t = date($request->years);
                $b =  date('m');
            }
            else
                {
                $t = date('Y');
                $b = date('m');
            }
            
            $thns = date('Y');
            $coabuk = $request->buku == '' ? "coa IS NOT NULL" :  "coa = '$request->buku'";
            
            $kntr1 = $request->kota == '' ? "transaksi.id_kantor IS NOT NULL"       : "transaksi.id_kantor = $request->kota";
            $kntr2 = $request->kota == '' ? "kantor IS NOT NULL"                    : "kantor = $request->kota";
            
            $buku1 = $request->buku == '' ? "transaksi.coa_debet IS NOT NULL"       : "transaksi.coa_debet = '$request->buku' ";
            $buku12 = $request->buku == '' ? "transaksi.coa_kredit IS NOT NULL"       : "transaksi.coa_kredit = '$request->buku' ";
            $buku2 = $request->buku == '' ? "coa_kredit IS NOT NULL"                :  "coa_kredit = '$request->buku'";
            $buku22 = $request->buku == '' ? "coa_debet IS NOT NULL"                :  "coa_debet = '$request->buku'";
            $buku3 = $request->buku == '' ? "(coa_kredit IS NOT NULL AND coa_debet IS NOT NULL)" :  "(coa_kredit = '$request->buku' AND coa_debet = '$request->buku')";
            $buku111 = $request->buku == '' || null ? "prog.coa2 IS NOT NULL" : "prog.coa2 = '$request->buku'";
            $buku1111 = $request->buku == '' || null ? "prog.coa1 IS NOT NULL" : "prog.coa1 = '$request->buku'";
        
            $jen_tran1 = $request->jen_tran == '' ? "(coa.nama_coa IS NOT NULL AND transaksi.akun IS NOT NULL)" :  "(coa.nama_coa = '$request->jen_tran' AND transaksi.akun ='$request->jen_tran')";
            $jen_tran2 = $request->jen_tran == '' ? "(pengeluaran.jenis_transaksi IS NOT NULL AND coa.nama_coa IS NOT NULL)" : "(pengeluaran.jenis_transaksi = '$request->jen_tran' AND coa.nama_coa = '$request->jen_tran' )";
            $jen_tran3 = $request->jen_tran == '' ? "jurnal.jenis_transaksi IS NOT NULL" :  "jurnal.jenis_transaksi = '$request->jen_tran' ";
            $jenis3 = $request->jen == '0' ? "nominal_debit != 0"       : ($request->jen == '1' ? "nominal_kredit != 0": "jurnal.nominal_debit  > 0 OR jurnal.nominal_kredit > 0");
            $jenis2 = $request->jen == '0' ? "nominal = 0"       : ($request->jen == '1' ? "nominal != 0":"nominal > 0");
            $jenis1 = $request->jen == '0' ? "transaksi.jumlah != 0"       : ($request->jen == '1' ? "transaksi.jumlah = 0": "transaksi.jumlah > 0");
            
            $jenis22 = $request->jen == '0' ? " nominal != 0"       : ($request->jen == '1' ? "nominal = 0":"pengeluaran.nominal > 0");
            $jenis11 = $request->jen == '0' ? "transaksi.jumlah = 0"       : ($request->jen == '1' ? "transaksi.jumlah > 0 ": "transaksi.jumlah > 0");
            
            
            
            if( $request->via_jurnal == '0' ){
                $via_jurnal1 =  "pengeluaran.via_input ='transaksi'";
                $via_jurnal =  "transaksi.via_input ='transaksi'";
            }else if($request->via_jurnal == '1'){
                $via_jurnal1 = "pengeluaran.via_input ='pengeluaran' AND pengeluaran.via_input ='penyaluran'";
                $via_jurnal = "transaksi.via_input = 'penerimaan'";
            }else if($request->via_jurnal == '2'){
                $via_jurnal1 = "pengeluaran.via_input = 'mutasi'";
                $via_jurnal = "transaksi.via_input = 'mutasi'";
            }else if($request->via_jurnal == '3'){
                $via_jurnal1 = "pengeluaran.via_input = 'hha'";
                $via_jurnal = "transaksi.via_input = 'hha'";
            }else{
                $via_jurnal1 = "pengeluaran.via_input IS NOT NULL";
                $via_jurnal = "transaksi.via_input IS NOT NULL";
            }
            
            if($request->prd == '0'){
                $prd1 = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
                $prd2 = "DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai'";
                $prd3 = "DATE(jurnal.tanggal) >= '$dari' AND DATE(jurnal.tanggal) <= '$sampai'";
            }else if($request->prd == '1') {
                $prd1 = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
                $prd2 = "MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'";
                $prd3 = "MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'";
            }else if($request->prd == '2') {
                $prd1 = $request->years == "" ? "YEAR(transaksi.tanggal) = '$thns'"    : "YEAR(transaksi.tanggal) = '$request->years'";
                $prd2 = $request->years == "" ? "YEAR(tgl) = '$thns'"                  : "YEAR(tgl) = '$request->years'";
                $prd3 = $request->years == "" ? "YEAR(tanggal) = '$thns'"              : "YEAR(tanggal) = '$request->years'";
            }
            
            $rj_grup = $request->groupby;
            
            if($rj_grup == ''){
                $grupBy = 'tgjur, coa, nama_coa, ket_penerimaan, debit, kredit, id_transaksi, via_input, crt, ids, urut';
            }else{
                $grupBy = 'tgjur, coa, nama_coa';
            }
            
            
     
            $sel_transaksideb = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, coa_debet as coa, coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, transaksi.jumlah as debit, '0' as kredit, transaksi.id_transaksi as id_transaksi, via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 1 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, transaksi.coa_debet as coa, coa.nama_coa as nama_coa, SUM(transaksi.jumlah) as debit, '0' as kredit";
            $sel_transaksikre = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, '0' debit, transaksi.jumlah as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 2 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, '0' debit, SUM(transaksi.jumlah) as kredit";
            $sel_jurnal = $rj_grup == ''
                ? "tanggal as tgjur, coa_use as coa, coa.nama_coa as nama_coa, keterangan as ket_penerimaan, nominal_debit as debit, nominal_kredit as kredit, no_resi as id_transaksi, via_input as via_input, jurnal.created_at AS crt, jurnal.id_jurnal AS ids, 7 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(jurnal.tanggal, '%Y'), DATE_FORMAT(jurnal.tanggal, '%Y-%m')), jurnal.tanggal) as tgjur, coa_use as coa, coa.nama_coa as nama_coa, SUM(nominal_debit) as debit, SUM(nominal_kredit) as kredit";
            $sel_pengeluarandeb = $rj_grup == '' 
                ? "pengeluaran.tgl as tgjur, coa_debet as coa,  coa.nama_coa as nama_coa, keterangan as ket_penerimaan, nominal as debit, '0' as kredit, no_resi as id_transaksi, pengeluaran.via_input as via_input, pengeluaran.created_at AS crt, pengeluaran.id AS ids, 5 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(pengeluaran.tgl, '%Y'), DATE_FORMAT(pengeluaran.tgl, '%Y-%m')), pengeluaran.tgl) as tgjur, coa_debet as coa,  coa.nama_coa as nama_coa, SUM(nominal) as debit,  '0' as kredit";
            $sel_pengeluarankre = $rj_grup == '' 
                ? "pengeluaran.tgl as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, keterangan as ket_penerimaan, '0' as debit,  nominal as kredit, no_resi as id_transaksi, pengeluaran.via_input as via_input, pengeluaran.created_at AS crt, pengeluaran.id AS ids, 6 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(pengeluaran.tgl, '%Y'), DATE_FORMAT(pengeluaran.tgl, '%Y-%m')), pengeluaran.tgl) as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, '0' as debit, SUM(nominal) as kredit";
            $sel_dp = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, prog.coa1 as coa,  coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, '0' debit, (transaksi.dp/100)*transaksi.jumlah  as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 4 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, prog.coa1 as coa, coa.nama_coa as nama_coa, '0' as debit, SUM((transaksi.dp/100)*transaksi.jumlah) as kredit";
            $sel_penyaluran = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, prog.coa2 as coa,  coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, (transaksi.dp/100)*transaksi.jumlah as debit, '0' as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 3 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, prog.coa2 as coa, coa.nama_coa as nama_coa, SUM((transaksi.dp/100)*transaksi.jumlah) as debit, '0' as kredit";
     
            
            $stat_tran  = "transaksi.approval = 1";
            $stat_peng  = "pengeluaran.acc = 1";

            $pisan = DB::table('jurnal')
                ->join('coa','coa.coa','=','jurnal.coa_debet')
                ->selectRaw($sel_jurnal)
                ->whereRaw("$prd3 AND $kntr2 AND $buku3 AND $jenis3 AND $jen_tran3")
                ->groupByRaw($grupBy)
                ;
            $alay = DB::table('pengeluaran')
                ->join('coa','coa.coa','=','pengeluaran.coa_kredit')
                ->selectRaw($sel_pengeluarankre)
                ->whereRaw("$jen_tran2 AND $prd2 AND $kntr2 AND $buku2 AND $jenis2 AND $via_jurnal1 AND $stat_peng")
                ->groupByRaw($grupBy)
                ;
            $alay1 = DB::table('pengeluaran')
                ->join('coa','coa.coa','=','pengeluaran.coa_debet')
                ->selectRaw($sel_pengeluarandeb)
                ->whereRaw("$prd2 AND $kntr2 AND $buku22 AND $jenis22  AND $via_jurnal1 AND $jen_tran2 AND $stat_peng")
                ->groupByRaw($grupBy)
                ;
               
            $transaksi1 = DB::table('transaksi')
                ->leftjoin('coa','coa.coa','=','transaksi.coa_kredit')
                ->selectRaw($sel_transaksikre)
                ->whereRaw("$prd1 AND $kntr1 AND $buku12 AND jumlah > 0 AND  $jenis11 AND $via_jurnal AND $jen_tran1 AND transaksi.via_input != 'mutasi' AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
            
             $dp = DB::table('transaksi')
                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                ->leftJoin('coa','coa.coa','=','prog.coa1')
                ->selectRaw($sel_dp)
                ->whereRaw("$prd1 AND $kntr1 AND $buku1111 AND jumlah > 0 AND  $jenis11 AND $via_jurnal AND transaksi.via_input = 'transaksi' AND transaksi.pembayaran != 'noncash' AND  $jen_tran1 AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
                    
             $penyaluran = DB::table('transaksi')
                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                ->leftJoin('coa','coa.coa','=','prog.coa2')
                ->selectRaw($sel_penyaluran)
                ->whereRaw("$prd1 AND $kntr1 AND $buku111 AND jumlah > 0 AND  $jenis1 AND $via_jurnal AND transaksi.via_input = 'transaksi' AND transaksi.pembayaran != 'noncash' AND $jen_tran1 AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
                    
            $transaksi = DB::table('transaksi')
                ->leftjoin('coa','coa.coa','=','transaksi.coa_debet')
                ->selectRaw($sel_transaksideb)
                ->unionAll($transaksi1)
                ->unionAll($penyaluran)
                ->unionAll($dp)
                ->unionAll($alay)
                ->unionAll($alay1)
                ->unionAll($pisan)
                ->whereRaw("$prd1 AND jumlah > 0 AND $kntr1 AND $buku1 AND $jenis1 AND $via_jurnal AND $jen_tran1 AND transaksi.via_input != 'mutasi' AND $stat_tran")
                ->groupByRaw($grupBy)
                ; 
    
            $or_jur = $rj_grup == '' ? 'crt DESC, ids ASC, urut ASC' : 'tgjur DESC';
            $iya = $transaksi->orderByRaw($or_jur)->get();
            $k = [];
            $output = [];
            
            if($request->tab == 'tab1'){
                $data = [];
                $debit = 0;
                $kredit = 0;
                $jumlah= 0;
                foreach($iya as $o){
                    $debit += $o->debit;
                    $kredit += $o->kredit;
                }
                
                $jumlah = $debit - $kredit;
                
                $data = [
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'jumlah' => $jumlah
                ];
                
                return $data;
            }else{
                $jumlah = 0;
                for($i= 0; $i < count($iya); $i++){
                    $jumlah += $iya[$i]->debit - $iya[$i]->kredit;
                    if(isset($iya[$i]->via_input)){
                        if($iya[$i]->via_input == 'transaksi'){
                            $output = 'Otomatis';
                        } else if($iya[$i]->via_input == 'pengeluaran' || $iya[$i]->via_input == 'penyaluran' || $iya[$i]->via_input == 'penerimaan' ){
                            $output = 'Oprasional';
                        } else if($iya[$i]->via_input == 'mutasi'){
                            $output = 'Mutasi';
                        }else if($iya[$i]->via_input == null ){
                            $output = 'Kosong';
                        }else if($iya[$i]->via_input == 'penyesuaian'){
                            $output = 'Penyesuaian';
                        }
                    }
                        $k[] = [
                            'crt' => $iya[$i]->crt ?? $iya[$i]->tgjur,
                            'ids' => $iya[$i]->ids ?? $iya[$i]->tgjur,
                            'urut' => $iya[$i]->urut ?? $iya[$i]->tgjur,
                            'jumlahs' => $jumlah,
                            'tanggal' => $iya[$i]->tgjur,
                            'nama_coa' => $iya[$i]->nama_coa,
                            'debit' => $iya[$i]->debit,
                            'via_jurnal' => $output ?? null,
                            'coa_debet' => $iya[$i]->coa,
                            'kredit' => $iya[$i]->kredit,
                            'id_transaksi' => $iya[$i]->id_transaksi ?? null,
                            'ket_penerimaan' => $iya[$i]->ket_penerimaan ?? null,
                        ];
                        
                }
                
                return DataTables::of($k)
                ->make(true);
            }
        }
      
        
        return view('akuntasi.buku_besar', compact('kantor','coa'));
    }
    public function bukubesarexport(Request $request){
        if ($request->month != '') {
            $tgl = explode('-', $request->month);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else if($request->years != '')
        {
            $t = date($request->years);
            $b =  date('m');
        }
        else
            {
            $t = date('Y');
            $b = date('m');
        }
        $prd = $request->prd;
     
        if ($request->daterange != '') {
            $tgl = explode(' s/d ', $request->daterange);
            // return($tgl);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        
        if($prd == '0'){
            $namaFileXLSX = 'Buku Besar Periode '. $dari .' Sampai '. $sampai .'.xlsx';
            $namaFileCSV = 'Buku Besar Periode '. $dari .' Sampai '. $sampai .'.csv';
        }
        elseif($prd == '1'){
            $namaFileXLSX = 'Buku Besar Bulan ' . $b.'-'.$t . '.xlsx';
            $namaFileCSV = 'Buku Besar Bulan '. $b.'-'.$t .'.csv';
        }
        elseif($prd == '2'){
            $namaFileXLSX = 'Buku Besar Tahun '. $t .'.xlsx';
            $namaFileCSV = 'Buku Besar Tahun '. $t .'.csv';
        }
        
        if($request->tombol == 'xls'){
            $r = Excel::download(new BukuBesarExport($request), $namaFileXLSX);
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new BukuBesarExport($request), $namaFileCSV);
            ob_end_clean();
            return $r;
        }
        
        
    }
    public function exportJurnal(Request $request){
      
        if ($request->month != '') {
            $tgl = explode('-', $request->month);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else if($request->years != '')
        {
            $t = date($request->years);
            $b =  date('m');
        }
        else
            {
            $t = date('Y');
            $b = date('m');
        }
        $prd = $request->prd;
     
        if ($request->daterange != '') {
            $tgl = explode(' s/d ', $request->daterange);
            // return($tgl);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        
        if($prd == '0'){
            $namaFileXLSX = 'Jurnal Periode '. $dari .' Sampai '. $sampai .'.xlsx';
            $namaFileCSV = 'Jurnal Periode '. $dari .' Sampai '. $sampai .'.csv';
        }
        elseif($prd == '1'){
            $namaFileXLSX = 'Jurnal Bulan ' . $b.'-'.$t . '.xlsx';
            $namaFileCSV = 'Jurnal Bulan '. $b.'-'.$t .'.csv';
        }
        elseif($prd == '2'){
            $namaFileXLSX = 'Jurnal Tahun '. $t .'.xlsx';
            $namaFileCSV = 'Jurnal Tahun '. $t .'.csv';
        }
       
        
        if($request->tombol == 'xls'){
            $r = Excel::download(new JurnalExport($request), $namaFileXLSX);
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new JurnalExport($request), $namaFileCSV);
            ob_end_clean();
            return $r;
        }
    }
    
    
     function rekapjurnal(Request $request){
      
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com',Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
            $duar = Auth::user()->id_kantor;
            if($k == null){
                $kantor = Kantor::where('id', Auth::user()->id_kantor)->where('id_com',Auth::user()->id_com)->get();
            }else{
                $kantor = Kantor::whereRaw("(id = '$duar' OR id = '$k->id')")->where('id_com',Auth::user()->id_com)->get();
            }
        }
        $coa = COA::all();
        
        if ($request->ajax()) {
            
            if ($request->daterange != '') {
                $tgl = explode(' s/d ', $request->daterange);
                // return($tgl);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }else{
                $hari_ini = date('Y-m-d');
                $dari = date('Y-m-d', strtotime($hari_ini));
                $sampai = date('Y-m-d', strtotime($hari_ini));
            }
            
            if ($request->month != '') {
                $tgl = explode('-', $request->month);
                $t = date($tgl[0]);
                $b = date($tgl[1]);
            }else if($request->years != '')
            {
                $t = date($request->years);
                $b =  date('m');
            }
            else
                {
                $t = date('Y');
                $b = date('m');
            }
            
            $thns = date('Y');
            $coabuk = $request->buku == '' ? "coa IS NOT NULL" :  "coa = '$request->buku'";
            
            $kntr1 = $request->kota == '' ? "transaksi.id_kantor IS NOT NULL"       : "transaksi.id_kantor = $request->kota";
            $kntr2 = $request->kota == '' ? "kantor IS NOT NULL"                    : "kantor = $request->kota";
            
            $buku1 = $request->buku == '' ? "transaksi.coa_debet IS NOT NULL"       : "transaksi.coa_debet = '$request->buku' ";
            $buku12 = $request->buku == '' ? "transaksi.coa_kredit IS NOT NULL"       : "transaksi.coa_kredit = '$request->buku' ";
            $buku2 = $request->buku == '' ? "coa_kredit IS NOT NULL"                :  "coa_kredit = '$request->buku'";
            $buku22 = $request->buku == '' ? "coa_debet IS NOT NULL"                :  "coa_debet = '$request->buku'";
            $buku3 = $request->buku == '' ? "(coa_kredit IS NOT NULL AND coa_debet IS NOT NULL)" :  "(coa_kredit = '$request->buku' AND coa_debet = '$request->buku')";
            $buku111 = $request->buku == '' || null ? "prog.coa2 IS NOT NULL" : "prog.coa2 = '$request->buku'";
            $buku1111 = $request->buku == '' || null ? "prog.coa1 IS NOT NULL" : "prog.coa1 = '$request->buku'";
        
            $jen_tran1 = $request->jen_tran == '' ? "(coa.nama_coa IS NOT NULL AND transaksi.akun IS NOT NULL)" :  "(coa.nama_coa = '$request->jen_tran' AND transaksi.akun ='$request->jen_tran')";
            $jen_tran2 = $request->jen_tran == '' ? "(pengeluaran.jenis_transaksi IS NOT NULL AND coa.nama_coa IS NOT NULL)" : "(pengeluaran.jenis_transaksi = '$request->jen_tran' AND coa.nama_coa = '$request->jen_tran' )";
            $jen_tran3 = $request->jen_tran == '' ? "jurnal.jenis_transaksi IS NOT NULL" :  "jurnal.jenis_transaksi = '$request->jen_tran' ";
            $jenis3 = $request->jen == '0' ? "nominal_debit != 0"       : ($request->jen == '1' ? "nominal_kredit != 0": "jurnal.nominal_debit  > 0 OR jurnal.nominal_kredit > 0");
            $jenis2 = $request->jen == '0' ? "nominal = 0"       : ($request->jen == '1' ? "nominal != 0":"nominal > 0");
            $jenis1 = $request->jen == '0' ? "transaksi.jumlah != 0"       : ($request->jen == '1' ? "transaksi.jumlah = 0": "transaksi.jumlah > 0");
            
            $jenis22 = $request->jen == '0' ? " nominal != 0"       : ($request->jen == '1' ? "nominal = 0":"pengeluaran.nominal > 0");
            $jenis11 = $request->jen == '0' ? "transaksi.jumlah = 0"       : ($request->jen == '1' ? "transaksi.jumlah > 0 ": "transaksi.jumlah > 0");
            
            
            
            if( $request->via_jurnal == '0' ){
                $via_jurnal1 =  "pengeluaran.via_input ='transaksi'";
                $via_jurnal =  "transaksi.via_input ='transaksi'";
            }else if($request->via_jurnal == '1'){
                $via_jurnal1 = "pengeluaran.via_input ='pengeluaran' AND pengeluaran.via_input ='penyaluran'";
                $via_jurnal = "transaksi.via_input = 'penerimaan'";
            }else if($request->via_jurnal == '2'){
                $via_jurnal1 = "pengeluaran.via_input = 'mutasi'";
                $via_jurnal = "transaksi.via_input = 'mutasi'";
            }else if($request->via_jurnal == '3'){
                $via_jurnal1 = "pengeluaran.via_input = 'hha'";
                $via_jurnal = "transaksi.via_input = 'hha'";
            }else{
                $via_jurnal1 = "pengeluaran.via_input IS NOT NULL";
                $via_jurnal = "transaksi.via_input IS NOT NULL";
            }
            
            if($request->prd == '0'){
                $prd1 = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
                $prd2 = "DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai'";
                $prd3 = "DATE(jurnal.tanggal) >= '$dari' AND DATE(jurnal.tanggal) <= '$sampai'";
            }else if($request->prd == '1') {
                $prd1 = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
                $prd2 = "MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'";
                $prd3 = "MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'";
            }else if($request->prd == '2') {
                $prd1 = $request->years == "" ? "YEAR(transaksi.tanggal) = '$thns'"    : "YEAR(transaksi.tanggal) = '$request->years'";
                $prd2 = $request->years == "" ? "YEAR(tgl) = '$thns'"                  : "YEAR(tgl) = '$request->years'";
                $prd3 = $request->years == "" ? "YEAR(tanggal) = '$thns'"              : "YEAR(tanggal) = '$request->years'";
            }
            
            $rj_grup = $request->groupby;
            
            if($rj_grup == ''){
                $grupBy = 'tgjur, coa, nama_coa, ket_penerimaan, debit, kredit, id_transaksi, via_input, crt, ids, urut';
            }else{
                $grupBy = 'tgjur, coa, nama_coa';
            }
            
            
     
            $sel_transaksideb = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, coa_debet as coa, coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, transaksi.jumlah as debit, '0' as kredit, transaksi.id_transaksi as id_transaksi, via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 1 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, transaksi.coa_debet as coa, coa.nama_coa as nama_coa, SUM(transaksi.jumlah) as debit, '0' as kredit";
            $sel_transaksikre = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, '0' debit, transaksi.jumlah as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 2 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, '0' debit, SUM(transaksi.jumlah) as kredit";
            $sel_jurnal = $rj_grup == ''
                ? "tanggal as tgjur, coa_use as coa, coa.nama_coa as nama_coa, keterangan as ket_penerimaan, nominal_debit as debit, nominal_kredit as kredit, no_resi as id_transaksi, via_input as via_input, jurnal.created_at AS crt, jurnal.id_jurnal AS ids, 7 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(jurnal.tanggal, '%Y'), DATE_FORMAT(jurnal.tanggal, '%Y-%m')), jurnal.tanggal) as tgjur, coa_use as coa, coa.nama_coa as nama_coa, SUM(nominal_debit) as debit, SUM(nominal_kredit) as kredit";
            $sel_pengeluarandeb = $rj_grup == '' 
                ? "pengeluaran.tgl as tgjur, coa_debet as coa,  coa.nama_coa as nama_coa, keterangan as ket_penerimaan, nominal as debit, '0' as kredit, no_resi as id_transaksi, pengeluaran.via_input as via_input, pengeluaran.created_at AS crt, pengeluaran.id AS ids, 5 AS urut" 
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(pengeluaran.tgl, '%Y'), DATE_FORMAT(pengeluaran.tgl, '%Y-%m')), pengeluaran.tgl) as tgjur, coa_debet as coa,  coa.nama_coa as nama_coa, SUM(nominal) as debit,  '0' as kredit";
            $sel_pengeluarankre = $rj_grup == '' 
                ? "pengeluaran.tgl as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, keterangan as ket_penerimaan, '0' as debit,  nominal as kredit, no_resi as id_transaksi, pengeluaran.via_input as via_input, pengeluaran.created_at AS crt, pengeluaran.id AS ids, 6 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(pengeluaran.tgl, '%Y'), DATE_FORMAT(pengeluaran.tgl, '%Y-%m')), pengeluaran.tgl) as tgjur, coa_kredit as coa, coa.nama_coa as nama_coa, '0' as debit, SUM(nominal) as kredit";
            $sel_dp = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, prog.coa1 as coa,  coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, '0' debit, (transaksi.dp/100)*transaksi.jumlah  as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 4 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, prog.coa1 as coa, coa.nama_coa as nama_coa, '0' as debit, SUM((transaksi.dp/100)*transaksi.jumlah) as kredit";
            $sel_penyaluran = $rj_grup == '' 
                ? "transaksi.tanggal as tgjur, prog.coa2 as coa,  coa.nama_coa as nama_coa, transaksi.ket_penerimaan as ket_penerimaan, (transaksi.dp/100)*transaksi.jumlah as debit, '0' as kredit, transaksi.id_transaksi as id_transaksi,via_input as via_input, transaksi.created_at AS crt, transaksi.id AS ids, 3 AS urut"
                : "IF('$rj_grup' >= 1, IF('$rj_grup' > 1, DATE_FORMAT(transaksi.tanggal, '%Y'), DATE_FORMAT(transaksi.tanggal, '%Y-%m')), transaksi.tanggal) as tgjur, prog.coa2 as coa, coa.nama_coa as nama_coa, SUM((transaksi.dp/100)*transaksi.jumlah) as debit, '0' as kredit";
     
            
            $stat_tran  = "transaksi.approval = 1";
            $stat_peng  = "pengeluaran.acc = 1";

            $pisan = DB::table('jurnal')
                ->join('coa','coa.coa','=','jurnal.coa_debet')
                ->selectRaw($sel_jurnal)
                ->whereRaw("$prd3 AND $kntr2 AND $buku3 AND $jenis3 AND $jen_tran3")
                ->groupByRaw($grupBy)
                ;
            $alay = DB::table('pengeluaran')
                ->join('coa','coa.coa','=','pengeluaran.coa_kredit')
                ->selectRaw($sel_pengeluarankre)
                ->whereRaw("$jen_tran2 AND $prd2 AND $kntr2 AND $buku2 AND $jenis2 AND $via_jurnal1 AND $stat_peng")
                ->groupByRaw($grupBy)
                ;
            $alay1 = DB::table('pengeluaran')
                ->join('coa','coa.coa','=','pengeluaran.coa_debet')
                ->selectRaw($sel_pengeluarandeb)
                ->whereRaw("$prd2 AND $kntr2 AND $buku22 AND $jenis22  AND $via_jurnal1 AND $jen_tran2 AND $stat_peng")
                ->groupByRaw($grupBy)
                ;
               
            $transaksi1 = DB::table('transaksi')
                ->leftjoin('coa','coa.coa','=','transaksi.coa_kredit')
                ->selectRaw($sel_transaksikre)
                ->whereRaw("$prd1 AND $kntr1 AND $buku12 AND jumlah > 0 AND  $jenis11 AND $via_jurnal AND $jen_tran1 AND transaksi.via_input != 'mutasi' AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
            
             $dp = DB::table('transaksi')
                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                ->leftJoin('coa','coa.coa','=','prog.coa1')
                ->selectRaw($sel_dp)
                ->whereRaw("$prd1 AND $kntr1 AND $buku1111 AND jumlah > 0 AND  $jenis11 AND $via_jurnal AND transaksi.via_input = 'transaksi' AND transaksi.pembayaran != 'noncash' AND  $jen_tran1 AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
                    
             $penyaluran = DB::table('transaksi')
                ->leftJoin('prog','prog.id_program','=','transaksi.id_program')
                ->leftJoin('coa','coa.coa','=','prog.coa2')
                ->selectRaw($sel_penyaluran)
                ->whereRaw("$prd1 AND $kntr1 AND $buku111 AND jumlah > 0 AND  $jenis1 AND $via_jurnal AND transaksi.via_input = 'transaksi' AND transaksi.pembayaran != 'noncash' AND $jen_tran1 AND $stat_tran")
                ->groupByRaw($grupBy)
                ;
                    
            $transaksi = DB::table('transaksi')
                ->leftjoin('coa','coa.coa','=','transaksi.coa_debet')
                ->selectRaw($sel_transaksideb)
                ->unionAll($transaksi1)
                ->unionAll($penyaluran)
                ->unionAll($dp)
                ->unionAll($alay)
                ->unionAll($alay1)
                ->unionAll($pisan)
                ->whereRaw("$prd1 AND jumlah > 0 AND $kntr1 AND $buku1 AND $jenis1 AND $via_jurnal AND $jen_tran1 AND transaksi.via_input != 'mutasi' AND $stat_tran")
                ->groupByRaw($grupBy)
                ; 
    
            $or_jur = $rj_grup == '' ? 'crt DESC, ids ASC, urut ASC' : 'tgjur DESC';
            $iya = $transaksi->orderByRaw($or_jur)->get();
            $k = [];
            $output = [];
            
            if($request->tab == 'tab1'){
                $data = [];
                $debit = 0;
                $kredit = 0;
                $jumlah= 0;
                foreach($iya as $o){
                    $debit += $o->debit;
                    $kredit += $o->kredit;
                }
                
                $jumlah = $debit - $kredit;
                
                $data = [
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'jumlah' => $jumlah
                ];
                
                return $data;
            }else{
                for($i= 0; $i < count($iya); $i++){
                    
                    if(isset($iya[$i]->via_input)){
                        if($iya[$i]->via_input == 'transaksi'){
                            $output = 'Otomatis';
                        } else if($iya[$i]->via_input == 'pengeluaran' || $iya[$i]->via_input == 'penyaluran' || $iya[$i]->via_input == 'penerimaan' ){
                            $output = 'Oprasional';
                        } else if($iya[$i]->via_input == 'mutasi'){
                            $output = 'Mutasi';
                        }else if($iya[$i]->via_input == null ){
                            $output = 'Kosong';
                        }else if($iya[$i]->via_input == 'penyesuaian'){
                            $output = 'Penyesuaian';
                        }
                    }
                        $k[] = [
                            'crt' => $iya[$i]->crt ?? $iya[$i]->tgjur,
                            'ids' => $iya[$i]->ids ?? $iya[$i]->tgjur,
                            'urut' => $iya[$i]->urut ?? $iya[$i]->tgjur,
                            'jumlahs' => $iya[$i]->debit - $iya[$i]->kredit,
                            'tanggal' => $iya[$i]->tgjur,
                            'nama_coa' => $iya[$i]->nama_coa,
                            'debit' => $iya[$i]->debit,
                            'via_jurnal' => $output ?? null,
                            'coa_debet' => $iya[$i]->coa,
                            'kredit' => $iya[$i]->kredit,
                            'id_transaksi' => $iya[$i]->id_transaksi ?? null,
                            'ket_penerimaan' => $iya[$i]->ket_penerimaan ?? null,
                        ];
                        
                }
                
                return DataTables::of($k)
                ->make(true);
            }
        }
        
        return view('akuntasi.rekap_jurnal', compact('kantor','coa'));
    }
    
    public function postjurnal(Request $request){
        
            $nominal_d = [];
            $nominal_k = [];
            $jenis_t = [];
            $keterangan = [];
            $tgl = [];
            $keterangan = [];
            $kredit = [];
            $debet = [];
            $use_c = [];
            $use_n = [];
            
            foreach($request->arr_jurnal as $val){
               
                if($val['jenis'] == 'debit'){
                    $nominal_d[] = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
                    $nominal_k[] = 0;
                    $kredit[] = 0;
                    $debet[] = $val['coa'];
                    // $Use[] = $val['coa'];
                }elseif($val['jenis'] == 'kredit'){
                    $nominal_d[] = 0;
                    $nominal_k[] = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);  
                    $debet[] = 0;
                    $kredit[] = $val['coa'];
                    // $Use[] = $val['coa'];
                }
                
                $use_c[] = $val['coa'];
                $use_n[] = preg_replace("/[^0-9]/", "", $val['nominal']);
                
                $jenis_t[] = $val['akun'];
                $id_kantor[] = $val['id_kantor'];
                $tgl[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
                $keterangan[] = $val['keterangan'];   
                
                // return($val['coa']);
            }
        // return($use);
        
        for($i = 0; $i < count($request->arr_jurnal); $i++){
                $data = new Jurnal;
                $data->coa_use = $use_c[$i];
                $data->nominal_use = $use_n[$i];
                $data->coa_debet = $debet[$i];
                $data->coa_kredit = $kredit[$i];
                $data->jenis_transaksi = $jenis_t[$i];
                $data->nominal_debit = $nominal_d[$i];
                $data->nominal_kredit = $nominal_k[$i];
                $data->keterangan = $keterangan[$i];
                $data->kantor = $id_kantor[$i];
                $data->tanggal = $tgl[$i];
                $data->acc = 1;
                $data->via_input = 'penyesuaian';
                $data->user_input = Auth::user()->id;
                $data->user_update = Auth::user()->id;
                
                $data->save();
        }
        // return($data);
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function laporan_bulanan(Request $request){
        
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
                    
                if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
        if ($request->ajax()) {
            
            // $kntr = $request->kota;
            $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
            $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
            $currentYear = $p;
            $range = 1; // rentang tahun yang ingin ditampilkan
            $oldestYear = $currentYear - $range;
                
            $thh = [];
            $blnn = [];
                
            
            for ($year = $currentYear; $year >= $oldestYear; $year--) {
                $thh[] = $year;
            }
            
            for ($i = 1; $i <= 12; $i++) {
                $bulan = date('Y-m', mktime(0, 0, 0, $i, 1));
                $blnn[] = $bulan;
            }
            
            $p = [];
            
            // Tampilkan daftar bulan Desember tahun ini
            for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $p[] = date("Y-m", $date) ;
            }
            
            
            $d = $thh;
            
            $baru = SaldoAw::selectRaw("DISTINCT(bulan)")->whereYear('bulan',$d[0])->latest();
            $ngitung = count($baru->get());
            
            if($ngitung > 0){
                $l = date('m', strtotime($baru->first()->bulan));
            }else{
                $l = '12';
            }
            
            $saldo = DB::table('b as t')
                    ->selectRaw("root, t.coa, t.id_parent, t.level ,t.nama_coa, t.id_kantor, SUM(t1.saldo_awal) as saldo1,  SUM(t2.saldo_awal) as saldo2, 
                                SUM(t3.saldo_awal) as saldo3, SUM(t4.saldo_awal) as saldo4, SUM(t5.saldo_awal) as saldo5, SUM(t6.saldo_awal) as saldo6, 
                                SUM(t7.saldo_awal) as saldo7, SUM(t8.saldo_awal) as saldo8, SUM(t9.saldo_awal) as saldo9, SUM(t10.saldo_awal) as saldo10, 
                                SUM(t11.saldo_awal) as saldo11, SUM(t12.saldo_awal) as saldo12, SUM(t13.saldo_awal) as saldo13, SUM(t14.saldo_awal) as saldo14,
                                t.id, t.grup,t.id_kantor, t.id_kantor")
                    ->withRecursiveExpression('b', $query)
                    
                    ->leftjoin('saldo_awal as t1',function($join) use ($d) {
                            $join->on('t1.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t1.bulan) = '$d[1]' AND MONTH(t1.bulan) = '12'");
                    })
                    
                    ->leftjoin('saldo_awal as t2',function($join) use ($d, $l) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t2.bulan) = '$d[0]' AND MONTH(t2.bulan) = $l");
                    })
                    
                    ->leftjoin('saldo_awal as t3',function($join) use ($d) {
                            $join->on('t3.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t3.bulan) = '$d[0]' AND MONTH(t3.bulan) = '01'");
                    })
                    
                    ->leftjoin('saldo_awal as t4',function($join) use ($d, $l) {
                            $join->on('t4.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t4.bulan) = '$d[0]' AND MONTH(t4.bulan) = '02'");
                    })
                    
                    ->leftjoin('saldo_awal as t5',function($join) use ($d) {
                            $join->on('t5.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t5.bulan) = '$d[0]' AND MONTH(t5.bulan) = '03'");
                    })
                    
                    ->leftjoin('saldo_awal as t6',function($join) use ($d, $l) {
                            $join->on('t6.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t6.bulan) = '$d[0]' AND MONTH(t6.bulan) = '04'");
                    })
                    
                    ->leftjoin('saldo_awal as t7',function($join) use ($d, $l) {
                            $join->on('t7.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t7.bulan) = '$d[0]' AND MONTH(t7.bulan) = '05'");
                    })
                    
                    ->leftjoin('saldo_awal as t8',function($join) use ($d, $l) {
                            $join->on('t8.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t8.bulan) = '$d[0]' AND MONTH(t8.bulan) = '06'");
                    })
                    
                    ->leftjoin('saldo_awal as t9',function($join) use ($d, $l) {
                            $join->on('t9.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t9.bulan) = '$d[0]' AND MONTH(t9.bulan) = '07'");
                    })
                    
                    ->leftjoin('saldo_awal as t10',function($join) use ($d, $l) {
                            $join->on('t10.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t10.bulan) = '$d[0]' AND MONTH(t10.bulan) = '08'");
                    })
                    
                    ->leftjoin('saldo_awal as t11',function($join) use ($d, $l) {
                            $join->on('t11.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t11.bulan) = '$d[0]' AND MONTH(t11.bulan) = '09'");
                    })
                    
                    ->leftjoin('saldo_awal as t12',function($join) use ($d, $l) {
                            $join->on('t12.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t12.bulan) = '$d[0]' AND MONTH(t12.bulan) = '10'");
                    })
                    
                    ->leftjoin('saldo_awal as t13',function($join) use ($d, $l) {
                            $join->on('t13.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t13.bulan) = '$d[0]' AND MONTH(t13.bulan) = '11'");
                    })
                    
                    ->leftjoin('saldo_awal as t14',function($join) use ($d, $l) {
                            $join->on('t14.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t14.bulan) = '$d[0]' AND MONTH(t14.bulan) = '12'");
                    })
                    
                      ->leftjoin('saldo_awal as t15',function($join) use ($kntr) {
                            $join->on('t15.coa' ,'=', 't.coa')
                                ->whereRaw("$kntr");
                    })
                    
                    ->groupBy('root')
                    ->get();
            
            $data = [];
                    
            $a = [];
            foreach($saldo as $keys => $s){
                
                    $a[] = [
                        'coa' => $s->coa,
                        'nama_coa' => $s->nama_coa,
                        'level' => $s->level,
                        'grup' => $s->grup,
                        'kota' => $s->id_kantor,
                        'id_parent' => $s->id_parent,
                        'saldo1' => $s->saldo1,
                        'saldo2' => $s->saldo2,
                        'saldo3' => $s->saldo3,
                        'saldo4' => $s->saldo4,
                        'saldo5' => $s->saldo5,
                        'saldo6' => $s->saldo6,
                        'saldo7' => $s->saldo7,
                        'saldo8' => $s->saldo8,
                        'saldo9' => $s->saldo9,
                        'saldo10' => $s->saldo10,
                        'saldo11' => $s->saldo11,
                        'saldo12' => $s->saldo12,
                        'saldo13' => $s->saldo13,
                        'saldo14' => $s->saldo14,
                    ];
            }
            
        
            foreach ($a as $da) {
                
                // $kota =  $request->kantor != '' ? $da['kota'] == $request->kantor : $da['kota'] != null ;
                
                
                if($request->jenis == 0){
                    if ($da['level'] != '3' && $da['level'] != '4'  && $da['grup'] == '1,9' || $da['grup'] == '6' && $da['level'] != '2') {
                        $data[] = [
                            'coa' => $da['coa'],
                            'nama_coa' => $da['nama_coa'],
                            'level' => $da['level'],
                            'grup' => $da['grup'],
                            'id_parent' => $da['id_parent'],
                            'saldo1' => $da['saldo1'] == null ? 0 : $da['saldo1'],
                            'saldo2' => $da['saldo2'] == null ? 0 : $da['saldo2'],
                            'saldo3' => $da['saldo3'] == null ? 0 : $da['saldo3'],
                            'saldo4' => $da['saldo4'] == null ? 0 : $da['saldo4'],
                            'saldo5' => $da['saldo5'] == null ? 0 : $da['saldo5'],
                            'saldo6' => $da['saldo6'] == null ? 0 : $da['saldo6'],
                            'saldo7' => $da['saldo7'] == null ? 0 : $da['saldo7'],
                            'saldo8' => $da['saldo8'] == null ? 0 : $da['saldo8'],
                            'saldo9' => $da['saldo9'] == null ? 0 : $da['saldo9'],
                            'saldo10' => $da['saldo10'] == null ? 0 : $da['saldo10'],
                            'saldo11' => $da['saldo11'] == null ? 0 : $da['saldo11'],
                            'saldo12' => $da['saldo12'] == null ? 0 : $da['saldo12'],
                            'saldo13' => $da['saldo13'] == null ? 0 : $da['saldo13'],
                            'saldo14' => $da['saldo14'] == null ? 0 : $da['saldo14'],
                            'wew' => $request->jenis
                        ];
                    }
                }else{
                   if ($da['grup'] == '6' && $da['level'] == '1') {
                        $data[] = [
                            'coa' => $da['coa'],
                            'nama_coa' => $da['nama_coa'],
                            'level' => $da['level'],
                            'grup' => $da['grup'],
                            'id_parent' => $da['id_parent'],
                            'saldo1' => $da['saldo1'] == null ? 0 : $da['saldo1'],
                            'saldo2' => $da['saldo2'] == null ? 0 : $da['saldo2'],
                            'saldo3' => $da['saldo3'] == null ? 0 : $da['saldo3'],
                            'saldo4' => $da['saldo4'] == null ? 0 : $da['saldo4'],
                            'saldo5' => $da['saldo5'] == null ? 0 : $da['saldo5'],
                            'saldo6' => $da['saldo6'] == null ? 0 : $da['saldo6'],
                            'saldo7' => $da['saldo7'] == null ? 0 : $da['saldo7'],
                            'saldo8' => $da['saldo8'] == null ? 0 : $da['saldo8'],
                            'saldo9' => $da['saldo9'] == null ? 0 : $da['saldo9'],
                            'saldo10' => $da['saldo10'] == null ? 0 : $da['saldo10'],
                            'saldo11' => $da['saldo11'] == null ? 0 : $da['saldo11'],
                            'saldo12' => $da['saldo12'] == null ? 0 : $da['saldo12'],
                            'saldo13' => $da['saldo13'] == null ? 0 : $da['saldo13'],
                            'saldo14' => $da['saldo14'] == null ? 0 : $da['saldo14'],
                            'wew' => $request->jenis 
                        ];
                    } 
                }
                
                // }
            }

            // return($data);
            
            
            return DataTables::of($data)
            ->addColumn('coah', function($data){
                if($data['wew'] == 0){
                    if($data['level'] == 1) {
                        if($data['id_parent'] == 0){
                            $ttr = '<b>'. $data['nama_coa']. '</b>';
                        }else{
                            
                            $ttr = $data['nama_coa'];
                        }
                    }else if($data['level'] == 2){
                        $ttr = '&nbsp;&nbsp;'.$data['nama_coa'];
                    }else if($data['level'] == 3){
                        $ttr = '&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
                    }else {
                        $ttr = '&nbsp;&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
                    }
                }else{
                    if($data['level'] == 1) {
                            $ttr = '<b>'.strtoupper($data['nama_coa']). '</b>';
                    }else if($data['level'] == 2){
                        $ttr = '&nbsp;&nbsp;'.$data['nama_coa'];
                    }else if($data['level'] == 3){
                        $ttr = '&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
                    }else {
                        $ttr = '&nbsp;&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
                    }
                }
                return $ttr;
            })
            
            ->rawColumns(['coah'])
            ->make(true);
        }
        
        return view ('fins-laporan.laporan_bulanan',compact('kantor'));
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
    
    // public function laporan_keuangan(Request $request){
       
    //     $query = DB::table('coa as t1')
    //                 ->select('t1.*', 't1.id as root')
                    
    //                 ->unionAll(
    //                     DB::table('b as t0')
    //                         ->select('t3.*', 't0.root')
    //                         ->join('coa as t3', 't3.id_parent', '=', 't0.id')
    //                 );
        
        
    //      $k = Auth::user()->id_kantor;
    //     $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();  

    //     if(Auth::user()->level == 'admin'){
    //          $kantor = Kantor::whereRaw("id_coa != '' ")->get();
    //     }elseif(Auth::user()->level == 'kacab'){
    //         if($kan == null){
    //             $kantor = Kantor::whereRaw("id = $k")->get();
    //         }else{
    //             $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
    //         }
    //     }
        
        
    //   $jenis = JenlapKeuangan::where('aktif','y')->get();
        
    //     if ($request->ajax()) {
            
    //         // $kntr = $request->kota != "" ? "id_kantor = '$request->kota'" : "id_kantor IS NOT NULL";
    //         $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
    //         // $kntr = $request->kota;
    //         // $bulans = $request->bln ;
            
    //         if($request->mulbul == 1){
    //             // $jumbln = count($request->bln2);
    //             $bulans = !$request->bln2 ? date('m') : $request->bln2[count($request->bln2)-1];
    //         }else{
    //             $bulans = $request->bln == '' ? date('m') : $request->bln;
    //         }
          
            

    //         $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
    //         $currentYear = $p;
    //         $range = 1; // rentang tahun yang ingin ditampilkan
    //         $oldestYear = $currentYear - $range;
            
      
        
          
    //         $d = [];
            
    //         for ($year = $currentYear; $year >= $oldestYear; $year--) {
    //             $d[] = $year;
    //         }
            

    //         // return($d);
    //         $baru = SaldoAw::selectRaw("DISTINCT(bulan)")->whereYear('bulan',$d[0])->latest();
            
    //         $ngitung = count($baru->get());
    //         // return($baru->first()->bulan);
            
    //         if($ngitung > 0){
    //             $l = date('m', strtotime($baru->first()->bulan));
    //         }else{
    //             $l = '12';
    //         }
    
    //             $saldo = DB::table('b as t')
    //                 ->selectRaw("root, t.coa, t.id_parent, t.level ,t.nama_coa, SUM(t2.konak) as saldo1, SUM(t3.konak) as saldo2, t.id, t.grup, t.id_kantor")
    //                 ->withRecursiveExpression('b', $query)
                    
    //                 ->leftjoin('saldo_awal as t2',function($join) use ($d,$bulans) {
    //                         $join->on('t2.coa' ,'=', 't.coa')
    //                             ->whereRaw("YEAR(t2.bulan) = '$d[1]' AND MONTH(t2.bulan) = '$bulans'");
    //                 })
                    
    //                 ->leftjoin('saldo_awal as t3',function($join) use ($d,$l,$bulans) {
    //                         $join->on('t3.coa' ,'=', 't.coa')
    //                             ->whereRaw("YEAR(t3.bulan) = '$d[0]' AND MONTH(t3.bulan) = '$bulans'");
    //                 })
                    
    //                 ->leftjoin('saldo_awal as t4',function($join) use ($kntr) {
    //                         $join->on('t4.coa' ,'=', 't.coa')
    //                             ->whereRaw("$kntr");
    //                 })
                
    //                 ->groupBy('root')
    //                 ->get();
                    

    //         $data = [];
                    
    //         $a = [];
    //         foreach($saldo as $keys => $s){
                
    //                 $a[] = [
    //                     'coa' => $s->coa,
    //                     'nama_coa' => $s->nama_coa,
    //                     'id_kantor' => $s->id_kantor,
    //                     'level' => $s->level,
    //                     'grup' => $s->grup,
    //                     'id_parent' => $s->id_parent,
    //                     'saldo1' => $s->saldo1,
    //                     'saldo2' => $s->saldo2,
    //                 ];
    //         }
            
                   

    //         foreach ($a as $da) {
    //             if ($da['level'] != '3' && $da['level'] != '4'  && $da['grup'] == '1,9' || $da['grup'] == '6' && $da['level'] != '2'  ) {
    //                 $data[] = [
    //                     'coa' => $da['coa'],
    //                     'nama_coa' => $da['nama_coa'],
    //                     'level' => $da['level'],
    //                     'grup' => $da['grup'],
    //                     'id_parent' => $da['id_parent'],
    //                     'saldo1' => $da['saldo1'] == null ? 0 : $da['saldo1'],
    //                     'saldo2' => $da['saldo2'] == null ? 0 : $da['saldo2'],
    //                 ];
    //             }
    //         }

          
    //         // return($saldo);
            
         

          
    //     //   $saldo = DB::table('b as t')
    //     //             ->selectRaw("root, t.coa, t.id_parent, t.level ,t.nama_coa, SUM(t2.konak) as saldo1, SUM(t3.konak) as saldo2, t.id, t.grup, t.id_kantor")
    //     //             ->withRecursiveExpression('b', $query)
                    
                    
    //     //             ->leftjoin('saldo_awal as t2',function($join) use ($d) {
    //     //                     $join->on('t2.coa' ,'=', 't.coa')
    //     //                         ->whereRaw("YEAR(t2.bulan) = '$d[1]' AND MONTH(t2.bulan) = '12");
    //     //             })
                    
    //     //             ->leftjoin('saldo_awal as t3',function($join) use ($d, $l) {
    //     //                     $join->on('t3.coa' ,'=', 't.coa')
    //     //                         ->whereRaw("YEAR(t3.bulan) = '$d[0]' AND MONTH(t3.bulan) = $l");
    //     //             })
                    
    //     //             ->groupBy('root')
    //     //             ->get();
            
            
    //     //     $data = [];
                    
    //     //     $a = [];
    //     //     foreach($saldo as $keys => $s){
                
    //     //             $a[] = [
    //     //                 'coa' => $s->coa,
    //     //                 'nama_coa' => $s->nama_coa,
    //     //                 'id_kantor' => $s->id_kantor,
    //     //                 'level' => $s->level,
    //     //                 'grup' => $s->grup,
    //     //                 'id_parent' => $s->id_parent,
    //     //                 'saldo1' => $s->saldo1,
    //     //                 'saldo2' => $s->saldo2,
    //     //             ];
    //     //     }
            
                   

    //     //     foreach ($a as $da) {
    //     //         if ($da['level'] != '3' && $da['level'] != '4'  && $da['grup'] == '1,9' || $da['grup'] == '6' && $da['level'] != '2'  ) {
    //     //             $data[] = [
    //     //                 'coa' => $da['coa'],
    //     //                 'nama_coa' => $da['nama_coa'],
    //     //                 'level' => $da['level'],
    //     //                 'grup' => $da['grup'],
    //     //                 'id_parent' => $da['id_parent'],
    //     //                 'saldo1' => $da['saldo1'] == null ? 0 : $da['saldo1'],
    //     //                 'saldo2' => $da['saldo2'] == null ? 0 : $da['saldo2'],
    //     //             ];
    //     //         }
              
    //     //     }
          
    //         // return($saldo);
    //         return DataTables::of($data)
    //         ->addColumn('coah', function($data){
    //             if($data['level'] == 1) {
    //                 if($data['id_parent'] == 0){
    //                     $ttr = '<b>'. $data['nama_coa']. '</b>';
    //                 }else{
                        
    //                     $ttr = $data['nama_coa'];
    //                 }
    //             }else if($data['level'] == 2){
    //                 $ttr = '&nbsp;&nbsp;'.$data['nama_coa'];
    //             }else if($data['level'] == 3){
    //                 $ttr = '&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
    //             }else {
    //                 $ttr = '&nbsp;&nbsp;&nbsp;&nbsp;'.$data['nama_coa'];
    //             }
    //             return $ttr;
    //         })
    //         ->rawColumns(['coah'])
    //         ->make(true);
    //     }
        
    //     return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    // }
    
     public function laporan_keuangan(Request $request){
       
     
        $query = DB::table('rumlap_keuangan as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('rumlap_keuangan as t0')
                            ->select('t3.*', 't0.root')
                            ->join('rumlap_keuangan as t3', 't3.id', '=', 't0.id')
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
            if($request->mulbul == 1){
        
            // $bulans = !$request->bln2 ? [] : $request->bln2;
            // $bulans = !$request->bln2 == [] ?  date('m') : $request->bln2;
            $bulans = !$request->bln2 == '' ? date('m') : $request->bln2[count($request->bln2)];
            }else{
                $bulans = $request->bln == '' ? date('m') : $request->bln;
            }
          
            $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
            $currentYear = $p;
            $range = 1; // rentang tahun yang ingin ditampilkan
            $oldestYear = $currentYear - $range;
            
            // $bulans2 = $request->bln == '' ? date('m') : $request->bln;
            // $bln2 = $bulans2 -01;
            
            $mon = $request->bln == '' ? date('m') : $request->bln;  
            
            $blnskrng = date("Y-m-t", strtotime('01-'.$mon.'-'.$p));
            $blnkurang = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$mon.'-'.$p)));
            
            // $inbulan = date("Y-m-t", strtotime('01-'.$bulans .$currentYear));
            // $inbulan2 = date("Y-m-t", strtotime('01-'.$bln .$currentYear));
            
       
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
   
        $wowd = $request->jenis;
        $via = $request->via;
   
        $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$wowd)->where('aktif','y ')->get();
      
        foreach($tet as $s){
             $a[] = [
                        // 'indik' => $s->indikator,
                        'saldo' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
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
        //$s1[$i] = $saldo[$i];
        //$s1[$i] = $saldo[$i];
      
        }

        foreach($tet as $s){
             $z[] = [
                        'indik' => $s->indikator,
                    ];
        }    



         $teto = DB::table('rumlap_keuangan')
            ->whereRaw("id_jenlap = $wowd AND aktif = 'y'")
         ->get();

      
          if($via == '0'){
              
          
             $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                )
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa1, prog.coa2, 0 as jumlah, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                        );
                
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
          
            $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                        ->whereIn('t.coa', $s1)    
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulans, $d) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereMonth('sub.tanggal', $bulans)
                                ->whereYear('sub.tanggal', '$d[0]')
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                            
                        ->withRecursiveExpression('b', $query)
                        ->whereIn('t.coa', $s1)
                        ->leftjoin('saldo_awal as t2',function($join) use ($blnkurang) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereDate('t2.bulan', $blnkurang);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
             
            
        
             
            $inArray = [];
            foreach($saldo as $i => $val){
                if($saldo2[$i]->coa == $val->coa & $s1){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                        $inArray[] = [
                            'id' => $val->id,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($a + $val->debit) - $val->kredit + $val->debit_s - $val->kredit_s,
                         
                        ];
                    
                
            }
        
          }
      
            return DataTables::of($teto)
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
            
    
        ->addColumn('saldo1', function($s1)  use ($d,$bulans,$teto,$a ,&$via ,&$z,&$blnskrng,&$blnkurang,&$inArray){
            
        //  for($i = 0; $i < count($z); $i++){
        //   $indiknya[] = $z[$i]['indik'];
         
        // }
        
      
        // foreach($indiknya as $s){
        //      $mmk[] = [
        //                 'indddd' =>  $s,
        //             ];
        // }    

        // $getindik = [];
        //  $getindik = DB::table('rumlap_keuangan')
        //   ->selectRaw("indikator")
        //     ->where(function ($query) use ($indiknya) {
        //         foreach ($indiknya as $value) {
        //             $query->orWhere('indikator', '=', $value);
        //         }
        //     })
        //     ->get();
       



    //dari sini yang di pake 

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
                    'sd' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*,SUM(t.saldo_awal) as ass ")
                    ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m') = $bulans ")
                    ->whereIn('t.coa', $cc)
                    ->first();
             
            return  $tetos->ass ;
            
         }else if ($via == '0'){
             
         
        return($inArray);
        // $testt = 'realtime';     
        // return $testt;   
            
        
  }
   
     
    
        //sampe sini yang di pake 
  
            })
            
            
        ->addColumn('saldo2', function($s1)  use ($z,$blnskrng,$blnkurang,$teto,$d,$bulans,$via){
            
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
                    'sd' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
                    ];
        }   
                  $cc = $bb['sd'];
                  
                  

        $tetos = DB::table('saldo_awal as t')
                     ->selectRaw("t.*,SUM(t.saldo_awal) as ass ")
                    ->whereRaw("YEAR(t.bulan) = '$d[1]' AND DATE_FORMAT(t.bulan,'%m') = $bulans ")
                    ->whereIn('t.coa', $cc)
                    ->first();
             
            return  $tetos->ass ;
         }else if ($via == '0'){
             
            
            //   $union = DB::table('transaksi')
            //             ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
            //             ->unionAll(DB::table('pengeluaran')
            //                     ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp"))
            //             ->unionAll(DB::table('jurnal')
            //                     ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp"))
            //             ->unionAll(DB::table('transaksi')
            //                     ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
            //                     ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
            //                     )
            //             ->unionAll(DB::table('transaksi')
            //                     ->selectRaw("prog.coa1, prog.coa2, 0 as jumlah, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
            //                     ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
            //             );
                
            // $query = DB::table('coa as t1')
            //         ->selectRaw("t1.*, t1.id as root")
                    
            //         ->unionAll(
            //             DB::table('b as t0')
            //                 ->selectRaw("t3.*, t0.root")
            //                 ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
            //         );
          
            // $saldo = DB::table('b as t')
            //             ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
            //             ->withRecursiveExpression('b', $query)
                            
            //             ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulans, $d) {
            //                 $join->on('sub.coa_debet' ,'=', 't.coa')
            //                     ->whereMonth('sub.tanggal', $bulans)
            //                     ->whereYear('sub.tanggal', '$d[0]')
            //                     ->where('acc', 1)
            //                     // ->where(function($que){
            //                     //     if('sub.dp' == 1){
            //                     //     $que->where('sub.via_input', 'transaksihahahahahaha')
            //                     //         ->where('sub.pembayaran','!=', 'noncash');
            //                     //         }
            //                     // })
            //                     ;
            //             })
                            
            //             ->groupBy('root')
            //             ->get(); 
                        
                // $saldo2 = DB::table('b as t')
                //         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                            
                //         ->withRecursiveExpression('b', $query)
                        
                //         ->leftjoin('saldo_awal as t2',function($join) use ($inbulan2) {
                //             $join->on('t2.coa' ,'=', 't.coa')
                //                 ->whereDate('t2.bulan', $inbulan2);
                //         })
                            
                //         ->groupBy('root')
                //         ->get(); 
             
             
             
        //      $results = DB::table('rumlap_keuangan')
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
        //             'sd' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
        //             ];
        // }   
        //           $cc = $bb['sd'];
                  
                  

        // $tetos = DB::table('saldo_awal as t')
        //              ->selectRaw("t.*,SUM(t.saldo_awal) as ass ")
        //             ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan,'%m') = $bulans ")
        //             ->whereIn('t.coa', $cc)
        //             ->first();
             
            $testt = 'realtime';     
            return $testt;
  }
            
            
            
            
    //   $results = DB::table('rumlap_keuangan')
    //       ->selectRaw("rumus,nama")
    //     //   ->whereIn('rumus', $s1)
    //         ->where(function ($query) use ($s1) {
    //             foreach ($s1 as $value) {
    //                 $query->orWhere('rumus', '=', $value);
    //             }
    //         })
    //         ->get();
            
    //     foreach($results as $s){
    //          $b = [
    //                     'nama' => $s->nama,
    //                     'sd' =>  preg_split("/[\ ][\+][\ ]+/",$s->rumus),
    //                 ];
    //     }   
    //               $c = $b['sd'];
                  
                  

    //     $tetos1 = DB::table('saldo_awal as t')
    //                  ->selectRaw("t.*,SUM(t.saldo_awal) as ass ")
    //                 ->whereRaw("YEAR(t.bulan) = '$d[1]' AND MONTH(t.bulan) = '$bulans' ")
    //                 ->whereIn('t.coa', $c)
    //                 ->first();
                  
    //         return $tetos1->ass;
            
            })
            ->rawColumns(['coah','saldo1','saldo2'])
            ->make(true);
        }
        
        return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    }
       public function getnamcoa(Request $request){
         $coa = $request->coa;
         $getcoa = COA::whereRaw("coa = '$coa'")->first();
       
        return $getcoa;
    }
       public function getnamcoadet(Request $request){
         $coa = $request->peng_det;
         $getcoa = COA::whereRaw("coa = '$coa'")->first();
        return $getcoa;
    }
    
}