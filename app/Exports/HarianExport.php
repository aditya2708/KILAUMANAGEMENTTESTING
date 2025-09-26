<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Models\COA;
use App\Models\SaldoAw;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
class HarianExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
            $r_bayar    = $request->pembayaran != '' ? $request->pembayaran : [];
            $r_kan      = $request->unit;
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
            
            if($dp_only != 1){
                $dat[0]  = [
                            'no' => null,
                            'tanggal' => null,
                            'coa' => null,
                            'jentran' => 'Saldo Awal',
                            'debit' => null,
                            'kredit' => null,
                            'id_tran' => null,
                            'ket' =>  null,
                            'saldo' => $dp_only == 1 || $r_kan == 'all_kan' ? 0 : $salwal,
                            'id' => null,
                            'via' => null,
                            'dp' => null,
                            'status' => null,
                        ];
            }
            
            for($i=0; $i < count($trans); $i++){
               
                    if($i == 0){
                        $sal[$i] = ($dp_only == 1 ? 0 : $salwal) + $trans[$i]->debit - $trans[$i]->kredit;
                    }else{
                        $sal[$i] = $sal[$i-1] + $trans[$i]->debit - $trans[$i]->kredit;
                    }
                
                if($r_grup == null){
                    $ket = $trans[$i]->dp == 1 ? '#DP '.$trans[$i]->ket : $trans[$i]->ket;
                }
                
                // if($request->tab == 'tab1'){
                    $totdeb += $trans[$i]->debit;
                    $totkre += $trans[$i]->kredit;
                // }
                $dat[$dp_only == 1 ? $i : $i + 1]  = [
                            'no' => $i + 1,
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
            
                $dat[$dp_only == 1 ? count($trans) : count($trans) + 1]  = [
                            'no' => null,
                            'tanggal' => null,
                            'coa' => null,
                            'jentran' => 'Total',
                            'debit' => $totdeb,
                            'kredit' => $totkre,
                            'id_tran' => null,
                            'ket' =>  null,
                            'saldo' => $dp_only == 1 || $r_kan == 'all_kan' ? 0 : $salwal + $totdeb - $totkre,
                            'id' => null,
                            'via' => null,
                            'dp' => null,
                            'status' => null,
                        ];
                        
            if($r_plhtgl == 0){
                $periode = ' Tanggal '.$awal . ' s/d ' . $akhir;
           }else if($r_plhtgl == 1){
                    $tg1     = explode('-', $bln);
                    $thn1    = date($tg1[0]);
                    $bln1    = date($tg1[1]);
                    $tg2     = explode('-', $tobln);
                    $thn2    = date($tg2[0]);
                    $bln2    = date($tg2[1]);
                $periode = $request->rmonth != null ? ' Bulan '.$bln1.'-'. $thn1.' s/d '.$bln2.'-'. $thn2 : ' Bulan '.$bln1.'-'. $thn1;
           }else{
                $periode = ' Tahun '.$r_thn;
           }
           
           if($r_kan == 'all_kan'){
                $inbuku = 0; 
                $judul  = 'Semua Kantor';
            }else{
                if($r_buku == 'all_kas'){
                    $inbuku = 0; 
                    $judul  = 'Semua Kas';
                }else if($r_buku == 'all_bank'){
                    $inbuku = 0; 
                    $judul  = 'Semua Rekening';
                }else{
                    $inbuku = 1; 
                    $judul  = COA::selectRaw("coa, nama_coa")->where('coa',$r_buku)->first();
                }
            }
            
                return view('ekspor.harianexport',[
                    'data' => $dat,
                    // 'aww' => $waa,
                    // 'saldo' => $saldo,
                    // 'debit' => $debit,
                    // 'kredit' => $kredit,
                    // 'plhtgl' => $plhtgl,
                    'inbuku' => $inbuku,
                    'judul' => $judul,
                    'priode' => $periode,
                    'company' => DB::table('company')->selectRaw('name')->where('id_com', Auth::user()->id_com)->first()
                ]);
            // }
            
        
        
            
            //  $waa = DB::table("saldo_awal")->selectRaw('saldo_awal')->whereRaw(" $coabuk AND YEAR(bulan) = $currentYear")->first();
            //  $iya = $transaksi->get();
            //  $k = [];
            //  $jml = [];
            //     for($i= 0; $i < count($iya); $i++){
            //         if($plhtgl == 0){
            //             if($i == 0){
            //                 $jml[$i] = ($saldo+$debit-$kredit) + $iya[$i]->debit - $iya[$i]->kredit;
            //             }else{
            //                 $jml[$i] = $jml[$i-1] + $iya[$i]->debit - $iya[$i]->kredit;
            //             }
            //         }else{
            //             if($i == 0){
            //                 $jml[$i] = $waa->saldo_awal + $iya[$i]->debit - $iya[$i]->kredit;
            //             }else{
            //                 $jml[$i] = $jml[$i-1] + $iya[$i]->debit - $iya[$i]->kredit;
            //             }
            //         }
                    
                    
            //         $k[] = [
            //             'jumlahs' =>  $iya[$i]->dp ? 0 : $jml[$i],
            //             'tanggal' => $iya[$i]->tanggal,
            //             'nama_coa' => $iya[$i]->nama_coa,
            //             'debit' => $iya[$i]->debit,
            //             'kredit' => $iya[$i]->kredit,
            //             'id_transaksi' => $grup != null ? null : $iya[$i]->id_transaksi,
            //             'ket_penerimaan' =>  $grup != null ? null : $iya[$i]->ket_penerimaan,
            //             'saldo' =>  $grup != null ? null :  $iya[$i]->saldo,
            //             'id' => $grup != null ? null :  $iya[$i]->id,
            //             'via' => $grup != null ? null : $iya[$i]->via,
            //             'coa' =>$iya[$i]->coa_kredit,
            //             'dp' =>$grup != null ? null : $iya[$i]->dp,
            //             'status' => $grup != null ? null :$iya[$i]->status,
            //         ];
            //     }
            //     return view('ekspor.harianexport',[
            //         'data' => $k,
            //         'aww' => $waa,
            //         'saldo' => $saldo,
            //         'debit' => $debit,
            //         'kredit' => $kredit,
            //         'plhtgl' => $plhtgl,
            //         'judul' =>  COA::selectRaw("coa, nama_coa")->whereRaw(" $coabuk")->first(),
            //         'priode' => $plhtgl == 0 ? $dari . ' s/d ' . $sampai : 'Bulan ' . $b . ' Tahun ' . $t,
            //         'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
            //     ]);

            
    }    
    
}
