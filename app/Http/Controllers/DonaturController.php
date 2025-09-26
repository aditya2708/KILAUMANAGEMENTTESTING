<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donatur;
use App\Models\Kolektors;
use App\Models\Prosp;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Jalur;
use App\Models\SumberDana;
use App\Models\Prog;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Tunjangan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DonaturExport;
use App\Imports\DonaturImport;
// use Illuminate\Support\Str;
use App\Exports\CekDonaturExport;
use Carbon\Carbon;
use Str;

use DB;

use DataTables;

use Auth;

class DonaturController extends Controller
{

    public function index(Request $request, Donatur $donas)
    {
        if ($request->ajax()) {
            
            $kondisi = $request->prosp == '' ? 'closing' : $request->prosp;
            
            if($request->koordinat == ''){
                $koordinat = "donatur.id > 0";
            }else if($request->koordinat == '1'){
                $koordinat = "latitude IS NOT NULL AND longitude IS NOT NULL";
            }else if($request->koordinat == '0'){
                $koordinat = "latitude IS NULL AND longitude IS NULL";
            }
            
            if(Auth::user()->level == 'admin' || Auth::user()->level == 'operator pusat' || Auth::user()->keuangan == 'keuangan pusat'){

                if ($request->tgl != '') {
                    $tgl = explode(' - ', $request->tgl);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
                
                $now = date('Y-m-d');
                $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
                
                $w = $request->warning;
                $s = $request->status;
                $kotah = $request->kota;

                $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND $koordinat")
                    ->where(function($query) use ($request, $w) {
                        if(isset($request->warning)){
                            $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                            $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                            $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                            $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                            
                        }
                    })
                    
                    ->where(function($query) use ($request, $s) {
                        if(isset($request->status)){
                            $query->whereIn('status', $s);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kota)){
                            $query->whereIn('donatur.id_kantor', $kotah);
                        }
                    })
                    
                    
                    ->whereIn('donatur.id', function($query) use ($kondisi){
                        $query->select('id_don')->from('prosp')->where('ket',$kondisi);
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->petugas)){
                            $query->where('petugas', $request->petugas);
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->ui)){
                            $query->where('user_insert', $request->ui);
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->jk)){
                            if($request->jk == 'unknown'){
                                $query->where('jk', null);
                            }else{
                                $query->where('jk', $request->jk);
                            }
                        }
                    });
                    
            }else{
                if (Auth::user()->level == 'spv') {
                    $k = null;
                } else {
                    $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                }
                
                $kot = Auth::user()->id_kantor;
                
                if ($request->tgl != '') {
                    $tgl = explode(' - ', $request->tgl);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
    
                $now = date('Y-m-d');
                $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
                
                $w = $request->warning;
                $s = $request->status;
                $kotah = $request->kota;
    
                if ($k == null) {
    
                    $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND id_kantor = '$kot' AND $koordinat")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        })
                        ->where(function($query) use ($request) {
                            if(isset($request->petugas)){
                                $query->where('petugas', $request->petugas);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->ui)){
                                $query->where('user_insert', $request->ui);
                            }
                        })
                        
                        ->whereIn('donatur.id', function($query) use ($kondisi){
                            $query->select('id_don')->from('prosp')->where('ket',$kondisi);
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->jk)){
                                if($request->jk == 'unknown'){
                                    $query->where('jk', null);
                                }else{
                                    $query->where('jk', $request->jk);
                                }
                            }
                        });
                    
                } else {
                    
                    $w = $request->warning;
                    $s = $request->status;
                    $kotah = $request->kota;
                    
    
                    $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND $koordinat")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        })
                        ->where(function($query) use ($request, $kot, $k, $kotah) {
                            if(isset($request->kota)){
                                $query->whereIn('donatur.id_kantor', $kotah);
                            }else{
                                $query->whereRaw("(id_kantor = '$kot' OR id_kantor = '$k->id')");
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->petugas)){
                                $query->where('petugas', $request->petugas);
                            }
                        })
                        
                        ->whereIn('donatur.id', function($query) use ($kondisi){
                            $query->select('id_don')->from('prosp')->where('ket',$kondisi);
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->ui)){
                                $query->where('user_insert', $request->ui);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->jk)){
                                if($request->jk == 'unknown'){
                                    $query->where('jk', null);
                                }else{
                                    $query->where('jk', $request->jk);
                                }
                            }
                        });
                }
            }
            
            if(isset($request->program)){
                // $data->where(function($query) use ($request) {
                $data->join('prosp', function($join) use ($request) {
                    $join->on('prosp.id_don' ,'=', 'donatur.id')
                            ->select('prosp.id_prog')
                            ->where('prosp.id_prog', $request->program);
                });
            }
            
            if(isset($request->traktif) && $request->traktif != ''){
                if ($request->traktif != '') {
                    $tglt = explode(' - ', $request->traktif);
                    $darit = date('Y-m-d', strtotime($tglt[0]));
                    $sampait = date('Y-m-d', strtotime($tglt[1]));
                }
                
                $tglst = "DATE(transaksi.tanggal) >= '$darit' AND DATE(transaksi.tanggal) <= '$sampait'";
                
                $data->join('transaksi', function($join) use ($request, $tglst) {
                    $join->on('transaksi.id_donatur' ,'=', 'donatur.id')
                            ->select('transaksi.tanggal')
                            ->whereRaw("$tglst AND transaksi.status = 'Donasi'");
                });
            }
            
            
            if(isset($request->traknon) && $request->traknon != ''){
                if ($request->traknon != '') {
                    $tgln = explode(' - ', $request->traknon);
                    $darin = date('Y-m-d', strtotime($tgln[0]));
                    $sampain = date('Y-m-d', strtotime($tgln[1]));
                }
                
                $tglsn = "DATE(lee.tanggal) >= '$darin' AND DATE(lee.tanggal) <= '$sampain'";
                
                $data->join('transaksi as lee', function($join) use ($request, $tglsn) {
                    $join->on('lee.id_donatur' ,'=', 'donatur.id')
                            ->select('lee.tanggal')
                            ->whereRaw("$tglsn AND lee.jumlah <= 0");
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('program', function ($data) {
                    $prosp = Prosp::select('id_prog')->where('id_don', $data->id)->where('ket', 'closing')->get();

                    // $prog = $data->program === 'b:0;' || @unserialize($data->program) !== false ? unserialize($data->program) : [];
                    $isi = '';

                    if (count($prosp) > 0) {
                        foreach ($prosp as $val => $key) {
                            $pr = Prog::where('id_program',  $key->id_prog)->first();
                            $isi .= '<li>' . $pr->program . '</li>';
                        }
                        $head = '<ul>' . $isi . '</ul>';
                    } else {
                        $head = '';
                    }

                    return $head;
                })

                ->addColumn('regis', function ($data) {
                    $status = date('Y-m-d', strtotime($data->created_at));

                    return $status;
                })

                ->addColumn('st', function ($data) {
                    if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                        $status = '<button class="donat btn btn-primary btn-sm" id="' . $data->id . '">Aktifkan</button>';
                    } else {
                        $status = '<button class="donat btn btn-warning btn-sm" id="' . $data->id . '">Non-Aktifkan</button>';
                        // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                    }
                    return $status;
                })
                
                ->addColumn('kematian', function ($data) {
                    $slug = $data->id;
                    $link = url('riwayat-donasi/'.$slug);

                    $edit ='<a class="btn btn-success btn-sm tabindex="0" data-toggle="tooltip" title="Edit" target="blank_" href="' . url('/donatur/edit/' . $data->id) . '"><i class="fa fa-edit"></i></a>';
                    
                    $rincian = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Rincian">
                    
                                <div class="basic-dropdown">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-eye"></i></button>

                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="' . $link . '" target="_blank">Rincian Donasi</a>
                                            <a class="dropdown-item" target="_blank" href="' . url('/riwayat-kunjungan/' . $data->id) . '">Rincian Kunjungan</a>
                                        </div>
                                    </div>
                                </div>
                                
                                </span>';
                                
                    $rindon = '<a class="btn btn-primary btn-sm" tabindex="0" data-toggle="tooltip" title="Rincian Donasi" href="' . $link . '" target="_blank"><i class="fa fa-donate"></i></a>';
                    $rinkun = '<a class="btn btn-info btn-sm" tabindex="0" data-toggle="tooltip" title="Rincian Kunjungan" target="_blank" href="' . url('/riwayat-kunjungan/' . $data->id) . '"><i class="fa fa-list"></i></a>';
                            
                    $hapus = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Hapus"><button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></span>';
                    
                    
                    $button =   '<div class="dropdown dropstart">
    								<button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false">
    								    <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
    								</button>
    									
    								<div class="dropdown-menu dropdown-menu-eleh" style="width: 240px; background: #fff; border: 1px">
    									<div class="d-flex justify-content-evenly">
        									
        									<div>'.$edit.'</div>
        									<div>'.$rindon.'</div>
        									<div>'.$rinkun.'</div>
        									<div>'.$hapus.'</div>
    									</div>
    								</div>
    							</div>';
    				return $button;
                })

                ->editColumn('wow', function ($data) {
                    if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                        $c = 'checked';
                    } else {
                        $c = '';
                    }
                    // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="' . ($data->status  == 'Ditarik' | $data->status  == 'Off' ? 'Nonaktif' : 'Aktif') . '"><label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $data->id . '"  type="checkbox" ' . ($data->status  == 'Ditarik' | $data->status  == 'Off' ? "" : "checked") . ' /> <div class="slider round"> </div> </label></span>';
                    return $button;
                })

                ->addColumn('hapus', function ($data) {
                    $status = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Hapus"><button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></span>';

                    return $status;
                })

                ->addColumn('edito', function ($data) {
                    $slug = $data->id;
                    $link = url('riwayat-donasi/'.$slug);

                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit"><div class="btn-group">
                                        <a class="btn btn-success btn-sm" target="blank_" href="' . url('/donatur/edit/' . $data->id) . '"><i class="fa fa-edit"></i></a>
                                   </div></span>';
                    return $button;
                })

                ->addColumn('action', function ($data) {
                    $slug = $data->id;
                    $link = url('riwayat-donasi/'.$slug);

                    // <i class="fa fa-angle-down ms-3"></i>
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Rincian">
                    
                    <div class="basic-dropdown">
                    <div class="dropdown">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-eye"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="' . $link . '" target="_blank">Rincian Donasi</a>
                        <a class="dropdown-item" target="_blank" href="' . url('/riwayat-kunjungan/' . $data->id) . '">Rincian Kunjungan</a>
                    </div>
                    </div>
                    </span>';


                    return $button;
                })
                
                ->addColumn('gmbr', function ($data) {
                    $gmbr = $data->gambar_donatur;
                    
                    $link1 = 'https://kilauindonesia.org/datakilau/gambarDonatur/'.$data->gambar_donatur;
                    $link2 = 'https://kilauindonesia.org/kilau/gambarDonatur/'.$data->gambar_donatur;
                            
                    if(!empty($link1)){
                        $oe = 'https://kilauindonesia.org/datakilau/gambarDonatur/'.$data->gambar_donatur ;
                    }else {
                        if(!empty($link2)){
                            $oe = 'https://kilauindonesia.org/kilau/gambarDonatur/'.$data->gambar_donatur ;
                        }else{
                            // $oe = 'https://kilauindonesia.org/kilau/gambarDonatur/'.$data->gambar_donatur ;
                            $oe = '#';
                        }
                    }
                    
                    $button = '<img src="'.$oe.'"  width="25px" height="25px" id="zoomImage" alt="Image" onclick="uwuw(this)" >';
                    
                    return $button;
                })
                
                ->rawColumns(['action', 'st', 'regis', 'program', 'edito', 'hapus', 'wow', 'gmbr', 'kematian'])

                ->make(true);
        }
        
        $kot = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kot)->first();
        
        $datstat = DB::select("SELECT distinct status from donatur");
        $datjen = DB::select("SELECT distinct jk from donatur");
        
        if(Auth::user()->level == 'admin'  || Auth::user()->keuangan == 'keuangan pusat'|| Auth::user()->level == 'operator pusat'){
            $user_insert = Donatur::select('user_insert')->where('user_insert','!=', null)->distinct()->get();
            $petugas = Donatur::select('petugas','id_koleks')->where('id_koleks','!=', null)->orderBy('petugas', 'asc')->distinct()->get();
        }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
            if($k == null){
                $user_insert = Donatur::select('user_insert')->whereRaw("user_insert IS NOT NULL AND id_kantor = '$kot'")->distinct()->get();
                $petugas = Donatur::select('petugas','id_koleks')->whereRaw("id_koleks IS NOT NULL AND id_kantor = '$kot'")->orderBy('petugas', 'asc')->distinct()->get();   
            }else{
                $user_insert = Donatur::select('user_insert')->whereRaw("user_insert IS NOT NULL AND (id_kantor = '$kot' OR id_kantor = '$k->id')")->distinct()->get();
                $petugas = Donatur::select('petugas','id_koleks')->whereRaw("id_koleks IS NOT NULL AND (id_kantor = '$kot' OR id_kantor = '$k->id')")->orderBy('petugas', 'asc')->distinct()->get();
            }
        }
        
        
        return view('donatur.index',compact('datstat','datjen','user_insert','petugas'));
    }
    
    public function donatur_tab(Request $request, Donatur $donas)
    {
        if ($request->ajax()) {
            
            if(Auth::user()->level == 'admin'  || Auth::user()->keuangan == 'keuangan pusat'){

                // $status = $request->status != '' ? "status = '$request->status'" : "status != ''";
                // $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor IS NOT NULL";
                if ($request->tgl != '') {
                    $tgl = explode(' - ', $request->tgl);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
    
                $now = date('Y-m-d');
                $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
                
                $w = $request->warning;
                $s = $request->status;
                $kotah = $request->kota;
                

                $data = Donatur::whereRaw("$tgls")
                    ->where(function($query) use ($request, $w) {
                        if(isset($request->warning)){
                            $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                            $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                            $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                            $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                            
                        }
                    })
                    
                    ->where(function($query) use ($request, $s) {
                        if(isset($request->status)){
                            $query->whereIn('status', $s);
                        }
                    })
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kota)){
                            $query->whereIn('donatur.id_kantor', $kotah);
                        }
                    });
    
                    // if ($request->warning == 'aktif') {
                    //     $data = Donatur::whereRaw("$kota AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    // } elseif ($request->warning == 'nonaktif') {
                    //     $data = Donatur::whereRaw("$kota AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    // } elseif ($request->warning == 'warning') {
                    //     $data = Donatur::whereRaw("$kota AND $status AND warning = 1 AND $tgls");
                    // } else {
                    //     $data = Donatur::whereRaw("$kota AND $status AND $tgls");
                    // }
                    
            }else if(Auth::user()->level == 'agen'){
                $status = $request->status != '' ? "status = '$request->status'" : "status != ''";
                $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor IS NOT NULL";
                if ($request->tgl != '') {
                    $tgl = explode(' - ', $request->tgl);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
                
                $me = Auth::user()->id;
    
                $now = date('Y-m-d');
                $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
    
                if ($request->warning == 'aktif') {
                    $data = Donatur::whereRaw("$kota AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls AND id_koleks = '$me'");
                } elseif ($request->warning == 'nonaktif') {
                    $data = Donatur::whereRaw("$kota AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls AND id_koleks = '$me'");
                } elseif ($request->warning == 'warning') {
                    $data = Donatur::whereRaw("$kota AND $status AND warning = 1 AND $tgls AND id_koleks = '$me'");
                } else {
                    $data = Donatur::whereRaw("$kota AND $status AND $tgls AND id_koleks = '$me'");
                }
            }else{
                if (Auth::user()->level == 'spv') {
                    $k = null;
                } else {
                    $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                }
                
                $kot = Auth::user()->id_kantor;

                // $status = $request->status != '' ? "status = '$request->status'" : "status != ''";
                // $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != ''";
                
                if ($request->tgl != '') {
                    $tgl = explode(' - ', $request->tgl);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
    
                $now = date('Y-m-d');
                $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
                
                $w = $request->warning;
                $s = $request->status;
                $kotah = $request->kota;
    
                if ($k == null) {
    
                    $data = Donatur::whereRaw("$tgls AND id_kantor = '$kot'")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        });
                    
                    // if ($request->warning == 'aktif') {
                    //     $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    // } elseif ($request->warning == 'nonaktif') {
                    //     $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    // } elseif ($request->warning == 'warning') {
                    //     $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND warning = 1 AND $tgls");
                    // } else {
                    //     $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND $tgls");
                    // }
                    
                } else {
                    
                    $w = $request->warning;
                    $s = $request->status;
                    $kotah = $request->kota;
                    
    
                    $data = Donatur::whereRaw("$tgls")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        })
                        ->where(function($query) use ($request, $kot, $k, $kotah) {
                            if(isset($request->kota)){
                                $query->whereIn('donatur.id_kantor', $kotah);
                            }else{
                                $query->whereRaw("(id_kantor = '$kot' OR id_kantor = '$k->id')");
                            }
                        });
                    
                    
                    // if ($request->kota != '') {
                    //     if ($request->warning == 'aktif') {
                    //         $data = Donatur::whereRaw("$kota AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    //     } elseif ($request->warning == 'nonaktif') {
                    //         $data = Donatur::whereRaw("$kota AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    //     } elseif ($request->warning == 'warning') {
                    //         $data = Donatur::whereRaw("$kota AND $status AND warning = 1 AND $tgls");
                    //     } else {
                    //         $data = Donatur::whereRaw("$kota AND $status AND $tgls");
                    //     }
                    // } else {
                    //     if ($request->warning == 'aktif') {
                    //         $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    //     } elseif ($request->warning == 'nonaktif') {
                    //         $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    //     } elseif ($request->warning == 'warning') {
                    //         $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND warning = 1 AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND warning = 1 AND $tgls");
                    //     } else {
                    //         $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND $tgls");
                    //     }
                    // }
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('program', function ($data) {
                    $prosp = Prosp::select('id_prog')->where('id_don', $data->id)->where('ket', 'closing')->get();

                    // $prog = $data->program === 'b:0;' || @unserialize($data->program) !== false ? unserialize($data->program) : [];
                    $isi = '';

                    if (count($prosp) > 0) {
                        foreach ($prosp as $val => $key) {
                            $pr = Prog::where('id_program',  $key->id_prog)->first();
                            $isi .= '<li>' . $pr->program . '</li>';
                        }
                        $head = '<ul>' . $isi . '</ul>';
                    } else {
                        $head = '';
                    }

                    return $head;
                })

                ->addColumn('regis', function ($data) {
                    $status = date('Y-m-d', strtotime($data->created_at));

                    return $status;
                })

                ->addColumn('st', function ($data) {
                    if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                        $status = '<button class="donat btn btn-primary btn-sm" id="' . $data->id . '">Aktifkan</button>';
                    } else {
                        $status = '<button class="donat btn btn-warning btn-sm" id="' . $data->id . '">Non-Aktifkan</button>';
                        // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                    }
                    return $status;
                })

                ->editColumn('wow', function ($data) {
                    if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                        $c = 'checked';
                    } else {
                        $c = '';
                    }
                    // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="' . ($data->status  == 'Ditarik' | $data->status  == 'Off' ? 'Nonaktif' : 'Aktif') . '"><label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $data->id . '"  type="checkbox" ' . ($data->status  == 'Ditarik' | $data->status  == 'Off' ? "" : "checked") . ' /> <div class="slider round"> </div> </label></span>';
                    return $button;
                })

                ->addColumn('hapus', function ($data) {
                    $status = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Hapus"><button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></span>';

                    return $status;
                })

                ->addColumn('edito', function ($data) {
                    $slug = $data->id;
                    $link = url('riwayat-donasi/'.$slug);

                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit"><div class="btn-group">
                                        <a class="btn btn-success btn-sm" target="blank_" href="' . url('/donatur/edit/' . $data->id) . '"><i class="fa fa-edit"></i></a>
                                   </div></span>';
                    return $button;
                })

                ->addColumn('action', function ($data) {
                    $slug = $data->id;
                    $link = url('riwayat-donasi/'.$slug);

                    // <i class="fa fa-angle-down ms-3"></i>
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Rincian">
                    
                    <div class="basic-dropdown">
                    <div class="dropdown">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-eye"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="' . $link . '" target="_blank">Rincian Donasi</a>
                        <a class="dropdown-item" target="_blank" href="' . url('/riwayat-kunjungan/' . $data->id) . '">Rincian Kunjungan</a>
                    </div>
                    </div>
                    </span>';


                    return $button;
                })
                ->rawColumns(['action', 'st', 'regis', 'program', 'edito', 'hapus', 'wow', 'program'])

                ->make(true);
        }
        
        $datstat = DB::select("SELECT distinct status from donatur");
        
        return view('donatur.donatur_tab',compact('datstat'));
    }

    public function indexkacab(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->level == 'spv') {
                $k = null;
            } else {
                $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            }

            $kot = Auth::user()->id_kantor;

            $status = $request->status != '' ? "status = '$request->status'" : "status != ''";
            $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != ''";
            if ($request->tgl != '') {
                $tgl = explode(' - ', $request->tgl);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }

            $now = date('Y-m-d');
            $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";

            if ($k == null) {
                if ($request->warning == 'aktif') {
                    $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                } elseif ($request->warning == 'nonaktif') {
                    $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                } elseif ($request->warning == 'warning') {
                    $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND warning = 1 AND $tgls");
                } else {
                    $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND $tgls");
                }
            } else {
                if ($request->kota != '') {
                    if ($request->warning == 'aktif') {
                        $data = Donatur::whereRaw("$kota AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    } elseif ($request->warning == 'nonaktif') {
                        $data = Donatur::whereRaw("$kota AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    } elseif ($request->warning == 'warning') {
                        $data = Donatur::whereRaw("$kota AND $status AND warning = 1 AND $tgls");
                    } else {
                        $data = Donatur::whereRaw("$kota AND $status AND $tgls");
                    }
                } else {
                    if ($request->warning == 'aktif') {
                        $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND status != 'Ditarik' AND status != 'Off' AND $tgls");
                    } elseif ($request->warning == 'nonaktif') {
                        $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND (status = 'Ditarik' OR status = 'Off') AND $tgls");
                    } elseif ($request->warning == 'warning') {
                        $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND warning = 1 AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND warning = 1 AND $tgls");
                    } else {
                        $data = Donatur::whereRaw("id_kantor = '$kot' AND $status AND $tgls")->orWhereRaw("id_kantor = '$k->id' AND $status AND $tgls");
                    }
                }
            }

            if (Auth::user()->level == 'kacab' || Auth::user()->level == 'agen') {
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('program', function ($data) {
                        $prosp = Prosp::select('id_prog')->where('id_don', $data->id)->where('ket', 'closing')->get();

                        $isi = '';

                        if (count($prosp) > 0) {
                            foreach ($prosp as $val => $key) {
                                $pr = Prog::where('id_program',  $key->id_prog)->first();
                                $isi .= '<li>' . $pr->program . '</li>';
                            }
                            $head = '<ul>' . $isi . '</ul>';
                        } else {
                            $head = '';
                        }

                        return $head;
                    })

                    ->addColumn('st', function ($data) {
                        if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                            $status = '<button class="donat btn btn-primary btn-sm" id="' . $data->id . '">Aktifkan</button>';
                        } else {
                            $status = '<button class="donat btn btn-warning btn-sm" id="' . $data->id . '">Non-Aktifkan</button>';
                            // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                        }

                        // $status .= '&nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';

                        return $status;
                    })
                    ->addColumn('regis', function ($data) {
                        $status = date('Y-m-d', strtotime($data->created_at));

                        return $status;
                    })
                    ->addColumn('action', function ($data) {
                        
                        $button = '<a class="btn btn-info btn-sm" target="blank_" href="' . url('/riwayat-kunjungan/' . $data->id) . '"><i class="fa fa-eye"></i></a>';


                        return $button;
                    })

                    ->addColumn('hapus', function ($data) {
                        $status = '<button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';

                        return $status;
                    })

                    ->editColumn('wow', function ($data) {
                        if ($data->status  == 'Ditarik' | $data->status  == 'Off') {
                            $c = 'checked';
                        } else {
                            $c = '';
                        }
                        // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                        $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $data->id . '"  type="checkbox" ' . ($data->status  == 'Ditarik' | $data->status  == 'Off' ? "" : "checked") . ' /> <div class="slider round"> </div> </label>';
                        return $button;
                    })

                    ->addColumn('edito', function ($data) {
                        $slug = $data->id;
                        $link = url('riwayat-donasi/'.$slug);

                        $button = '<div class="btn-group">
                                        <a class="btn btn-success btn-sm" target="blank_" href="' . url('/donatur/edit/' . $data->id) . '"><i class="fa fa-edit"></i></a>
                                   </div>';

                        return $button;
                    })
                    // ->rawColumns(['st'])
                    ->rawColumns(['action', 'st', 'regis', 'program', 'edito', 'hapus', 'wow'])


                    ->make(true);
            } else {

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('program', function ($data) {
                        $prosp = Prosp::select('id_prog')->where('id_don', $data->id)->where('ket', 'closing')->get();

                        // $prog = $data->program === 'b:0;' || @unserialize($data->program) !== false ? unserialize($data->program) : [];
                        $isi = '';

                        if (count($prosp) > 0) {
                            foreach ($prosp as $val => $key) {
                                $pr = Prog::where('id_program',  $key->id_prog)->first();
                                $isi .= '<li>' . $pr->program . '</li>';
                            }
                            $head = '<ul>' . $isi . '</ul>';
                        } else {
                            $head = '';
                        }

                        return $head;
                    })

                    ->addColumn('regis', function ($data) {
                        $status = date('Y-m-d', strtotime($data->created_at));

                        return $status;
                    })

                    ->rawColumns(['regis', 'program'])
                    ->make(true);
            }
        }
        return view('donatur.indexkacab');
    }

    function filstatus(Request $request)
    {

        $stat = $request->status;
        $kot = $request->kota;
        //  $don = Donatur::where('status', '=', $stat)->get();
        if ($stat == '' && $kot == '') {
            $don = Donatur::all();
        } elseif ($stat == '' && $kot != '') {
            $don = Donatur::where('kota', '=', $kot)->get();
        } elseif ($stat != '' && $kot == '') {
            $don = Donatur::where('status', '=', $stat)->get();
        } else {
            $don = Donatur::where('status', '=', $stat)->where('kota', '=', $kot)->get();
        }

        // dd ($don);

        return response()->json($don);
    }


    public function show(Donatur $donatur)
    {
        return view('donatur.show', compact('donatur'));
    }



    public function import(Request $request)
    {

        // $this->validate($request, [
        //     'file' => 'required|mimes:csv,xls,xlsx'
        // ]);

        // $file = $request->file('file');

        // // membuat nama file unik
        // $nama_file = $file->hashName();

        // //temporary file
        // $path = $file->storeAs('public/excel/',$nama_file);

        // // import data
        // $import = Excel::import(new DonaturImport(), storage_path('app/public/excel/'.$nama_file));

        // //remove from server
        // Storage::delete($path);

        // if($import) {
        //     //redirect
        //     return redirect('donaturbandung');
        // } else {
        //     //redirect
        //     return redirect('donaturbandung');
        // }

        $file = $request->file('file');
        $nama = $file->getClientOriginalName();
        $file->move('excel', $nama);

        Excel::import(new DonaturImport, ('/home/kilauindonesia/public_html/kilau/excel/' . $nama));
        \LogActivity::addToLog(Auth::user()->name . ' Mengimport Data Donatur');
        if (Auth::user()->level == ('user')) {
            return redirect('donaturcabang');
        } elseif (Auth::user()->level == ('kacaba')) {
            return redirect('donaturcabang');
        } elseif (Auth::user()->level == ('useri')) {
            return redirect('donaturcabang');
        } elseif (Auth::user()->level == ('kacab')) {
            return redirect('donaturcabang');
        } elseif (Auth::user()->level == ('kacabi')) {
            return redirect('donaturcabang');
        }
        //  return redirect('donaturbandung');

        //  Excel::import(new DonaturImport, $request->file('file')->store('temp'));
        return redirect('donatur');
    }

    public function export(Request $request)
    {
        
        if ($request->tgl != '') {
            $tgl = explode(' - ', $request->tgl);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        $p = [];
        $x = [];
        
        if(isset($request->kota)){
            if(count($request->kota) > 0){
                $col = Kantor::whereIn('id', $request->kota)->get();
                foreach($col as $c){
                    $p[] = $c->unit;
                    $x[] = $c->unit;
                }
                
                $ah = 'Donatur '.implode(', ', $p);
                $oh = strtolower('donatur_'.implode('_', $p));
            }
        }else{
            $ah = 'Semua Donatur';
            $oh = 'semua_donatur';
        }
        
        
        $ea = $ah;
        
        $txt_judul = $request->tgl != '' ? 'Periode '. $dari.' s.d '.$sampai : 'Semua Periode' ;
        $txt_file = $request->tgl != '' ? '_periode_'. $dari.'_'.$sampai : 'semua_periode' ;
        
        // $tgls = $request->daterange != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
        // $w = $request->warning == null ? '' : json_encode($request->warning) ;
        // $s = $request->status == null ? '' : json_encode($request->status) ;
        // $kotah = $request->kota  == null ? '' : json_encode($request->kota); 
        
        // $traknon = $request->traknon == null ? '' : $request->traknon;
        // $traktif = $request->traktif == null ? '' : $request->traktif;
        // $program = $request->program == null ? '' : $request->program;
        
        // $jk = $request->jk == null ? '' : $request->jk;
        // $ui = $request->ui == null ? '' : $request->ui;
        // $petugas = $request->petugas == null ? '' : $request->petugas;
        
        $response = Excel::download(new DonaturExport($request, $txt_judul, $ea), $oh.'_'.$txt_file.'.xlsx');
        ob_end_clean();
        \LogActivity::addToLog(Auth::user()->name . ' Mengeksport Data Donatur');
        return $response;
    }
    
    public function cek_aja_nih(Request $request){
        
        $response = Excel::download(new CekDonaturExport, 'ea.xlsx');
        ob_end_clean();
        
        return $response;
    }

    public function destroy($id)
    {
        // dd('sda');
        $donatur = Donatur::findOrFail($id);
        $donatur->delete();
        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Donatur ' . $donatur->nama);
        //   if(Auth::user()->level == ('user')) {
        //     return back();
        // } elseif (Auth::user()->level == ('kacaba')) {
        //     return back();
        // } elseif (Auth::user()->level == ('useri')) {
        //     return back();
        // }elseif (Auth::user()->level == ('kacab')) {
        //     return back();
        // }elseif (Auth::user()->level == ('kacabi')) {
        //     return back();
        // }
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function changeoffdon(Request $request)
    {
        
        $data = Donatur::where('id', $request->id)->first();
        // dd($request->cek);
        $bayar = $data->pembayaran;
        $status_sekarang = $data->status;

        if ($bayar == 'transfer') {
            if ($status_sekarang == 'Ditarik' | $status_sekarang == 'Off') {
                Donatur::where('id', $request->id)->update([
                    'status' => 'belum dikunjungi',
                    'tgl_aktif' => date('Y-m-d'),
                    'user_update' => Auth::user()->id

                ]);
                \LogActivity::addToLoghfm(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id , 'kosong', 'donatur', 'update', $request->id);
                // \LogActivity::addToLog(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id);
            } else {
                Donatur::where('id', $request->id)->update([
                    'status' => 'Off',
                    'tgl_nonaktif' => date('Y-m-d'),
                    'user_update' => Auth::user()->id
                ]);
                // \LogActivity::addToLog(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id);
                \LogActivity::addToLoghfm(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id , 'kosong', 'donatur', 'update', $request->id);
            }
        } else {
            if ($status_sekarang == 'Ditarik' | $status_sekarang == 'Off') {
                Donatur::where('id', $request->id)->update([
                    'status' => 'belum dikunjungi',
                    'tgl_aktif' => date('Y-m-d'),
                    'user_update' => Auth::user()->id

                ]);
                // \LogActivity::addToLog(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id);
                \LogActivity::addToLoghfm(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id , 'kosong', 'donatur', 'update', $request->id);
            } else {
                Donatur::where('id', $request->id)->update([
                    'status' => 'Ditarik',
                    'tgl_nonaktif' => date('Y-m-d'),
                    'user_update' => Auth::user()->id
                ]);
                // \LogActivity::addToLog(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id);
                \LogActivity::addToLoghfm(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama . ' Dengan ID ' . $request->id , 'kosong', 'donatur', 'update', $request->id);
            }
        }
        // if($request->cek == 'email' || $request->cek == 'email_entitas'){
        //     $datas = Donatur::Where('email', $data->email)->get();
        // }elseif($request->cek == 'nohp'){
        //     $datas = Donatur::Where('no_hp', $data->no_hp)->get();
        // }elseif($request->cek == 'nohp_entitas'){
        //     $datas = Donatur::Where('nohap', $data->nohap)->get();
        // }
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function offdon($id, Request $request)
    {
        // public function offdon($id, Request $request){
        $data = Donatur::where('id', $id)->first();
        // dd($request->cek);
        $bayar = $data->pembayaran;
        $status_sekarang = $data->status;

        if ($bayar == 'transfer') {
            if ($status_sekarang == 'Ditarik' | $status_sekarang == 'Off') {
                Donatur::where('id', $id)->update([
                    'status' => 'belum dikunjungi',
                    'user_update' => Auth::user()->id

                ]);
                \LogActivity::addToLog(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama);
            } else {
                Donatur::where('id', $id)->update([
                    'status' => 'Off',
                    'user_update' => Auth::user()->id
                ]);
                \LogActivity::addToLog(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama);
            }
        } else {
            if ($status_sekarang == 'Ditarik' | $status_sekarang == 'Off') {
                Donatur::where('id', $id)->update([
                    'status' => 'belum dikunjungi',
                    'user_update' => Auth::user()->id

                ]);
                \LogActivity::addToLog(Auth::user()->name . ' Mengaktifkan Data Donatur ' . $data->nama);
            } else {
                Donatur::where('id', $id)->update([
                    'status' => 'Ditarik',
                    'user_update' => Auth::user()->id
                ]);
                \LogActivity::addToLog(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $data->nama);
            }
        }
        if ($request->cek == 'email' || $request->cek == 'email_entitas') {
            $datas = Donatur::Where('email', $data->email)->get();
        } elseif ($request->cek == 'nohp') {
            $datas = Donatur::Where('no_hp', $data->no_hp)->get();
        } elseif ($request->cek == 'nohp_entitas') {
            $datas = Donatur::Where('nohap', $data->nohap)->get();
        }
        return response()->json(['success' => 'Data is successfully updated', 'data' => $datas]);
    }


    public function add()
    {
        if (Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang')) {
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $datdon =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->get();
        } else {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->get();
            $datdon =  Kantor::select('unit', 'id')->get();
        }
        return view('donatur.create_don', compact('petugas', 'datdon'));
    }


    public function add_new()
    {
        if (Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang')) {
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $datdon =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->where('users.id_com', Auth::user()->id_com)->get();
        } else {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->get();
            $datdon =  Kantor::select('unit', 'id')->where('id_com',Auth::user()->id_com)->get();
        }
        return view('donatur.add_donatur', compact('petugas', 'datdon'));
    }

    public function add_don(Request $request)
    {
        
        $verif = Donatur::where('no_hp', $request->nohp)->orWhere('email', $request->email)->get();
        $token = Str::random(60);

        if (count($verif) > 0) {
            return response()->json(['errors' => 'No Hp atau E-mail sudah digunakan']);
        } else {
            // dd('bener');
            $id_peg = [];
            $id_sumdan = [];
            $id_program = [];
            $statprog = [];
            foreach ($request->arr as $val) {
                $id_peg[] = $val['id_peg'];
                $id_sumdan[] = $val['id_sumdan'];
                $id_program[] = $val['id_program'];
                $statprog[] = $val['statprog'];
            }

            if (!empty($request->jalur)) {
                $jalur = Jalur::where('id_jalur', $request->jalur)->first();
            }

            $data = new Donatur;
            $data->jenis_donatur = $request->jenis;
            $data->id_koleks = $request->id_koleks;
            $data->petugas = $request->petugas;
            $data->id_kantor = $request->id_kantor;
            $data->status = 'belum dikunjungi';
            if ($request->jenis == 'personal') {
                $data->nama = $request->nama;
                $data->tahun_lahir = $request->tahun_lahir;
                $data->jk = $request->jk;
                $data->email = $request->email;
                $data->no_hp = $request->nohp;
                $data->pekerjaan = $request->pekerjaan;
                $data->provinsi = $request->provinsi;
                $data->kota = $request->kota;
                $data->alamat = $request->alamat;
            }

            if ($request->jenis == 'entitas') {
                $data->email = $request->email1;
                $data->alamat = $request->alamat1;
                $data->nama = $request->perusahaan;
                $data->nohap = $request->nohap;
                $data->kota = $request->kotaa;
                $data->provinsi = $request->provinsii;
                $data->orng_dihubungi = $request->orng_dihubungi;
                $data->jabatan = $request->jabatan;
                $data->no_hp = $request->no_hp2;
            }
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->id_peg = serialize($id_peg);
            $data->token = $token;
            $data->id_sumdan = serialize($id_sumdan);
            $data->program = serialize($id_program);
            $data->statprog = serialize($statprog);
            $data->pembayaran = $request->pembayaran;
            $data->id_jalur = $request->jalur == '' ? null : $jalur->id_jalur;
            $data->jalur = $request->jalur == '' ? null : $jalur->nama_jalur;
            $data->user_insert = Auth::user()->id;

            if ($request->hasFile('foto')) {
                // $folderPath = "kilau/gambarDonatur/";
                // $image_parts = explode(";base64,", $request->foto);
                // $image_type_aux = explode("image/", $image_parts[0]);
                // $image_type = $image_type_aux[1];
                // $image_base64 = base64_decode($image_parts[1]);
                // $image_name = $request->namafile;
                // $file = $folderPath . $image_name;
                // file_put_contents($file, $image_base64);

                // $data->gambar_donatur = $image_name;
                $image = $request->file('foto');

                if ($image->isValid()) {
                    $image_name = $image->getClientOriginalName();
                    $upload_path = 'gambarUpload';
                    $image->move($upload_path, $image_name);
                    
                    $data->gambar_donatur = $image_name;
                }
            }

            $data->save();

            $don = Donatur::where('token', $token)->first();

            for ($i = 0; $i < count($id_sumdan); $i++) {
                $data = new Prosp;
                $data->id_peg = $id_peg[$i];
                $data->id_don = $don->id;
                $data->id_prog = $id_program[$i];
                $data->id_sumdan = $id_sumdan[$i];
                $data->ket = 'closing';
                $data->status = 1;
                $data->tgl_fol = date('Y-m-d');

                $data->save();
            }


            return response()->json(['success' => 'Data is successfully added']);
        }
        // dd($data);
    }
    
    public function add_don_new(Request $request)
    {
        // return $request->all();
        if($request->jenis == 'entitas'){
            $verif = Donatur::where('no_hp', $request->nohap)->orWhere('email', $request->email1)->get();
        }else{
            $verif = Donatur::where('no_hp', $request->nohp)->orWhere('email', $request->email)->get();
        }
        $token = Str::random(60);
        
        // return($verif);

        if (count($verif) > 0) {
            return response()->json(['errors' => 'No Hp atau E-mail sudah digunakan']);
        } else {
            // dd('bener');
            $id_peg = [];
            $id_sumdan = [];
            $id_program = [];
            $statprog = [];
            foreach ($request->arr as $val) {
                $id_peg[] = $val['id_peg'];
                $id_sumdan[] = $val['id_sumdan'];
                $id_program[] = $val['id_program'];
                $statprog[] = $val['statprog'];
            }

            if (!empty($request->jalur)) {
                $jalur = Jalur::where('id_jalur', $request->jalur)->first();
            }

            $data = new Donatur;
            $data->jenis_donatur = $request->jenis;
            $data->id_koleks = $request->id_koleks;
            $data->petugas = $request->petugas;
            $data->id_kantor = $request->id_kantor;
            $data->status = 'belum dikunjungi';
            if ($request->jenis == 'personal') {
                $data->nama = $request->nama;
                $data->tahun_lahir = $request->tahun_lahir;
                $data->jk = $request->jk;
                $data->email = $request->email;
                $data->no_hp = $request->nohp;
                $data->pekerjaan = $request->pekerjaan;
                $data->alamat = $request->alamat;
            }

            if ($request->jenis == 'entitas') {
                $data->email = $request->email1;
                $data->alamat = $request->alamat;
                $data->nama = $request->perusahaan;
                $data->nohap = $request->nohap;
                // $data->kota = $request->kotaa;
                // $data->provinsi = $request->provinsii;
                $data->orng_dihubungi = $request->orng_dihubungi;
                $data->jabatan = $request->jabatan;
                $data->no_hp = $request->no_hp2;
            }
            $data->provinsi = $request->provinsi;
            $data->kota = $request->kota;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->id_peg = serialize($id_peg);
            $data->token = $token;
            $data->id_sumdan = serialize($id_sumdan);
            $data->program = serialize($id_program);
            $data->statprog = serialize($statprog);
            $data->pembayaran = $request->pembayaran;
            $data->id_com = Auth::user()->id_com;
            $data->id_jalur = $request->jalur == '' ? null : $jalur->id_jalur;
            $data->jalur = $request->jalur == '' ? null : $jalur->nama_jalur;
            $data->user_insert = Auth::user()->id;
            
            $data->nik = $request->nik;
            $data->kecamatan = $request->kec;
            $data->desa = $request->des;
            $data->rtrw = $request->rtrw;
            $data->	alamat_detail = $request->lainnya;

            if ($request->hasFile('foto')) {
                // $folderPath = "kilau/gambarDonatur/";
                // $image_parts = explode(";base64,", $request->foto);
                // $image_type_aux = explode("image/", $image_parts[0]);
                // $image_type = $image_type_aux[1];
                // $image_base64 = base64_decode($image_parts[1]);
                // $image_name = $request->namafile;
                // $file = $folderPath . $image_name;
                // file_put_contents($file, $image_base64);

                // $data->gambar_donatur = $image_name;
                $image = $request->file('foto');

                if ($image->isValid()) {
                    $image_name = $image->getClientOriginalName();
                    $upload_path = 'gambarUpload';
                    $image->move($upload_path, $image_name);
                    
                    $data->gambar_donatur = $image_name;
                }
            }

            $data->save();

            $don = Donatur::where('token', $token)->first();

            for ($i = 0; $i < count($id_sumdan); $i++) {
                $data = new Prosp;
                $data->id_peg = $id_peg[$i];
                $data->id_don = $don->id;
                $data->id_prog = $id_program[$i];
                $data->id_sumdan = $id_sumdan[$i];
                $data->ket = 'closing';
                $data->status = 1;
                $data->tgl_fol = date('Y-m-d');

                $data->save();
            }


            return response()->json(['success' => 'Data is successfully added']);
        }
        // dd($data);
    }
    
    public function get_riw($id, Request $request)
    {
        if ($request->ajax()) {
            $data = Transaksi::select('transaksi.*', 'prog.program')->join('prog', 'prog.id_program', '=', 'transaksi.id_program')->whereRaw("transaksi.id_donatur = '$id'");
            return DataTables::of($data)
                ->addColumn('tanggal', function ($data) {
                    $trr = date('Y-m-d H:i:s', strtotime($data->created_at));
                    return $trr;
                })
                ->make(true);
        }
        return view('donatur.edit_don');
    }

    public function edit_don($id)
    {
        $data = Donatur::where('id', $id)->first();
        // return($data);
        $prosp = Prosp::where('id_don', $id)->where('ket', 'closing')->get();
        if($data->kota == null ){
            $kota = null;
            $provinsi = null;
        }else{
            $datkot = Kota::where('name', $data->kota)->first();
            $kota = $datkot->name;
            $provinsi = Provinsi::where('province_id', $datkot->province_id)->first()->province_id;
        }
        $arr = [];

        // $id_sumdan = $data->id_sumdan == '' ? [0] : unserialize($data->id_sumdan);
        // $program = $data->program == '' ? [0] : unserialize($data->program);
        // $statprog = $data->statprog == '' ? [0] : unserialize($data->statprog);
        // $id_peg = $data->id_peg == '' ? [0] : unserialize($data->id_peg);

        if (count($prosp) == 0) {
            // $id_prosp = [];
            // $nama_prog = [];
            // $id_peg = [];
            // $program = [];
            // $sumdan = [];
            // $ket = [];
            // $tglfol = [];
            // $statprog = [];
            // $hide = [];
            // $ket_prog = [];
            $arr = [
                // 'id_pros' => '',
                // 'id_peg' => '',
                // 'peg' => '',
                // 'id_sumdan' => '',
                // 'id_program' => '',
                // 'sumdan' => '',
                // 'program' => '',
                // 'tgl_fol' => '',
                // 'statprog' => '',
                // 'hide' => '',
                // 'ket_prog' => '',

            ];
        } else {
            foreach ($prosp as $x => $v) {
                $y = Prog::where('id_program', $v->id_prog)->first();
                $sumdan = SumberDana::where('id_sumber_dana', $v->id_sumdan)->first();
                $user = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                    ->select('jabatan.jabatan', 'users.*')->where('users.id',  $v->id_peg)->first();
                $arr[] = [
                    'id_pros' => $v->id,
                    'id_peg' => $v->id_peg,
                    'peg' => !empty($user) ? $user->name . ' (' . $user->jabatan . ')' : '',
                    'id_sumdan' => $v->id_sumdan,
                    'id_program' => $v->id_prog,
                    'sumdan' => $sumdan->sumber_dana,
                    'program' => $y->program,
                    'tgl_fol' => $v->tgl_fol,
                    'statprog' => $v->status,
                    'hide' => 0,
                    'ket_prog' => $y->ket,

                ];

                // $nama_prog[] = $y->program;
                // $id_prosp[] = $v->id;
                // $id_peg[] = $v->id_peg;
                // $program[] = $v->id_prog;
                // $sumdan[] = $v->id_sumdan;
                // $ket[] = $v->ket;
                // $tglfol[] = $v->tgl_fol;
                // $statprog[] = $v->status;
                // $hide[] = 0;
                // $ket_prog[] = $y->ket;
            }
        }


        // foreach($id_sumdan as $key => $val){
        //     $sumdan = SumberDana::where('id_sumber_dana', $val)->first();
        //     $programs = Prog::where('id_program', $program[$key])->first();
        //     $user = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
        //             ->select('jabatan.jabatan', 'users.*')->where('users.id', $id_peg[$key])->where('users.aktif', 1)->first();
        //     $arr[] = [
        //         'id_peg' => !empty($user) ? $id_peg[$key] : '',
        //         'peg' => !empty($user) ? $user->name.' ('.$user->jabatan.')' : '',
        //         'id_sumdan' => $val,
        //         'id_program' => $program[$key],
        //         'sumdan' => $sumdan->sumber_dana,
        //         'program' => $programs->program,
        //         'statprog' => $statprog[$key]
        //     ];
        // }

        // dd($arr);

        if (Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang') {
            $idk = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $datdon =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->get();
            $pet_so = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')
                ->where(function($q) use ($k, $idk){
                    if($k == null){
                        $q->where('users.id_kantor', $idk);
                    }else{
                        $q->whereRaw("users.id_kantor = '$k->id' OR users.id_kantor = '$idk'");
                    }
                })
                ->get();
        } else {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->get();
            $pet_so = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->get();
            $datdon =  Kantor::select('unit', 'id')->get();
        }
        return view('donatur.edit_don', compact('data', 'kota', 'provinsi', 'petugas', 'arr', 'datdon', 'pet_so'));
    }

    public function petugas_so(Request $request)
    {
        $idk = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        $q = $request->search;
        if (Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang') {
            $list = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)
                ->where(function ($query) use ($q) {
                    $query->where('users.name', 'LIKE', '%' . $q . '%');
                })
                ->where(function ($query2) use ($k, $idk) {
                    if($k == null){
                        $query2->where('users.id_kantor', Auth::user()->id_kantor);
                    }else{
                        $query2->whereRaw("users.id_kantor = '$idk' OR users.id_kantor = '$k->id'");
                    }
                    // $query2->where('users.id_kantor', Auth::user()->id_kantor)->orWhere('users.kantor_induk', Auth::user()->id_kantor);
                })
                ->where('users.id_com', Auth::user()->id_com)
                ->where('users.id_karyawan', '!=', NULL)
                ->orderBy('users.name', 'ASC')
                ->get();
        } else {
            $list = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%');
                })
                ->where('users.id_com', Auth::user()->id_com)
                ->where('users.id_karyawan', '!=', NULL)
                ->orderBy('users.name', 'ASC')
                ->get();
        }
        if (count($list) > 0) {
            $response = array();
            foreach ($list as $l) {
                $response[] = array(
                    "id" => $l->id,
                    "text" => $l->name . ' (' . $l->jabatan . ')',
                    "name" => $l->name
                );
            }

            return json_encode($response);
        } else {
            return 'hai';
        }
    }


    public function update_donhfm(Request $request, $id)
    {
        
        
        
        $id_sumdan = [];
        $id_peg = [];
        $id_program = [];
        $statprog = [];
        $id_pros = [];
        if ($request->arr != '') {
            foreach ($request->arr as $val) {
                $id_pros[] = $val['id_pros'];
                $id_peg[] = $val['id_peg'];
                $id_sumdan[] = $val['id_sumdan'];
                $id_program[] = $val['id_program'];
                $statprog[] = $val['statprog'];
            }
        }


        if (!empty($request->jalur)) {
            $jalur = Jalur::where('id_jalur', $request->jalur)->first();
        }
        // $kantor = User::where('id', $request->id_koleks)->first();
        // dd($kantor);
        $data = Donatur::findOrFail($id);
        
         // mapping dari $request ke database
        if($request->jenis == 'entitas'){
              $keyMapping = [
                            'jenis' => 'jenis_donatur',
                            'tahun_lahir' => 'tahun_lahir',
                            'jk' => 'jk',
                            'email' => 'email',
                            'pekerjaan' => 'pekerjaan',
                            // 'provinsi' => 'provinsi',
                            'alamat' => 'alamat',
                            'pembayaran' => 'pembayaran',
                            'jalur' => 'id_jalur',
                            'id_kantor' => 'id_kantor',
                            'namafile' => 'gambar_donatur',
                            'petugas' => 'petugas',
                            'id_koleks' => 'id_koleks',
                            'perusahaan' => 'nama',
                            'nohap' => 'nohap',
                            // 'kotaa' => 'kota',
                            'orng_dihubungi' => 'orng_dihubungi',
                            'jabatan' => 'jabatan',
                            'provinsi' => 'provinsi',
                            'kota' => 'kota',
                        ];
        }else if($request->jenis == 'personal'){
              $keyMapping = [
                            'jenis' => 'jenis_donatur',
                            'nama' => 'nama',
                            'tahun_lahir' => 'tahun_lahir',
                            'jk' => 'jk',
                            'email' => 'email',
                            'nohp' => 'no_hp',
                            'pekerjaan' => 'pekerjaan',
                            'provinsi' => 'provinsi',
                            'kota' => 'kota',
                            'alamat' => 'alamat',
                            'pembayaran' => 'pembayaran',
                            'jalur' => 'id_jalur',
                            'id_kantor' => 'id_kantor',
                            'namafile' => 'gambar_donatur',
                            'petugas' => 'petugas',
                            'id_koleks' => 'id_koleks',
                            'no_hp2' => 'no_hp',
                            'latitude' =>'latitude',
                            'longitude'=>'longitude',
                        ];
        }
          
                $perbedaan = [];
                foreach ($keyMapping as $kunciRequest => $kunciCari) {
                    $nilaiRequest = $request->all()[$kunciRequest];
                    $nilaiCari = $data[$kunciCari];
                
                    if ($nilaiRequest !== $nilaiCari && $nilaiRequest !== null && $nilaiCari !== $nilaiRequest ) {
                        $perbedaan[$kunciRequest] = [
                            'lama' => $nilaiCari,
                            'baru' => $nilaiRequest,
                        ];
                    }
                }
            $perbedaan = array_filter($perbedaan);

            $perbedaanString = '';
            foreach ($perbedaan as $kunci => $nilai) {
                if ($nilai['lama'] != $nilai['baru']) {
                    $perbedaanString .= "$kunci: Lama = {$nilai['lama']} , Baru = {$nilai['baru']} \n";
                }
            }
            
            $perbedaanString = rtrim($perbedaanString);
        
        // $data->jenis_donatur = $request->jenis;
        $data->id_koleks = $request->id_koleks;
        $data->id_kantor = $request->id_kantor;
        $data->petugas = $request->petugas;
        // $data->status = 'belum dikunjungi';
        if ($request->jenis == 'personal') {
            $data->nama = $request->nama;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->jk = $request->jk;
            $data->email = $request->email;
            $data->no_hp = $request->nohp;
            $data->pekerjaan = $request->pekerjaan;
            $data->alamat = $request->alamat;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
           
        }

        if ($request->jenis == 'entitas') {
            $data->email = $request->email1;
            // $data->alamat = $request->alamat1;
            $data->alamat = $request->alamat;
            $data->nama = $request->perusahaan;
            $data->nohap = $request->nohap;
            // $data->kota = $request->kotaa;
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->no_hp = $request->no_hp2;
        }
        $data->kota = $request->kota;
        $data->provinsi = $request->provinsi;
         $data->nik = $request->nik;
          $data->kecamatan = $request->kec;
           $data->desa = $request->des;
            $data->rtrw = $request->rtrw;
             $data->alamat_detail = $request->lainnya;
        $data->id_peg = !empty($request->arr) ? serialize($id_peg) : null;
        $data->id_sumdan = !empty($request->arr) ? serialize($id_sumdan) : null;
        $data->program =  !empty($request->arr) ? serialize($id_program) : null;
        $data->statprog =  !empty($request->arr) ? serialize($statprog) : null;
        $data->pembayaran = $request->pembayaran;
        $data->id_jalur = $request->jalur == '' ? null : $jalur->id_jalur;
        $data->jalur = $request->jalur == '' ? null : $jalur->nama_jalur;
        $data->user_update = Auth::user()->id;

        if (!empty($request->foto)) {
            $folderPath = "/home/kilauindonesia/public_html/kilau/gambarDonatur/";
            $image_parts = explode(";base64,", $request->foto);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->namafile;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);

            $data->gambar_donatur = $image_name;
            // dd('stop');
        }


       

        $data->update();
        Prosp::where('ket', 'closing')->where('id_don', $id)->whereNotIn('id', $id_pros)->delete();
        
        $arr = $request->arr == '' ? [] : $request->arr;

        if(count($arr) > 0){
            for ($i = 0; $i < count($request->arr); $i++) {
            if ($request->arr[$i]['id_pros'] == 0) {
                $data = new Prosp;
                $data->id_peg = $request->arr[$i]['id_peg'];
                $data->id_don = $id;
                $data->id_prog = $request->arr[$i]['id_program'];
                $data->id_sumdan = $request->arr[$i]['id_sumdan'];
                $data->ket = 'closing';
                $data->status = $request->arr[$i]['statprog'];
                $data->tgl_fol = date('Y-m-d');
                $data->created_at = NULL;
                $data->save();
            } else {
                $data = Prosp::find($request->arr[$i]['id_pros']);
                $data->id_peg = $request->arr[$i]['id_peg'];
                $data->id_don = $id;
                $data->id_prog = $request->arr[$i]['id_program'];
                $data->id_sumdan = $request->arr[$i]['id_sumdan'];
                $data->ket = 'closing';
                $data->status = $request->arr[$i]['statprog'];
                // if($ket[$i] == 'open'){
                //     $data->tgl_fol = $tglfol[$i];
                // }else if($ket[$i] == 'closing' | $ket[$i] == 'cancel'){
                //     $data->tgl_fol = date('Y-m-d');
                // }else if($data->ket == $ket[$i]){
                // $data->tgl_fol = $request->arr[$i]['tgl_fol'];
                $data->konprog = 0;
                // }else{
                //     $data->tgl_fol = date('Y-m-d');
                // }
                $data->update();
            }
        }
            
        }
        // dd($request->arr);
       
        \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Donatur , dengan id'. $id,$perbedaanString,'donatur','update',$id);
        // \LogActivity::addToLog(Auth::user()->name . ' Edit Data Dari Halaman Donatur , dengan id'. $id);

        return response()->json(['success' => 'Data is successfully updated']);
    }


      public function update_don(Request $request, $id)
    {
        // dd($request->arr[0]['id_program']);
        $id_sumdan = [];
        $id_peg = [];
        $id_program = [];
        $statprog = [];
        $id_pros = [];
        if ($request->arr != '') {
            foreach ($request->arr as $val) {
                $id_pros[] = $val['id_pros'];
                $id_peg[] = $val['id_peg'];
                $id_sumdan[] = $val['id_sumdan'];
                $id_program[] = $val['id_program'];
                $statprog[] = $val['statprog'];
            }
        }

        // dd($id_pros);

        if (!empty($request->jalur)) {
            $jalur = Jalur::where('id_jalur', $request->jalur)->first();
        }
        // dd($jalur);
        // $kantor = User::where('id', $request->id_koleks)->first();
        // dd($kantor);
        $data = Donatur::findOrFail($id);
        // $data->jenis_donatur = $request->jenis;
        $data->id_koleks = $request->id_koleks;
        $data->id_kantor = $request->id_kantor;
        $data->petugas = $request->petugas;
        // $data->status = 'belum dikunjungi';
        if ($request->jenis == 'personal') {
            $data->nama = $request->nama;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->jk = $request->jk;
            $data->email = $request->email;
            $data->no_hp = $request->nohp;
            $data->pekerjaan = $request->pekerjaan;
            $data->kota = $request->kota;
            $data->alamat = $request->alamat;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
        }

        if ($request->jenis == 'entitas') {
            $data->email = $request->email1;
            // $data->alamat = $request->alamat1;
            $data->alamat = $request->alamat;
            $data->nama = $request->perusahaan;
            $data->nohap = $request->nohap;
            $data->kota = $request->kotaa;
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->no_hp = $request->no_hp2;
        }
        $data->id_peg = !empty($request->arr) ? serialize($id_peg) : null;
        $data->id_sumdan = !empty($request->arr) ? serialize($id_sumdan) : null;
        $data->program =  !empty($request->arr) ? serialize($id_program) : null;
        $data->statprog =  !empty($request->arr) ? serialize($statprog) : null;
        $data->pembayaran = $request->pembayaran;
        $data->id_jalur = $request->jalur == '' ? null : $jalur->id_jalur;
        $data->jalur = $request->jalur == '' ? null : $jalur->nama_jalur;
        $data->user_update = Auth::user()->id;

        if (!empty($request->foto)) {
            $folderPath = "/home/kilauindonesia/public_html/kilau/gambarDonatur/";
            $image_parts = explode(";base64,", $request->foto);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->namafile;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);

            $data->gambar_donatur = $image_name;
            // dd('stop');
        }


        //mapping dari $request ke database
        // if($request->jenis == 'entitas'){
        //       $keyMapping = [
        //                     'jenis' => 'jenis_donatur',
        //                     'tahun_lahir' => 'tahun_lahir',
        //                     'jk' => 'jk',
        //                     'email' => 'email',
        //                     'pekerjaan' => 'pekerjaan',
        //                     'provinsi' => 'provinsi',
        //                     'alamat' => 'alamat',
        //                     'pembayaran' => 'pembayaran',
        //                     'jalur' => 'id_jalur',
        //                     'id_kantor' => 'id_kantor',
        //                     'namafile' => 'gambar_donatur',
        //                     'petugas' => 'petugas',
        //                     'id_koleks' => 'id_koleks',
        //                     'perusahaan' => 'nama',
        //                     'nohap' => 'nohap',
        //                     'kotaa' => 'kota',
        //                     'orng_dihubungi' => 'orng_dihubungi',
        //                     'jabatan' => 'jabatan',
        //                 ];
        // }else if($request->jenis == 'personal'){
        //       $keyMapping = [
        //                     'jenis' => 'jenis_donatur',
        //                     'nama' => 'nama',
        //                     'tahun_lahir' => 'tahun_lahir',
        //                     'jk' => 'jk',
        //                     'email' => 'email',
        //                     'nohp' => 'no_hp',
        //                     'pekerjaan' => 'pekerjaan',
        //                     'provinsi' => 'provinsi',
        //                     'kota' => 'kota',
        //                     'alamat' => 'alamat',
        //                     'pembayaran' => 'pembayaran',
        //                     'id_jalur' => 'id_jalur',
        //                     'id_kantor' => 'id_kantor',
        //                     'namafile' => 'gambar_donatur',
        //                     'petugas' => 'petugas',
        //                     'id_koleks' => 'id_koleks',
        //                     'no_hp2' => 'no_hp',
        //                     'latitude' =>'latitude',
        //                     'longitude'=>'longitude',
        //                 ];
        // }
          
      
        //         $perbedaan = [];
        //         foreach ($keyMapping as $kunciRequest => $kunciCari) {
        //             $nilaiRequest = $request->all()[$kunciRequest];
        //             $nilaiCari = $data[$kunciCari];
                
        //             if ($nilaiRequest !== $nilaiCari && $nilaiRequest !== null) {
        //                 $perbedaan[$kunciRequest] = [
        //                     'lama' => $nilaiCari,
        //                     'baru' => $nilaiRequest,
        //                 ];
        //             }
        //         }
        //     $perbedaan = array_filter($perbedaan);
        //     $perbedaanString = '';
        //         foreach ($perbedaan as $kunci => $nilai) {
        //             $perbedaanString .= "$kunci: Lama = {$nilai['lama']}, Baru = {$nilai['baru']}\n";
        //         }
        
        //     $perbedaanString = rtrim($perbedaanString);
        

        $data->update();
        $arr = $request->arr == '' ? [] : $request->arr;
        // return($request);

        Prosp::where('ket', 'closing')->where('id_don', $id)->whereNotIn('id', $id_pros)->delete();

        if(count($arr) > 0){
            
            for ($i = 0; $i < count($request->arr); $i++) {
                if ($request->arr[$i]['id_pros'] == 0) {
                    $data = new Prosp;
                    $data->id_peg = $request->arr[$i]['id_peg'];
                    $data->id_don = $id;
                    $data->id_prog = $request->arr[$i]['id_program'];
                    $data->id_sumdan = $request->arr[$i]['id_sumdan'];
                    $data->ket = 'closing';
                    $data->status = $request->arr[$i]['statprog'];
                    $data->tgl_fol = date('Y-m-d');
                    $data->created_at = NULL;
                    $data->save();
                } else {
                    $data = Prosp::find($request->arr[$i]['id_pros']);
                    $data->id_peg = $request->arr[$i]['id_peg'];
                    $data->id_don = $id;
                    $data->id_prog = $request->arr[$i]['id_program'];
                    $data->id_sumdan = $request->arr[$i]['id_sumdan'];
                    $data->ket = 'closing';
                    $data->status = $request->arr[$i]['statprog'];
                    // if($ket[$i] == 'open'){
                    //     $data->tgl_fol = $tglfol[$i];
                    // }else if($ket[$i] == 'closing' | $ket[$i] == 'cancel'){
                    //     $data->tgl_fol = date('Y-m-d');
                    // }else if($data->ket == $ket[$i]){
                    // $data->tgl_fol = $request->arr[$i]['tgl_fol'];
                    $data->konprog = 0;
                    // }else{
                    //     $data->tgl_fol = date('Y-m-d');
                    // }
                    $data->update();
                }
            }
        }
        // dd($request->arr);
       
        \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Donatur , dengan id'. $id,$perbedaanString,'donatur','update',$id);

        return response()->json(['success' => 'Data is successfully updated']);
    }    

    public function provinces()
    {
        $data = Provinsi::all();
        return response()->json($data);
    }

    public function cities($id)
    {
        $data = Kota::where('province_id', $id)->get();
        return response()->json($data);
    }

    public function get_nm(Request $request)
    {
        $kantor = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kantor)->first();
        
        $q = $request->search;
        $data = Donatur::where(function ($query) use ($q) {
                    $query->where('nama', 'LIKE', '%' . $q . '%')
                        ->orWhere('no_hp', 'LIKE', '%' . $q . '%')
                        ->orWhere('email', 'LIKE', '%'.$q.'%');
                })
                ->where(function ($query) use ($k){
                    if(Auth::user()->level == 'kacab'){
                        if($k == null){
                            $query->where('id_kantor', Auth::user()->id_kantor);
                        }else{
                            $query->where('id_kantor', Auth::user()->id_kantor)
                                    ->orWhere('id_kantor', $k->id);
                        }
                    }
                })
                ->whereRaw("status != 'Ditarik' AND status != 'Off'")
                ->get();
        if (count($data) > 0) {
            //  $list = array();
            foreach ($data as $key => $val) {
                $list[] = [
                    "text" => $val->nama,
                    "no_hp" => $val->no_hp,
                    "kota" => $val->kota,
                    "email" => $val->email,
                    "alamat" => $val->alamat,
                    "nama" => $val->nama,
                    "id" => $val->id,

                ];
            }
            return json_encode($list);
        } else {
            return "hasil kosong";
        }
    }

    public function cek_email($name, $id, Request $request)
    {
        if ($name == 'personal') {
            if ($id == 'email') {
                $verif = Donatur::Where('email', $request->email)->get();
            } elseif ($id == 'nohp') {
                $verif = Donatur::where('no_hp', $request->nohp)->get();
            } else {
                $verif = null;
            }
        } elseif ($name == 'entitas') {
            if ($id == 'email') {
                $verif = Donatur::Where('email', $request->email)->get();
            } elseif ($id == 'nohp') {
                $verif = Donatur::where('nohap', $request->nohap)->get();
            } else {
                $verif = null;
            }
        }

        $data = [];


        foreach ($verif as $val) {
            $program = [];
            $prog = $val->program === 'b:0;' || @unserialize($val->program) !== false ? unserialize($val->program) : [];
            if (count($prog) > 0) {
                foreach ($prog as $v => $k) {
                    $pr = Prog::where('id_program', $k)->first();
                    $program[] = $pr->program;
                }
            } else {
                $program = [];
            }

            $data[] = [
                'id' => $val->id,
                'nama' => $val->nama,
                'program' => $program,
                'no_hp' => $val->no_hp,
                'alamat' => $val->alamat,
                'status' => $val->status
            ];
        }
        // dd($prog);


        if (count($verif) > 0 || $verif == null) {
            return response()->json(['errors' => 'sudah digunakan', 'data' => $data]);
        } else {
            return response()->json(['success' => 'bisa digunakan']);
        }
    }
    
    public function getDntr(Request $request){
        
        $auth = Auth::user()->id_kantor;
        
        $k = Kantor::where('kantor_induk', $auth)->first();
        
        $lmt = $request->lmt == '' ? '100' : $request->lmt;
        
        if($request->aktif == ''){
            $aktif = "status IS NOT NULL";
        }else if($request->aktif == '1'){
            $aktif = "status != 'Ditarik' AND status != 'Off'";
        }else if($request->aktif == '0'){
            $aktif = "(status = 'Ditarik' OR status = 'Off')";
        }
        
        $now = $request->tahun == '' ? date('Y') : $request->tahun;
        $data = Donatur::selectRaw("donatur.id as id_donaturss, nama, latitude, longitude, YEAR(created_at) as tahun, donatur.status, gambar_donatur, donatur.alamat, no_hp, jenis_donatur, orng_dihubungi, email, donatur.petugas")
                ->whereRaw("$aktif")
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->limit($lmt)
                ->get();
                
        $siji = Donatur::selectRaw("COUNT(id) as kon")
                ->whereRaw("$aktif")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($query) use ($request) {
                    if($request->kor == 'true'){
                        $query->whereRaw(" latitude IS NOT NULL AND longitude IS NOT NULL");
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->get();
            
        $loro = Donatur::selectRaw("COUNT(id) as kon")
                ->whereRaw("$aktif AND latitude IS NOT NULL AND longitude IS NOT NULL ")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                
                ->where(function($query) use ($request) {
                    if($request->kor == 'true'){
                        $query->whereRaw(" latitude IS NOT NULL AND longitude IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->get();
        $telu = Donatur::selectRaw("COUNT(id) as kon")
                ->whereRaw("$aktif AND latitude IS NULL AND longitude IS NULL ")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                
                ->where(function($query) use ($request) {
                    if($request->kor == 'true'){
                        $query->whereRaw(" latitude IS NOT NULL AND longitude IS NOT NULL");
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->get();
                
        $roar = Donatur::selectRaw("DISTINCT(id) as roar")
                ->whereRaw("$aktif")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->get();
               
        $p = []; 
        foreach($roar as $r){
            $p[] = $r->roar; 
        }
                
        $ehe = Transaksi::selectRaw("SUM(jumlah) as jum")
                ->whereRaw("approval = 1 AND via_input != 'mutasi' ")
                
                ->where(function($q) use ($request) {
                    if(isset($request->jalur)){
                    $q->whereIn('id_kantor', function($query) use ($request) {
                            $query->select('id_kantor')->from('jalur')->whereIn('id_jalur', $request->jalur);
                        });
                    }
                })
                
                ->where(function($q) use ($request, $p) {
                    if(isset($request->aktif)){
                            $q->whereIn('id_donatur', function($query) use ($request,  $p) {
                                $query->select('id_donatur')->from('donatur')->whereIn('id_donatur', $p);
                        });
                    }
                })
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                
                // ->where(function($query) use ($request) {
                //     if($request->kor != '' && $request->kor == true){
                //         $query->whereRaw("latitude IS NOT NULL AND longitude IS NOT NULL");
                //     }
                // })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(tanggal)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(tanggal) IS NOT NULL");
                    }
                })
                
                ->get();
        
        return [$data, $siji, $ehe, $loro, $telu];
    }
    
    public function map_donatur(Request $request){
        
        $auth = Auth::user()->id_kantor;
        
        $k = Kantor::where('kantor_induk', $auth)->first();
        
        $lmt = $request->lmt == '' ? '100' : $request->lmt;
        
        if($request->aktif == ''){
            $aktif = "status IS NOT NULL";
        }else if($request->aktif == '1'){
            $aktif = "status != 'Ditarik' AND status != 'Off'";
        }else if($request->aktif == '0'){
            $aktif = "(status = 'Ditarik' OR status = 'Off')";
        }
        
        $now = $request->tahun == '' ? date('Y') : $request->tahun;
        $data = Donatur::selectRaw("donatur.id as id_donaturss, nama, latitude, longitude, YEAR(created_at) as tahun, donatur.status, gambar_donatur, donatur.alamat, no_hp, jenis_donatur, orng_dihubungi, email, donatur.petugas")
                ->whereRaw("$aktif")
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->dntr)){
                        $q->where('id', $request->dntr);
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->limit($lmt)
                ->get();
        
        return $data;
    }
    
    public function list_donat(Request $request){
        $auth = Auth::user()->id_kantor;
        
        $k = Kantor::where('kantor_induk', $auth)->first();
        
        $lmt = $request->lmt == '' ? '100' : $request->lmt;
        
        if($request->aktif == ''){
            $aktif = "status IS NOT NULL";
        }else if($request->aktif == '1'){
            $aktif = "status != 'Ditarik' AND status != 'Off'";
        }else if($request->aktif == '0'){
            $aktif = "(status = 'Ditarik' OR status = 'Off')";
        }
        
        $now = $request->tahun == '' ? date('Y') : $request->tahun;
        $data = Donatur::selectRaw("donatur.id as id_donaturss, nama, latitude, longitude, YEAR(created_at) as tahun, donatur.status, gambar_donatur, donatur.alamat, no_hp, jenis_donatur, orng_dihubungi, email, donatur.petugas")
                ->whereRaw("$aktif")
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if($request->kor == true){
                        $query->whereRaw("latitude IS NOT NULL AND longitude IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                ->limit($lmt)
                ->get();
        
        $h1 = [];
        
        foreach ($data as $key => $val) {
            if($val->latitude != null && $val->longitude != null){
                $h1[] = [
                    "text" => $val->nama . '-' . $val->no_hp . '-' . $val->alamat,
                    "nama" => $val->nama,
                    "id" => $val->id_donaturss,
                    "nohp" => $val->no_hp,
                    "alamat" => $val->alamat,
                ];
            }
        }
        return response()->json($h1);
    }
    
    public function lokdon_detail(Request $request){
        
        $auth = Auth::user()->id_kantor;
        
        $k = Kantor::where('kantor_induk', $auth)->first();
        
        $lmt = $request->lmt == '' ? '100' : $request->lmt;
        
        if($request->aktif == ''){
            $aktif = "status IS NOT NULL";
        }else if($request->aktif == '1'){
            $aktif = "status != 'Ditarik' AND status != 'Off'";
        }else if($request->aktif == '0'){
            $aktif = "(status = 'Ditarik' OR status = 'Off')";
        }
        
        if($request->id == 'don'){
            $data = Donatur::selectRaw("*")
                ->whereRaw("$aktif")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                // ->limit($lmt)
                
                ->get();
            
        }else if($request->id == 'ada'){
            $data = Donatur::selectRaw("*")
                ->whereRaw("$aktif AND latitude IS NOT NULL AND longitude IS NOT NULL ")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                // ->limit($lmt)
                
                ->get();
            
        }else if($request->id == 'gada'){
            $data = Donatur::selectRaw("*")
                ->whereRaw("$aktif AND latitude IS NULL AND longitude IS NULL ")
                
                ->where(function($q) use ($request, $k, $auth) {
                    if(isset($request->kotal)){
                        $q->whereIn('id_kantor', $request->kotal);
                    }else{
                        if(Auth::user()->level == 'admin'){
                            $q->where('id_kantor', $auth);
                        }else if(Auth::user()->level == 'kacab'){
                            if($k == null){
                                $q->where('id_kantor', $auth);
                            }else{
                                $q->whereRaw(" id_kantor = '$auth' OR  id_kantor = '$k->id'" );
                            }
                        }
                    }
                })
                
                ->where(function($q) use ($request) {
                    if(isset($request->tahun)){
                        $q->whereIn(DB::raw('YEAR(created_at)'), $request->tahun);
                    }else{
                        $q->whereRaw("YEAR(created_at) IS NOT NULL");
                    }
                })
                
                ->where(function($query) use ($request) {
                    if(isset($request->jalur)){
                        $query->whereIn('id_jalur', $request->jalur);
                    }
                })
                
                // ->limit($lmt)
                
                ->get();    
        }else if($request->id == 'him'){
            $data = [];
        }
        
        return $data;
    }
    
    
        public function donatur_det(Request $request){
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            $data = Donatur::selectRaw("nama,alamat,kota,jenis_donatur,no_hp")
            ->whereRaw("transaksi.id_program = '$request->id_prog' AND transaksi.tanggal >= '$dari' AND transaksi.tanggal <= '$sampai' AND transaksi.approval = '1' ")
            ->get();
            return DataTables::of($data)
                ->make(true);

    }
    
    public function setting_warning(Request $request){
        return view('donatur.setting_warning');
    }
    
}
