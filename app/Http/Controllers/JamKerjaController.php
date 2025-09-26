<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JamKerja;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kantor;
use Auth;
use DB;
use DataTables;
use Excel;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    public function index(Request $request)
    {   
        
        $val_status = JamKerja::select('status')->distinct()->get();
        $val_hari = JamKerja::selectRaw('nama_hari as hari')->distinct()->get();
        $id_com = Auth::user()->id_com;
        
        if($request->ajax()){
            $com = $request->com;
            
            $status = function($query) use ($request){
              if($request->status != ''){
                  $query->where('status', $request->status);
              }  
            };
            
            $shift = function($query) use ($request){
                  if($request->shift == ''){
                      $query->where('shift', 1);
                  } else{
                       $query->where('shift', $request->shift);
                  }  
            };
            
            if(Auth::user()->level_hc == '1'){
                $filCompany = $com == '' || $com == '0' ? "id_com != 'haha'" : "id_com = '$com'";
            }else{
                $filCompany = "id_com = '$id_com'";
            }
            
            if($request->tab == 'filterShift'){
                $val_shift = JamKerja::select('shift')
                    ->where(function($query) use ($com){
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
                    })->distinct()->get();
                return $val_shift;    
            }

            $data = JamKerja::where($status)
                ->where($shift)
                ->where(function($query) use ($com){
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
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('jam-kerja.index', compact(['val_status', 'val_hari']));
    }
    
    public function update($id, Request $request){
        // dd($request);
        try {
             // update OR create where id
            $jamKerja = JamKerja::updateOrCreate(
            ['id_jamker' => $id],[
                'terlambat' => $request->terlambat,
                'status' => $request->status,
                'cek_in' => $request->cek_in,
                'cek_out' => $request->cek_out,
                'break_in' => $request->break_in,
                'break_out' => $request->break_out,
                'user_update' => Auth::user()->id,
                // 'id_com' => Auth::user()->id_com,
            ]);
            // update where id
            // $jamKerja = JamKerja::where('id_jamker', $id)->update([
            //     'terlambat' => $request->terlambat,
            //     'status' => $request->status,
            //     'cek_in' => $request->cek_in,
            //     'cek_out' => $request->cek_out,
            //     'break_in' => $request->break_in,
            //     'break_out' => $request->break_out,
            //     'user_update' => Auth::user()->id,
            // ]);
            
            return response()->json(['message' => 'Berhasil!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request){
        
        // return response()->json(['error' => 'Sedang dalam pengembangan!']);
            $val_shift = 0;
            $val_shift = JamKerja::select('shift')
                ->where('id_com', Auth::user()->id_com)
                ->orderBy('shift', 'desc')
                ->first();
                // dd($val_shift->shift ?? 0);
            $maxShiftValue = $val_shift->shift ?? 0 + 1;
            
            foreach ($request->input('addhari') as $key => $hari) {
                $jamKerja = new JamKerja;
            
                $jamKerja->nama_hari = $hari;
                $jamKerja->terlambat = $request->addterlambat[$key];
                $jamKerja->shift = $maxShiftValue;
                $jamKerja->cek_in = $request->addcek_in[$key];
                $jamKerja->cek_out = $request->addcek_out[$key];
                $jamKerja->break_in = $request->addbreak_in[$key];
                $jamKerja->break_out = $request->addbreak_out[$key];
                $jamKerja->status = $request->addstatus[$key];
                $jamKerja->id_com = $request->id_coms == '' ? Auth::user()->id_com :  $request->id_coms ;
            
                $jamKerja->save();
            }

            return response()->json(['success' => 'Berhasil!']);
    
       

    }
    
}
