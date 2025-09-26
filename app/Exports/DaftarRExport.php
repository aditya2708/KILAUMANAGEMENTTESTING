<?php
namespace App\Exports;

use Auth;
use App\Models\RequestKar;
use App\Models\RumlapKeuangan;
use App\Models\Pengeluaran;
use App\Models\Penutupan;
use App\Models\SaldoAw;
use App\Models\Transaksi;
use App\Models\Jurnal;
use App\Models\Tunjangan;
use App\Models\Kantor;
use App\Models\Bank;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as BladeView;;
use Maatwebsite\Excel\Concerns\FromView;
use DataTables;
use Illuminate\Support\Collection;
use DB;

class DaftarRExport implements  FromView
{
public function __construct($request)
    {
      
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {   
        
    $request = $this->request;
// dd($request);
    $id_com = $request->com;
    if($request->daterange != '') {
        $tgl = explode(' - ', $request->daterange);
        $dari = date('Y-m-d', strtotime($tgl[0]));
        $sampai = date('Y-m-d', strtotime($tgl[1]));
    }

    $now = date('Y-m-d');
    $tgls = $request->daterange != '' ? "DATE(request.created_at) >= '$dari' AND DATE(request.created_at) <= '$sampai'" : "DATE(request.created_at) IS NOT NULL AND DATE(request.created_at) IS NOT NULL" ;

    $jabat = $request->jabatan != '' ? "request.id_jabatan = '$request->jabatan'" : "request.id_jabatan IS NOT NULL";
    $kota = $request->unit != '' ? "request.id_kantor = '$request->unit'" : "request.id_kantor IS NOT NULL";
    $stts = $request->status != '' ? "request.status = '$request->status'" : "request.status IS NOT NULL";
    $kett = $request->kett != '' ? "request.acc = '$request->kett'" : "request.acc IS NOT NULL";
    
    $datesss = $request->daterange == '' ? $now : $request->daterange;
    
    $kntr = Auth::user()->id_kantor;
    $k = Kantor::where('kantor_induk', $kntr)->first();
    // dd($request);
    if(Auth::user()->id_com != null ){
            $data = RequestKar::select('request.*', 'jabatan.jabatan')->join('jabatan', 'jabatan.id', '=', 'request.id_jabatan')
                ->whereRaw("$jabat AND $tgls AND $stts AND $kett")
                // ->where('request.id_com', $id_com)
                ->where(function($query) use ($k, $kntr, $request){
                    if(Auth::user()->kepegawaian == 'kacab'){
                        if($k == null){
                            $query->where('id_kantor', Auth::user()->id_kantor);
                        }else{
                            if($request->unit == ''){
                                $query->whereRaw("id_kantor = '$k->id' OR id_kantor = '$kntr'");
                            }else{
                                 $query->where('id_kantor', $request->unit);
                            }
                        }
                    }else{
                        if($request->unit == ''){
                            $query->whereRaw("id_kantor IS NOT NULL");
                        }else{
                            $query->where('id_kantor', $request->unit);
                        }
                    }
                })
                
                ->whereIn('id_karyawan', function($query) use ($id_com){
                    $query->select('id_karyawan')
                            ->from('karyawan')
                            ->where(function($query) use ($id_com){
                                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                    if($id_com > 0){
                                        $query->where('id_com', $id_com);
                                    }else if($id_com == '0'){
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    }else{
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    } 
                                }else{
                                    $query->where('karyawan.id_com', Auth::user()->id_com);
                                }
                            });
                    
                });
            }

    if($id_com != null ){
        $company = DB::table('company')->select('name')->where('id_com',$id_com)->first()->name;
    }else {
        $company = DB::table('company')->select('name')->where('id_com',Auth::user()->id_com)->first()->name;
    }
        return view('ekspor.requestkaryawan',[
            'data' => $data->get(),
            'priode' => $datesss,
            'company' =>$company  
        ]);

 
        }
    }

