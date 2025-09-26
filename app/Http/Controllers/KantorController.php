<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantor;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\COA;
use App\Models\Profile;
use Auth;
use DataTables;

class KantorController extends Controller
{
    public function index(Request $request)
    {
        
        $jab = Jabatan::select('jabatan','id')
            ->where('id_com', Auth::user()->id_com)->get();
        $kan = Kantor::where('id_com', Auth::user()->id_com)->get();
        if($request->tab == 'getkan'){
        $kan = Kantor::selectRaw('unit, id_coa')->where('id_com', Auth::user()->id_com)->get();
            return response()->json($kan);
        }
        
        $company = Profile::where(function($query) {
            if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
            }else{
                $query->where('id_com', Auth::user()->id_com);
            }
        })->get();
        
        if ($request->ajax()) {
            $id_com = $request->com != '' ?$request->com:  Auth::user()->id_com  ;

            if(Auth::user()->level_hc == '1'){
                // if($id_com == 0){
                //     $data = Kantor::get();
                    
                // }else{
                    $data = Kantor::where('id_com', $id_com)->get();

                // }
            }else{
                $data = Kantor::where('id_com', Auth::user()->id_com)->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('ki', function ($data) {
                    $pr_kan = Kantor::where('id', $data->kantor_induk)->where('id_com', Auth::user()->id_com);
                    if(count($pr_kan->get()) > 0){
                        $kantor_in = $pr_kan->first()->unit;
                    }else{
                        $kantor_in = '';
                    }
                    return $kantor_in;
                })
                ->addColumn('tj_daerah', function ($data) {
                    $tj = 'Rp. ' . number_format($data->tj_daerah, 0, ',', '.');
                    return $tj;
                })
                // ->addColumn('acc', function ($data) {
                //     if ($data->acc_up != 0) {
                //         $button2 = '<button type="button" name="update" id="' . $data->id . '" class="update btn btn-warning btn-sm"><i class="fa fa-toggle-off"></i></button>';
                //     } else {
                //         $button2 = '<button type="button" name="update" id="' . $data->id . '" class="update btn btn-info btn-sm"><i class="fa fa-toggle-on"></i></button>';
                //     }
                //     return $button2;
                // })

                ->editColumn('acc', function ($data) {
                    if ($data->acc_up == 1) {
                        $c = 'checked';
                    } else {
                        $c = '';
                    }
                    // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                    $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" status="' . $data->acc_up . '" data-id="' . $data->id . '"  data-value="' . $data->acc_up . '" type="checkbox" ' . ($data->acc_up  == 1 ? "checked" : "") . ' /> <div class="slider round"> </div> </label>';
                    return $button;
                })


                ->addColumn('action', function ($data) {
                    $button = ' <div class="btn-group"><a href="#" style="margin-right: 10px" class="edit btn btn-rounded btn-success btn-sm" id="' . $data->id . '" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-edit"></i></a>';
                    $button .= '<button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-rounded btn-danger btn-sm"><i class="fa fa-trash"></i></button> </div>';
                    return $button;
                })

                ->rawColumns(['action', 'acc'])
                ->make(true);
        }
        return view('hcm.kantor', compact('kan','jab','company'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        // dd($input);
        $id = null;
        if ($request->cek_coa != 'on') {
            // dd('STOP');
            $cek = COA::where('coa_parent', $request->coa_parent)->get();
            $cek1 = COA::where('coa', $request->coa_parent)->first();

            // dd($cek);
            if (count($cek) == 0) {
                $b = explode(".", $cek1->coa);
                // dd($b);
                $level = $cek1->level + 1;
                if ($level == 1) {
                    $dp = $b[0] + 1;
                    $id = sprintf("%03s", $dp) . "." . $b[1] . "." . $b[2] . "." . $b[3];
                }
                if ($level == 2) {
                    $dp = $b[1] + 1;
                    $id = $b[0] . "." . sprintf("%02s", $dp) . "." . $b[2] . "." . $b[3];
                }
                if ($level == 3) {
                    $dp = $b[2] + 1;
                    $id = $b[0] . "." . $b[1] . "." . sprintf("%03s", $dp) . "." . $b[3];
                }
                if ($level == 4) {
                    $dp = $b[3] + 1;
                    $id = $b[0] . "." . $b[1] . "." . $b[2] . "." . sprintf("%03s", $dp);
                }
            } else {
                foreach ($cek as $val) {
                    $b = explode(".", $val->coa);
                    if ($val->level == 1) {
                        $dp = $b[0] + 1;
                        $id = sprintf("%03s", $dp) . "." . $b[1] . "." . $b[2] . "." . $b[3];
                    }
                    if ($val->level == 2) {
                        $dp = $b[1] + 1;
                        $id = $b[0] . "." . sprintf("%02s", $dp) . "." . $b[2] . "." . $b[3];
                    }
                    if ($val->level == 3) {
                        $dp = $b[2] + 1;
                        $id = $b[0] . "." . $b[1] . "." . sprintf("%03s", $dp) . "." . $b[3];
                    }
                    if ($val->level == 4) {
                        $dp = $b[3] + 1;
                        $id = $b[0] . "." . $b[1] . "." . $b[2] . "." . sprintf("%03s", $dp);
                    }
                }
            }


            $datas = new COA;
            $datas->coa = $id;
            $datas->coa_parent = $request->coa_parent;
            $datas->nama_coa = $request->unit;
            $datas->id_parent = $cek1->id;
            $datas->level = $cek1->level + 1;
            $datas->grup = 4;
            $datas->parent = "n";
            $datas->aktif = "y";
            
            $datas->save();
        }
        // dd($id);
        // if($id != null){
        //     $id_coa = COA::where('coa', $id)->first();
        // }else{
        //     $id_coa = null;
        // }

        $data = new Kantor;
        $data->id_coa = $id == null ? $request->id_coa : $id;
        $data->unit = $request->unit;
        $data->level = $request->level;
        $data->kantor_induk = $request->kantor_induk;
        $data->no_hp = $request->no_hp;
        $data->alamat = $request->alamat;
        $data->id_com = $request->perus != '' ? $request->perus : Auth::user()->id_com;
        $data->user_insert = Auth::user()->id;
        $data->id_pimpinan = $request->id_direktur;
        $data->id_jabpim = $request->id_jabdir;
            
        $data->save();
        
        \LogActivity::addToLog(Auth::user()->name . ' Menambahkan Data Kantor ' . $request->unit);

        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Kantor::findOrFail($id);

            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        // return($request);
        $form_data = array(
            'unit'    =>  $request->unit,
            'no_hp'     =>  $request->no_hp,
            'alamat'    =>  $request->alamat,
            'kantor_induk'  => $request->kantor_induk,
            'level' => $request->level,
            'user_update' => Auth::user()->id,
            'id_coa' => $request->id_coa,
            'id_pimpinan' => $request->direktur,
            'id_jabpim' => $request->id_jabdir,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            // 'tj_daerah' => $request->tj_daerah != '' ? preg_replace("/[^0-9]/", "", $request->tj_daerah) : 0
        );
        
        Kantor::whereId($request->hidden_id)->update($form_data);
        \LogActivity::addToLog(Auth::user()->name . ' Mengupdate Data Kantor ' . $request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
    }

    // public function update_kantor($id)
    // {
    //     $kantor = Kantor::where('id', $id)->first();
    //     if ($kantor->acc_up == 0) {
    //         $form_data = array(
    //             'acc_up' => 1,
    //             'user_update' => Auth::user()->id
    //         );
    //         \LogActivity::addToLog(Auth::user()->name . ' Mengaktifkan Izin Update Lokasi Data Kantor ' . $kantor->unit);
    //     } else {
    //         $form_data = array(
    //             'acc_up' => 0,
    //             'user_update' => Auth::user()->id
    //         );
    //         \LogActivity::addToLog(Auth::user()->name . ' Menonaktifkan Izin Update Lokasi Data Kantor ' . $kantor->unit);
    //     }
    //     Kantor::where('id', $id)->update($form_data);

    //     return response()->json(['success' => 'Data is successfully updated']);
    // }
    
    public function coa_coa_kntr(Request $request){
        $data = COA::whereRaw("grup = 3 AND id_kantor IS NULL AND parent = 'n'")->orderBy('coa', 'ASC')->get();
        return $data;
    }

    public function updatekantor(Request $request){
        $data = Kantor::where('id',$request->id)->first();
    
        $aktif = $data->acc_up;
    
        if($aktif == 1){
            \LogActivity::addToLog(Auth::user()->name.' Menonaktifkan Kota '.$data->unit);
            Kantor::where('id',$request->id)->update([
                'acc_up'=> 0
                
            ]);
        }else{
             \LogActivity::addToLog(Auth::user()->name.' Mengaktifkan Kota '.$data->unit);
             Kantor::where('id',$request->id)->update([
                'acc_up'=> 1
            ]);
        }
      
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $kantor = Kantor::findOrFail($id);
        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Kantor ' . $kantor->unit);
        $kantor->delete();
        // return back();
    }
}
