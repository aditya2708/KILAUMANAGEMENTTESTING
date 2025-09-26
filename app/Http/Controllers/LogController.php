<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity as LogActivityModel;

use Illuminate\Support\Facades\Auth;
use DataTables;

class LogController extends Controller
{
    public function index(Request $request){
        
        if($request->ajax())
        {
            if(Auth::user()->level == 'admin'){
                $data = LogActivityModel::whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->get();
            }else{
                
                $data = LogActivityModel::where('id_kantor', auth()->user()->id_kantor)->whereDate('created_at', date('Y-m-d'))->orWhere('kantor_induk', auth()->user()->id_kantor)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->get();
            }
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('waktu', function($data){
                        $tj = $data->created_at->diffForHumans();
                        return $tj;
                    })
                    ->rawColumns(['waktu'])
                    ->make(true);
        }
        return view ('logactivity.index');
        // return view('logActivity',compact('logs'));
    }
    
    public function cek(){
        // \LogActivity::addToLog('My Testing Add To Log.');
        dd(Auth::guard('user')->user()->id);
    }
}