<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Models\SaldoDana;
use App\Models\SaldoAw;
use App\Models\COA;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
class TrialBalanceExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        
        
        $ngecek = 0; 
        $lev = COA::select('level')->distinct()->get();
        
            $inArray = [];
            if ($request->fil == '') {
            
            $inper  = $request->inper == '' ? 'b' : $request->inper;
            
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
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                        ->whereRaw("$tgtran AND approval = 1 AND via_input != 'mutasi' AND jumlah > 0")
                        ->unionAll(DB::table('pengeluaran')
                                ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                                ->whereRaw("$tgpeng AND acc = 1"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                                ->whereRaw("$tgjurn AND acc = 1"))
                        ->unionAll(DB::table('transaksi')
                                ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                                ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                                ->whereRaw("$tgtran AND transaksi.approval = 1 AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND transaksi.jumlah > 0")
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
                                // ->whereMonth('sub.tanggal', $bulan)
                                // ->whereYear('sub.tanggal', $tahun)
                                // ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
            
            $saldox = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
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
                            'saldo_awal' => $a, 
                            'grup' => $val->grup,
                            
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
                            'saldo_awal' => '', 
                            'grup' => '',
                            
                            'debit' => '' ,
                            'kredit' => '',
                            'kredit_s' => '',
                            'debit_s' => '',
                            'neraca_saldo' => '',
                            'neraca_s' => '',
                            'closing' => '',
                            'closed' => '',
                            'foot' => 2
                ];
                
            $ass = collect($inArray)->where('coa', '100.00.000.000')->first();
            $kwj = collect($inArray)->where('coa', '200.00.000.000')->first();
            
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
            
            // if($request->lvl == '' && $request->coa == '' && $request->grup == ''){
            // $inArray[$con+1] = [
            //                 'root' => max($idcoa)+2,
            //                 'id' => max($idcoa)+2,
            //                 'parent' => '',
            //                 'coa' => '',
            //                 'nama_coa' => 'Saldo Awal [Asset Vs (Kewajiban+SD)]',
            //                 'id_parent' => '',
            //                 'level' => '',
            //                 'saldo_awal' => '', 
            //                 'grup' => '',
                            
            //                 'debit' => $ass['saldo_awal'],
            //                 'kredit' => $kwj['saldo_awal'] + $sdf['saldo_awal'],
            //                 'kredit_s' => '',
            //                 'debit_s' => '',
            //                 'neraca_saldo' => '',
            //                 'neraca_s' => '',
            //                 'closing' => '',
            //                 'closed' => '',
            //                 'foot' => 1
            //     ];
            
            // $inArray[$con+2] = [
            //                 'root' => max($idcoa)+3,
            //                 'id' => max($idcoa)+3,
            //                 'parent' => '',
            //                 'coa' => '',
            //                 'nama_coa' => 'Mutasi [Debet Vs Kredit]',
            //                 'id_parent' => '',
            //                 'level' => '',
            //                 'saldo_awal' => '', 
            //                 'grup' => '',
                            
            //                 'debit' => $ass['debit'] + $salkre,
            //                 'kredit' => $ass['kredit'] + $saldeb,
            //                 'kredit_s' => '',
            //                 'debit_s' => '',
            //                 'neraca_saldo' => '',
            //                 'neraca_s' => '',
            //                 'closing' => '',
            //                 'closed' => '',
            //                 'foot' => 1
            //     ];
                
            // $inArray[$con+3] = [
            //                 'root' => max($idcoa)+4,
            //                 'id' => max($idcoa)+4,
            //                 'parent' => '',
            //                 'coa' => '',
            //                 'nama_coa' => 'Penyesuaian [Debet Vs Kredit]',
            //                 'id_parent' => 10000,
            //                 'level' => '',
            //                 'saldo_awal' => '', 
            //                 'grup' => '',
                            
            //                 'debit' => $debit_s,
            //                 'kredit' => $kredit_s,
            //                 'kredit_s' => '',
            //                 'debit_s' => '',
            //                 'neraca_saldo' => '',
            //                 'neraca_s' => '',
            //                 'closing' => '',
            //                 'closed' => '',
            //                 'foot' => 1
            //     ];
                
            // $inArray[$con+4] = [
            //                 'root' => max($idcoa)+5,
            //                 'id' => max($idcoa)+5,
            //                 'parent' => '',
            //                 'coa' => '',
            //                 'nama_coa' => 'Saldo Akhir [Asset Vs (Kewajiban+SD)]',
            //                 'id_parent' => '',
            //                 'level' => '',
            //                 'saldo_awal' => '', 
            //                 'grup' => '',
                            
            //                 'debit' => $ass['neraca_s'],
            //                 'kredit' => $kwj['neraca_s'] + $salakh,
            //                 'kredit_s' => '',
            //                 'debit_s' => '',
            //                 'neraca_saldo' => '',
            //                 'neraca_s' => '',
            //                 'closing' => '',
            //                 'closed' => '',
            //                 'foot' => 1
            //     ];
            // }
            // function filter($person) {
            //     return $person->parent = $request->coa;
            // }
            
            // filter di PHP
                $filRay = array_filter($inArray, function ($p) use ($request) {
                    $grup = explode(",",$p['grup']);
                    $fillvl = $request->lvl == '' ? $p['level'] != 'haha' : $p['level'] == $request->lvl;
                    $filcoa = $request->coa == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->coa;
                    $filgrup = $request->grup == '' ? $p['grup'] != 'haha' : array_intersect($grup, $request->grup);
                    return $fillvl && $filcoa && $filgrup;
                });
                
                $inArray = array_values($filRay);
                
                
                $arid = array_column($inArray, 'id');
                
    
                foreach ($inArray as $key => $obj) {
                    if (!in_array($obj['id_parent'], $arid)) {
                        $inArray[$key]['id_parent'] = '';
                    }
                }
            }
            return view('ekspor.trialbalance',[
                'data' => $inArray,
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
            ]);
    }    
    
}
