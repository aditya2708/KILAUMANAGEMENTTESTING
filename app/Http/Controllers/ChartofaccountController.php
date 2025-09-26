<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantor;
use App\Models\Prog;
use App\Models\Karyawan;
use App\Models\GrupCOA;
use App\Models\Anggaran;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\SaldoDana;
use App\Models\SaldoAw;
use App\Models\COA;
use Auth;
use DataTables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use App\Exports\COAExport;
use Excel;



class ChartofaccountController extends Controller
{
    public function index(Request $request){
        $group = GrupCOA::all();
        $coa = COA::select('level')->distinct()->get();
        // $coa_parent= COA::where('parent', 'y')->get();
        return view ('fins.coa', compact('group','coa'));

    }
    
    public function coa_kun(Request $request){
        // $parent = $request->parent == '' ? 'parent IS NOT NULL' : "parent = '$request->parent'";
        // $level = $request->level == '' ? 'level IS NOT NULL' : "level = '$request->level'";
        // $aktif = $request->aktif == '' ? "aktif IS NOT NULL" : "aktif = '$request->aktif'";
        
        // $grupCoa = is_array($request->grup) ? implode(',', $request->grup) : '';
        // $grup = $grupCoa !== '' ? "grup LIKE '%$grupCoa%'" : "grup IS NOT NULL";
        // // $aktif = $request->aktif == '' ? "aktif IS NOT NULL" : "aktif = '$request->aktif'";
        // $data = COA::whereRaw("$parent AND $aktif AND $grup")->get();
       
        $data = COA::all();
        
        foreach($data as $key => $val){
            $inArray[] = [
                "id" => $val->id,
                "coa" => $val->coa,
                "nama_coa" => $val->nama_coa,
                "coa_parent" => $val->coa_parent,
                "id_parent" => $val->id_parent,
                "level" => $val->level,
                "id_kantor" => $val->id_kantor,
                "id_jabatan" => $val->id_jabatan,
                "grup" => $val->grup,
                "parent" => $val->parent,
                "aktif" => $val->aktif,
            ];
        }
        
        $filRay = array_filter($inArray, function ($p) use ($request) {
            $grup = explode(",",$p['grup']);
            $fillvl = $request->level == '' ? $p['level'] != 'haha' : $p['level'] == $request->level;
            $filcoa = $request->parent == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->parent;
            $filakt = $request->aktif == '' ? $p['aktif'] != 'haha' : $p['aktif'] == $request->aktif;
            $filgrup = $request->grup == '' ? $p['grup'] != 'haha' : array_intersect($grup, $request->grup);
            return $fillvl && $filcoa && $filgrup && $filakt;
        });
        
        $inArray = array_values($filRay);
        
        
        $arid = array_column($inArray, 'id');
        
    
        foreach ($inArray as $key => $obj) {
            if (!in_array($obj['id_parent'], $arid)) {
                $inArray[$key]['id_parent'] = '';
            }
        }
        
        return $inArray;
    }
    
    public function coaExport(Request $req){
        
        
        $date = date('d-m-Y');
        if($req->exp == 'xls'){
            $r = Excel::download(new COAExport($req->level, $req->parent , $req->aktif , $req->grup), 'COA.xlsx');
        }else{
            $r = Excel::download(new COAExport($req->level, $req->parent , $req->aktif , $req->grup), 'COA.csv');
        }
        ob_end_clean();
        return $r;
    }
    
