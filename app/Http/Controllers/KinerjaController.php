<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use\App\Userkolektor;

use\App\Kinerja;
use App\User;
use\App\Kantor;
use\App\Donatur;
use\App\Pengaduan;
use\App\Tunjangan;
use Session;
use DB;
use Carbon\Carbon;
use DataTables;
// use DataTables\Editor,
//     DataTables\Editor\Field,
//     DataTables\Editor\Format,
//     DataTables\Editor\Mjoin,
//     DataTables\Editor\Options,
//     DataTables\Editor\Upload,
//     DataTables\Editor\Validate,
//     DataTables\Editor\ValidateOptions;
use Auth;

class KinerjaController extends Controller
{
    // public function index(Userkolektor $kolektor)
    // {
       
    //     return view('kinerja.index', compact('kerja'));
    // }

    public function kinerja(Request $request)
    {
         if($request){
            $kerja = Donatur::where('jalur', 'like', '%'.$request->cari.'%')->get(); 
        }else{
            $kerja = Donatur::all(); 
        }
        // $kerja = Donatur::where('petugas', $kolektor->name)->orderBy('tgl_kolek','asc')->get();
        return view ('kinerja.index', compact('kerja','request'));
    }
    
      public function test1(Request $request)
    {
         if($request){
            $kerja = Donatur::where('jalur', 'like', '%'.$request->cari.'%')->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->orderBy('created_at','desc')->simplePaginate(300);
        }else{
            $kerja = Donatur::where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->get(); 
        }
        
        $sinkron = \DB::select("SELECT * from sinkron where id = '1' ");
        
        $belumass = \DB::select("SELECT jalur, kota,
         COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
         COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
         FROM donatur
         GROUP BY kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
         OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0");
        // $kerja = Donatur::where('petugas', $kolektor->name)->orderBy('tgl_kolek','asc')->get();
        return view ('kinerja.asis', compact('kerja','request','sinkron', 'belumass'));
    }
    
    
    public function asis(Request $request)
    {
        if($request->ajax()){
        // if($request->kota){
        //     $kerja = Donatur::where('jalur', $request->cari)->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->where('kota', Auth::user()->kota)->orderBy('created_at','desc');
        // }else{
        //     $kerja = Donatur::where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->where('kota', Auth::user()->kota); 
        // }
        
        $jalur = $request->jalurah != '' ? "id_jalur = '$request->jalurah'" : "id_jalur != 'hahaha'";
        $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != 'hahaha'";
        $petugas = $request->petugas != '' ? "petugas = '$request->petugas'" : "id != 'hahaha'";
        $pembayaran = $request->pembayaran != '' ? "pembayaran = '$request->pembayaran'" : "pembayaran != '-'";
        $stts = $request->stts != '' ? "status = '$request->stts'" : "status IS NOT NULL";
        $kot = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        if(Auth::user()->kolekting != 'spv' ){
            if($k == null){
                $kerja = Donatur::whereRaw("$jalur AND $petugas AND id_kantor = '$kot' AND status != 'Ditarik' AND status != 'Off' AND $stts AND $pembayaran");
            }else{
                if($request->kota != ''){
                    $kerja = Donatur::whereRaw("$jalur AND $petugas AND id_kantor = '$request->kota' AND status != 'Ditarik' AND status != 'Off' AND $stts AND $pembayaran");
                }else{
                    $kerja = Donatur::whereRaw("$jalur AND $petugas AND id_kantor = '$kot' AND status != 'Ditarik' AND status != 'Off' AND $stts AND $pembayaran")
                                ->orWhereRaw("id_kantor = '$k->id' AND $petugas AND $jalur AND status != 'Ditarik' AND status != 'Off' AND $stts AND $pembayaran");
                    
                }
            }
            
        }else{
            $kerja = Donatur::whereRaw("$jalur AND id_kantor = '$kot' AND $petugas AND status != 'Ditarik' AND status != 'Off' AND $stts AND $pembayaran");
        }
        
         return DataTables::of($kerja)
                    ->addIndexColumn()
                    ->addColumn('status', function($kerja){
                        if($kerja->status == 'Donasi'){
                            $st = '<label class="label label-success">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Tidak Donasi'){
                            $st = '<label class="label label-warning">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Tutup'){
                            $st = '<label class="label label-danger">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Tutup 2x'){
                            $st = '<label class="label label-success">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Ditarik'){
                            $st = '<label class="label label-danger">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Off'){
                            $st = '<label class="label label-danger">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'Kotak Hilang'){
                            $st = '<label class="label label-danger">'.$kerja->status.'</label>';
                        }elseif($kerja->status == 'belum dikunjungi'){
                            $st = '<label class="label label-primary">'.$kerja->status.'</label>';
                        }else{
                            $st = '<label class="label label-secondary">'.$kerja->status.'</label>';
                        }
                        return $st;
                    })
                    ->editColumn('action', function($kerja){
                        if($kerja->acc == 1){
                            $c = 'checked';
                        }else{
                          $c = '';
                        }
                       $button = '<label class="switch"> <input onchange="change_status_action(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class"  data-id="'. $kerja->id . '" data-value="'. $kerja->acc . '" type="checkbox" '.($kerja->acc == true ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                        return $button;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        
        $sinkron = \DB::select("SELECT * from sinkron where id = '1' ");
        $pemb = Donatur::select('pembayaran')->whereRaw("(pembayaran = 'transfer' OR pembayaran = 'dijemput')")->distinct()->get();
        $stat = Donatur::select('status')->whereRaw("status != 'Off' AND status != 'Ditarik'")->distinct()->get();
        
        $belumass = \DB::select("SELECT jalur, id_kantor,
         COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
         COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
         FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m')
         GROUP BY id_kantor, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
         OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0");
        // $kerja = Donatur::where('petugas', $kolektor->name)->orderBy('tgl_kolek','asc')->get();
        return view ('kinerja.asis', compact('request','sinkron', 'belumass','pemb','stat'));
    }
    
    
    public function getlist(Request $request){
         if($request){
            $kerja = Donatur::where('jalur', 'like', '%'.$request->cari.'%')->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->orderBy('created_at','desc')->simplePaginate(300);
        }else{
            $kerja = Donatur::where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')->get(); 
        }
        
        $sinkron = \DB::select("SELECT * from sinkron where id = '1' ");
        
        $belumass = \DB::select("SELECT jalur, kota,
         COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
         COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
         FROM donatur
         GROUP BY kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
         OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0");
        // $kerja = Donatur::where('petugas', $kolektor->name)->orderBy('tgl_kolek','asc')->get();
        return view ('kinerja.tbl_out', compact('kerja','request','sinkron', 'belumass'));
    }
    
    
  



//      public function create(Kolektors $kolektor){
//         return view ('kinerja.create',compact('kolektor'));
// }

public function store(Request $request){
    $input = $request->all();

    $request->validate([
        'nama' => 'required|string|max:40', 
        
    ]); 
    Kinerja::create($input);
    Session::flash('sukses','Telah ter-Update');
    return view('back');

}

public function edit($id)

{
    $kinerja = Kinerja::findOrFail($id);
    return view('kinerja.edit',compact('kinerja'));
}

public function update($id, Request $request)

{
    $kinerja = Kinerja::findOrFail($id);

    $input = $request->all();
  
    $kinerja->update($input);
    return "data berhasil disimpan";
}

public function changeStatus(Request $request) {
    $don = Donatur::find($request->id);
    $petugas = User::where('id', $don->id_koleks)->first();
    $trans = Tunjangan::first();
    
    if($don->warning == 1 && $petugas->id_jabatan == $trans->kolektor && Auth::user()->kolekting == 'spv'){
        $don = [];
    }else{
        $don->acc = $request->acc;
        $don->update();
    }
    return response()->json(['success' => 'Update Berhasil']);
}

public function laporankerja()
{
    $laporan = Kinerja::where('id_koleks', $kolektor->id_koleks)->orderBy('created_at','desc')->get();
    return view ('laporan.kinerja', compact('laporan'));
}


function action(Request $request)
{
    if($request->ajax())
    {
        if($request->action == 'edit')
        {
            $data = array(
                 'petugas'	=>	$request->petugas,
                'tgl_kolek'	=>	$request->tgl_kolek,
                'pembayaran'	=>	$request->pembayaran,
                
            );
            DB::table('donatur')
                ->where('id', $request->id)
                ->update($data);
        }
        if($request->action == 'delete')
        {
            DB::table('donatur')
                ->where('id', $request->id)
                ->delete();
        }
        return response()->json($request);
    }
}

function actions(Request $request)
{
    if($request->ajax())
    {
        if($request->action == 'edit')
        {
            $data = array(
                 'tgl_kolek'	=>	$request->tgl_kolek,
                
            );
            DB::table('donatur')
                ->where('id', $request->id)
                ->update($data);
        }
        if($request->action == 'delete')
        {
            DB::table('donatur')
                ->where('id', $request->id)
                ->delete();
        }
        return response()->json($request);
    }
}

function actionsb(Request $request)
{
    if($request->ajax())
    {
        if($request->action == 'edit')
        {
            $data = array(
                 'petugas'	=>	$request->petugas,
                
            );
            DB::table('donatur')
                ->where('id', $request->id)
                ->update($data);
        }
       
        return response()->json($request);
    }
}

function actionskacab(Request $request)
{
    if($request->ajax())
    {
        if($request->action == 'edit')
        {
            // dd('vo');
            $petugas = User::where('id', $request->petugas)->first();
            // $data = array(
            //     'petugas'	=>	$petugas->name,
            //     'id_koleks' => $request->petugas,
            //     'tgl_kolek' => $request->tgl_kolek,
            //     'pembayaran'	=>	$request->pembayaran,
                
            // );
            $trans = Tunjangan::first();
            $don = Donatur::find($request->id);
            
            if($don->warning == 1 && $petugas->id_jabatan == $trans->kolektor && Auth::user()->kolekting == 'spv'){
                $don = [];
            }else{
                $don->petugas	    = $petugas->name;
                $don->id_koleks     = $request->petugas;
                $don->tgl_kolek     = $request->tgl_kolek;
                $don->pembayaran	= $request->pembayaran;
                $don->update();
            }
            // dd($request->id);
            // DB::table('donatur')
            //     ->where('id', $request->id)
            //     ->update($data);
        }
       
        return response()->json($request);
    }
}

     public function form($donatur){
           $dat = \DB::select("SELECT distinct kolektor,id_transaksi,kota from transaksi where id_transaksi = $donatur ");
        return view ('pengaduan.form',compact('dat'));
}


public function storepengaduan(Request $request){
    $input = $request->all();

    $request->validate([
        'aduan' => 'required|string|max:150', 
        
    ]); 
    Pengaduan::create($input);
    Session::flash('sukses','Keluhan Anda Sudah Tersimpan');
    return back();

}


public function pengaduan(){
    $aduan = Pengaduan::get();
    return view ('pengaduan.ps',compact('aduan'));
}

public function sinkronwarning($idsin){
    $stat = Carbon::now()->format('d/m/Y , H:i:s');
    $up = Carbon::now()->format('m/Y');
    
        \DB::select('call `up_stat`()');
        \DB::select('call `up_warning`()');
        \DB::select("update sinkron set status = '$stat', up = '$up' where id = '$idsin'");
        return back();
}

public function index_edit_table(){
    return view('kinerja.edit_table');
}


public function edit_table(){
    
 
// Build our Editor instance and process the data coming from _POST
    // Editor::inst( $db, 'donatur' )
    // ->fields(
    //     Field::inst( 'nama' )
    //         ->validator( Validate::notEmpty( ValidateOptions::inst()
    //             ->message( 'A first name is required' ) 
    //         ) )
    // )
    // ->debug(true)
    // ->process( $_POST )
    // ->json();
    
        
    $column = array("id", "petugas","nama", "id_koleks");

    $query = "SELECT * FROM donatur ";
    if(isset($_POST["search"]["value"]))
    {
    	$query .= 'WHERE nama LIKE "%'.$_POST["search"]["value"].'%" ';
    }
    if(isset($_POST["order"])){
    	$query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
    }else{
    	$query .= 'ORDER BY id DESC ';
    }
    $query1 = '';
    
    if($_POST["length"] != -1){
    	$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }
    $statement = \DB::select("$query");
    $number_filter_row = count($statement);
    $result = \DB::select("$query  $query1");
    $data = array();
    
    foreach($result as $row)
    {
    	$sub_array = array();
    	$sub_array[] = $row->id;
    	$sub_array[] = $row->petugas;
    	$sub_array[] = $row->nama;
    	$sub_array[] = $row->id_koleks;
    	$data[] = $sub_array;
    }
    
    $dat = Donatur::all()->count();
    $output = array(
    	'draw'		=>	intval($_POST['draw']),
    	'recordsTotal'	=>	$dat,
    	'recordsFiltered'	=>	$number_filter_row,
    	'data'		=>	$data
    );
    return json_encode($output);

    
}

public function update_edtab(){
    // dd($_POST["name"]);
    if($_POST["name"] == "id_koleks"){
        $user = User::where('id', $_POST["value"])->first();
        $update = Donatur::where('id', $_POST["pk"])->update([
            "petugas" => $user->name,
            $_POST["name"] =>  $_POST["value"],   
        ]);
    }else{
        $update = Donatur::where('id', $_POST["pk"])->update([
            $_POST["name"] =>  $_POST["value"],   
        ]);
    }
    
    // $query = "UPDATE donatur SET ".$_POST["name"]." = '".$_POST["value"]."' WHERE id = '".$_POST["pk"]."' ";
    // $data = \DB::
}


}
