<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use\App\Jabatan;
use App\Models\Karyawan;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\Pembayaran;
use App\Models\COA;
use Auth;
use DataTables;

class BankController extends Controller
{
    public function index(Request $request){
        $id_com = $request->com;
        $kan = Kantor::where(function($query) use ($id_com){
                                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                    if($id_com > 0){
                                        $query->where('id_com', $id_com);
                                    }else if($id_com == '0'){
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    }else{
                                       $query->where('id_com', Auth::user()->id_com);
                                    } 
                                }else{
                                    $query->where('id_com', Auth::user()->id_com);
                                }
                            })->get();
        if($request->ajax()){
            $id_com = $request->com;
            $kantor = $request->id_kantor == "" ? "id_kantor != ''" : "id_kantor = '$request->id_kantor'";
            $data = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')
                    ->whereRaw("$kantor")
                    ->select('bank.*','tambahan.unit', 'tambahan.id' )
                    ->where(function($query) use ($id_com){
                                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                    if($id_com > 0){
                                        $query->where('id_com', $id_com);
                                    }else if($id_com == '0'){
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    }else{
                                       $query->where('id_com', Auth::user()->id_com);
                                    } 
                                }else{
                                    $query->where('id_com', Auth::user()->id_com);
                                }
                            });
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $button = ' <div class="btn-group">
                                    <button class="edit btn btn-success btn-sm" id="'.$data->id_bank.'" data-bs-toggle="modal" data-bs-target="#modal-default" >Edit</button>
                                </div>';
                    $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="'.$data->id_bank.'" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('fins.bank', compact('kan'));
    }
    
    public function pembayaran(Request $request){
        if($request->ajax()){
            $id_com = $request->com;
            $kantor = $request->id_kantor == "" ? "id_kantor != ''" : "id_kantor = '$request->id_kantor'";
            $data = Pembayaran::where(function($query) use ($id_com){
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($id_com > 0){
                                $query->where('id_com', $id_com);
                            }else if($id_com == '0'){
                                $query->whereIn('id_com', function($q) {
                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                });
                            }else{
                               $query->where('id_com', Auth::user()->id_com);
                            } 
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    });
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $button = ' <div class="btn-group">
                                    <button class="edit btn btn-success btn-sm" id="'.$data->id.'" data-bs-toggle="modal" data-bs-target="#modal-default" >Edit</button>
                                </div>';
                    $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('fins.pembayaran');
    }
    
    public function store(Request $request){
        $input = $request->all();
        // dd($input);
        $id = null;
        if($request->cek_coa != 'on'){
            // dd('STOP');
            $cek = COA::where('coa_parent', $request->coa_parent)->get();
            $cek1 = COA::where('coa', $request->coa_parent)->first();
            
            // dd(count($cek));
            if(count($cek) == 0){
                $b = explode("." , $cek1->coa);
                // dd($b);
                $level = $cek1->level + 1;
                if($level == 1){
                    $dp = $b[0] + 1;
                    $id = sprintf("%03s", $dp).".".$b[1].".".$b[2].".".$b[3];
                }
                if($level == 2){
                    $dp = $b[1] + 1;
                    $id = $b[0].".".sprintf("%02s", $dp).".".$b[2].".".$b[3];
                }
                if($level == 3){
                    $dp = $b[2] + 1;
                    $id = $b[0].".".$b[1].".".sprintf("%03s", $dp).".".$b[3];
                }
                if($level == 4){
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
            
            
            $datas = new COA;
            $datas->coa = $id;
            $datas->coa_parent = $request->coa_parent;
            $datas->nama_coa = $request->nama_bank;
            $datas->id_parent = $cek1->id;
            $datas->level = $cek1->level + 1;
            $datas->grup = 4;
            $datas->parent = "n";
            $datas->aktif = "y";
            
            // dd($datas);
            $datas->save();
                
        }
        // dd($id);
        // if($id != null){
        //     $id_coa = COA::where('coa', $id)->first();
        // }else{
        //     $id_coa = null;
        // }
        
        
        
        
        
        // dd($id_coa);
        $data = new Bank;
        $data->id_coa = $id == null ? $request->id_coa : $id;
        $data->id_kantor = $request->id_kantor;
        $data->nama_bank = $request->nama_bank;
        $data->jenis_rek = $request->jenis_rek;
        $data->no_rek = $request->no_rek;
        $data->save();
        // $input['tj_daerah'] = $request->tj_daerah != '' ? preg_replace("/[^0-9]/", "", $request->tj_daerah) : 0;
        // Kantor::create($input);
        // return back();
        
        \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Kantor '.$request->unit);

        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function edit($id){
        // dd($id);
        if(request()->ajax()){
            $data = Bank::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request) {
        // dd($request->all());
        $form_data = array(
            'nama_bank'    =>  $request->nama_bank,
            'no_rek' => $request->no_rek,
            'id_kantor' => $request->id_kantor,
            'id_coa' => $request->id_coa,
            'jenis_rek' => $request->jenis_rek
        );
        Bank::where('id_bank', $request->hidden_id)->update($form_data);
        // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kantor '.$request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
        
    }

    // 
    public function destroy($id) {
        $kantor = Bank::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $kantor->delete();
        return response()->json(['success' => 'Data is successfully Deleted']);
        // return back();
    }
    
    public function coa_bank($id){
        $coa = COA::where('coa_parent', $id)->orderBy('coa', 'ASC')->get();
        $h1 = [];
        foreach($coa as $key => $val){
            $h1[] = [
                "text" => $val->id,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
        return response()->json($h1);
    }
}
