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
use App\Models\SaldoAw2;
use App\Models\SaldoDana;
use App\Models\Penutupan;
use App\Models\Pengeluaran;
use App\Models\LinkParam;
use DataTables;
use DB;
use Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod; 
use DateTime;
use App\Models\Transaksi;
use App\Models\Tunjangan;
use App\Models\Jurnal;
use Staudenmeir\LaravelCte;
use App\Models\HapusPengeluaran;
use App\Models\HapusTransaksi;
use App\Exports\TrialBalanceExport;
use App\Exports\DetailTrialBalanceExport;
use App\Exports\DetailBatalClosing;
use Illuminate\Support\Collection;
class TesController extends Controller
{
     
    public function trial_balance(Request $request){
        
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
        
        $ngecek = 0; 
        $lev = COA::select('level')->distinct()->get();
        
        $getlp = LinkParam::where('id_user', Auth::user()->id)->where('link', 'trial')->where('aktif', 1)->first();
        if($getlp != null){
            $tgfil = date('m-Y', strtotime($getlp->p1));
            $thfil = date('Y', strtotime($getlp->p1));
            
            $datam = LinkParam::find($getlp->id);
            $datam->aktif   = 0;
            $datam->update();
        }else{
            $tgfil = date('m-Y');
            $thfil = date('Y');
        }
        
        
        if ($request->ajax()) {
            $inArray = [];
            
            $inper  = $request->inper == '' ? 'b' : $request->inper;
            $unit  = $request->unit;
            
            if($unit == '' || $unit == 'all_kan'){
                $transnit = "AND transaksi.id_kantor > 0";
                $pengnit = "AND pengeluaran.kantor > 0";
                $jurnit = "AND jurnal.kantor > 0";
            }else{
                $transnit = "AND transaksi.id_kantor = '$unit'";
                $pengnit = "AND pengeluaran.kantor = '$unit'";
                $jurnit = "AND jurnal.kantor = '$unit'";
            }
            
            if($inper == 'b'){
                $bln    = $request->blns == '' ? date('m-Y') : $request->blns;
                $bulan  = date('m', strtotime('01-'.$bln));
                $tahun  = date('Y', strtotime('01-'.$bln));
                
                $bln2   = date("m-Y", strtotime("-1 month", strtotime('01-'.$bln)));
                
                $inbulan = date("Y-m-t", strtotime('01-'.$bln));
                $inbulan2 = date("Y-m-t", strtotime('01-'.$bln2));
                
                $tgtran = "MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun'";
                $tgpeng = "MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'";
                $tgjurn = "MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
            }else{
                $thn    = $request->thns == '' ? date('Y') : $request->thns;
                $bulan  = $thn == date('Y') ? date('m') : 12;
                $tahun  = $thn;
                $inbulan = date("Y-m-t", strtotime('01-'.$bulan.'-'.$thn));
                
                $bln2   = date("m-Y", strtotime("-1 month", strtotime('01-01-'.$thn)));
                $inbulan2 = date("Y-m-t", strtotime('01-'.$bln2));
                // dd($bln2, $inbulan2);
                
                $tgtran = "YEAR(transaksi.tanggal) = '$tahun'";
                $tgpeng = "YEAR(tgl) = '$tahun'";
                $tgjurn = "YEAR(tanggal) = '$tahun'";
            }
        
            $cek = SaldoAw::whereDate('bulan', $inbulan)->first();
                            
            $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp, transaksi.id_kantor as kantor")
                        ->whereRaw("$tgtran AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0 $transnit")
                        // ->when($unit, function($q) use ($unit) {
                        //     if($unit == ''){
                        //         $q->where('transaksi.id_kantor', 'haha');
                        //     }else if($unit != 'all_kan'){
                        //         $q->where('transaksi.id_kantor', $unit);
                        //     }
                        // })
                        
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp, pengeluaran.kantor as kantor")
                                // ->when($unit, function($q) use ($unit) {
                                //     if($unit == ''){
                                //         $q->where('pengeluaran.kantor', 'haha');
                                //     }else if( $unit != 'all_kan'){
                                //         $q->where('pengeluaran.kantor', $unit);
                                //     }
                                // })
                                ->whereRaw("$tgpeng AND acc = 1 $pengnit"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp, jurnal.kantor as kantor")
                                // ->when($unit, function($q) use ($unit) {
                                //     if($unit == ''){
                                //         $q->where('jurnal.kantor', 'haha');
                                //     }else if( $unit != 'all_kan'){
                                //         $q->where('jurnal.kantor', $unit);
                                //     }
                                // })
                                ->whereRaw("$tgjurn AND acc = 1 $jurnit"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp, transaksi.id_kantor as kantor")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                // ->when($unit, function($q) use ($unit) {
                                //     if($unit == ''){
                                //         $q->where('transaksi.id_kantor', 'haha');
                                //     }else if( $unit != 'all_kan'){
                                //         $q->where('transaksi.id_kantor', $unit);
                                //     }
                                // })
                                ->whereRaw("$tgtran AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0 $transnit")
                                )
                        ;
                        
            $unionSql = $union->toSql();
            
            // return $unionSql;
            // $unionBindings = $union->getBindings();
            
            // return [$unionSql, $unionBindings];
                
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
                    
            // if($unit == ''){
            //     $kondis = "(t.id_kantor != 'hahaha' OR t.id_kantor IS NULL)";
            // }else{
            //     $kondis = "(t.id_kantor = '$unit' OR t.id_kantor IS NULL)";
            // }
          
            $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo, t.id_kantor as kantor")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$unionSql}) as sub"),function($join) use ($bulan, $tahun) {
                            $join
                                ->on('sub.coa_debet' ,'=', 't.coa')
                                // ->whereMonth('sub.tanggal', $bulan)
                                // ->whereYear('sub.tanggal', $tahun)
                                // ->where('acc', 1)
                                ;
                        })
                        // ->whereRaw("$kondis")    
                        ->groupBy('root')
                        ->get(); 
            
            // return $saldo;
            
            $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$unionSql}) as sub"),function($join) use ($bulan, $tahun) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                // ->whereMonth('sub.tanggal', $bulan)
                                // ->whereYear('sub.tanggal', $tahun)
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                            
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($inbulan2) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereDate('t2.bulan', $inbulan2)
                                ->where('t2.coa', 'NOT LIKE', '4%')
                                ->where('t2.coa', 'NOT LIKE', '5%');
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent, IF(SUM(t2.closing) IS NOT NULL, SUM(t2.closing), 0) as closing, IF(COUNT(t2.closing) IS NOT NULL, COUNT(t2.closing), 0) as conclos, t2.canclos, t2.tgl_closing")
                                
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
                    $closed  = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->saldo_awal : 0;
                    $closing = $saldo3[$i]->coa == $val->coa ? ($saldo3[$i]->closing > 0 && $saldo3[$i]->closing == $saldo3[$i]->conclos ? 1 : 0) : 0;
                    $canclos = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->canclos : 0;
                    $tglclos = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->tgl_closing : 0;
                }else{
                    $closed  = 0;
                    $closing = 0;
                    $canclos = 0;
                    $tglclos = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                
                // $saldan = SaldoDana::All();
                // dd($saldan);
                
                $grup = explode(",",$val->grup);
                $aws = $request->grup;
                
                // $coba1 = $request->lvl != '' ? $val->level == $request->lvl : $val->level != null ;
                // $coba2 = $request->coa != '' ? $val->parent == $request->coa : $val->parent != null ;
                // $coba3 = $request->grup != '' ? array_intersect($grup, $aws) : $val->grup != null;
                
                
                //     if($coba1 && $coba2 && $coba3){
                        $idcoa[] = $val->id;
                        $inArray[] = [
                            'root' => $val->root,
                            'id' => $val->id,
                            'parent' => $val->parent,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'id_parent' => $val->id_parent,
                            'level' => $val->level,
                            'kantor' => $val->kantor,
                            'saldo_awal' => $a, 
                            'grup' => $val->grup,
                            'in_sd' => 0,
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            // 'neraca_saldo' => ($a + $deb) - $kre,
                            // edit disini substr($val->coa, 0, 1) == 2 
                            'neraca_saldo' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2 ? $a - $deb + $kre : ($a + $deb) - $kre,
                            // 'neraca_s' => ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'neraca_s' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2 ? $a - $deb + $kre + $val->debit_s - $val->kredit_s : ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'closing' => $closing ,
                            'closed' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 5 ? 0 : $closed,
                            'canclos' => $canclos,
                            'tglclos' => $tglclos,
                            'foot' => 0
                        ];
                        // }
                    
            }
            
            $d = $inArray;
            $coasd = collect($inArray)->where('grup','LIKE', '%6%')->count();
            $saldan = SaldoDana::all();
            foreach($saldan as $i => $sd){
                $re = unserialize($sd->coa_receipt);
                $ex = unserialize($sd->coa_expend); 
                
                $ix = array_search($sd->coa_dana, array_column($inArray,'coa'));
                
                $saldeb = collect($inArray)->whereIn('coa', $re)->sum('neraca_s');
                $salkre = collect($inArray)->whereIn('coa', $ex)->sum('neraca_s');
                
                $salakh = $d[$ix]['saldo_awal'] + ($saldeb) - ($salkre);
                
                $debit_s = collect($inArray)->where('parent', 'n')->sum('debit_s');
                $kredit_s = collect($inArray)->where('parent', 'n')->sum('kredit_s');
                
                $inArray[$ix] = [
                                'root' => $d[$ix]['root'],
                                'id' => $d[$ix]['id'],
                                'parent' => $d[$ix]['parent'],
                                'coa' => $d[$ix]['coa'],
                                'nama_coa' => $d[$ix]['nama_coa'],
                                'id_parent' => $d[$ix]['id_parent'],
                                'level' => $d[$ix]['level'],
                                'kantor' => null,
                                'saldo_awal' => $d[$ix]['saldo_awal'], 
                                'grup' => $d[$ix]['grup'],
                                'in_sd' => 1,
                                'debit' => $saldeb,
                                'kredit' => $salkre,
                                'kredit_s' => $d[$ix]['kredit_s'], 
                                'debit_s' => $d[$ix]['debit_s'],
                                'neraca_saldo' => $salakh,
                                'neraca_s' => $salakh,
                                'closing' => $d[$ix]['closing'] ,
                                'closed' => $d[$ix]['closed'],
                                'canclos' => $d[$ix]['canclos'],
                                'tglclos' => $d[$ix]['tglclos'],
                                'foot' => 0
                    ];
            }    
            $con = count($inArray);
            $inArray[$con] = [
                            'root' => '',
                            'id' => max($idcoa)+1,
                            'parent' => '',
                            'coa' => '',
                            'nama_coa' => '',
                            'id_parent' => '',
                            'level' => '',
                            'kantor' => '',
                            'saldo_awal' => '', 
                            'grup' => '',
                            'in_sd' => 0,
                            'debit' => '' ,
                            'kredit' => '',
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'canclos' => '',
                            'tglclos' => '',
                            'foot' => 2
                ];
                
            $ass = collect($inArray)->where('coa', '100.00.000.000')->first();
            $kwj = collect($inArray)->where('coa', '200.00.000.000')->first();
            $pen = collect($inArray)->where('coa', '400.00.000.000')->first();
            $pey = collect($inArray)->where('coa', '500.00.000.000')->first();
            
            $saldeb = 0;
            $salkre = 0;
            $salakh = 0;
            $sdf = collect($inArray)->where('coa', '300.00.000.000')->first();
            
            if($sdf != NULL){
            $sd = SaldoDana::where('coa_dana', '300.00.000.000')->first();
                $re = unserialize($sd->coa_receipt);
                $ex = unserialize($sd->coa_expend); 
                
                $saldeb = collect($inArray)->whereIn('coa', $re)->sum('neraca_s');
                $salkre = collect($inArray)->whereIn('coa', $ex)->sum('neraca_s');
                
                $salakh = $sdf['saldo_awal'] + ($saldeb) - ($salkre);
            }  
            
            if($request->lvl == '' && $request->coa == '' && $request->grup == ''){
            $inArray[$con+1] = [
                            'root' => max($idcoa)+2,
                            'id' => max($idcoa)+2,
                            'parent' => '',
                            'coa' => '',
                            'nama_coa' => 'Saldo Awal [Asset Vs (Kewajiban+SD)]',
                            'id_parent' => '',
                            'level' => '',
                            'kantor' => '',
                            'saldo_awal' => '', 
                            'grup' => '',
                            'in_sd' => 0,
                            'debit' => $ass['saldo_awal'],
                            'kredit' => $kwj['saldo_awal'] + $sdf['saldo_awal'],
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'canclos' => '',
                            'tglclos' => '',
                            'foot' => 1
                ];
            
            $inArray[$con+2] = [
                            'root' => max($idcoa)+3,
                            'id' => max($idcoa)+3,
                            'parent' => '',
                            'coa' => '',
                            'nama_coa' => 'Mutasi [Debet Vs Kredit]',
                            'id_parent' => '',
                            'level' => '',
                            'kantor' => '',
                            'saldo_awal' => '', 
                            'grup' => '',
                            'in_sd' => 0,
                            'debit' => $ass['debit'] + $kwj['debit'] + $pen['debit'] + $pey['debit'],
                            'kredit' => $ass['kredit'] + $kwj['kredit'] + $pen['kredit'] + $pey['kredit'],
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'canclos' => '',
                            'tglclos' => '',
                            'foot' => 1
                ];
                
            $inArray[$con+3] = [
                            'root' => max($idcoa)+4,
                            'id' => max($idcoa)+4,
                            'parent' => '',
                            'coa' => '',
                            'nama_coa' => 'Penyesuaian [Debet Vs Kredit]',
                            'id_parent' => 10000,
                            'level' => '',
                            'kantor' => '',
                            'saldo_awal' => '', 
                            'grup' => '',
                            'in_sd' => 0,
                            'debit' => $debit_s,
                            'kredit' => $kredit_s,
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'canclos' => '',
                            'tglclos' => '',
                            'foot' => 1
                ];
                
            $inArray[$con+4] = [
                            'root' => max($idcoa)+5,
                            'id' => max($idcoa)+5,
                            'parent' => '',
                            'coa' => '',
                            'nama_coa' => 'Saldo Akhir [Asset Vs (Kewajiban+SD)]',
                            'id_parent' => '',
                            'level' => '',
                            'kantor' => '',
                            'saldo_awal' => '', 
                            'grup' => '',
                            'in_sd' => 0,
                            'debit' => $ass['neraca_s'],
                            'kredit' => $kwj['neraca_s'] + $salakh,
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'canclos' => '',
                            'tglclos' => '',
                            'foot' => 1
                ];
            }
            // function filter($person) {
            //     return $person->parent = $request->coa;
            // }
            
            // filter di PHP
                $filRay = array_filter($inArray, function ($p) use ($request) {
                    $grup = explode(",",$p['grup']);
                    $fillvl = $request->lvl == '' ? $p['level'] != 'haha' : $p['level'] == $request->lvl;
                    $filcoa = $request->coa == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->coa;
                    $filgrup = $request->grup == '' ? $p['grup'] != 'haha' : array_intersect($grup, $request->grup);
                    $filunit = $request->unit == '' ? $p['kantor'] != 'haha' || $p['kantor'] == null : $p['kantor'] == $request->unit || $p['kantor'] == null ;
                    return $fillvl && $filcoa && $filgrup && $filunit;
                    // return $fillvl && $filcoa && $filgrup ;
                });
                
                $inArray = array_values($filRay);
                
                
                $arid = array_column($inArray, 'id');
                
    
                foreach ($inArray as $key => $obj) {
                    if (!in_array($obj['id_parent'], $arid)) {
                        $inArray[$key]['id_parent'] = '';
                    }
                }
            return($inArray);
        }
        $grup = GrupCoa::all();
        
        return view ('akuntasi.trial_balance', compact('ngecek', 'lev', 'grup', 'tgfil', 'thfil', 'kantor'));
    }
    
    public function trial_balance_export(Request $request){
        if($request->tombol == 'xls'){
            $r = Excel::download(new TrialBalanceExport($request), 'trial_balance.xls');
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new TrialBalanceExport($request), 'trial_balance.csv');
            ob_end_clean();
            return $r;
        }
    }
    
    public function postClosing(Request $request){
        // dd('block');
        $blns = $request->blns == '' ? date('m-Y') : $request->blns;
        $tglin = date('Y-m-t', strtotime('01-'.$blns));
        $lisal = SaldoAw::selectRaw("DISTINCT(bulan) AS bulan")->whereDate('bulan', '>', $tglin)->get();
        $confor = $request->all == 1 ? count($lisal) : 0;
        for($x = 0; $x <= $confor; $x++){
            $blnx = $x > 0 ? $lisal[$x-1]['bulan'] : $tglin;
            
            $bln = date('m-Y', strtotime($blnx));
            
            $bulan = date('m', strtotime('01-'.$bln));
            $tahun = date('Y', strtotime('01-'.$bln));
            
            $bln2 = date("m-Y", strtotime("-1 month", strtotime('01-'.$bln)));
            $bulan2 = date('m', strtotime('01-'.$bln2));
            $tahun2 = date('Y', strtotime('01-'.$bln2));
            
            $inbulan = date("Y-m-t", strtotime('01-'.$bln));
            $inbulan2 = date("Y-m-t", strtotime('01-'.$bln2));
        
        
            $cek = SaldoAw::whereDate('bulan', $inbulan)->first();
                            
            $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                        ->whereRaw("MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun' AND acc = 1"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND acc = 1"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                ->whereRaw("MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
                                )
                        ;
            
                
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
                                ->whereMonth('sub.tanggal', $bulan)
                                ->whereYear('sub.tanggal', $tahun)
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
            
            $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
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
                                ->whereDate('t2.bulan', $inbulan2)
                                ->where('t2.coa', 'NOT LIKE', '4%')
                                ->where('t2.coa', 'NOT LIKE', '5%');
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent, IF(SUM(t2.closing) IS NOT NULL, SUM(t2.closing), 0) as closing, IF(COUNT(t2.closing) IS NOT NULL, COUNT(t2.closing), 0) as conclos, t2.id AS id_sal, t2.canclos")
                                
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
                    $canclos = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->canclos : 0;
                }else{
                    $closed = 0;
                    $closing = 0;
                    $canclos = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                
                $grup = explode(",",$val->grup);
                $aws = $request->grup;
                
                        $idcoa[] = $val->id;
                        $inArray[] = [
                            'root' => $val->root,
                            'id' => $val->id,
                            'parent' => $val->parent,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'id_parent' => $val->id_parent,
                            'level' => $val->level,
                            'saldo_awal' => $a, 
                            'grup' => $val->grup,
                            
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'neraca_saldo' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2  ? $a - $deb + $kre : ($a + $deb) - $kre,
                            'neraca_s' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2  ? $a - $deb + $kre + $val->debit_s - $val->kredit_s : ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'closing' => $closing ,
                            'closed' => $closed,
                            'canclos' => $canclos,
                            'foot' => 0
                        ];
                    
            }
            
            $d = $inArray;
            $coasd = collect($inArray)->where('grup','LIKE', '%6%')->count();
            $saldan = SaldoDana::all();
            foreach($saldan as $i => $sd){
                $re = unserialize($sd->coa_receipt);
                $ex = unserialize($sd->coa_expend); 
                
                $ix = array_search($sd->coa_dana, array_column($inArray,'coa'));
                
                $saldeb = collect($inArray)->whereIn('coa', $re)->sum('neraca_s');
                $salkre = collect($inArray)->whereIn('coa', $ex)->sum('neraca_s');
                
                $salakh = $d[$ix]['saldo_awal'] + ($saldeb) - ($salkre);
                
                $debit_s = collect($inArray)->sum('debit_s');
                $kredit_s = collect($inArray)->sum('kredit_s');
                
                $inArray[$ix] = [
                                'root' => $d[$ix]['root'],
                                'id' => $d[$ix]['id'],
                                'parent' => $d[$ix]['parent'],
                                'coa' => $d[$ix]['coa'],
                                'nama_coa' => $d[$ix]['nama_coa'],
                                'id_parent' => $d[$ix]['id_parent'],
                                'level' => $d[$ix]['level'],
                                'saldo_awal' => $d[$ix]['saldo_awal'], 
                                'grup' => $d[$ix]['grup'],
                                
                                'debit' => $saldeb,
                                'kredit' => $salkre,
                                'kredit_s' => $d[$ix]['kredit_s'], 
                                'debit_s' => $d[$ix]['debit_s'],
                                'neraca_saldo' => $salakh,
                                'neraca_s' => $salakh,
                                'closing' => $d[$ix]['closing'] ,
                                'closed' => $d[$ix]['closed'],
                                'canclos' => $d[$ix]['canclos'],
                                'foot' => 0
                    ];
            }  
            
            foreach($inArray as $i => $val){
            
                $id_sal = 0;
                if($cek != null){
                    if($saldo3[$i]->coa == $val['coa']){
                        $id_sal = $saldo3[$i]->id_sal > 0 ? $saldo3[$i]->id_sal : 0;
                    }else{
                        $id_sal = 0;
                    }
                }
                
                if($val['canclos'] == 1){
                    $canclos = SaldoAw::whereDate('bulan', '>', $inbulan)
                                        ->where('coa', $val['coa'])
                                        ->update([ 
                                                    'closing' => 0,
                                                    // 'canclos' => 1,
                                                ]);
                }
                
                $input['coa'] = $val['coa'];
                $input['saldo_awal'] = $val['parent'] == 'n' ? $val['neraca_s'] : 0;
                $input['closing'] = 1;
                $input['canclos'] = 0;
                $input['tgl_closing'] = date('Y-m-d');
                $input['bulan'] = $inbulan;
                
                if($cek != null && $id_sal > 0) {
                    SaldoAw::where('id',$id_sal)->update($input);
                }else{
                    SaldoAw::create($input);
                }
                
            }
        }
        
    return response()->json(['success' => 'Data Added successfully.']);    
        
    }
    
    public function detailRow(Request $request){
        // return($request);
        $sip = [];
        $p  = SaldoDana::where('coa_dana', $request->coa)->get();
        $ceer = [];
        $cee = [];
        $ce_e = [];
        $ce_er = [];
        
        
        
        if(count($p) > 0){
            foreach($p as $tem){
                $cr = unserialize($tem->coa_receipt); //4
                $ce = unserialize($tem->coa_expend); //5
            }

            for($i = 0; $i < count($cr); $i++){
                
                if(preg_match('/\.0{2}\b/', $cr[$i]) > 0 ){
                    $yya = str_replace(".", "",  $cr[$i]);
                    $yy = rtrim($yya, '0');
                }else{
                    $yy = str_replace(".000", "", $cr[$i]);
                }
                
                
                $ceer[] = [
                    'cr' => $yy
                ];
            }
            
            for($i = 0; $i < count($ce); $i++){
                
                if(preg_match('/\.0{2}\b/', $ce[$i]) > 0 ){
                    $wwe = str_replace(".", "",  $ce[$i]);
                    $ww = rtrim($wwe, '0');
                }else{
                    $ww = str_replace(".000", "", $ce[$i]);
                }
                
                
                $cee[] = [
                    'ce' => $ww
                ];
            }
            
            foreach($ceer as $ceeer){
                $ce_er[] = $ceeer['cr'];
            }
            
            foreach($cee as $ceee){
                $ce_e[] = $ceee['ce'];
            }
        }
        
        
        
        if ($request->bulan != '') {
            $tgl = explode('-', $request->bulan);
            $b = date($tgl[0]);
            $t = date($tgl[1]);
        }else{
            $t = date('Y');
            $b = date('m');
        }
        
        $inper  = $request->inper == '' ? 'b' : $request->inper;
            
        if($inper == 'b'){
            $tahuns = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
            $tahunz = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$t'";
        }else{
            $tahuns = "YEAR(transaksi.tanggal) = '$request->tahun'";
            $tahunz = "YEAR(pengeluaran.tgl) = '$request->tahun'";
        }
        
        // $tahuns = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
        // $tahunz = "MONTH(pengeluaran.tgl) = '$b' AND YEAR(pengeluaran.tgl) = '$t'";
            
        if($request->ajax()){
            $dp = $request->dp;
            $coa = $request->coa;
            $hasil = [];
            $prog = DB::table('prog')
                    ->selectRaw("id_program")
                    ->where(function($q) use ($coa){
                        $q->where('coa1', $coa)->orWhere('coa2', $coa);
                    })
                    ->get();
            foreach($prog as $val){
                $hasil[] = $val->id_program;
            }
            
            // if($request->tab == 'tab1'){
                
            //     if($request->dataDebit != ''){
            //         $pengeluaran = DB::table('pengeluaran')
            //                         ->selectRaw("SUM(pengeluaran.nominal) as debit")
            //                         ->where(function($query) use ($hasil,$coa){
            //                         if(empty($hasil)){
            //                             $query->where('coa_debet', $coa);
            //                         }else{
            //                             $query->whereIn('program', $hasil);
            //                         }
            //                     })
            //                     ->whereRaw("$tahunz AND acc = 1");
            //         $transaksi = DB::table('transaksi')
            //             ->selectRaw(
            //                         empty($hasil)
            //                             ? "SUM(transaksi.jumlah) as debit"
            //                             : "SUM((transaksi.jumlah * transaksi.dp) / 100) as debit"
            //                     )
            //             ->where(function($query) use ($hasil,$coa){
            //                 if(empty($hasil)){
            //                     $query->where('coa_debet', $coa);
            //                 }else{
            //                     $query->whereIn('id_program', $hasil);
            //                 }
            //             })
            //             ->unionAll($pengeluaran)
            //             ->whereRaw("$tahuns AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");
                        
            //     }else if($request->dataKredit != ''){
            //         $pengeluaran = DB::table('pengeluaran')
            //             ->selectRaw("SUM(pengeluaran.nominal) as kredit")
            //             ->where(function($query) use ($hasil, $coa){
            //                   if(empty($hasil)){
            //                         $query->where('coa_kredit', $coa);
            //                     }else{
            //                         $query->whereIn('program', $hasil);
            //                     }
            //                 })
            //             ->whereRaw("$tahunz AND acc = 1");
            //         $transaksi = DB::table('transaksi')
            //             ->selectRaw(
            //                         empty($hasil)
            //                             ? "SUM(transaksi.jumlah) as kredit"
            //                             : "SUM((transaksi.jumlah * transaksi.dp) / 100) as kredit"
            //                     )
            //             ->where(function($query) use ($hasil, $coa){
            //                 if(empty($hasil)){
            //                     $query->where('coa_kredit', $coa);
            //                 }else{
            //                     $query->whereIn('id_program', $hasil);
            //                 }
            //             })
            //             ->unionAll($pengeluaran)
            //             ->whereRaw("$tahuns AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");
            //     }
            //     $data = $transaksi->get();
            //     $result = $request->dataDebit != '' ? $data[0]->debit + $data[1]->debit : $data[0]->kredit + $data[1]->kredit ;
            //     return $result;
            // }
            
            if($request->dataDebit != ''){
                $pengeluaran = DB::table('pengeluaran')
                                ->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$t' as t, '$b' as b, nominal as debit")
                                ->where(function($query) use ($hasil,$coa,$ce_e){
                                    $angka = $coa;

                                    $angka = str_replace('.', '', $angka);
                                    
                                    if (substr($angka, 0, 1) === '3'){
                                        $query->whereIn(DB::raw('coa_debet'), function ($query) use ($ce_e) {
                                            foreach ($ce_e as $term2) {
                                                $query->select(DB::raw('coa_debet'))->from('pengeluaran')->orWhere(DB::raw('coa_debet'), 'LIKE', '%' . $term2 . '%');
                                            }
                                        });
                                    }else{
                                        if(empty($hasil)){
                                        $query->where('coa_debet', $coa);
                                        }else{
                                            $query->whereIn('program', $hasil);
                                        }
                                    }
                                    
                                })
                                ->whereRaw("$tahunz AND acc = 1");
                $transaksi = DB::table('transaksi')
                    ->selectRaw("transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$t' as t, '$b' as b, transaksi.jumlah as debit")
                    ->where(function($query) use ($hasil,$coa,$ce_e){
                            $angka = $coa;

                            $angka = str_replace('.', '', $angka);
                            if (substr($angka, 0, 1) === '3'){
                                $query->whereIn(DB::raw('coa_debet'), function ($query) use ($ce_e) {
                                    foreach ($ce_e as $term1) {
                                        $query->select(DB::raw('coa_debet'))->from('transaksi')->orWhere(DB::raw('coa_debet'), 'LIKE', '%' . $term1 . '%');
                                    }
                                });
                            }else{
                                if(empty($hasil)){
                                    $query->where('coa_debet', $coa);
                                }else{
                                    $query->whereIn('id_program', $hasil);
                                }
                            }
                
                        })
                    ->unionAll($pengeluaran)
                    ->whereRaw("$tahuns AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");
                    
            }
            else if($request->dataKredit != ''){
                $pengeluaran = DB::table('pengeluaran')
                         ->where(function($query) use ($hasil,$coa,$ce_er){
                                $angka = $coa;

                                $angka = str_replace('.', '', $angka);
                                
                                if (substr($angka, 0, 1) === '3'){
                                    $query->whereIn(DB::raw('coa_kredit'), function ($query) use ($ce_er) {
                                        foreach ($ce_er as $term2) {
                                            $query->select(DB::raw('coa_kredit'))->from('pengeluaran')->orWhere(DB::raw('coa_kredit'), 'LIKE', '%' . $term2 . '%');
                                        }
                                    });
                                }else{
                                    if(empty($hasil)){
                                    $query->where('coa_kredit', $coa);
                                    }else{
                                        $query->whereIn('program', $hasil);
                                    }
                                }
                                
                            })
                        ->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$t' as t, '$b' as b, nominal as kredit")
                        ->whereRaw("$tahunz AND acc = 1");
                $transaksi = DB::table('transaksi')
                    ->where(function($query) use ($hasil,$coa,$ce_er){
                            $angka = $coa;

                            $angka = str_replace('.', '', $angka);
                            if (substr($angka, 0, 1) === '3'){
                                $query->whereIn(DB::raw('coa_kredit'), function ($query) use ($ce_er) {
                                    foreach ($ce_er as $term1) {
                                        $query->select(DB::raw('coa_kredit'))->from('transaksi')->orWhere(DB::raw('coa_kredit'), 'LIKE', '%' . $term1 . '%');
                                    }
                                });
                            }else{
                                if(empty($hasil)){
                                    $query->where('coa_kredit', $coa);
                                }else{
                                    $query->whereIn('id_program', $hasil);
                                }
                            }
                
                        })
                    ->selectRaw("transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$t' as t, '$b' as b, transaksi.jumlah as kredit")
                    ->unionAll($pengeluaran)
                    ->whereRaw("$tahuns AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0");
            }
            
            $data = $transaksi->get();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        } 
    }
    
    public function detail_export(Request $request){
         if ($request->bulan != '') {
            $tgl = explode('-', $request->bulan);
            $b = date($tgl[0]);
            $t = date($tgl[1]);
        }else{
            $t = date('Y');
            $b = date('m');
        }
        
        if($request->inper == 'b'){
            $perxls = ' Bulan '.$b.'-'.$t.'.xls';
            $percsv = ' Bulan '.$b.'-'.$t.'.csv';
        }else{
            $perxls = ' Tahun '.$request->tahun.'.xls';
            $percsv = ' Tahun '.$request->tahun.'.csv';
        }
        // dd($request);
        if($request->dataDebit != ''){
            $jenis = 'Debit';
        }else if($request->dataKredit != ''){
            $jenis = 'Kredit';
        }
    
        if($request->tombol == 'xls'){
            $r = Excel::download(new DetailTrialBalanceExport($request), 'Detail Transaksi '.$jenis.' COA '. $request->nama_coa . $perxls);
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new DetailTrialBalanceExport($request), 'Detail Transaksi '.$jenis.' COA '. $request->nama_coa . $percsv);
            ob_end_clean();
            return $r;
        }
    }
    
    public function rin_canclos(Request $request){
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
                            
        $listup = $tran->unionAll($peng)->unionAll($h_tran)->unionAll($h_peng)->orderByRaw('tgl')->get();
        return DataTables::of($listup)
                ->addIndexColumn()
                ->addColumn('name_pj', function($data){
                    $user = User::find($data->user_pj); 
                    $uspj = $user != null ? $user->name : '';
                    return $uspj;
                })
                ->rawColumns(['name_pj'])
                ->make(true);
    }
    
    public function batalClosingExport(Request $request){
        if ($request->bulan != '') {
            $tgl = explode('-', $request->bulan);
            $b = date($tgl[0]);
            $t = date($tgl[1]);
        }else{
            $t = date('Y');
            $b = date('m');
        }
        if($request->tombol == 'xlsBatClos'){
            $r = Excel::download(new DetailBatalClosing($request, $b, $t), 'Detail Batal Closing COA '. $request->nama_coa . ' Priode '.$b.'-'.$t.'.xls');
            ob_end_clean();
        }else if($request->tombol == 'csvBatClos'){
            $r = Excel::download(new DetailBatalClosing($request, $b, $t), 'Detail Batal Closing COA '. $request->nama_coa . ' Priode '.$b.'-'.$t.'.csv');
            ob_end_clean();
        }
        return $r;
    }
    
    public function detailBatclos($id, Request $request){
        $tab = $request->tab;
        if($tab == 'tran'){
            $data = Transaksi::selectRaw("transaksi.id, 'tran' AS tab, 'Transaksi' AS ket_tab, transaksi.tanggal AS tgl, transaksi.jumlah AS nominal, transaksi.approval AS acc, transaksi.via_input, coa_debet.nama_coa AS coa_debet, coa_kredit.nama_coa AS coa_kredit, IF(DATE(transaksi.created_at) = DATE(transaksi.updated_at), DATE(transaksi.created_at), '') AS dibuat, IF(DATE(transaksi.created_at) != DATE(transaksi.updated_at), DATE(transaksi.updated_at), '') AS diupdate, '' AS dihapus, user.name as user, transaksi.pembayaran as pembayaran, transaksi.keterangan as ket, transaksi.bukti as bukti, confirm.name as confirm")
                ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'transaksi.coa_debet')
                ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'transaksi.coa_kredit')
                ->join('users AS user', 'user.id','=','transaksi.user_insert')
                ->join('users AS confirm', 'confirm.id','=','transaksi.user_approve')
                ->where('transaksi.id', $id)->first();

        }else if($tab == 'peng'){
            $data   = Pengeluaran::selectRaw("pengeluaran.id, 'peng' AS tab, 'Pengeluaran' AS ket_tab, pengeluaran.tgl, pengeluaran.nominal,pengeluaran. acc, pengeluaran.via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, IF(DATE(pengeluaran.created_at) = DATE(pengeluaran.updated_at), DATE(pengeluaran.created_at), '') AS dibuat, IF(DATE(pengeluaran.created_at) != DATE(pengeluaran.updated_at), DATE(pengeluaran.updated_at), '') AS diupdate, '' AS dihapus, user.name as user, pengeluaran.pembayaran as pembayaran, pengeluaran.keterangan as ket , pengeluaran.bukti as bukti , confirm.name as confirm")
                ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'pengeluaran.coa_debet')
                ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'pengeluaran.coa_kredit')
                ->join('users AS user', 'user.id','=','pengeluaran.user_input')
                ->join('users AS confirm', 'confirm.id','=','pengeluaran.user_approve')
                        ->where('pengeluaran.id', $id)->first();
        }else if($tab == 'h_tran'){
            // $data = HapusTransaksi::selectRaw("hapus_pengeluaran.id, 'h_tran' AS tab, 'Hapus Transaksi' AS ket_tab, hapus_transaksi.tanggal AS tgl, hapus_transaksi.jumlah AS nominal, hapus_transaksi.approval AS acc, hapus_transaksi.via_input as via_input, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, '' AS dibuat, '' AS diupdate, DATE(hapus_transaksi.created_at) AS dihapus, user.name as user, hapus_transaksi.pembayaran as pembayaran, hapus_transaksi.keterangan as ket , hapus_transaksi.bukti as bukti , confirm.name as confirm")
            //     ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'hapus_transaksi.coa_debet')
            //     ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'hapus_transaksi.coa_kredit')
            //     ->join('users AS user', 'user.id','=','hapus_transaksi.user_insert')
            //     ->join('users AS confirm', 'confirm.id','=','hapus_transaksi.user_approve')
            //     ->where('hapus_transaksi.id', $id)->get();
                $data = HapusTransaksi::selectRaw("hapus_transaksi.id, 'Hapus Transaksi' AS ket_tab, hapus_transaksi.hapus_alasan as alasan, 'h_tran' as tab, hapus_transaksi.tanggal as tanggal, user.name as user, confirm.name as confirm, hapus_transaksi.pembayaran as pembayaran, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, hapus_transaksi.jumlah as nominal, hapus_transaksi.bukti as bukti, hapus_transaksi.ket_penerimaan as ket")
                ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'hapus_transaksi.coa_debet')
                ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'hapus_transaksi.coa_kredit')
                ->join('users AS user', 'user.id','=','hapus_transaksi.user_insert')
                ->join('users AS confirm', 'confirm.id','=','hapus_transaksi.user_delete')
                ->where('hapus_transaksi.id', $id)->first();
        }else if($tab == 'h_peng'){
            $data = HapusPengeluaran::selectRaw("hapus_pengeluaran.id, 'Hapus Pengeluaran' AS ket_tab, hapus_pengeluaran.hapus_alasan as alasan,'h_peng' as tab, hapus_pengeluaran.tgl as tanggal, user.name as user, confirm.name as confirm, hapus_pengeluaran.pembayaran as pembayaran, coa_debet.nama_coa as coa_debet, coa_kredit.nama_coa as coa_kredit, hapus_pengeluaran.nominal as nominal, hapus_pengeluaran.bukti as bukti, hapus_pengeluaran.keterangan as ket")
                ->join('coa AS coa_debet', 'coa_debet.coa', '=', 'hapus_pengeluaran.coa_debet')
                ->join('coa AS coa_kredit', 'coa_kredit.coa', '=', 'hapus_pengeluaran.coa_kredit')
                ->join('users AS user', 'user.id','=','hapus_pengeluaran.user_input')
                ->join('users AS confirm', 'confirm.id','=','hapus_pengeluaran.user_delete')
                ->where('hapus_pengeluaran.id', $id)->first(); 
        }
        if (!empty($data) || $data != '') {
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }
    
    
    public function postClosingall(Request $request){
        dd('block');
        
            
            $bln = $request->blns == '' ? date('m-Y') : $request->blns;
            
            $tglin = date('Y-m-t', strtotime('01-'.$bln));
            $lisal = SaldoAw::whereDate('bulan', '>=', $tglin)->distinct('bulan');
            dd($lisal);
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
                        ->whereRaw("MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun' AND acc = 1"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                                ->whereRaw("MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND acc = 1"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                ->whereRaw("MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
                                )
                        ;
            
                
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
                                ->whereMonth('sub.tanggal', $bulan)
                                ->whereYear('sub.tanggal', $tahun)
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
            
            $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
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
                                ->whereDate('t2.bulan', $inbulan2)
                                ->where('t2.coa', 'NOT LIKE', '4%')
                                ->where('t2.coa', 'NOT LIKE', '5%');
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent, IF(SUM(t2.closing) IS NOT NULL, SUM(t2.closing), 0) as closing, IF(COUNT(t2.closing) IS NOT NULL, COUNT(t2.closing), 0) as conclos, t2.id AS id_sal, t2.canclos")
                                
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
                    $canclos = $saldo3[$i]->coa == $val->coa ? $saldo3[$i]->canclos : 0;
                }else{
                    $closed = 0;
                    $closing = 0;
                    $canclos = 0;
                }
                
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                if($saldox[$i]->coa == $val->coa){
                    $deb = $saldox[$i]->debit + $val->debit;
                    $kre = $saldox[$i]->kredit + $val->kredit;
                }
                
                $grup = explode(",",$val->grup);
                $aws = $request->grup;
                
                        $idcoa[] = $val->id;
                        $inArray[] = [
                            'root' => $val->root,
                            'id' => $val->id,
                            'parent' => $val->parent,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'id_parent' => $val->id_parent,
                            'level' => $val->level,
                            'saldo_awal' => $a, 
                            'grup' => $val->grup,
                            
                            'debit' => $deb == null ? 0 : $deb ,
                            'kredit' => $kre == null ? 0 : $kre,
                            'kredit_s' => $val->kredit_s == null ? 0 : $val->kredit_s, 
                            'debit_s' => $val->debit_s == null ? 0 : $val->debit_s,
                            'neraca_saldo' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2  ? $a - $deb + $kre : ($a + $deb) - $kre,
                            'neraca_s' => substr($val->coa, 0, 1) == 4 | substr($val->coa, 0, 1) == 2  ? $a - $deb + $kre + $val->debit_s - $val->kredit_s : ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            'closing' => $closing ,
                            'closed' => $closed,
                            'canclos' => $canclos,
                            'foot' => 0
                        ];
                    
            }
            
            $d = $inArray;
            $coasd = collect($inArray)->where('grup','LIKE', '%6%')->count();
            $saldan = SaldoDana::all();
            foreach($saldan as $i => $sd){
                $re = unserialize($sd->coa_receipt);
                $ex = unserialize($sd->coa_expend); 
                
                $ix = array_search($sd->coa_dana, array_column($inArray,'coa'));
                
                $saldeb = collect($inArray)->whereIn('coa', $re)->sum('neraca_s');
                $salkre = collect($inArray)->whereIn('coa', $ex)->sum('neraca_s');
                
                $salakh = $d[$ix]['saldo_awal'] + ($saldeb) - ($salkre);
                
                $debit_s = collect($inArray)->sum('debit_s');
                $kredit_s = collect($inArray)->sum('kredit_s');
                
                $inArray[$ix] = [
                                'root' => $d[$ix]['root'],
                                'id' => $d[$ix]['id'],
                                'parent' => $d[$ix]['parent'],
                                'coa' => $d[$ix]['coa'],
                                'nama_coa' => $d[$ix]['nama_coa'],
                                'id_parent' => $d[$ix]['id_parent'],
                                'level' => $d[$ix]['level'],
                                'saldo_awal' => $d[$ix]['saldo_awal'], 
                                'grup' => $d[$ix]['grup'],
                                
                                'debit' => $saldeb,
                                'kredit' => $salkre,
                                'kredit_s' => $d[$ix]['kredit_s'], 
                                'debit_s' => $d[$ix]['debit_s'],
                                'neraca_saldo' => $salakh,
                                'neraca_s' => $salakh,
                                'closing' => $d[$ix]['closing'] ,
                                'closed' => $d[$ix]['closed'],
                                'canclos' => $d[$ix]['canclos'],
                                'foot' => 0
                    ];
            }  
            
               
            foreach($inArray as $i => $val){
                
                $id_sal = 0;
                if($cek != null){
                    if($saldo3[$i]->coa == $val['coa']){
                        $id_sal = $saldo3[$i]->id_sal > 0 ? $saldo3[$i]->id_sal : 0;
                    }else{
                        $id_sal = 0;
                    }
                }
                
                if($val['canclos'] == 1){
                    $canclos = SaldoAw::whereDate('bulan', '>', $inbulan)
                                        ->where('coa', $val['coa'])
                                        ->update([ 
                                                    'closing' => 0,
                                                    // 'canclos' => 1,
                                                ]);
                }
               
                $input['coa'] = $val['coa'];
                $input['saldo_awal'] = $val['parent'] == 'n' ? $val['neraca_s'] : 0;
                $input['closing'] = 1;
                $input['canclos'] = 0;
                $input['tgl_closing'] = date('Y-m-d');
                $input['bulan'] = $inbulan;
            
                if($cek != null && $id_sal > 0) {
                    SaldoAw::where('id',$id_sal)->update($input);
                }else{
                    SaldoAw::create($input);
                }
                
            }
            
        return response()->json(['success' => 'Data Added successfully.']); 
        
    }
    
}