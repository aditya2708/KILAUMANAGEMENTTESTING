<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jalur;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\Karyawan;
use App\Models\Tunjangan;
use Auth;
use DataTables;

class JalurController extends Controller{
    public function getjalur(Request $request){
        $id = Auth::user()->level;
        
        if($id == 'admin' || $id == 'keuangan pusat'){
            $data = Jalur::all();
        }else if ($id == 'kacab' || $id == 'keuangan cabang'){
            if(!empty($request->id_don)){
                $don = Donatur::where('id', $request->id_don)->first();
                $data = Jalur::where('id_kantor', $don->id_kantor)->get();
            }else{
                $data = Jalur::where('id_kantor', Auth::user()->id_kantor)->get();
            }
        }
        return response()->json($data);
    }
    
    public function index(Request $request){
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kantors = Auth::user()->id_kantor;
        
        if(Auth::user()->level == 'admin'){
            $kantor = Kantor::all();
        }else {
            if($k == null){
                $kantor = Kantor::whereRaw("id = '$kantors'")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = '$kantors' OR id = '$k->id')")->get();
            }
        }
        
        if($request->ajax())
        {
            $spv = Auth::user()->id_karyawan;
            
            if(Auth::user()->level == 'admin'){
                $data = Jalur::latest();
            }else if(Auth::user()->level == 'kacab'){
                if($k == null){
                    $data = Jalur::whereRaw("id_kantor = '$kantors'")->latest();
                }else{
                    $data = Jalur::whereRaw("(id_kantor = '$kantors' OR id_kantor = '$k->id')")->latest();
                }
            // }else{
            //     $data = Jalur::whereRaw("id_kantor = '$kantor' AND id_spv = '$spv'")->latest();
            }
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        // $button = ' <div class="btn-group">
                        //                 <a href="#" class="edit btn btn-success btn-sm" id="'.$data->id_jalur.'" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-edit"></i></a>
                        //             </div>';
                         $button = '';   
                        if(Auth::user()->kolekting=='admin'){
                            $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="'.$data->id_jalur.'" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                            
                        }else{
                            $button .= '';
                        }
                        
                        return $button;
                    })
                    ->addColumn('spv', function($data){
                        $uw = Karyawan::where('id_karyawan', $data->id_spv);
                        if($uw->count() > 0){
                            $button = $uw->first()->nama;
                        }else{
                            $button = '';
                            
                        }
                        return $button;
                    })
                    ->rawColumns(['action','spv'])
                    ->make(true);
        }
        return view('crm.jalur', compact('kantor'));
    }
    
    public function add_jalur(Request $request){
        $input = $request->all();
        // dd($input);
        
        $data = new Jalur;
        $data->id_kantor = $request->id_kantor;
        $data->nama_jalur = $request->nama_jalur;
        $data->kota = $request->kantor;
        $data->id_spv = $request->id_spv != '' ?  $request->id_spv : NULL;
        $data->save();
        // \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Kantor '.$request->unit);

        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function edit_jalur($id){
        if(request()->ajax())
        {
            $data = Jalur::findOrFail($id);
            
            $tj = Tunjangan::first();
            $uwuw = Karyawan::whereRaw("id_kantor = $data->id_kantor AND jabatan = '$tj->spv_kol' ")->get();
            
            return response()->json(['result' => $data, 'spv' => $uwuw]);
        }
    }
    
    public function update_jalur(Request $request) {
        $input = $request->all();
        // dd($input);
        $form_data = array(
            'id_kantor'    =>  $request->id_kantor,
            'nama_jalur'     =>  $request->nama_jalur,
            'kota'    =>  $request->kantor,
            'id_spv' => $request->id_spv != '' ? $request->id_spv : NULL,
            
        );
        Jalur::whereId_jalur($request->hidden_id)->update($form_data);
        // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kantor '.$request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
      
    }
    
    public function delete($id) {
        $kantor = Jalur::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $kantor->delete();
        // return back();
    }
    
    public function getspvid(){
        $kota = $_GET['kota'];
        $tj = Tunjangan::first();
        $data = Karyawan::whereRaw("id_kantor = $kota AND jabatan = '$tj->spv_kol' ")->get();
        return $data;
    }
    
    public function updatejalur(Request $request) {
        // $input = $request->all();
        // return($input);
        // dd($input);
        $form_data = array(
            'id_spv'    =>  $request->nm_spv
        );
        // Jalur::where($request->hidden_id)->update($form_data);
        Jalur::whereIn('id_jalur',$request->jlr)->update($form_data);
        Jalur::whereNotIn('id_jalur',$request->jlr)->where('id_spv', $request->nm_spv)->update(['id_spv' => NULL]);
        return response()->json(['success' => 'Data is successfully updated', 'data' => $form_data]);
      
    }
    
    public function getjalurspv(Request $request) {
        $data = Jalur::select('id_jalur')->where('id_spv', $request->spv)->get();
        $itung = [];
        foreach($data as $d){
            $itung[] = [$d->id_jalur];
        }
        return $itung;
    }
    
    public function adajalur(Request $request) {
        $itung = [];
        $itung['data'] = Jalur::whereRaw("(id_spv = $request->spv OR id_spv IS NULL) AND id_kantor = '$request->kota'")->get();
        $data = Jalur::select('id_jalur')->where('id_spv', $request->spv)->get();
        foreach($data as $d){
            $itung['aw'][] = [$d->id_jalur];
        }
        return $itung;
    }

}
