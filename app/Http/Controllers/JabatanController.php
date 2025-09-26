<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Profile;
use Auth;
use DataTables;

class JabatanController extends Controller
{
    public function index(Request $request){
        $jab = Jabatan::where('id_com', Auth::user()->id_com)->get();
        // $com = $request->com != '' ? "id_com = $request->com" : "id_com IS NOT NULL";
        $com = $request->com;
        $company = Profile::where(function($query) {
            if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
            }else{
                $query->where('id_com', Auth::user()->id_com);
            }
        })->get();
        
        // $pr_jab = Jabatan::where('id','=',1)->first();
        if($request->ajax())
        {
            $data = Jabatan::where(function($query) use ($com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($com > 0){
                            $query->where('id_com', $com);
                        }else if($com == '0'){
                            $query->whereIn('id_com', function($q) {
                                $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                            });
                        }else{
                           $query->where('id_com', Auth::user()->id_com);
                        } 
                    }else{
                        $query->where('id_com', Auth::user()->id_com);
                    }
                })->orderBy('pr_jabatan','ASC');
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('parent', function($data) use($request){
                          if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                                if( $request->com == '0' || $request->com == null ){
                                $parent = Jabatan::where('id', $data->pr_jabatan);
                                }else{
                                $parent = Jabatan::where('id', $data->pr_jabatan)->where('id_com',$request->com);
                                }
                          }else{
                            $parent = Jabatan::where('id', $data->pr_jabatan)->where('id_com', Auth::user()->id_com);
                          }
                        if(count($parent->get()) > 0){
                            $wow = $parent->first()->jabatan;
                        }else{
                            $wow = '';
                        }
                        return $wow;
                        
                    })
                    ->addColumn('action', function($data){
                        $button = ' <div class="btn-group">
                                        <a href="#" class="btn btn-success btn-sm edit" id="'.$data->id.'" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-edit"></i></a>
                                    </div>';
                        $button .= '&nbsp;&nbsp;&nbsp;<button type="button" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view ('jabatan.index', compact('jab','company'));

    }

    public function store(Request $request){
        $input = $request->all();
        $input['tj_jabatan'] = $request->tj_jabatan != '' ? preg_replace("/[^0-9]/", "", $request->tj_jabatan) : 0;
        $input['tj_training'] = $request->cek == 'on' ? '1' : '0';
        $input['kon_tj_plt'] = $request->kon_plt != '' ? $request->kon_plt : NULL;
        $input['tj_plt'] = $request->kon_plt == 'n' ? $request->nom : $request->pres;
        $input['id_com'] = $request->perus != '' ? $request->perus : Auth::user()->id_com;
        // return($input);
        \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Jabatan '.$request->jabatan);
        Jabatan::create($input);
        // return back();
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function edit($id){
        if(request()->ajax())
        {
            $data = Jabatan::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request) {
        $form_data = array(
            'jabatan'    =>  $request->jabatan,
            'pr_jabatan'     =>  $request->pr_jabatan,
            // 'tj_jabatan' => $request->tj_jabatan != '' ? preg_replace("/[^0-9]/", "", $request->tj_jabatan) : 0
        );
        $coba = Jabatan::whereId($request->hidden_id)->first();
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Jabatan '.$coba->jabatan.' ke '.$request->jabatan);
        Jabatan::whereId($request->hidden_id)->update($form_data);
        

        return response()->json(['success' => 'Data is successfully updated']);
        
        // Karyawan::where('jabatan', $id)->update([
        //     'pr_jabatan' => $request->pr_jabatan,
        // ]);
        // return back();
    }

    public function destroy($id) {
        $jabatan = Jabatan::findOrFail($id);
        \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Jabatan '.$jabatan->jabatan);
        $jabatan->delete();
        
        // return back();
        return response()->json(['success' => 'Data is successfully updated']);
    }
}
