<?php 

namespace App\Exports;

use App\Models\Jurnal;
use App\Models\COA;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JurnalExport implements FromView
{
    use Exportable;
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function  view(): View
    {
        $request  = $this->request ;
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
                        'crt' => $rj_grup != '' ? null : $iya[$i]->crt,
                        'ids' => $rj_grup != '' ? null : $iya[$i]->ids,
                        'urut' => $rj_grup != '' ? null : $iya[$i]->urut,
                        'jumlahs' => $iya[$i]->debit - $iya[$i]->kredit,
                        'tanggal' => $iya[$i]->tgjur,
                        'nama_coa' => $iya[$i]->nama_coa,
                        'debit' => $iya[$i]->debit,
                        'via_jurnal' => $rj_grup != '' ? null : $output,
                        'coa_debet' => $iya[$i]->coa,
                        'kredit' => $iya[$i]->kredit,
                        'id_transaksi' => $rj_grup != '' ? null : $iya[$i]->id_transaksi,
                        'ket_penerimaan' => $rj_grup != '' ? null : $iya[$i]->ket_penerimaan,
                    ];
                }
                
                $debit = 0;
                $kredit = 0;
                $jumlah= 0;
                foreach($iya as $o){
                    $debit += $o->debit;
                    $kredit += $o->kredit;
                }
                
                $jumlah = $debit - $kredit;
                
                    $k[count($iya)] = [
                        'crt' => null,
                        'ids' => null,
                        'urut' => null,
                        'jumlahs' => null,
                        'tanggal' => null,
                        'nama_coa' => 'Total',
                        'debit' => $debit,
                        'via_jurnal' => null,
                        'coa_debet' => null,
                        'kredit' => $kredit,
                        'id_transaksi' => null,
                        'ket_penerimaan' => $jumlah,
                    ];       
            
        return view('ekspor.jurnalexport',[
            'grup' => $rj_grup,
            'data' => $k,
            'priode' => $request->prd == '0' ? "Periode Tanggal " . $dari . 'Sampai ' . $sampai : ($request->prd == '1' ? "Periode Bulan " . $b.'-'.$t :"Periode Tahun " .$t),
            'company' => DB::table('company')->selectRaw('name')->where('id_com', Auth::user()->id_com)->first()
        ]);
        
    }
}
