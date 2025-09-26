<?php
namespace App\Exports;

use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Models\SaldoDana;

class DetailTrialBalanceExport implements FromView
{
    
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View { 
        
        $request = $this->request;
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
            if($request->dataDebit != ''){
                $pengeluaran = DB::table('pengeluaran')
                                ->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$t' as t, '$b' as b, nominal as total")
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
                                ->whereRaw("$tahunz AND acc >= 1");
                $transaksi = DB::table('transaksi')
                    ->selectRaw("transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$t' as t, '$b' as b, transaksi.jumlah as total")
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
                    ->whereRaw("$tahuns AND approval >= 1 AND via_input != 'mutasi' AND jumlah > 0");
                    
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
                        ->selectRaw("pengeluaran.tgl, coa_debet, coa_kredit,  keterangan, '$t' as t, '$b' as b, nominal as total")
                        ->whereRaw("$tahunz AND acc >= 1");
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
                    ->selectRaw("transaksi.tanggal, coa_debet, coa_kredit, transaksi.ket_penerimaan, '$t' as t, '$b' as b, transaksi.jumlah as total")
                    ->unionAll($pengeluaran)
                    ->whereRaw("$tahuns AND approval >= 1 AND via_input != 'mutasi' AND jumlah > 0");
            }
            
            $data = $transaksi->get();
            // return $data;
            return view('ekspor.detailtrialbalanceexport',[
                'data' => $data,
                'nama_coa' => $request->nama_coa,
                'b' => $b,
                't' => $t,
                'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
            ]);
    }
}