    public function getcoa(){
        $coa_parent= COA::orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .' '.$val->nama_coa.'-'.$val->level,
                "coa" => $val->coa,
                "id" => $val->coa,
                
                // "id" => $val->id,
                "grup" => $val->grup,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoalagi(){
        $coa_parent= COA::orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .' '.$val->nama_coa.'-'.$val->level,
                "coa" => $val->coa,
                "id" => $val->coa,
                // "id" => $val->id,
                "grup" => $val->grup,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoakondisi(){
        $duar = Auth::user()->id_kantor;
        $coa_parent= COA::orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .' '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                // "id" => $val->id,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoapenyaluran(){
        $coa_parent= COA::orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .''.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoapndp(){
        $coa_parent= COA::where('grup', 'like', '%7%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .' '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoapngdp(){
        $coa_parent= COA::where('grup', 'like', '%8%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .''.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoapenerimaan(){
        $coa_parent= COA::where('grup', 'like', '%1%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoapersediaan(){
        $coa_parent= COA::where('grup', 'like', '%5%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoasumberdana(){
        $coa_parent= COA::where('grup', 'like', '%6%')->where('parent', 'like', '%n%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
        public function data_anggaran(Request $request){
            
        $a = $request->tgl == '' ? Carbon::now()->format('Y-m') : date('Y-m', strtotime($request->tgl));
        $wus = $request->p == '' ? "coa.coa = ''" : "coa.coa = '$request->p'";
        // $wus = $request->p == '' ? "coa = $request->p" : "coa != ''";  
        $kntr = $request->kntr != '' ? "kantor = '$request->kntr'" : "kantor != ''";  
        $din = "anggaran.acc = '1'";
        
        
        
         $coa_parent= COA::selectRaw("anggaran.id_anggaran,anggaran.tanggal,anggaran.nama_akun,anggaran.coa,anggaran.uang_pengeluaran,anggaran.kantor,anggaran.keterangan,coa.coa,coa.nama_coa,coa.parent,anggaran.acc,
        DATE_FORMAT(anggaran.created_at,'%Y-%m-%d')as tglbuat,anggaran.anggaran , anggaran.tambahan , anggaran.relokasi")
        ->leftjoin('anggaran', 'anggaran.coa', '=', 'coa.coa')
        ->whereRaw(" $wus AND $kntr AND $din AND DATE_FORMAT(anggaran.tanggal,'%Y-%m') ='$a'")
        ->orderBy('coa.coa', 'ASC')
        ->get();
        
        // if(Auth::user()->name == 'Management'){
        //     return $coa_parent;
        // }
        
        
        // return $coa_parent;
        
          $h1 = [];
      foreach($coa_parent as $key => $val){
           $h1[]= [
                "text" => $val->id_anggaran.' - '.$val->anggaran.' - '.$val->uang_pengeluaran.' - '.$val->relokasi.' - '.$val->tambahan.' - '.$val->tanggal,
                "id_anggaran" => $val->id_anggaran,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "kantor" => $val->kantor,
                "nama_coa" => $val->nama_coa,
                'tanggal' => $val->tanggal,
                'created' => $val->tglbuat,
                'keterangan' => $val->keterangan,
                'totsem' => $val->anggaran + $val->tambahan + $val->relokasi,
                'total' => $val->anggaran + $val->tambahan + $val->relokasi - $val->uang_pengeluaran,
                'uang_pengeluaran' => $val->uang_pengeluaran,
                'acc' => $val->acc,
            ]; 
        }
        return response()->json($h1) ;
    }
    
    
    
   public function getcoaamil(Request $request){
        $kntr = $request->kntr != '' ? "kantor = $request->kntr" : "kantor != ''";  
        $coa_parent= COA::whereRaw("grup LIKE '%9%' OR coa.coa LIKE '%503%' OR coa.grup LIKE '%2%'")
        ->orderBy('coa.coa', 'ASC')
        ->get();
        
        $c = Carbon::now()->format('m-Y');
        $b = Carbon::createFromFormat('m-Y', $c)->format('m');
        $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
        
        $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%403%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
        $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%503%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        
        
        $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
        $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        foreach($coa_parent as $key => $val){
          
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "kantor" => $val->kantor,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->konak,
                'tanggal' => $val->tanggal,
                'total' => $val->tot,
                'acc' => $val->acc,
                'debit' => $debit,
                'kredit' => $kredit
            ]; 

        }
        // return($coa_parent);
        return response()->json($h1);
    }
    
    public function getcoaapbn(Request $request){
        $kntr = $request->kntr != '' ? "kantor = $request->kntr" : "kantor != ''";  
        $coa_parent= COA::whereRaw("(grup LIKE '%9%' OR coa.coa LIKE '%505%') AND coa.grup LIKE '%2%'")
        ->orderBy('coa.coa', 'ASC')
        ->get();
            foreach($coa_parent as $key => $val){
            $h1[] = [
                 "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                 "kantor" => $val->kantor,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new,
                'tanggal' => $val->tanggal,
                'total' => $val->tot,
                'acc' => $val->acc,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoahibah(Request $request){
        $coa_parent= COA::whereRaw("(coa.coa LIKE '%504%' ) AND coa.grup LIKE '%2%'")
        ->orderBy('coa', 'ASC')
        ->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoainfaqt(Request $request){
        $coa_parent= COA::whereRaw("( coa.coa LIKE '%502.02%' OR coa.coa LIKE '%501.01.002%') AND coa.grup LIKE '%2%'")
        ->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoainfaqtd(Request $request){
        $coa_parent= COA::whereRaw("( coa.coa LIKE '%502.03%' OR coa.coa LIKE '%501.01.001%' ) AND coa.grup LIKE '%2%'")
            ->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoawakaf(Request $request){
        $coa_parent= COA::whereRaw("coa.coa LIKE '%507%' AND coa.grup LIKE '%2%'")
        ->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoadilarang(Request $request){
        $coa_parent= COA::whereRaw("( coa.coa LIKE '%506%') AND coa.grup LIKE '%2%'")
         ->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoazkt(Request $request){
        $coa_parent= COA::whereRaw("( coa.coa LIKE '%501%') AND coa.grup LIKE '%2%'")
        ->orderBy('coa', 'ASC')->get();
        $transaksi = Transaksi::selectRaw("SUM(jumlah) as total")->whereRaw("coa_debet LIKE '%401%' ")->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
                'saldo' => $val->saldo_new,
                'saldo_t' => $transaksi
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoamutasipengirim(Request $request){
        $kots = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kots)->first();
        $mm = SaldoDana::all();
        
        if($request->ajax()){
            $unit = $request->unit;
            if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
                $coa_parent= COA::where('parent','n')->where('id_kantor',$unit)
                                ->where(function($query) {
                                    $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
                                })->orderBy('coa', 'ASC')->get();
                
            }else if(Auth::user()->keuangan == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
                if($k == null){
                    $coa_parent= COA::where('parent','n')->where('id_kantor',Auth::user()->id_kantor)
                                ->where(function($query) {
                                    $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
                                })->orderBy('coa', 'ASC')->get();
                                
                }else{
                    if($unit == ''){
                        $coa_parent= COA::where('parent','n')->where('id_kantor',Auth::user()->id_kantor)
                                    ->where(function($query) {
                                        if(isset($unit)){
                                            $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');  
                                        }else{
                                            $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
                                        }
                                    })->orderBy('coa', 'ASC')->get();
                    }else{
                        $coa_parent= COA::where('parent','n')->where('id_kantor',$unit)
                                    ->where(function($query) {
                                        if(isset($unit)){
                                            $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');  
                                        }else{
                                            $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
                                        }
                                    })->orderBy('coa', 'ASC')->get();
                    }
                }
            }
        
        }
        
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->nama_coa.'-'.$val->grup,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
    
    public function getcoamutasipenerima(){
        $coa_parent= COA::where('parent','n')->where(function($query) {
            $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
        })->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
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
    
    public function coba(){
        // $inArray = array(
        //     array('ID' => '1', 'parentcat_ID' => '0'),
        //     array('ID' => '2', 'parentcat_ID' => '0'),
        //     array('ID' => '6', 'parentcat_ID' => '1'),  
        //     array('ID' => '7', 'parentcat_ID' => '1'),
        //     array('ID' => '8', 'parentcat_ID' => '6'),          
        //     array('ID' => '9', 'parentcat_ID' => '1'),  
        //     array('ID' => '13', 'parentcat_ID' => '7'),
        //     array('ID' => '14', 'parentcat_ID' => '8'),     
        // );
        
        $data = COA::all();
        $inArray = [];
        foreach($data as $val){
            $dot = COA::where('id', $val->id_parent)->first();
            $inArray[]=[
                'id' => $val->id,
                'coa' => $val->coa,
                'nama_coa' => $val->nama_coa,
                'id_parent' => $val->id_parent,
                'coa_parent' => $val->id_parent != 0 ? $dot->coa : 0,
                'level' => $val->level,
            ];
        }
        
        // dd($inArray);
        
        
        $outArray = array();
        $this->makeParentChildRelations($inArray, $outArray);
        // dd($outArray);
        // print_r($outArray);
        return response()->json(['data' => $outArray]);
    }
    
    public function store(Request $request){
        // dd($request);
        // $input = $request->all();
        $input = new COA; 
        $group = implode(",",$request->group);
        // dd($request->multiple);
        if($request->id_parent != null){
            $cek = COA::where('coa_parent', $request->id_parent)->get();
            $cek1 = COA::where('coa', $request->id_parent)->first();
            // dd($cek); 
            // $tot = COA::where('id_parent', $request->coa_parent)->count();
            // dd($request->all());
            if(count($cek) == 0){
                $b = explode("." , $cek1->coa);
                // dd($b);
                if($request->level == 1){
                    $dp = $b[0] + 1;
                    $id = sprintf("%03s", $dp).".".$b[1].".".$b[2].".".$b[3];
                }
                if($request->level == 2){
                    $dp = $b[1] + 1;
                    $id = $b[0].".".sprintf("%02s", $dp).".".$b[2].".".$b[3];
                }
                if($request->level == 3){
                    $dp = $b[2] + 1;
                    $id = $b[0].".".$b[1].".".sprintf("%03s", $dp).".".$b[3];
                }
                if($request->level == 4){
                    $dp = $b[3] + 1;
                    $id = $b[0].".".$b[1].".".$b[2].".".sprintf("%03s", $dp);
                }
                
                
            }else{
                foreach($cek as $val){
                    $b = explode("." , $val->coa);
                    if($val->level == 1){
                        $dp = $b[0] + 1;
                        $id = sprintf("%03s", $dp).".".$b[1].".".$b[2].".".$b[3];
                    }
                    if($val->level == 2){
                        $dp = $b[1] + 1;
                        $id = $b[0].".".sprintf("%02s", $dp).".".$b[2].".".$b[3];
                    }
                    if($val->level == 3){
                        $dp = $b[2] + 1;
                        $id = $b[0].".".$b[1].".".sprintf("%03s", $dp).".".$b[3];
                    }
                    if($val->level == 4){
                        $dp = $b[3] + 1;
                        $id = $b[0].".".$b[1].".".$b[2].".".sprintf("%03s", $dp);
                    }
                }
            }
            $input->id_parent = $cek1->id;
            $input->coa_parent = $cek1->coa;
        }else{
            $cek = COA::where('id_parent', 0)->count();
            $dp = $cek+1;
            $id = $dp."00.00.000.000";
            $input->coa_parent = "0";
            $input->id_parent = "0";
        }
        
        
        
        // dd($id);
        
        
        $input->nama_coa = $request->nama_coa;
        $input->level = $request->level;
        $input->aktif = $request->aktif;
        $input->parent = $request->parent;
        $input->coa = $id;
        $input->grup = $group;
        // dd($input);
        $input->save();
        // dd($input);
        // // $input['tj_daerah'] = $request->tj_daerah != '' ? preg_replace("/[^0-9]/", "", $request->tj_daerah) : 0;
        // // Kantor::create($input);
        // // return back();
        // COA::create($input);
        // // \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Kantor '.$request->unit);

        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function edit($id){
        if(request()->ajax())
        {
            $data = COA::findOrFail($id);
            $tr = Transaksi::where('coa_debet', $data->coa)->orWhere('coa_kredit', $data->coa)->first();
            $pg = Pengeluaran::where('coa_debet', $data->coa)->orWhere('coa_kredit', $data->coa)->first();
            if($tr != null || $pg != null || $data->parent == 'y'){
                $data['kondat'] = 1;
            }else{
                $data['kondat'] = 0;
            }
            
            return response()->json(['result' => $data]);
        }
    }
    
    public function update(Request $request) {
        
        $group = implode(",",$request->group);
        $cek1 = COA::where('coa', $request->id_parent)->first();
        // dd($request->id_parent);
        // $form_data = array(
        //     // 'coa' => $request->coa,
        //     'nama_coa' => $request->nama_coa,
        //     'id_parent' => $request->id_parent,
        //     'level' => $request->level,
        //     'parent' => $request->parent,
        //     'aktif' => $request->aktif,
        //     'coa_parent' => $cek1->coa,
        //     'grup' => $group,
        // );
        
        // COA::where('id','=',$request->hidden_id)->update($form_data);
        
        $data = COA::findOrFail($request->hidden_id);
        $data->nama_coa = $request->nama_coa;
        $data->level = $request->level;
        $data->parent = $request->parent;
        $data->aktif = $request->aktif;
        $data->grup = $group;
        
        if($request->id_parent != NULL){
            $data->id_parent = $cek1->id;
            $data->coa_parent = $cek1->coa;
        }
        
        if($request->id_parent != NULL && $request->kondat == 0){
            $cek = COA::where('coa', $request->id_parent)->get();
            if(count($cek) == 0){
                $b = explode("." , $cek1->coa);
                if($request->level == 1){
                    $dp = $b[0] + 1;
                    $id = sprintf("%03s", $dp).".".$b[1].".".$b[2].".".$b[3];
                }
                if($request->level == 2){
                    $dp = $b[1] + 1;
                    $id = $b[0].".".sprintf("%02s", $dp).".".$b[2].".".$b[3];
                }
                if($request->level == 3){
                    $dp = $b[2] + 1;
                    $id = $b[0].".".$b[1].".".sprintf("%03s", $dp).".".$b[3];
                }
                if($request->level == 4){
                    $dp = $b[3] + 1;
                    $id = $b[0].".".$b[1].".".$b[2].".".sprintf("%03s", $dp);
                }
                
                
            }else{
                foreach($cek as $val){
                    $b = explode("." , $val->coa);
                    if($val->level == 1){
                        $dp = $b[0] + 1;
                        $id = sprintf("%03s", $dp).".".$b[1].".".$b[2].".".$b[3];
                    }
                    if($val->level == 2){
                        $dp = $b[1] + 1;
                        $id = $b[0].".".sprintf("%02s", $dp).".".$b[2].".".$b[3];
                    }
                    if($val->level == 3){
                        $dp = $b[2] + 1;
                        $id = $b[0].".".$b[1].".".sprintf("%03s", $dp).".".$b[3];
                    }
                    if($val->level == 4){
                        $dp = $b[3] + 1;
                        $id = $b[0].".".$b[1].".".$b[2].".".sprintf("%03s", $dp);
                    }
                }
            }
            
            $data->coa = $id;
        }
        
        $data->update();
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data COA '.$request->hidden_id);
        
        return response()->json(['success' => 'Data is successfully updated']);
      
    }
    
    public function destroy($id) {
        $coa = COA::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $coa->delete();
        // return back();
    }
    
    public function cari_saldo(Request $request){
        
        $c = Carbon::now()->format('m-Y');
        $b = Carbon::createFromFormat('m-Y', $c)->format('m');
        $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
        
        $p  = SaldoDana::join('coa', 'coa.coa','=','saldo_dana.coa_dana')->get();
        
        $sip = [];
        foreach($p as $xx){
             $sip[] = [ 
                'nama_coa' => ' '.$xx->nama_coa, 
                'coa' => $xx->coa,
                'ca' => $xx->coa_expend,
                'cr' => $xx->coa_receipt
            ]; 
        }
        
        if($request->level == " Dana Amil"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%403%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%503%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana Zakat"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%401%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana Infaq / Sedekah Terikat"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.01%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501.01.002%' OR coa_kredit LIKE '%502.02%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana Hibah"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%404%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%504%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana Wakaf"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%407%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%507%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana APBN/APBD"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%405%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%505%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else if($request->level == " Dana Yang Dilarang Syariah"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%406%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%506%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }else{
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        }
        
        
        $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
        $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                        
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
        
        $saldo = DB::table('b as t')
                ->selectRaw("root, t.coa, t.id_parent, t.id, t.nama_coa,  SUM(t.konak) as saldo")
                ->withRecursiveExpression('b', $query)
                
                ->groupBy('root')
                ->get(); 
                
        $a = [];
            
        foreach($saldo as $t){
            $a[] = [
                    'coa' => $t->coa,
                    'id' => $t->id,
                    'saldo' => $t->saldo
                ];
        }
        
        $key_title = [];
        
        foreach ($a as $key => $data) {
            if ($data['coa'] == $request->coa) {
                $sald = $data['saldo'] == null ? 0 : $data['saldo'];
                $key_title = [
                    'saldo_b' => $sald,
                    'kredit' => $kredit,
                    'debit' => $debit,
                    'saldo' => ($debit + $sald) - $kredit
                ];
            }
        }
        
        
        return $key_title;
    }

   
    // public function cari_saldox_diroh(Request $request){
        
    //     $c = Carbon::now()->format('m-Y');
    //     $b = Carbon::createFromFormat('m-Y', $c)->format('m');
    //     $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
        
    //     $p  = SaldoDana::join('coa', 'coa.coa','=','saldo_dana.coa_dana')->get();
        
    //     $sip = [];
    //     foreach($p as $xx){
    //         $cr = unserialize($xx->coa_receipt);
    //         $ca = unserialize($xx->coa_expend);
            
    //          $sip[] = [ 
    //             'nama_coa' => ' '.$xx->nama_coa, 
    //             'coa' => $xx->coa,
    //             'ca' => $ca,
    //             // coa 400
    //             'cr' => $cr 
    //             // coa 500
    //         ]; 
    //     }
    //     // $awak = [];
    //     // // foreach($sip as $s){
    //     //     $awak =  preg_split("/./", $sip[0]['ca']);
    //     // // }
    //     //     // $awak = $ojo;
    //     $cilok = implode(" ", ($sip[0]['ca']));
     
    //     $eye = $sip[0]['coa'];
    //     $johit = (string)$cilok;
        
    //     $rr = preg_split("/\./", $eye);
        
        
        
    //     $cc = implode(" ", ($sip[0]['ca']));
    //     $dikit = (string)$cc;
    //     $canya = preg_split("/\./", $dikit);
        
        
    //     $coak = implode(" ", ($sip[0]['cr']));
    //     $dikit = (string)$coak;
    //     $crnya = preg_split("/\./", $dikit);
         
     
       
       
        
        
    //     if($request->level == " Dana Amil"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%400%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%500%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana Zakat"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%401%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana Infaq / Sedekah Terikat"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.01%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501.01.002%' OR coa_kredit LIKE '%502.02%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana Hibah"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%404%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%504%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana Wakaf"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%407%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%507%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana APBN/APBD"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%405%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%505%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else if($request->level == " Dana Yang Dilarang Syariah"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%406%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%506%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }else{
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //     }
        
        
    //     $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
    //     $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        
    //     $query = DB::table('coa as t1')
    //                 ->select('t1.*', 't1.id as root')
                        
    //                 ->unionAll(
    //                     DB::table('b as t0')
    //                         ->select('t3.*', 't0.root')
    //                         ->join('coa as t3', 't3.id_parent', '=', 't0.id')
    //                 );
        
    //     $saldo = DB::table('b as t')
    //             ->selectRaw("root, t.coa, t.id_parent, t.id, t.nama_coa,  SUM(t.konak) as saldo")
    //             ->withRecursiveExpression('b', $query)
                
    //             ->groupBy('root')
    //             ->get(); 
                
    //     $a = [];
            
    //     foreach($saldo as $t){
    //         $a[] = [
    //                 'coa' => $t->coa,
    //                 'id' => $t->id,
    //                 'saldo' => $t->saldo
    //             ];
    //     }
        
    //     $key_title = [];
        
    //     foreach ($a as $key => $data) {
    //         if ($data['coa'] == $request->coa) {
    //             $sald = $data['saldo'] == null ? 0 : $data['saldo'];
    //             $key_title = [
    //                 'saldo_b' => $sald,
    //                 'kredit' => $kredit,
    //                 'debit' => $debit,
    //                 'saldo' => ($debit + $sald) - $kredit
    //             ];
    //         }
    //     }
        
        
    //     return $key_title;
    // }
    
    public function cari_saldox(Request $request){
           
        // return($request)  ;
        
        $c = Carbon::now()->format('m-Y');
        $b = Carbon::createFromFormat('m-Y', $c)->format('m');
        $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
        
        $p  = SaldoDana::join('coa', 'coa.coa','=','saldo_dana.coa_dana')->where('coa.coa', $request->coa)->first();
        
        
        $sip = [];
        $ceer = [];
        $cee = [];
        
        $cr = unserialize($p->coa_receipt); //4
        $ce = unserialize($p->coa_expend); //5
        
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
        
        // coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND
        
        $ini_r = implode(', ', $ce_er);
        $ini_e = implode(', ', $ce_e);
        
    
        $transaksi1 = Transaksi::selectRaw("transaksi.jumlah")
                   
                    ->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND approval = 1")
                    ->whereIn(DB::raw('coa_kredit'), function ($query) use ($ce_er) {
                        foreach ($ce_er as $term1) {
                            $query->select(DB::raw('coa_kredit'))->from('transaksi')->orWhere(DB::raw('coa_kredit'), 'LIKE', '%' . $term1 . '%');
                        }
                    })
                    ->get();
                  
        $jum1 = 0;
        
        foreach($transaksi1 as $be){
            $jum1 += $be->jumlah;
        }
        
        $transaksi2 = Pengeluaran::selectRaw(" nominal as jumlah ")
                    ->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' AND acc = 1")
                    ->whereIn(DB::raw('coa_debet'), function ($query) use ($ce_e) {
                        foreach ($ce_e as $term2) {
                            $query->select(DB::raw('coa_debet'))->from('pengeluaran')->orWhere(DB::raw('coa_debet'), 'LIKE', '%' . $term2 . '%');
                        }
                    })
                    ->get();
                    
        $jum2 = 0;
        
        foreach($transaksi2 as $bi){
            $jum2 += $bi->jumlah;
        }
        
        $prog1= Prog::selectRaw("(transaksi.jumlah * transaksi.dp / 100) as dp1")
                    ->leftJoin('transaksi', 'prog.id_program', '=', 'transaksi.id_program')
                    ->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND pembayaran != 'noncash' AND approval = 1")
                    ->whereIn(DB::raw('coa1'), function ($query) use ($ce_er) {
                        foreach ($ce_er as $term1) {
                            $query->select(DB::raw('coa1'))->from('prog')->orWhere(DB::raw('coa1'), 'LIKE', '%' . $term1 . '%');
                        }
                    })
                    ->get();
        
        $prog2 = Prog::selectRaw("(transaksi.jumlah * transaksi.dp / 100) as dp2")
                    ->leftJoin('transaksi', 'prog.id_program', '=', 'transaksi.id_program')
                    ->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND pembayaran != 'noncash' AND via_input = 'transaksi' AND approval = 1 ")
                    ->whereIn(DB::raw('coa2'), function ($query) use ($ce_e) {
                        foreach ($ce_e as $term2) {
                            $query->select(DB::raw('coa2'))->from('prog')->orWhere(DB::raw('coa2'), 'LIKE', '%' . $term2 . '%');
                        }
                    })
                    ->get();
        
        $dp1 = 0;
        $dp2 = 0;
        
        foreach($prog1 as $pp1){
            $dp1 += $pp1->dp1;
        }
        
        foreach($prog2 as $pp2){
            $dp2 += $pp2->dp2;
        }
        
        $now = date('Y-m-d');
        $oho = date('Y-m-t', strtotime('-1 month', strtotime($now)));
        
        $bulan = date('m', strtotime($oho));
        $tahun = date('Y', strtotime($oho));
        $coa = SaldoAw::select('saldo_awal')->whereRaw("coa = '$request->coa' AND MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ");
        
        if(count($coa->get()) > 0 ){
            $y = $coa->first()->saldo_awal;
        }else{
            $y = 0;
        }
        
        // return($y);
        // return([($jum1+$dp1), ($jum2 + $dp2)]);
        
        
        return $y + ($jum1+$dp1) - ($jum2 + $dp2);
        // return( [$jum1, ($jum2+$dp) ]);
        
        // $transaksi3 = Transaksi::selectRaw("'coa_rev' as judul, '$b' as bulan, '$t' as tahun, '$ini_r' as coanya, prog.coa2, prog.coa1, (transaksi.jumlah * transaksi.dp / 100) as dp, transaksi.jumlah")
        //             ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
        //             ->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'mutasi'")
        //             ->whereIn(DB::raw('coa_kredit'), function ($query) use ($ce_er) {
        //                 foreach ($ce_er as $term11) {
        //                     $query->select(DB::raw('coa_kredit'))->from('transaksi')->orWhere(DB::raw('coa_kredit'), 'LIKE', '%' . $term11 . '%');
        //                 }
        //             })
        //             ->get();
                  
        // $jum3 = 0;  
        // foreach($transaksi3 as $bl){
        //     // $dp2 += $bi->dp;
        //     $jum3 += $bl->jumlah;
        // }
        // return($transaksi3);
        
        
                    
        
        
        // $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
        
        // $cuak[] = [$transaksi1, $transaksi2];
        
        
        
        // $cr_im = implode(" ", ($cr));
        // $ce_im = implode(" ", ($ce));
        
        // $sip = [
        //     'cr' => preg_split("/\./", $cr_im),
        //     'ce' => preg_split("/\./",$ce_im)
        //     ];
        
        // return(count($ce));
        
        
    //     foreach($p as $xx){
            
    //          $sip[] = [ 
    //             'nama_coa' => ' '.$xx->nama_coa, 
    //             'coa' => $xx->coa,
    //             'ca' => $ca,
    //             'cr' => $cr
    //         ]; 
    //     }
        
        
    //     $ca_im = implode(" ", ($sip[0]['ca']));
    //     $eye = $sip[0]['coa'];
    //     $johit = $cilok;
        
    //     $rr = preg_split("/\./", $eye);
        
        
        
    //     $cc = implode(" ", ($sip[0]['ca']));
    //     $dikit = $cc;
    //     $canya = preg_split("/\./", $dikit);
        
        
        
    //     $coak = implode(" ", ($sip[0]['cr']));
    //     $dikit = $coak;
    //     $crnya = preg_split("/\./", $dikit);
        
        
         
      
    //   if($request->level == " Dana Amil" || $request->level == " Dana Zakat" || $request->level == " Dana Hibah" || $request->level == " Dana Wakat" || $request->level == "Dana APBN/APBD" || $request->level == " Dana Yang Dilarang Syariah"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '$canya%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '$crnya%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //   }else if($request->level == " Dana Infaq / Sedekah Terikat"){
    //         $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.01%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //         $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501.01.002%' OR coa_kredit LIKE '%502.02%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get(); 
    //   }else 
    //   if($request->level == " Dana Infaq / Tidak Sedekah Terikat")
    //   {
    //      $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
    //      $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
    //   }
        
        
       // amil zakat hibah wakaf apbn/abdp dilarang
        
        
      //         if($request->level == " Dana Amil"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%400%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%500%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana Zakat"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%401%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana Infaq / Sedekah Terikat"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.01%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%501.01.002%' OR coa_kredit LIKE '%502.02%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana Hibah"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%404%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%504%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana Wakaf"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%407%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%507%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana APBN/APBD"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%405%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%505%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else if($request->level == " Dana Yang Dilarang Syariah"){
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%406%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%506%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }else{
//             $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
//             $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
//         }
          //dana sedekah tidak terikat
      
        
        
        // $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
        // $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        
        // $query = DB::table('coa as t1')
        //             ->select('t1.*', 't1.id as root')
                        
        //             ->unionAll(
        //                 DB::table('b as t0')
        //                     ->select('t3.*', 't0.root')
        //                     ->join('coa as t3', 't3.id_parent', '=', 't0.id')
        //             );
        
        // $saldo = DB::table('b as t')
        //         ->selectRaw("root, t.coa, t.id_parent, t.id, t.nama_coa,  SUM(t.konak) as saldo")
        //         ->withRecursiveExpression('b', $query)
                
        //         ->groupBy('root')
        //         ->get(); 
                
        // $a = [];
            
        // foreach($saldo as $t){
        //     $a[] = [
        //             'coa' => $t->coa,
        //             'id' => $t->id,
        //             'saldo' => $t->saldo
        //         ];
        // }
        
        // $key_title = [];
        
        // foreach ($a as $key => $data) {
        //     if ($data['coa'] == $request->coa) {
        //         $sald = $data['saldo'] == null ? 0 : $data['saldo'];
        //         $key_title = [
        //             'saldo_b' => $sald,
        //             'kredit' => $kredit,
        //             'debit' => $debit,
        //             'saldo' => ($debit + $sald) - $kredit
        //         ];
        //     }
        // }
        
        
        // return $key_title;
    }
    
     public function cari_saldonya(Request $request){
        
        $c = Carbon::now()->format('m-Y');
        $b = Carbon::createFromFormat('m-Y', $c)->format('m');
        $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
         $pilcoa = $request->coa;
        $p  = SaldoDana::join('coa', 'coa.coa','=','saldo_dana.coa_dana')->whereRaw("coa_dana = '$pilcoa'")->get();
       
        $sip = [];
        foreach($p as $xx){
            $cr = unserialize($xx->coa_receipt);
            $ca = unserialize($xx->coa_expend);
            
             $sip[] = [ 
                'nama_coa' => ' '.$xx->nama_coa, 
                'coa' => $xx->coa,
                'ca' => $ca,
                'cr' => $cr 
            ]; 
        }
   
        $ca_banyak = $sip[0]['ca'];
        $cr_banyak = $sip[0]['cr'];
        
          $dd = [];
          foreach($ca_banyak as $xxx){
             $dd[] = [ 
                'canya' => $xxx,
            ]; 
        }
        
        $kk = [];
        foreach($cr_banyak as $xxx){
             $kk[] = [ 
                'crnya' => $xxx,
            ]; 
        }
        
    
        
        $cc = implode(" ", ($sip[0]['ca']));
        $dikit = (string)$cc;
        $canya = preg_split("/\./", $dikit);
        
        
        $coak = implode(" ", ($sip[0]['cr']));
        $dikitoi = (string)$coak;
        $crnya = preg_split("/\./", $dikitoi);
         
      if($request->level == " Dana Amil" || $request->level == " Dana Zakat" || $request->level == " Dana Hibah" || $request->level == " Dana Wakat" || $request->level == "Dana APBN/APBD" || $request->level == " Dana Yang Dilarang Syariah"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '$canya%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '$crnya%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
      }else if($request->level == " Dana Infaq / Sedekah Terikat"){
            $cariparent = COA::selectRaw("coa.id")->whereRaw("coa = '$pilcoa'")->get();
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '$canya%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '$crnya' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();  

      }else{
          $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '$canya%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
          $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '$crnya' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
      }
        
        

       
        
        $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
        $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                        
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
        
        $saldo = DB::table('b as t')
                ->selectRaw("root, t.coa, t.id_parent, t.id, t.nama_coa,  SUM(t.konak) as saldo")
                ->withRecursiveExpression('b', $query)
                
                ->groupBy('root')
                ->get(); 
                
        $a = [];
            
        foreach($saldo as $t){
            $a[] = [
                    'coa' => $t->coa,
                    'id' => $t->id,
                    'saldo' => $t->saldo
                ];
        }
        
        $key_title = [];
        
        foreach ($a as $key => $data) {
            if ($data['coa'] == $request->coa) {
                $sald = $data['saldo'] == null ? 0 : $data['saldo'];
                $key_title = [
                    'saldo_b' => $sald,
                    'kredit' => $kredit,
                    'debit' => $debit,
                    'saldo' => ($debit + $sald) - $kredit
                ];
            }
        }
        
        
        return $key_title;
    }
    
     public function cari_saldo2(Request $request){
        
        $c = Carbon::now()->format('m-Y');
        $b = Carbon::createFromFormat('m-Y', $c)->format('m');
        $t = Carbon::createFromFormat('m-Y', $c)->format('Y');
         $pilcoa = $request->coa;
        $p  = SaldoDana::join('coa', 'coa.coa','=','saldo_dana.coa_dana')->whereRaw("coa_dana = '$pilcoa'")->get();
       
        $sip = [];
        foreach($p as $xx){
            $cr = unserialize($xx->coa_receipt);
            $ca = unserialize($xx->coa_expend);
            
             $sip[] = [ 
                'nama_coa' => ' '.$xx->nama_coa, 
                'coa' => $xx->coa,
                'ca' => $ca,
                'cr' => $cr 
            ]; 
        }
   
        $ca_banyak = $sip[0]['ca'];
        $cr_banyak = $sip[0]['cr'];
        
          $dd = [];
          foreach($ca_banyak as $xxx){
             $dd[] = [ 
                'canya' => $xxx,
            ]; 
        }
        
        $kk = [];
        foreach($cr_banyak as $yyy){
             $kk[] = [ 
                'crnya' => $yyy,
            ]; 
        }
      
       
    
        // $zzzzz = (string)$dd;
        // return($zzzzz);
        // $cccc = implode(" ", ($kk));
        // $aaa = preg_split("/\./", $zzzzz);
        

        $cc = implode(" ", ($sip[0]['ca']));
        $dikit = (string)$cc;
        $canya = preg_split("/\./", $dikit);
        
        
        $coak = implode(" ", ($sip[0]['cr']));
        $dikitoi = (string)$coak;
        $crnya = preg_split("/\./", $dikitoi);
         
      
      if($request->level == " Dana Amil" || $request->level == " Dana Zakat" || $request->level == " Dana Hibah" || $request->level == " Dana Wakat" || $request->level == "Dana APBN/APBD" || $request->level == " Dana Yang Dilarang Syariah"){
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '$canya%' AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '$crnya%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
      }else if($request->level == " Dana Infaq / Sedekah Terikat"){
            $cariparent = COA::selectRaw("coa.id")->whereRaw("coa = '$pilcoa'")->get();
            $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit = '$dd'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
            $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet = '$kk' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();  

      }else{
        //   $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit LIKE '%402.02%' OR coa_kredit LIKE '%402.03%' OR coa_kredit LIKE '%402.04%'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
        //   $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet LIKE '%502.03%' OR coa_debet LIKE '%501.01.001%' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
           
          $transaksi = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("coa_kredit = '$dd'  AND MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
          $pengeluaran = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("coa_debet = '$kk' AND MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' ")->get();
      }
        
        

       
        
        $debit = $transaksi[0]->jumlah == null ? 0 : $transaksi[0]->jumlah;
        $kredit = $pengeluaran[0]->nominal == null ? 0 : $pengeluaran[0]->nominal;
        
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                        
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
        
        $saldo = DB::table('b as t')
                ->selectRaw("root, t.coa, t.id_parent, t.id, t.nama_coa,  SUM(t.konak) as saldo")
                ->withRecursiveExpression('b', $query)
                
                ->groupBy('root')
                ->get(); 
                
        $a = [];
            
        foreach($saldo as $t){
            $a[] = [
                    'coa' => $t->coa,
                    'id' => $t->id,
                    'saldo' => $t->saldo
                ];
        }
        
        $key_title = [];
        
        foreach ($a as $key => $data) {
            if ($data['coa'] == $request->coa) {
                $sald = $data['saldo'] == null ? 0 : $data['saldo'];
                $key_title = [
                    'saldo_b' => $sald,
                    'kredit' => $kredit,
                    'debit' => $debit,
                    'saldo' => ($debit + $sald) - $kredit
                ];
            }
        }
        
        
        return $key_title;
    }
    
     public function coapengirimmutasi(Request $request){
        $unit = $request->unit;
        if($request->ajax()){
                $coa_parent= COA::where('parent','n')->where('id_kantor',$unit)
                                ->where(function($query) {
                                    $query->where('grup', 'like', '%4%')->orWhere('grup', 'like', '%3%');
                                })->orderBy('coa', 'ASC')->get();
        }
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'-'.$val->coa .' '.$val->nama_coa.'-'.$val->level,
                "coa" => $val->coa,
                "id" => $val->coa,
                // "id" => $val->id,
                "grup" => $val->grup,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        
        return response()->json($h1);
    }
    
}
