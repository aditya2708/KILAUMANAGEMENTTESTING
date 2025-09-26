<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Golongan;
use App\Models\Gapok;
use DataTables;
use Auth;

class GolonganController extends Controller
{
    public function index (Request $request) 
    {
        $gapok = Gapok::first();
        
        if($request->com == "0"){
            $id_com = "golongan.id_com != '9990' ";
        }else{
            $id_com = "golongan.id_com = '$request->com'";
        }
        //   dd($id_com);  
        $fil_com = function($query) use ($request, $id_com){
            if(empty($request->com) && $request->com == null){
                $query->where('golongan.id_com', Auth::user()->id_com);
            }else{
                $query->whereRaw($id_com);
            }               
        };
        
        if($request->ajax())
        {
            $data = Golongan::orderBy('golongan','asc')
            ->where($fil_com)
            ->get();
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $button = ' <div class="btn-group">
                                        <a href="#" class="btn btn-info btn-sm edit" id="'.$data->id_gol.'" data-bs-toggle="modal" data-bs-target="#exampleModal">Edit</a>
                                    </div>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view ('fins.golongan', compact('gapok'));
    }
    
    public function edit($id){
        if(request()->ajax())
        {
            $data = Golongan::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request) {
        $form_data = array(
            'kenaikan'     =>  $request->kenaikan,
        );
        Golongan::where('id_gol', $request->hidden_id)->update($form_data);
        $data = Golongan::where('id_gol',$request->hidden_id)->first();
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Golongan '.$data->golongan);
        return response()->json(['success' => 'Data is successfully updated']);
        // $kantor = Kantor::findOrFail($id);
        // $input = $request->all();
        // $kantor->update($input);
        
        // Karyawan::where('id_kantor', $id)->update([
        //     'unit_kerja' => $request->unit,
        //     'kantor_induk' => $request->kantor_induk,
        // ]);
        // return back();
    }
}