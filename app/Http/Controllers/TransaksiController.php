<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\TransaksiExport;
use App\Models\Transaksi;
use App\Models\Kantor;
use App\Models\Prosp;
use App\Models\Donatur;
use App\Models\LinkParam;
use App\Models\User;
use App\Models\Program;
use App\Models\Prog;
use App\Models\ProgBSZ;
use App\Models\Bank;
use App\Models\Pengeluaran;
use App\Models\RekapT;
use App\Models\COA;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Tunjangan;
use Carbon\Carbon;
use App\Models\HapusTransaksi;
use DataTables;
use Illuminate\Support\Facades\Http;

use Excel;
use PDF;
use App\Exports\RutinExport;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $getlp = LinkParam::where('id_user', Auth::user()->id)->where('link', 'transaksi')->where('aktif', 1)->first();
        if($getlp != null){
            $pending = $getlp->p1;
            $tglll = $getlp->p2;
            
            $datam = LinkParam::find($getlp->id);
            $datam->aktif   = 0;
            $datam->update();
        }else{
            $pending = [];
            $tglll = date('Y-m-d').' s.d. '.date('Y-m-d');
        }
        
        $bang = Bank::all();
        
        $idk = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        if(Auth::user()->level == 'admin'){
            $donat = Donatur::orderBy('nama', 'ASC')->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
               $donat = Donatur::whereRaw("id_kantor = $idk")->orderBy('nama', 'ASC')->get();
            }else{
                $donat = Donatur::whereRaw("(id_kantor = $idk OR id = $kan->id)")->orderBy('nama', 'ASC')->get();
            }
        }
        
        $kolektor = User::where('kota', Auth::user()->kota)->get(); 
        
        $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
        
        $program = Program::orderBy('program')->get();
            // $petugas = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
            //     ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();   
            
        $donatur = Donatur::where(function ($query) {
            $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
        })->get();    
            
            
        $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
            ->select('jabatan.jabatan', 'users.*')
            ->where('users.aktif', 1)
            // ->where('users.id_kantor', Auth::user()->id_kantor)
            ->where(function($q) use ($kan, $idk){
                if(Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang' ){
                    if($kan == null){
                        $q->whereRaw("id_kantor = '$idk'");
                    }else{
                        $q->whereRaw("(id_kantor = '$idk' OR id_kantor = '$kan->unit')");
                    }
                }
            })
            ->where('users.id_com', Auth::user()->id_com)
            ->get();
        
        if ($request->ajax()) {
            
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
            
            $cari = $request->cari;
            $program = Program::orderBy('program')->get();
            $max = (int)$request->max;
            $min = (int)$request->min;
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = $bln;
            $bln2 = $request->blnnnn != '' ? $request->blnnnn : $bln1;
            $thn = $request->thnn != '' ? $request->thnn : date('Y');
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            $kot = $request->kota;
            $arr = $request->statuus;
            $kol = $request->kol;
            $stat = $request->statak;
            $aha = $request->program;
            $bayar = $request->bayar;
            $bank = $request->bank;
            
            if(isset($aha)){
                $program = Prog::where('id_program', $aha)->first()->coa_individu;
            }
            
            
            if ($request->plhtgl == 0) {
                $plugin = $tgls;
            }else if($request->plhtgl == 1){
                $plugin = "DATE_FORMAT(transaksi.tanggal, '%m-%Y') >= '$bln' AND DATE_FORMAT(transaksi.tanggal,'%m-%Y') <= '$bln2'";
            }else{
                $plugin = "YEAR(tanggal) = '$thn' ";
            }
            
            $transaksi = Transaksi::whereRaw(" via_input = 'transaksi' AND $plugin AND $carmin AND $carmax")
                
                ->where(function($q) use ($request, $kan, $idk, $kot){
                    if(Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang' ){
                        if($kan == null){
                            $q->whereRaw("id_kantor = '$idk'");
                        }else{
                            if(isset($request->kota)){
                                $q->whereIn('id_kantor', $kot);
                            }else{
                                $q->whereRaw("(id_kantor = '$idk' OR id_kantor = '$kan->unit')");
                            }
                        }
                    }else if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat' ){
                        if(isset($kot)){
                            $q->whereIn('id_kantor', $kot);
                        }
                    }
                })
                
                ->where(function($query) use ($request, $arr) {
                    if(isset($request->statuus)){
                        $query->whereIn('status', $arr);
                    }
                })
                        
                ->where(function($query) use ($request, $kol) {
                    if(isset($request->kol)){
                        $query->whereIn('kolektor', $kol);
                    }
                })
                
                ->where(function($query) use ($request, $kot) {
                    if(isset($request->kota)){
                        $query->whereIn('id_kantor', $kot);
                    }
                })
                        
                ->where(function($query) use ($request, $bank) {
                    if(isset($request->bank)){
                        $query->where('id_bank', $bank);
                    }
                })
                
                ->where(function($query) use ($getlp) {
                    if($getlp != null){
                        $query->where('status', $getlp->p1);
                    }
                })
                
                ->where(function($query) use ($request, $program) {
                    if(isset($request->program)){
                        $query->where('coa_kredit', $program);
                    }
                })
                        
                ->where(function($query) use ($request, $bayar) {
                    if(isset($request->bayar)){
                        $query->whereIn('pembayaran', $bayar);
                    }
                })
                        
                ->where(function($query) use ($request, $stat) {
                    if(isset($request->statak)){
                        $query->whereIn('approval', $stat);
                    }
                });
            
            if($request->tab == '1'){
                $aye = $transaksi->selectRaw("COUNT(DISTINCT(id_donatur)) as donatur, COUNT(id_transaksi) as qty, SUM(jumlah) as capaian")
                ->where(function($query) use ($cari) {
                        if($cari != ''){
                            $query->whereRaw("kolektor LIKE '%$cari%' OR donatur LIKE '%$cari%' OR subprogram LIKE '%$cari%' OR alamat LIKE '%$cari%' OR status LIKE '%$cari%' OR pembayaran LIKE '%$cari%'");
                        }
                    })
                ->get();
                
                return $aye;
            }
                        
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->addColumn('jml', function ($transaksi) {
                    $jml = number_format($transaksi->jumlah, 0, ',', '.');
                    return $jml;
                })

                ->addColumn('stts', function ($transaksi) {
                    if ($transaksi->approval == 1) {
                        $button = '<span class="badge badge-success">Approved<span class="ms-1 fa fa-check"></span></span>';
                    } elseif ($transaksi->approval == 0) {
                        $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fa fa-ban"></span></span>';
                    } else {
                        $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                    }
                    return $button;
                })
                    
                ->addColumn('tgl', function ($transaksi) {
                    $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                    return $tgl;
                })

                ->addColumn('edit', function($transaksi){
                    $button = '<div class="btn-group"><a class="btn btn-rounded btn-success btn-sm" href="'. url('transaksi/edit/'.$transaksi->id) .'"><i class="fa fa-edit"></i></a></div>';
                    return $button;
                })

                ->addColumn('hapus', function ($transaksi) {
                    
                    if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
                        if ($transaksi->approval == 1) {
                            $y = '<a class="btn btn-rounded btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-left: 3%" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } elseif ($transaksi->approval == 0) {
                            $y = '<a class="btn btn-rounded btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" style="margin-left: 3%" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $y = '<a class="btn btn-rounded btn-primary btn-sm coba" id="' . $transaksi->id . '" href="javascript:void(0)" style="margin-left: 3%" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Untuk Konfirmasi Transaksi" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi"><i class="fa fa-grip-lines"></i></a>';
                        }
                            
                        $x = '<a class="btn btn-rounded btn-sm btn-danger delete" name="edit" id="' . $transaksi->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menghapus Transaksi" style="margin-left: 3%"> <i class="fa fa-trash"></i></a>';
                        $z = '<a class="btn btn-rounded btn-sm btn-primary kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi" style="margin-left: 3%"><i class="fa fa-paper-plane"></i></a>';
                        
                        $button =   '<div class="dropdown dropstart">
    								    <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false">
    								        <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
    								    </button>
    									
    									<div class="dropdown-menu dropdown-menu-eleh" style="width: 240px; background: #fff; border: 1px">
    										<div class="d-flex justify-content-evenly">
        										<div><a class="btn btn-rounded btn-success btn-sm" href="'. url('transaksi/edit/'.$transaksi->id) .'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Edit Transaksi"><i class="fa fa-edit"></i></a></div>
        										<div>'.$y.'</div>
        										<div>'.$x.'</div>
        										<div>'.$z.'</div>
    										</div>
    									</div>
    								</div>';
                    }else if(Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang' || Auth::user()->level == 'operator pusat'){
                        if ($transaksi->approval == 1) {
                            $y = '<a class="btn btn-rounded btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-left: 3%" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } else
                        if ($transaksi->approval == 0) {
                            $y = '<a class="btn btn-rounded btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" style="margin-left: 3%" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $y = '<a class="btn btn-rounded btn-primary btn-sm coba" id="' . $transaksi->id . '" href="javascript:void(0)" style="margin-left: 3%" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Untuk Konfirmasi Transaksi" data-bs-toggle="tooltip" data-bs-placement="top" ><i class="fa fa-grip-lines"></i></a>';
                        }
                        
                        if($transaksi->approval == 2){
                            $x = '<div><a class="btn btn-rounded btn-success btn-sm" href="'. url('transaksi/edit/'.$transaksi->id) .'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Edit Transaksi"><i class="fa fa-edit"></i></a></div>';
                            $pxl = '200px';
                        }else 
                        if($transaksi->approval == 0){
                            $x = '<div><a class="btn btn-rounded btn-success btn-sm" href="'. url('transaksi/edit/'.$transaksi->id) .'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Edit Transaksi"><i class="fa fa-edit"></i></a></div>';
                            $pxl = '200px';
                        }else{
                            $x = '';
                            $pxl = '160px';
                        }
                        
                        $z = '<a class="btn btn-rounded btn-sm btn-primary kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi" style="margin-left: 3%"><i class="fa fa-paper-plane"></i></a>';
                            
                            // if(Auth::user()->id == 6){
                        $button = '<div class="dropdown dropstart">
    									<button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false">
    							    		<svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
    									</button>
    					        		<div class="dropdown-menu dropdown-menu-eleh" style="width: '.$pxl.'; background: #fff; border: 1px">
    								    	<div class="d-flex justify-content-evenly">
        										'.$x.'
        										<div>'.$y.'</div>
        										<div>'.$z.'</div>
    										</div>
    									</div>
    								</div>';
                    }
					return $button;
                })
                    
                ->addColumn('image', function ($transaksi) {
                    if($transaksi->bukti2 == null){
                        $button = '';
                    }else{
                        $button = '<a href="https://kilauindonesia.org/kilau/gambarUpload/'.$transaksi->bukti2.'" target="_blank" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Bukti Noncash"><i class="fa fa-image"></i></button>';
                    }
                    return $button;
                })

                ->addColumn('akses', function ($transaksi) {
                    if ($transaksi->approval == 1) {
                        $button = '<a class="btn btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                    } elseif ($transaksi->approval == 0) {
                        $button = '<a class="btn btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                    } else {
                        // $button = '<a class="btn btn-primary btn-sm coba" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Untuk Reject Transaksi"><i class="fa fa-grip-lines"></i></a>';
                        $button =  '<div class="btn-group  mb-1">
                                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi">
                                        <i class="fa fa-grip-lines"></i>
                                        </button>
                                        <div class="dropdown-menu" style="margin: 0px;">
                                            <a class="dropdown-item aprov" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" >Approve</a>
                                            <a class="dropdown-item edito" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" >Reject</a>
                                        </div>
                                    </div>';
                    }
                        
                    return $button;
                })

                ->addColumn('kwitansi', function ($transaksi) {
                    $button = '<button type="button" class="btn btn-primary btn-sm mb-2 kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi"><i class="fa fa-paper-plane"></i></button>';
                    return $button;
                })
                    
                ->addColumn('idtrans', function ($transaksi) {
                    $button = '<a href="https://kilauindonesia.org/kilau/kwitansi/'.$transaksi->id_transaksi.'" style="color: blue" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Melihat Kwitansi">'. $transaksi->id_transaksi .'</a>';
                    return $button;
                })
                    
                ->rawColumns(['hapus', 'akses', 'kwitansi', 'stts', 'idtrans', 'image','edit'])
                ->make(true);
            
        }
        
        // $aha = json_encode($transaksi->get());
        
        $don = Donatur::where(function ($query) {
                $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
            })->limit(3750)
                ->get();
        $h1 = [];
            

        foreach ($don as $key => $val) {
            $h1[] = [
                "text" => $val->nama . '-' . $val->no_hp . '-' . $val->alamat,
                "nama" => $val->nama,
                "id" => $val->id,
                "nohp" => $val->no_hp,
                "alamat" => $val->alamat,
            ];
        }

        $deals = json_encode($h1);
        
        return view('transaksi.index', compact('kolektor', 'petugas', 'donatur', 'program', 'bank', 'bang','tglll','pending'));
    }
    
    public function transaksi_export(Request $request){
        
        $bln = $request->blns != '' ? $request->blns : date('m-Y');
        $bln1 = $bln;
        $bln2 = $request->blnnnn != '' ? $request->blnnnn : $bln1;
        $thn = $request->thnn != '' ? $request->thnn : date('Y');
        $daterange =  $request->input('daterange');
        if ($daterange != '') {
            $tglz = explode(' s.d. ', $daterange);
            $dariz = date('Y-m-d', strtotime($tglz[0]));
            $sampaiz = date('Y-m-d', strtotime($tglz[1]));
        }else{
            $dariz = date('Y-m-d');
            $sampaiz = date('Y-m-d');
        }
                    
        if($request->plhtgl == 0){
            $periode = ' Dari Tanggal '.$dariz. ' Sampai '.$sampaiz ;
        }else if($request->plhtgl == 1){
            $periode = ' Periode Dari Bulan '.$bln. ' Sampai '.$bln2 ;
        }else{
            $periode = ' Periode Tahun '.$thn ;
        }
        
        if($request->tombol == 'xls'){
            $r = Excel::download(new TransaksiExport($request->request->all()), 'Transaksi'.$periode .'.xlsx');
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new TransaksiExport($request->request->all()), 'Transaksi'.$periode .'.csv');
            ob_end_clean();
            return $r;
        }
    }
    
    public function transaksi_tab(Request $request)
    {
        if (Auth::user()->level == ('keuangan unit') | Auth::user()->level == ('spv')) {
            if ($request) {
                $transaksib = Transaksi::where('kolektor', 'like', '%' . $request->cari . '%')->where('via_input', 'transaksi')->orderBy('updated_at', 'desc')->paginate(900);
            } else {
                $transaksib = Transaksi::where('via_input', 'transaksi');
            }
            return view('transaksi.trfb', compact('transaksib', 'request'));
        } elseif (Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang' ) {
            $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')
                ->select('bank.*', 'tambahan.unit')->get();
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('users.aktif', 1)->where('users.id_kantor', Auth::user()->id_kantor)->where('users.id_com', Auth::user()->id_com)->get();
            $donatur = Donatur::where(function ($query) {
                $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
            })->where('kota', Auth::user()->kota)
                ->get();
            $program = Program::orderBy('program')->get();
            $kolektor = User::where('kolektor', '!=', null)->where('kota', Auth::user()->kota)->get();

            $max = (int)$request->max;
            $min = (int)$request->min;
            $kots = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";

            $bln = $request->blnn != '' ? $request->blnn : date('m-Y');
            $blns = date('Y-m-01', strtotime('01-' . $bln));
            // $blnx = $request->blnx != '' ? $request->blnx : date('m-Y');
            $blnx = $request->blnx != '' ? date('Y-m-t', strtotime('01-' . $request->blnx)) : date('Y-m-t', strtotime('01-' . $bln));

            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            // $carkota = $request->kota != '' ? "kota = '$request->kota'" : "kota != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            $tahoen = $request->thin != '' ? $request->thin : date('Y');
            
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";

            // if ($request->statak == '') {
            //     $statak = "transaksi.approval IS NOT NULL";
            // } else if ($request->statak == 2) {
            //     $statak = "transaksi.approval = '2'";
            // } else if ($request->statak == 1) {
            //     $statak = "transaksi.approval = '1'";
            // } else if ($request->statak == 0) {
            //     $statak = "transaksi.approval IS NOT NULL";
            // }

            if ($request->ajax()) {
                
                $arr = $request->statuus;
                $kol = $request->kol;
                $kot = $request->kota;
                $stat = $request->statak;
                $bayar = $request->bayar;

                if ($k == null) {
                    if ($request->plhtgl == 0) {
                        // $transaksi = Transaksi::whereRaw("id_kantor = '$kots' AND $tgls ")
                        $transaksi = Transaksi::whereRaw("id_kantor = '$kots' AND $carmin AND $carmax AND via_input = 'transaksi' AND $tgls ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        });
                        ;
                        // return($transaksi->get());
                    } elseif ($request->plhtgl == 1){
                        $transaksi = Transaksi::whereRaw("id_kantor = '$kots' AND $carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        });
                    }else{
                        $transaksi = Transaksi::whereRaw("id_kantor = '$kots' AND $carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen' ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        });
                    }
                    
                } else {
                    
                    if ($request->plhtgl == 0) {
                        $plugin = $tgls;
                    }else if($request->plhtgl == 1){
                        $plugin = "DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'";
                    }else{
                        $plugin = "YEAR(tanggal) = '$tahoen'";
                    }
                    
                    $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $plugin ")
                    
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kot, $kots, $k) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kot);
                        }else{
                            $query->whereRaw("(id_kantor = '$kots' OR id_kantor = '$k->unit')");
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    });
                }

                return DataTables::of($transaksi)
                    ->addIndexColumn()
                    ->addColumn('jml', function ($transaksi) {
                        $jml = number_format($transaksi->jumlah, 0, ',', '.');
                        return $jml;
                    })

                    ->addColumn('id_tr', function ($transaksi) {
                        $link = "https://kilauindonesia.org/kilau/kwitansi/$transaksi->id_transaksi";
                        $trr = '<a href="' . $link . '" target="_blank">' . $transaksi->id_transaksi . '</a>';
                        return $trr;
                    })

                    ->addColumn('tgl', function ($transaksi) {
                        $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                        // $tgl = date('d/m/Y H:i', strtotime($transaksi->tanggal));
                        return $tgl;
                    })

                    ->addColumn('stts', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<span class="badge badge-success">Approved<span class="ms-1 fa fa-check"></span></span>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fa fa-ban"></span></span>';
                        } else {
                            $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                        }
                        return $button;
                    })

                    ->addColumn('hapus', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-rounded btn-sm btn-danger delete" name="edit" id="' . $transaksi->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menghapus Transaksi"> <i class="fa fa-trash"></i></button>';
                        return $button;
                    })

                    ->addColumn('akses', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<a class="btn btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<a class="btn btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $button =  '<div class="btn-group mb-1">
                                        <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi">
                                        <i class="fa fa-grip-lines"></i>
                                        </button>
                                        <div class="dropdown-menu" style="margin: 0px;">
                                            <a class="dropdown-item aprov " href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" >Approve</a>
                                            <a class="dropdown-item edito " href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" >Reject</a>
                                        </div>
                                    </div>';
                        }
                        return $button;
                    })

                    ->addColumn('kwitansi', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-primary btn-sm mb-2 kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi"><i class="fa fa-paper-plane"></i></button>';

                        return $button;
                    })
                    ->rawColumns(['id_tr', 'hapus', 'akses', 'kwitansi', 'stts'])
                    ->make(true);
            }
            return view('transaksi.transaksi_tab', compact('kolektor', 'petugas', 'donatur', 'program', 'bank'));
        } elseif (Auth::user()->level == ('admin') | Auth::user()->level == ('keuangan pusat')) {
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
            $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
            $petugas = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')->where('users.id_com', Auth::user()->id_com)
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->get();
            $donatur = Donatur::where(function ($query) {
                $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
            })
                ->get();
                
                
            $program = Program::orderBy('program')->get();
            // dd($statak);
            $max = (int)$request->max;
            $min = (int)$request->min;
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";
            
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-' . $bln));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-' . $request->blnnnn)) : date('Y-m-t', strtotime('01-' . $bln));
            $thn = $request->thnn != '' ? $request->thnn : date('Y');
            // $carkota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";

            if ($request->ajax()) {
                
                $arr = $request->statuus;
                $kol = $request->kol;
                $kot = $request->kota;
                $stat = $request->statak;
                $bayar = $request->bayar;
                
                if ($request->plhtgl == 0) {
                    $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $tgls ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    });
                } elseif ($request->plhtgl == 1) {
                    // if(isset($request->statuus)) {
                    //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2'")->whereIn('status', $arr);
                    // }else{
                    //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2'");
                    // }
                    
                    $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    });
                } else {
                    // if(isset($request->statuus)) {
                    //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'")->whereIn('status', $arr);
                    // }else{
                    //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'");
                    // }
                    
                    $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    });
                }
                // return $datass;
                return DataTables::of($transaksi)
                    ->addIndexColumn()
                    ->addColumn('jml', function ($transaksi) {
                        $jml = number_format($transaksi->jumlah, 0, ',', '.');
                        return $jml;
                    })

                    ->addColumn('stts', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<span class="badge badge-success">Approved<span class="ms-1 fa fa-check"></span></span>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fa fa-ban"></span></span>';
                        } else {
                            $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                        }
                        return $button;
                    })

                    ->addColumn('tgl', function ($transaksi) {
                        $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                        return $tgl;
                    })

                    ->addColumn('hapus', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-rounded btn-sm btn-danger delete" name="edit" id="' . $transaksi->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menghapus Transaksi"> <i class="fa fa-trash"></i></button>';
                        return $button;
                    })

                    ->addColumn('akses', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<a class="btn btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<a class="btn btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $button =  '<div class="btn-group  mb-1">
                                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi">
                                        <i class="fa fa-grip-lines"></i>
                                        </button>
                                        <div class="dropdown-menu" style="margin: 0px;">
                                            <a class="dropdown-item aprov" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" >Approve</a>
                                            <a class="dropdown-item edito" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" >Reject</a>
                                        </div>
                                    </div>';
                        }
                        return $button;
                    })

                    ->addColumn('kwitansi', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-primary btn-sm mb-2 kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi"><i class="fa fa-paper-plane"></i></button>';

                        return $button;
                    })
                    ->rawColumns(['hapus', 'akses', 'kwitansi', 'stts'])
                    ->make(true);
            }

            $don = Donatur::where(function ($query) {
                $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
            })->limit(3750)
                ->get();
            $h1 = [];
            

            foreach ($don as $key => $val) {
                $h1[] = [
                    "text" => $val->nama . '-' . $val->no_hp . '-' . $val->alamat,
                    "nama" => $val->nama,
                    "id" => $val->id,
                    "nohp" => $val->no_hp,
                    "alamat" => $val->alamat,
                ];
            }

            $deals = json_encode($h1);

            return view('transaksi.transaksi_tab', compact('petugas', 'donatur', 'program', 'bank'));
        } elseif (Auth::user()->level == 'agen') {
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
            $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
            $petugas = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();
            $donatur = Donatur::where(function ($query) {
                $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
            })
                ->get();
            $program = Program::orderBy('program')->get();
            // dd($statak);
            $max = (int)$request->max;
            $min = (int)$request->min;
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-' . $bln));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-' . $request->blnnnn)) : date('Y-m-t', strtotime('01-' . $bln));
            $thn = $request->thnn != '' ? $request->thnn : date('Y');
            // $carkota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";
            $me = Auth::user()->id;

            if ($request->ajax()) {
                if ($request->plhtgl == 0) {
                    $plugin = $tgls;
                } else if ($request->plhtgl == 1) {
                    $plugin = "DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' ";
                } else {
                    $plugin = "YEAR(tanggal) = '$thn'";
                    // dd($transaksi);
                }
                
                
                $arr = $request->statuus;
                $kol = $request->kol;
                $stat = $request->statak;
                $bayar = $request->bayar;

                $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $plugin AND id_koleks = $me")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    });
                // return($transaksi);
                return DataTables::of($transaksi)
                    ->addIndexColumn()
                    ->addColumn('jml', function ($transaksi) {
                        $jml = number_format($transaksi->jumlah, 0, ',', '.');
                        return $jml;
                    })

                    ->addColumn('stts', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<span class="badge badge-success">Approved<span class="ms-1 fa fa-check"></span></span>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fa fa-ban"></span></span>';
                        } else {
                            $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                        }
                        return $button;
                    })

                    ->addColumn('tgl', function ($transaksi) {
                        $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                        return $tgl;
                    })

                    ->addColumn('hapus', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-rounded btn-sm btn-danger delete" name="edit" id="' . $transaksi->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menghapus Transaksi"> <i class="fa fa-trash"></i></button>';
                        return $button;
                    })

                    ->addColumn('akses', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<a class="btn btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<a class="btn btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $button =  '<div class="btn-group  mb-1">
                                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi">
                                        <i class="fa fa-grip-lines"></i>
                                        </button>
                                        <div class="dropdown-menu" style="margin: 0px;">
                                            <a class="dropdown-item aprov" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" >Approve</a>
                                            <a class="dropdown-item edito" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" >Reject</a>
                                        </div>
                                    </div>';
                        }
                        return $button;
                    })

                    ->addColumn('kwitansi', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-primary btn-sm mb-2 kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi"><i class="fa fa-paper-plane"></i></button>';

                        return $button;
                    })
                    ->rawColumns(['hapus', 'akses', 'kwitansi', 'stts'])
                    ->make(true);
            }

            return view('transaksi.transaksi_tab', compact('petugas', 'donatur', 'program', 'bank'));
        }
    }

    public function total(Request $request)
    {
        if (Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat') {
            $cari = $request->cari;
            $max = (int)$request->max;
            $min = (int)$request->min;
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-' . $bln));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-' . $request->blnnnn)) : date('Y-m-t', strtotime('01-' . $bln));
            $thn = $request->thnn != '' ? $request->thnn : date('Y');

            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";

            // $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            // $sampai = $request->sampai != '' ? $request->sampai : $dari;
            // $carkota = $request->kota != '' ? "id_kantor = '$request->kota'" : "kota != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";
            
            $arr = $request->statuus;
            $kol = $request->kol;
            $kot = $request->kota;
            $stat = $request->statak;
            $bayar = $request->bayar;
            $bank = $request->bank;

            if ($request->plhtgl == 0) {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND $tgls")
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $tgls ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $cari) {
                        if(isset($request->cari)){
                            $query->where('kolektor','LIKE','%'.$cari.'%');
                        }
                    })
                    
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }else{
                            $query->where('approval', '>', 0);
                        }
                    })
                ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            } elseif ($request->plhtgl == 1) {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2'")
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $cari) {
                        if(isset($request->cari)){
                            $query->where('kolektor','LIKE','%'.$cari.'%');
                        }
                    })
                    
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }else{
                            $query->where('approval', '>', 0);
                        }
                    })
                ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            } else {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'")
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                    ->where(function($query) use ($request, $cari) {
                        if(isset($request->cari)){
                            $query->where('kolektor','LIKE','%'.$cari.'%');
                        }
                    })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }else{
                            $query->where('approval', '>', 0);
                        }
                    })
                ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            }
        } elseif (Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang') {
            $kolektor = User::where('kolektor', 'kolektor')->where('kota', Auth::user()->kota)->get();
            $cari = $request->cari;
            $max = (int)$request->max;
            $min = (int)$request->min;
            $kot = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";

            $bln = $request->blnn != '' ? $request->blnn : date('m-Y');
            $blns = date('Y-m-01', strtotime('01-' . $bln));
            // $blnx = $request->blnx != '' ? $request->blnx : date('m-Y');
            $blnx = $request->blnx != '' ? date('Y-m-t', strtotime('01-' . $request->blnx)) : date('Y-m-t', strtotime('01-' . $bln));
            
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";

            // $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            // $sampai = $request->sampai != '' ? $request->sampai : $dari;
            // $carkota = $request->kota != '' ? "kota = '$request->kota'" : "kota != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            $tahoen = $request->thin != '' ? $request->thin : date('Y');
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";
            
            $arr = $request->statuus;
            $kol = $request->kol;
            $kotah = $request->kota;
            $stat = $request->statak;
            $bayar = $request->bayar;
            $bank = $request->bank;

            if ($k == null) {
                if ($request->plhtgl == 0) {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai'")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND $tgls ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $cari) {
                            if(isset($request->cari)){
                                $query->where('kolektor','LIKE','%'.$cari.'%');
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }else{
                                $query->where('approval', '>', 0);
                            }
                        })
                    ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                } elseif ($request->plhtgl == 1) {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' ")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $cari) {
                            if(isset($request->cari)){
                                $query->where('kolektor','LIKE','%'.$cari.'%');
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }else{
                                $query->where('approval', '>', 0);
                            }
                        })
                    ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                } else {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND  via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen'")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen' ")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $cari) {
                            if(isset($request->cari)){
                                $query->where('kolektor','LIKE','%'.$cari.'%');
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }else{
                                $query->where('approval', '>', 0);
                            }
                        })
                    ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                }
            } else {
                
                if ($request->plhtgl == 0) {
                        $plugin = $tgls;
                    }else if($request->plhtgl == 1){
                        $plugin = "DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'";
                    }else{
                        $plugin = "YEAR(tanggal) = '$tahoen'";
                    }
                    
                    $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $plugin ")
                    
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                        if(isset($request->bank)){
                            $query->where('id_bank', $bank);
                        }
                    })
                        
                    ->where(function($query) use ($request, $cari) {
                        if(isset($request->cari)){
                            $query->where('kolektor','LIKE','%'.$cari.'%');
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kotah, $kot, $k) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kotah);
                        }else{
                            $query->whereRaw("(id_kantor = '$kot' OR id_kantor = '$k->unit')");
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }else{
                            $query->where('approval', '>', 0);
                        }
                    })
                    ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                }
                
                
                // if ($request->kota != '') {
                //     if ($request->plhtgl == 0) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai'")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     } elseif ($request->plhtgl == 1) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND  DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     } else {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen'")->orderBy('tanggal', 'desc')->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     }
                // } else {
                //     if ($request->plhtgl == 0) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai'")
                //             ->orWhereRaw("id_kantor = '$k->id' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai'")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     } elseif ($request->plhtgl == 1) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'")
                //             ->orWhereRaw("id_kantor = '$k->id' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND  via_input = 'transaksi' ANDDATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     } else {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND via_input = 'transaksi' AND $statak AND YEAR(tanggal) = '$tahoen'")
                //             ->orWhereRaw("id_kantor = '$k->id' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen'")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
                //     }
                // }
            // }
        }else if(Auth::user()->level == ('agen')){
            $max = (int)$request->max;
            $min = (int)$request->min;
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-' . $bln));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-' . $request->blnnnn)) : date('Y-m-t', strtotime('01-' . $bln));
            $thn = $request->thnn != '' ? $request->thnn : date('Y');

            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";

            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            // $carkota = $request->kota != '' ? "id_kantor = '$request->kota'" : "kota != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah != ''";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah != ''";
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";
            $me = Auth::user()->id;
            
            if ($request->plhtgl == 0) {
                $plugin = $tgls;
            } else if ($request->plhtgl == 1) {
                $plugin = "DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' ";
            } else {
                $plugin = "YEAR(tanggal) = '$thn'";
            }
                
                
            $arr = $request->statuus;
                // $kol = $request->kol;
            $stat = $request->statak;
            $bayar = $request->bayar;
            $bank = $request->bank;

            $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $plugin AND id_koleks = $me")
                ->where(function($query) use ($request, $arr) {
                    if(isset($request->statuus)){
                        $query->whereIn('status', $arr);
                    }
                })
                    
                    // ->where(function($query) use ($request, $kol) {
                    //     if(isset($request->kol)){
                    //         $query->whereIn('kolektor', $kol);
                    //     }
                    // })
                    
                ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                ->where(function($query) use ($request, $stat) {
                    if(isset($request->statak)){
                        $query->whereIn('approval', $stat);
                    }else{
                        $query->where('approval', '>', 0);
                    }
                })
                    
                ->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            

            // if ($request->plhtgl == 0) {
            //     $datass = Transaksi::whereRaw(" $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND $tgls AND id_koleks = $me")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            // } elseif ($request->plhtgl == 1) {
            //     $datass = Transaksi::whereRaw("$carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' AND id_koleks = $me")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            // } else {
            //     $datass = Transaksi::whereRaw(" $carmin AND $carmax AND $statuus AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn' AND id_koleks = $me")->select(\DB::raw("SUM(jumlah) as jumlah"))->get();
            //     // dd($transaksi);
            // }
        }
        // dd($datass);
        return $datass;
    }

    public function ambil($id)
    {
        if (request()->ajax()) {
            $data = Transaksi::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function ambilkirim($id)
    {
        if (request()->ajax()) {
            $get = Transaksi::findOrFail($id);

            $data = [
                'data' => Transaksi::findOrFail($id),
                'datadonatur' => Donatur::where('id', $get->id_donatur)->first(),
                'don' => Transaksi::selectRaw('SUM(jumlah) as total')->where('id_transaksi', $get->id_transaksi)->where('via_input', 'transaksi')->first(),
                'jam' => date('d / m / Y H:i', strtotime($get->tanggal)),
                'nohpadm' => Kantor::where('unit', $get->kota)->first(),
            ];
            return response()->json(['result' => $data]);
        }
    }

    public function cek_kolektor(Request $request)
    {
        $kota = $request->kota;
        // return($request->plhtgl);
        if($request->plhtgl == 1){
            $bln1 = $request->blns == '' ? date('Y-m-01') : date('Y-m-01', strtotime('01-'.$request->blns));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-'.$request->blnnnn)) : date('Y-m-t', strtotime($bln1));
            $tgls = "DATE(transaksi.tanggal) >= '$bln1' AND DATE(transaksi.tanggal) <= '$bln2'";
        }else if($request->plhtgl == 2){
            $thn = $request->thnn == '' ? date('Y') : $request->thnn;
            $tgls = "YEAR(transaksi.tanggal) = '$thn'";
        }else{
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
        }
        // $data = DB::select("SELECT distinct kolektor from transaksi where id_kantor IN ($kota) AND $tgls");
        $data = Transaksi::select('kolektor')->whereRaw("$tgls")->whereIn('id_kantor', $kota)->distinct()->get();
        // return($kota);
        return $data;
    }

    public function test($donatur)
    {
        $kwitansi = Transaksi::where('id_transaksi', $donatur)->where('via_input', 'transaksi')->orderBy('created_at', 'desc')->get();
        $jumlah = Transaksi::where('id_transaksi', $donatur)->where('via_input', 'transaksi')->orderBy('created_at', 'desc')->sum('jumlah');
        $dat = DB::select("SELECT distinct kolektor, donatur, alamat, tanggal, id_transaksi from transaksi where id_transaksi = $donatur AND via_input = 'transaksi' LIMIT 0,1");
        $img = DB::select("SELECT bukti,bukti2 from transaksi where id_transaksi = $donatur AND via_input = 'transaksi' order by id DESC LIMIT 1 ");
        $kol = Transaksi::where('id_transaksi', $donatur)->first();
        return view('transaksi.tester', compact('kwitansi', 'dat', 'jumlah', 'img', 'kol'));
    }

    public function destroyy($id, Request $request)
    {
        $donatur = Transaksi::findOrFail($id);
        
        $aw = Transaksi::find($id);
            $input['id'] = $aw->id;
            $input['id_bank'] = $aw->id_bank;
            $input['id_transaksi'] = $aw->id_transaksi;
            $input['tanggal'] = $aw->tanggal;
            $input['kolektor'] = $aw->kolektor;
            $input['donatur'] = $aw->donatur;
            $input['alamat'] = $aw->alamat;
            $input['pembayaran'] = $aw->pembayaran;
            $input['id_koleks'] = $aw->id_koleks;
            $input['id_donatur'] = $aw->id_donatur;
            $input['id_sumdan'] = $aw->id_sumdan;
            $input['id_program'] = $aw->id_program;
            $input['program'] = $aw->program;
            $input['subprogram'] = $aw->subprogram;
            $input['keterangan'] = $aw->keterangan;
            $input['bukti'] = $aw->bukti;
            $input['bukti2'] = $aw->bukti2;
            $input['jumlah'] = $aw->jumlah;
            $input['subtot'] = $aw->subtot;
            
            $input['status'] = $aw->status;
            $input['kota'] = $aw->kota;
            $input['id_kantor'] = $aw->id_kantor;
            $input['kantor_induk'] = $aw->kantor_induk;
            
            $input['approval'] = $aw->approval;
            $input['alasan'] = $aw->alasan;
            $input['user_insert'] = $aw->user_insert;
            $input['user_update'] = $aw->user_update;
            
            $input['user_approve'] = $aw->user_approve;
            $input['id_pros'] = $aw->id_pros;
            $input['via_input'] = $aw->via_input;
            $input['akun'] = $aw->akun;
            
            $input['qty'] = $aw->qty;
            $input['ket_penerimaan'] = $aw->ket_penerimaan;
            $input['coa_debet'] = $aw->coa_debet;
            $input['coa_kredit'] = $aw->coa_kredit;
            
            $input['id_camp'] = $aw->id_camp;
            $input['hapus_token'] = $aw->hapus_token;
            $input['notif'] = $aw->notif;
            
            $input['hapus_alasan'] = $request->alasan;
            $input['user_delete'] = Auth::user()->id;
            
            $data = HapusTransaksi::create($input);
        
        
        $id_trans = $donatur->id_transaksi;
        $donatur->delete();

        $sumtran = 0;
        $sumtran = Transaksi::where('id_transaksi', $id_trans)->sum('jumlah');
        Transaksi::where('id_transaksi', $id_trans)->update([
            'subtot' => $sumtran,
        ]);

        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Transaksi ' . $donatur->nama);
        // return back();
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $donatur = Transaksi::findOrFail($id);
        $donatur->delete();
        return back();
    }


    public function aprove($id)
    {
        $data = Transaksi::where('id', $id)->where('via_input', 'transaksi')->first();

        $status_sekarang = $data->approval;

        if ($status_sekarang == 1) {
            Transaksi::where('id', $id)->update([
                'approval' => 0
            ]);
        } else {
            Transaksi::where('id', $id)->update([
                'approval' => 1,
                'alasan' => ''
            ]);
        }
        return back();
    }

    public function aprove_all(Request $request)
    {
        if (Auth::user()->level == ('kacab') | Auth::user()->keuangan == ('keuangan cabang') | Auth::user()->level == ('agen')) {
            $kolektor = User::where('kolektor', 'kolektor')->where('kota', Auth::user()->kota)->get();

            $max = (int)$request->max;
            $min = (int)$request->min;
            $kot = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status IS NOT NULL";
            
            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";

            $bln = $request->blnn != '' ? $request->blnn : date('m-Y');
            $blns = date('Y-m-01', strtotime('01-' . $bln));
            // $blnx = $request->blnx != '' ? $request->blnx : date('m-Y');
            $blnx = $request->blnx != '' ? date('Y-m-t', strtotime('01-' . $request->blnx)) : date('Y-m-t', strtotime('01-' . $bln));

            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;

            // $carkota = $request->kota != '' ? "kota = '$request->kota'" : "kota != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor IS NOT NULL";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            $tahoen = $request->thin != '' ? $request->thin : date('Y');
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval IS NOT NULL";
            
            $arr = $request->statuus;
            $kol = $request->kol;
            $kotes = $request->kota;
            $stat = $request->statak;
            $bayar = $request->bayar;
            $bank = $request->bank;

            if ($k == null) {
                if ($request->plhtgl == 0) {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 2")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND $tgls AND approval = 2 AND tanggal = DATE_FORMAT(created_at, '%Y-%m-%d')")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        })
                    ->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                } elseif ($request->plhtgl == 1) {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' AND approval = 2")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' AND approval = 2")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        })
                    ->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                } else {
                    // $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND  via_input='transaksi' AND YEAR(tanggal) = '$tahoen' AND approval = 2")
                    $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$tahoen' AND approval = 2 AND tanggal = DATE_FORMAT(created_at, '%Y-%m-%d')")
                        ->where(function($query) use ($request, $arr) {
                            if(isset($request->statuus)){
                                $query->whereIn('status', $arr);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                        
                        ->where(function($query) use ($request, $kol) {
                            if(isset($request->kol)){
                                $query->whereIn('kolektor', $kol);
                            }
                        })
                        
                        ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                        
                        ->where(function($query) use ($request, $stat) {
                            if(isset($request->statak)){
                                $query->whereIn('approval', $stat);
                            }
                        })
                    ->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                }
            } else {
                
                if ($request->plhtgl == 0) {
                        $plugin = $tgls;
                }else if($request->plhtgl == 1){
                        $plugin = "DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx'";
                }else{
                        $plugin = "YEAR(tanggal) = '$tahoen'";
                }
                
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $plugin AND approval = 2 AND tanggal = DATE_FORMAT(created_at, '%Y-%m-%d')")
                    
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $kotes, $kot, $k) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kotes);
                        }else{
                            $query->whereRaw("(id_kantor = '$kot' OR id_kantor = '$k->unit')");
                        }
                    })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    })
                    ->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                
                // if ($request->kota != '') {
                //     if ($request->plhtgl == 0) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND  DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     } elseif ($request->plhtgl == 1) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     } else {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND  via_input='transaksi' AND YEAR(tanggal) = '$tahoen' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     }
                // } else {
                //     if ($request->plhtgl == 0) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 2")
                //             ->orWhereRaw("id_kantor = '$k->unit' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     } elseif ($request->plhtgl == 1) {
                //         $datass = Transaksi::whereRaw("id_kantor = '$kot' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' AND approval = 2")
                //             ->orWhereRaw("id_kantor = '$k->unit' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$blns' AND DATE(tanggal) <= '$blnx' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     } else {
                //         $datass = Transaksi::whereRaw("id_kantor = '$request->kota' AND $carkol AND $carmin AND $carmax AND $statuus AND via_input='transaksi' AND $statak AND YEAR(tanggal) = '$tahoen' AND approval = 2")
                //             ->orWhereRaw("id_kantor = '$k->unit' AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND YEAR(tanggal) = '$tahoen' AND approval = 2")->update(['approval' => 1, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
                //     }
                // }
            }
        } elseif (Auth::user()->level == ('admin') | Auth::user()->keuangan == ('keuangan pusat')) {
            $max = (int)$request->max;
            $min = (int)$request->min;
            $statuus = $request->statuus != '' ? "status = '$request->statuus'" : "status != ''";
            $bln = $request->blns != '' ? $request->blns : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-' . $bln));
            $bln2 = $request->blnnnn != '' ? date('Y-m-t', strtotime('01-' . $request->blnnnn)) : date('Y-m-t', strtotime('01-' . $bln));
            $thn = $request->thnn != '' ? $request->thnn : date('Y');

            if ($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";


            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari;
            // $carkota = $request->kota != '' ? "id_kantor = '$request->kota'" : "id_kantor != ''";
            // $carkol = $request->kol != '' ? "kolektor = '$request->kol'" : "kolektor != ''";
            $carmin = $request->min != '' ? "jumlah >= $min" : "jumlah IS NOT NULL";
            $carmax = $request->max != '' ? "jumlah <= $max" : "jumlah IS NOT NULL";
            // $statak = $request->statak != '' ? "approval = '$request->statak'" : "approval != ''";
            
            $arr = $request->statuus;
            $kol = $request->kol;
            $kotah = $request->kota;
            $stat = $request->statak;
            $bayar = $request->bayar;
            $bank = $request->bank;

            if ($request->plhtgl == 0) {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND $tgls AND approval = 2")
                
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $tgls AND approval = 2 ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kota)){
                            $query->whereIn('id_kantor', $kotah);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    })
                
                ->update([
                    'approval' => 1,
                    'user_update' => Auth::user()->id,
                    'user_approve' => Auth::user()->id
                ]);
            } elseif ($request->plhtgl == 1) {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' AND approval = 2")
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2' AND approval = 2 ")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kotah);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    })
                ->update([
                    'approval' => 1,
                    'user_update' => Auth::user()->id,
                    'user_approve' => Auth::user()->id
                ]);
            } else {
                // $datass = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statuus AND $statak AND via_input='transaksi' AND YEAR(tanggal) = '$thn' AND approval = 2")
                $datass = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn' AND approval = 2")
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('status', $arr);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('kolektor', $kol);
                        }
                    })
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kot)){
                            $query->whereIn('id_kantor', $kotah);
                        }
                    })
                    
                    ->where(function($query) use ($request, $bank) {
                            if(isset($request->bank)){
                                $query->where('id_bank', $bank);
                            }
                        })
                    
                    ->where(function($query) use ($request, $bayar) {
                            if(isset($request->bayar)){
                                $query->whereIn('pembayaran', $bayar);
                            }
                        })
                    
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('approval', $stat);
                        }
                    })
                ->update([
                    'approval' => 1,
                    'user_update' => Auth::user()->id,
                    'user_approve' => Auth::user()->id
                ]);
                // dd($transaksi);
            }
        }
        // return ($datass);
        return response()->json(['success' => 'Data is successfully updated']);

    }

     public function aproves($id)
    {
        $data = Transaksi::where('id', $id)->first();
        $create_at = strtotime($data->created_at);
        $akses = Auth::user()->keuangan;
        $status_sekarang = $data->approval;
        
        if ($status_sekarang == 1) {
            Transaksi::where('id', $id)->update([
                'approval' => 0,
                'user_update' => Auth::user()->id,
                'user_approve' => Auth::user()->id,
            ]);
            \LogActivity::addToLog(Auth::user()->name . ' Menolak Data Transaksi ' . $data->donatur);
        } else {
            if($data->tanggal != $create_at && $akses == 'admin' ){
            Transaksi::where('id', $id)->update([
                'approval' => 1,
                'alasan' => NULL,
                'user_update' => Auth::user()->id,
                'user_approve' => Auth::user()->id,
            ]);
            }else if ($data->tanggal = $create_at ){
              Transaksi::where('id', $id)->update([
                'approval' => 1,
                'alasan' => NULL,
                'user_update' => Auth::user()->id,
                'user_approve' => Auth::user()->id,

            ]);
            }
            
            \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Data Transaksi ' . $data->donatur);
            
            // $t = Transaksi::findOrFail($id);
            
            // if($t->id_camp > 0 && $t->jumlah > 0){ 
            //     $response = Http::post('https://berbagibahagia.org/api/posttran', [
            //         'via_input' => 'kilau',
            //         'id_trans'  => $id,
            //         'id_camp'   => $t->id_camp,
            //         'nama'      => $t->donatur,
            //         'jumlah'    => $t->jumlah,
            //         'status'    => 1,
            //     ]);
            // }
            
            // $t->approval        = 1;
            // $t->alasan          = NULL;
            // $t->user_update     = Auth::user()->id;
            // $t->user_approve    = Auth::user()->id;
            // $t->update();
        }
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function appr(Request $request)
    {
            // return($request);
        if ($request->ajax()) {
            $id = $request->id_hidden;
            $kinerja = Transaksi::findOrFail($id);

            $input = $request->all();
            
            // if($kinerja->id_camp > 0 && $request->approval == 0){ 
            //     $response = Http::post('https://berbagibahagia.org/api/deltran/' . $id, [
            //         'via_input' => 'kilau',
            //     ]);
            // }
            
            $input['user_update'] = Auth::user()->id;

            $kinerja->update($input);
        }
        return response()->json(['success' => 'Data is successfully Send']);
    }

    public function trf()
    {
        $trf = Transaksi::all();
        return view('transaksi.trfb', compact('trf'));
    }

    public function riwayat(Request $request, $id)
    {
        //  dd($transaksi = Transaksi::where('id_donatur', $id)->get()); 
        $kolektor = User::where('kolektor', 'kolektor')->where('kota', Auth::user()->kota)->get();
        if ($request->ajax()) {
            if (Auth::user()->level == ('kacab') | Auth::user()->level == ('admin') | Auth::user()->level == 'agen') {

                $transaksi = Transaksi::where('id_donatur', $id)->where('via_input', 'transaksi')->get();
                // dd($transaksi);

            }
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->addColumn('jml', function ($transaksi) {
                    $jml = number_format($transaksi->jumlah, 0, ',', '.');
                    return $jml;
                })

                ->addColumn('id_tr', function ($transaksi) {
                    $link = url('riwayat-donasi/'.$transaksi->donatur);
                    $trr = '<a href="' . $link . '" target="_blank">' . $transaksi->id_transaksi . '</a>';
                    return $trr;
                })

                ->addColumn('tgl', function ($transaksi) {
                    $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                    return $tgl;
                })

                ->addColumn('hapus', function ($transaksi) {
                    $button = '<button type="button" name="edit" id="' . $transaksi->id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })

                ->addColumn('akses', function ($transaksi) {
                    if ($transaksi->approval == 1) {
                        $button = '<div class="btn-group"><a class="btn btn-warning edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="">Reject</a></div>';
                    } else {
                        $button = '<div class="btn-group"><a class="btn btn-success aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="">Approve</a></div>';
                    }
                    return $button;
                })

                ->addColumn('kwitansi', function ($transaksi) {
                    $button = '<div class="btn-group"><a class="btn btn-success kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="' . $transaksi->id . '"  href="">Kirim</a></div>';

                    return $button;
                })
                ->rawColumns(['id_tr', 'tgl', 'hapus', 'akses', 'kwitansi', 'donature'])
                ->make(true);
        }
        return view('donatur.riwayat_kunjungan', compact('kolektor'));
    }

    public function detaildon(Request $request, $id)
    {
        $kolektor = User::where('kolektor', 'kolektor')->where('kota', Auth::user()->kota)->get();
        $jmlh = Transaksi::where('id_donatur', $id)->where('via_input', 'transaksi')->sum('jumlah');
        if ($request->ajax()) {
            if (Auth::user()->level == ('kacab') | Auth::user()->level == ('admin')) {

                $transaksi = Transaksi::where('id_donatur', $id)->where('jumlah', '>', 0)->where('via_input', 'transaksi')->get();
                // dd($transaksi);

            }
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->addColumn('jml', function ($transaksi) {
                    $jml = 'Rp.' . number_format($transaksi->jumlah, 0, ',', '.');
                    return $jml;
                })

                ->addColumn('no_hp', function ($transaksi) {
                    $go = Donatur::select('no_hp')->where('id', $transaksi->id_donatur)->first();
                    $jml = $go->no_hp;
                    return $jml;
                })

                ->addColumn('id_tr', function ($transaksi) {
                    $link = url('riwayat-donasi/'.$transaksi->donatur);
                    $trr = '<a href="' . $link . '" target="_blank">' . $transaksi->id_transaksi . '</a>';
                    return $trr;
                })

                ->addColumn('tgl', function ($transaksi) {
                    $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                    return $tgl;
                })

                ->addColumn('kwitansi', function ($transaksi) {
                    $button = '<div class="btn-group"><a class="btn btn-success kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="' . $transaksi->id . '"  href="">Kirim</a></div>';

                    return $button;
                })
                // ->rawColumns(['no_hp'])
                ->make(true);
        }
        return view('donatur.riwayat_donasi', compact('kolektor', 'jmlh'));
    }

    function getdon(Request $request)
    {
        $term = $request->term;
        $don = Donatur::where(function ($query) {
            $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
        })->limit(5000)
            ->get();
        // dd ($don);
        foreach ($don as $key => $val) {
            $h1[] = [
                "text" => $val->nama . '-' . $val->no_hp . '-' . $val->alamat,
                "nama" => $val->nama,
                "id" => $val->id,
                "nohp" => $val->no_hp,
                "alamat" => $val->alamat,
            ];
        }
        return response()->json($h1);
    }

    public function posttrans(Request $request)
    {
        $user = User::where('id', $request->petugas)->first();
        $don = Donatur::where('id', $request->donatur)->first();
        $prog = Program::where('id', $request->program)->first();
        // dd($request->all());
        $donuser = Donatur::find($request->donatur);
        $donuser->setoran = $request->jumlah != '' ? preg_replace("/[^0-9]/", "", $request->jumlah) : 0;
        $donuser->status = 'Donasi';
        $donuser->user_trans = $user->id;
        $donuser->acc = 0;
        $donuser->dikolek = date('d/m/Y');
        $donuser->update();

        $input = $request->all();
        $input['id_transaksi'] = $don->id . date('dmY') . $user->id;
        $input['kolektor'] = $user->name;
        $input['id_koleks'] = $user->id;
        $input['id_kantor'] = $user->id_kantor;
        $input['id_donatur'] = $don->id;
        $input['donatur'] = $don->nama;
        $input['alamat'] = $don->alamat;
        $input['kota'] = $don->kota;
        $input['program'] = $prog->program;
        $input['subprogram'] = $prog->subprogram;
        $input['tanggal'] = date('Y-m-d');
        $input['status'] = 'Donasi';
        $input['dp'] = $prog->dp;
        $input['jumlah'] = $request->jumlah != '' ? preg_replace("/[^0-9]/", "", $request->jumlah) : 0;
        
        if ($request->hasFile('bukti')) {
            $image = $request->file('bukti');

            if ($image->isValid()) {
                $image_name = $image->getClientOriginalName();
                $upload_path = 'gambarUpload';
                $image->move($upload_path, $image_name);
                $input['bukti'] = $image_name;
            }
        }
        // dd($input);
        Transaksi::create($input);
        \LogActivity::addToLog(Auth::user()->name . ' Menambahkan Data Transaksi');

        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function add()
    {
        $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
        if (Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang')) {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->where('users.id_com', Auth::user()->id_com)->get();
        } else {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();
        }
        $donatur = Donatur::where(function ($query) {
            $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
        })
            ->get();
        $program = Program::orderBy('program')->get();
        return view('transaksi.add', compact('bank', 'petugas', 'donatur', 'program'));
    }

    public function add_tr()
    {
        $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
        if (Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang')) {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->where('users.id_com', Auth::user()->id_com)->get();
        } else {
            $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();
        }
        $donatur = Donatur::where(function ($query) {
            $query->where('status', '!=', 'Ditarik')->where('status', '!=', 'Off');
        })
            ->get();
        $program = Program::orderBy('program')->get();
        return view('transaksi.add_tr', compact('bank', 'petugas', 'donatur', 'program'));
    }

    public function getinfodon($id)
    {
        $data = Donatur::where('id', $id)->first();
        return $data;
    }

    public function getprosp($id, $prog)
    {
        // dd($id, $prog);
        $data = Prosp::whereRaw("id_don = $id AND id_prog = $prog");
        $progs = Prog::where('id_program', $prog)->first();
        if (count($data->get()) > 0) {
            $p['hasil'] = $data->first()->id;
            $p['test'] = $progs->jp;
            $p['camp'] = $progs->id_catcamp;
            
        } else {
            $p['hasil'] = 0;
            $p['test'] = $progs->jp;
            $p['camp'] = $progs->id_catcamp;
        }
        
        // $program = Prog::where('id_program', $prog)->first();
        // return($program);
        
        return $p;
    }

    public function posttest(Request $request)
    {
        return($request);
        $rek = $request->arr;
        foreach ($rek as $key => $val) {
            $user = User::where('id', $val['id_petugas'])->first();
            $don = Donatur::where('id', $val['id_donatur'])->first();
            $prog = Prog::where('id_program', $val['id_program'])->first();

            $donuser = Donatur::find($val['id_donatur']);
            $donuser->setoran = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;
            $donuser->status = 'Donasi';
            $donuser->acc = 0;
            $donuser->user_trans = $user->id;
            $donuser->dikolek = date('d/m/Y');
            $donuser->update();

            $hide_pros = $val['id_pros_hide_hide'];
            $tgl = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
            $tgl1 = $val['tgl'] == '' ? date('dmY') : date('dmY', strtotime($val['tgl']));

            $cari_jenis_donatur = $don->jenis_donatur;
            if ($cari_jenis_donatur === 'personal') {
                $cari_coa_kredit = $prog->coa_individu;
            } else {
                $cari_coa_kredit = $prog->coa_entitas;
            }

            if ($val['pembayaran'] == 'teller' || $val['pembayaran'] == 'dijemput') {
                $kantor = Auth::user()->id_kantor;
                $coa_debet = Kantor::where('id', $kantor)->first()->id_coa;
            } else if ($val['pembayaran'] == 'transfer') {
                $coa_debet = Bank::where('id_bank', $val['id_bank'])->first()->id_coa;
            }


            $input = $request->all();
            $input['id_transaksi'] = $don->id . $tgl1 . $user->id;
            $input['kolektor'] = $user->name;
            $input['id_koleks'] = $user->id;
            $input['id_kantor'] = $don->id_kantor;
            $input['id_donatur'] = $don->id;
            $input['donatur'] = $don->nama;
            $input['alamat'] = $don->alamat;
            $input['kota'] = $don->kota;
            $input['id_sumdan'] = $prog->id_sumber_dana;
            $input['id_program'] = $prog->id_program;
            $input['id_pros'] = $hide_pros;
            $input['subprogram'] = $prog->program;
            $input['pembayaran'] = $val['pembayaran'];
            $input['keterangan'] = $val['keterangan'];
            $input['tanggal'] = $tgl;
            $input['created_at'] = $tgl . date('H:i:s');
            $input['status'] = 'Donasi';
            $input['jumlah'] = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;
            $input['coa_debet'] = $coa_debet;
            $input['coa_kredit'] = $cari_coa_kredit;
            $input['via_input'] = 'transaksi';
            $input['akun'] = $prog->program;
            $input['qty'] = 1;
            $input['dp'] = $prog->dp;
            $input['ket_penerimaan'] = 'an: ' . $don->nama . ' | ' . $prog->program;
            $input['user_insert'] = Auth::user()->id;

            if ($val['pembayaran'] == 'transfer') {
                $folderPath = "/home/kilauindonesia/public_html/kilau/gambarUpload/";
                $image_parts = explode(";base64,", $val['bukti']);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $image_name = $val['nama_file'];
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);

                $input['bukti'] = $image_name;
                $input['id_bank'] = $val['id_bank'];
            }

            Transaksi::create($input);

            // dd($tgl, $tgl1);
        }
        \LogActivity::addToLog(Auth::user()->name . ' Menambahkan Data Transaksi');
        return response()->json(['success' => 'Data Added successfully.']);
        // dd($request->arr);
    }

    public function updatedon(Request $request)
    {
        $id = $request->id;
        Donatur::where('id', $id)->update([
            'no_hp' => $request->nohp,
            'alamat' => $request->alamat,
            'user_update' => Auth::user()->id
        ]);

        return response()->json(['success' => 'Data update donatur successfully.']);
    }

    public function getsave(Request $request)
    {
        // $as = [133211911202181, 234701911202144];
        if ($request->ajax()) {
            $transaksi = Transaksi::whereIn('id_transaksi', $request->id)->get();
            // dd($transaksi ,$request->id);
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->addColumn('jml', function ($transaksi) {
                    $jml = number_format($transaksi->jumlah, 0, ',', '.');
                    return $jml;
                })

                ->addColumn('id_tr', function ($transaksi) {
                    $link = "https://kilauindonesia.org/kilau/kwitansi/$transaksi->id_transaksi";
                    $trr = '<a href="' . $link . '" target="_blank">' . $transaksi->id_transaksi . '</a>';
                    return $trr;
                })

                ->addColumn('tgl', function ($transaksi) {
                    $tgl = (Carbon::parse($transaksi->tanggal))->isoFormat('D MMMM Y');
                    // $tgl = date('d/m/Y H:i', strtotime($transaksi->tanggal));
                    return $tgl;
                })

                ->addColumn('hapus', function ($transaksi) {
                    $button = '<button type="button" name="edit" id="' . $transaksi->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    return $button;
                })

                ->addColumn('stts', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<span class="badge badge-success">Approved<span class="ms-1 fa fa-check"></span></span>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fa fa-ban"></span></span>';
                        } else {
                            $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                        }
                        return $button;
                    })

                ->addColumn('akses', function ($transaksi) {
                        if ($transaksi->approval == 1) {
                            $button = '<a class="btn btn-warning btn-sm edito" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Reject Transaksi"><i class="fa fa-ban"></i></a>';
                        } elseif ($transaksi->approval == 0) {
                            $button = '<a class="btn btn-success btn-sm aprov" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Menyetujui Transaksi">Approved</a>';
                        } else {
                            $button =  '<div class="btn-group mb-1">
                                        <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Memilih Akses Transaksi">
                                        <i class="fa fa-grip-lines"></i>
                                        </button>
                                        <div class="dropdown-menu" style="margin: 0px;">
                                            <a class="dropdown-item aprov " href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2" id="' . $transaksi->id . '" >Approve</a>
                                            <a class="dropdown-item edito " href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $transaksi->id . '" >Reject</a>
                                        </div>
                                    </div>';
                        }
                        return $button;
                })

                ->addColumn('kwitansi', function ($transaksi) {
                        $button = '<button type="button" class="btn btn-primary btn-sm mb-2 kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$transaksi->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi"><i class="fa fa-paper-plane"></i></button>';

                        return $button;
                })
                ->rawColumns(['id_tr', 'hapus', 'akses', 'kwitansi', 'stts'])
                ->make(true);
        }
        // return $items;
    }

    public function get_prog(Request $request)
    {
        // dd($request->all());
        $q = $request->search;
        $data = Prog::where(function ($query) use ($q) {
            $query->where('program', 'LIKE', '%' . $q . '%');
            // ->orWhere('email', 'LIKE', '%'.$q.'%');
        })->where('parent', 'n')->get();
        if (count($data) > 0) {
            //  $list = array();
            foreach ($data as $key => $val) {
                $list[] = [
                    "text" => $val->program . ' ' . $val->program,
                    // "no_hp" => $val->no_hp,
                    // "kota" => $val->kota,
                    // "alamat" => $val->alamat,
                    "nama" => $val->program,
                    "id" => $val->id_program,

                ];
            }
            return json_encode($list);
        } else {
            return "hasil kosong";
        }
    }

    public function get_prog_prog(Request $request, $prog)
    {
        // dd($prog);

        $q = $request->search;

        if ($prog == 'teller' || $prog == 'dijemput') {
            $data = Prog::where(function ($query) use ($q) {
                $query->where('jp', '0')
                    ->orWhere('jp', '2');
                // ->orWhere('email', 'LIKE', '%'.$q.'%');
            })->where('program', 'LIKE', '%' . $q . '%')->where('parent', 'n')->get();
        } else if ($prog == 'transfer') {
            $data = Prog::where(function ($query) use ($q) {
                $query->where('jp', '0')
                    ->orWhere('jp', '2');
                // ->orWhere('email', 'LIKE', '%'.$q.'%');
            })->where('program', 'LIKE', '%' . $q . '%')->where('parent', 'n')->get();
        } else if ($prog == 'noncash') {
            $data = Prog::where(function ($query) use ($q) {
                $query->where('jp', '1')
                    ->orWhere('jp', '2');
                // ->orWhere('email', 'LIKE', '%'.$q.'%');
            })->where('program', 'LIKE', '%' . $q . '%')->where('parent', 'n')->get();
        }


        if (count($data) > 0) {
            //  $list = array();
            foreach ($data as $key => $val) {
                $list[] = [
                    "text" => $val->program . ' ' . $val->program,
                    // "no_hp" => $val->no_hp,
                    // "kota" => $val->kota,
                    // "alamat" => $val->alamat,
                    "nama" => $val->program,
                    "id" => $val->id_program,

                ];
            }
            return json_encode($list);
        } else {
            return "hasil kosong";
        }
    }

    public function post_trans(Request $request)
    {
        $rek = $request->arr;
        // dd($rek);
        foreach ($rek as $key => $val) {
            $jml = 0;

            // dd($jml += $val['jumlah']);
            $a = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;

            $jml += $a;

            $user = User::where('id', $val['id_petugas'])->first();
            $don = Donatur::where('id', $val['id_donatur'])->first();
            $prog = Prog::where('id_program', $val['id_program'])->first();

            $donuser = Donatur::find($val['id_donatur']);
            // $donuser->setoran = ($jml+$est);
            $donuser->status = 'Donasi';
            $donuser->acc = 0;
            $donuser->user_trans = $user->id;
            $donuser->dikolek = date('d/m/Y');
            $donuser->update();

            $hide_pros = $val['id_pros_hide_hide'];
            $tgl = $val['tgl'] == '' ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($val['tgl']));
            $tgl1 = $val['tgl'] == '' ? date('dmY') : date('dmY', strtotime($val['tgl']));
            $tgl2 = $val['tgl'] == '' ? date('Y-m-d') : date('Y-m-d', strtotime($val['tgl']));
            // dd($tgl);

            $cari_jenis_donatur = $don->jenis_donatur;
            if ($cari_jenis_donatur == 'personal') {
                $cari_coa_kredit = $prog->coa_individu;
            } else if($cari_jenis_donatur == 'entitas'){
                $cari_coa_kredit = $prog->coa_entitas;
            } else {
                $cari_coa_kredit = $prog->coa_individu;
            }

            if ($val['pembayaran'] == 'teller' || $val['pembayaran'] == 'dijemput') {
                $kantor = $user->id_kantor;
                $coa_debet = Kantor::where('id', $kantor)->first()->id_coa;
            } else if ($val['pembayaran'] == 'transfer') {
                $coa_debet = Bank::where('id_bank', $val['id_bank'])->first()->id_coa;
            } else if ($val['pembayaran'] == 'noncash') {
                $coa_debet = $val['non_cash'];
            }

            if ($val['pembayaran'] == 'teller' || $val['pembayaran'] == 'dijemput') {
                $p = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;
            } else if ($val['pembayaran'] == 'transfer') {
                $p = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;
            } else if ($val['pembayaran'] == 'noncash') {
                $p = $val['jumlah'] != '' ? preg_replace("/[^0-9]/", "", $val['jumlah']) : 0;
            }

            $id_trans = $don->id . $tgl1 . $user->id;
            $input = $request->all();
            $input['id_transaksi'] = $don->id . $tgl1 . $user->id;
            $input['kolektor'] = $user->name;
            $input['id_koleks'] = $user->id;
            $input['id_kantor'] = $don->id_kantor;
            $input['id_donatur'] = $don->id;
            $input['donatur'] = $don->nama;
            $input['alamat'] = $don->alamat;
            $input['id_camp'] = $val['id_camp'] == null ? null : $val['id_camp'];
            $input['kota'] = $don->kota;
            $input['id_sumdan'] = $prog->id_sumber_dana;
            $input['id_program'] = $prog->id_program;
            $input['id_pros'] = $hide_pros;
            $input['subprogram'] = $prog->program;
            $input['pembayaran'] = $val['pembayaran'];
            $input['keterangan'] = $val['keterangan'];
            $input['tanggal'] = $tgl2;
            $input['created_at'] = $tgl;
            $input['status'] = 'Donasi';
            $input['jumlah'] = $p;
            $input['coa_debet'] = $coa_debet;
            $input['coa_kredit'] = $cari_coa_kredit;
            $input['via_input'] = 'transaksi';
            $input['akun'] = $prog->program;
            $input['dp'] = $prog->dp;
            $input['qty'] = 1;
            $input['ket_penerimaan'] = 'an: ' . $don->nama . ' | ' . $prog->program;
            $input['user_insert'] = Auth::user()->id;

            if ($val['pembayaran'] == 'transfer') {
                $input['id_bank'] = $val['id_bank'];
            }
            
            if ($val['buktix'] != '') {
                $folderPath = "/home/kilauindonesia/public_html/kilau/gambarUpload/";
                $image_parts = explode(";base64,", $val['buktix']);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $image_name = $val['nama_filex'];
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);

                $input['bukti2'] = $image_name ;
            }else{
                $input['bukti2'] = null;
            }
            
            if ($val['bukti'] != ''){
                $folderPath = "/home/kilauindonesia/public_html/kilau/gambarUpload/";
                $image_parts = explode(";base64,", $val['bukti']);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $image_name = $val['nama_file'];
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);

                $input['bukti'] = $image_name ;
            }else{
                $input['bukti'] = null ;
            }
            

            // dd($input);

            Transaksi::create($input);

            // dd($tgl, $tgl1);
        }

        $sumtran = 0;
        $sumtran = Transaksi::where('id_transaksi', $id_trans)->sum('jumlah');
        Transaksi::where('id_transaksi', $id_trans)->update([
            'subtot' => $sumtran,
        ]);

        \LogActivity::addToLog(Auth::user()->name . ' Menambahkan Data Transaksi');
        return response()->json(['success' => 'Data Added successfully.']);
        // dd($request->arr);
    }
    
    public function notifya(){
        $id = Auth::user()->id;
        $array = [];
        $alay = DB::table('pengeluaran')->selectRaw("id, pengeluaran.tgl, coa_kredit, jenis_transaksi, keterangan, '0' as debit, nominal as kredit, '0' as jumlah, user_input, user_approve, 'pengeluaran' as one, 'kosong' as two")->whereRaw("acc = 0 AND user_input = '$id' AND notif = '1' ");
        $transaksi = DB::table('transaksi')->join('coa','coa.coa','=','transaksi.coa_debet')->selectRaw("transaksi.id,transaksi.tanggal, coa_debet, coa.nama_coa, transaksi.ket_penerimaan, transaksi.jumlah as debit, '0' as kredit, '0' as jumlah, user_insert, user_approve, 'kosong' as one, 'transaksi' as two")->unionAll($alay)->whereRaw("approval = 0 AND user_insert = '$id' AND notif ='1'")->get();
        foreach($transaksi as $t){
            $array[] = [
                'id' => $t->id,
                'nama_coa' => $t->nama_coa,
                'user_insert' => User::where('id', $t->user_insert)->first()->name ,
                'user_approve' => $t->user_approve == null ? null :  User::where('id', $t->user_approve)->first()->name,
                'tanggal' => $t->tanggal,
                'one' => $t->one,
                'two' => $t->two, 
            ];
        }
        $data['itung'] = count($transaksi);
        $data['data'] = $array;
        return $data;
    }
    
    public function getPengTransBy(Request $request, $id){
        
        $alay = DB::table('pengeluaran')->selectRaw("bukti, note, acc, nominal, tgl, user_input, user_approve, keterangan, jenis_transaksi")->where('id', $id);
        $data = Transaksi::selectRaw("bukti, keterangan as note, approval, jumlah, tanggal, user_insert, user_approve, ket_penerimaan, akun")->unionAll($alay)->where('id', $id)->first();
        $data->user_insert = User::where('id', $data->user_insert)->first()->name;
        $data->user_approve = $data->user_approve == null ? null : User::where('id', $data->user_approve)->first()->name;
        
        return $data;
    }
    
    public function changenotif($id){
        
        // return($id);
        
        if(Transaksi::find($id)){
            $ha = Transaksi::find($id)->update(['notif' => '0']);
        }else if(Pengeluaran::find($id)){
            $ha = Pengeluaran::find($id)->update(['notif' => '0']);
        }
        
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function getbbjudul(Request $request, $id)
    {
        $response = Http::get('https://berbagibahagia.org/api/detailcamp/'.$id);
        return $response;
    }
    
    public function edit($id)
 {
             $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
             if(Auth::user()->level == 'admin'){
             $donat = Donatur::orderBy('nama', 'ASC')->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $donat = Donatur::whereRaw("id_kantor = $k")->orderBy('nama', 'ASC')->get();
            }else{
                $donat = Donatur::whereRaw("(id_kantor = $k OR id = $kan->id)")->orderBy('nama', 'ASC')->get();
            }
        }
        $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();

        $transaksicari = Transaksi::leftjoin('bank', 'bank.id_bank', '=', 'transaksi.id_bank')->select('transaksi.*','bank.nama_bank')->findOrFail($id);
         $petugas = User::join('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();
        $data1 = Prog::where('parent', 'n')->get();
        
        $coa_parent= COA::where('grup', 'like', '%5%')->orderBy('coa', 'ASC')->get();
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,
            ];
        }
            
            // dd($transaksicari);

            return view('transaksi.edit',compact('transaksicari','petugas','data1','donat','bank'));
       
    }

 public function edittransaksi(Request $request)
   {
    //  return($request);
        $transaksi = Transaksi::where('id', $request->id)->first();
        $create_at = Carbon::parse($transaksi->created_at)->format('Y-m-d');
        $akses = Auth::user()->keuangan;
        $don = Donatur::where('id',  $request->eddon)->first();
        $tambahan = Tambahan::select('id_coa')->where('id',$request->id_kantor)->first();
        $bank = Bank::select('id_coa')->where('id_bank',$request->ed_bank)->first();
        $user = User::where('id', $request->id_koleks)->first();
        $props = Prosp::where('id_prog', $request->edidprg)->where('id_don',$request->eddon)->first();
        $prog = Prog::where('id_program',$request->edidprg)->first();

        // $cek = $props->id_prog = $transaksi->id_program && $props->id_don = $transaksi->id_donatur ;
        $id_trans = $don->id . date('dmY', strtotime($request->tanggal)) . $user->id;
       if( $request->edpbyr == 'noncash'){
           $coanon_cash = $request->ednon_cash ;
       }else if ($request->edpbyr == 'transfer' ){
            //  foreach ($bank as  $val) {
            //     $banknya[] = [
            //         "id_coa" => $val->id_coa,

            //     ];
            // }
           $coanon_cash = $bank->id_coa ;
       } else {
            //  foreach ($tambahan as  $val) {
            //     $tambahannya[] = [
            //         "id_coa" => $val->id_coa,

            //     ];
            // }
         $coanon_cash = $tambahan->id_coa ;
       }
       
   

        
        if ($request->buktix64 != '') {
                $folderPath = "/home/kilauindonesia/public_html/kilau/gambarUpload/";
                $image_parts = explode(";base64,", $request->buktix64);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $image_name =  $request->namafilex;
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);

               $bukti2 = $image_name ;
            }else{
                 $bukti2 =  $request->bukti2 ;
            }
            
            if ($request->bukti64 != ''){
                $folderPath = "/home/kilauindonesia/public_html/kilau/gambarUpload/";
                $image_parts = explode(";base64,", $request->bukti64);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $image_name = $request->namafile;
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);

                $bukti = $image_name ;
            }else{
                $bukti = $request->bukti ;
            }
        
              $data = new RekapT;
                // $data->id = $request->id;
                $data->akun = $request->akun;
                $data->alamat = $request->alamat;
                $data->approval = $request->approval;
                $data->bukti = $request->bukti;
                $data->bukti2 = $request->bukti2;
                $data->coa_debet = $request->coa_debet;
                $data->coa_kredit  = $request->coa_kredit;
                $data->donatur = $request->donatur;
                $data->id_bank = $request->id_bank == "null" ? NULL :  $request->id_bank; 
                $data->id_camp = $request->id_camp == "null" ? NULL :  $request->id_camp;
                $data->id_donatur = $request->id_donatur;
                $data->id_kantor = $request->id_kantor;
                $data->id_koleks = $request->id_koleks;
                $data->id_program = $request->id_program;
                $data->id_pros  = $request->id_pros == "null" ? NULL :  $request->id_pros;
                $data->id_sumdan = $request->id_sumdan;
                $data->id_transaksi = $request->id_transaksi;
                $data->jumlah = $request->jumlah;
                $data->kantor_induk = $request->kantor_induk == "null" ? NULL :  $request->kantor_induk;
                $data->ket_penerimaan = $request->ket_penerimaan;
                $data->keterangan = $request->keterangan == "null" ? NULL :  $request->keterangan;
                $data->kolektor = $request->kolektor;
                $data->kota = $request->kota;
                // $data->name  = $request->name;
                $data->via_input  = $request->via_input;
                $data->notif = $request->notif == "null" ? NULL :  $request->notif; 
                $data->pembayaran = $request->pembayaran;
                $data->program = $request->program;
                $data->qty = $request->qty;
                $data->status = $request->status;
                $data->subprogram = $request->subprogram;
                $data->subtot = $request->subtot;
                $data->tanggal = $request->tanggal;
                $data->user_approve  = $request->user_approve;
                $data->user_insert = $request->user_insert;
                $data->user_update  = $request->user_update;
             $data->save();
        

            
                $keyMapping = [
                    'eddon' => 'id_donatur',
                    'edpetugas' =>'id_koleks',
                    'namped' => 'kolektor',
                    'edpbyr' => 'pembayaran',
                    'edket' => 'ket_penerimaan',
                    'edprog' => 'subprogram',
                    'edidprg'=>'id_program',
                    'namdon'=>'donatur',
                    'ednon_cash'=>'coa_debet',
                    'ed_bank' =>'id_bank',
                    'namafile'=>'bukti',
                    'namafilex'=>'bukti2',
                    'ednom' => 'jumlah',
                ];
      
                $perbedaan = [];
                foreach ($keyMapping as $kunciRequest => $kunciCari) {
                    $nilaiRequest = $request->all()[$kunciRequest];
                    $nilaiCari = $transaksi[$kunciCari];
                
                    if ($nilaiRequest != $nilaiCari && $nilaiRequest !== null ) {
                        $perbedaan[$kunciRequest] = [
                            'lama' => $nilaiCari,
                            'baru' => $nilaiRequest,
                        ];
                    }
                }
            $perbedaan = array_filter($perbedaan);
            $perbedaanString = '';
                foreach ($perbedaan as $kunci => $nilai) {
                    $perbedaanString .= "$kunci: Lama = {$nilai['lama']}, Baru = {$nilai['baru']}\n";
                }
        
            $perbedaanString = rtrim($perbedaanString);
        
  
        if($transaksi->id_koleks != $request->edpetugas){
            if($props != null) {
            Transaksi::where('id', $request->id)->update([
                'jumlah' => preg_replace("/[^0-9]/", "", $request->ednom),
                'id_transaksi' => $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas,
                'tanggal' => $request->tanggal,
                'pembayaran' => $request->edpbyr,
                'keterangan' => $request->edket == "null" ? NULL :  $request->edket,
                'id_koleks' => $request->edpetugas,
                'kolektor' => $request->namped,
                'id_program' => $request->edidprg,
                'subprogram' => $request->edprog,
                'id_donatur' => $request->eddon,
                'donatur' => $request->namdon,
                'dp' => $prog->dp,
                'id_sumdan' =>$prog->id_sumber_dana == '' ? null : $prog->id_sumber_dana,
                'akun' => $request->edprog,
                'id_pros' => $props->id,
                //   $data->coa_debet = $request->coa_debet;
                'coa_debet' => $coanon_cash ,
                'id_bank' =>$request->edpbyr != 'transfer' ? NULL : $request->ed_bank ,
                'bukti' => $bukti,
                'bukti2' => $bukti2,
                'ket_penerimaan'=>'an:' . $don->nama . ' | '  .$request->edprog,
                'coa_kredit' => $don->jenis_donatur == "personal" ? $request->edcoa_kreditindi :  $request->edcoa_kreditenti,
                'id_kantor' => $don->id_kantor,
            ]);    
        
            }else{ Transaksi::where('id', $request->id)->update([
                'jumlah' => preg_replace("/[^0-9]/", "", $request->ednom),
                'id_transaksi' => $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas,
                'tanggal' => $request->tanggal,
                'pembayaran' => $request->edpbyr,
                'keterangan' => $request->edket == "null" ? NULL :  $request->edket,
                'id_koleks' => $request->edpetugas,
                'kolektor' => $request->namped,
                'id_program' => $request->edidprg,
                'subprogram' => $request->edprog,
                'id_donatur' => $request->eddon,
                'donatur' => $request->namdon,
                'dp' => $prog->dp,
                'id_sumdan' =>$prog->id_sumber_dana == '' ? null : $prog->id_sumber_dana,
                'akun' => $request->edprog,
                'id_pros' => 0,
                'coa_debet' => $coanon_cash ,
                'id_bank' =>$request->edpbyr != 'transfer' ? NULL :$request->ed_bank ,
                'bukti' => $bukti,
                'bukti2' => $bukti2,
                'ket_penerimaan'=>'an:' . $don->nama . ' | '  .$request->edprog,
                'coa_kredit' => $don->jenis_donatur == "personal" ? $request->edcoa_kreditindi :  $request->edcoa_kreditenti,
                'id_kantor' => $don->id_kantor,
            ]);     
            }
             
        }else if ($transaksi->id_koleks == $request->edpetugas){
             if($props != null) {
                Transaksi::where('id', $request->id)->update([
                'jumlah' => preg_replace("/[^0-9]/", "", $request->ednom),
                'id_transaksi' => $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas,
                'tanggal' => $request->tanggal,
                'pembayaran' => $request->edpbyr,
                'keterangan' => $request->edket == "null" ? NULL :  $request->edket,
                'id_koleks' => $request->edpetugas,
                'kolektor' => $request->namped,
                'id_program' => $request->edidprg,
                'subprogram' => $request->edprog,
                'id_donatur' => $request->eddon,
                'donatur' => $request->namdon,
                'dp' => $prog->dp,
                'id_sumdan' =>$prog->id_sumber_dana == '' ? null : $prog->id_sumber_dana,
                'akun' => $request->edprog,
                'id_pros' => $props->id == NULL ? 0 : $props->id,
                'coa_debet' => $coanon_cash ,
                'id_bank' =>$request->edpbyr != 'transfer' ? NULL :$request->ed_bank ,
                'bukti' => $bukti,
                'bukti2' => $bukti2,
                'ket_penerimaan'=>'an:' . $don->nama . ' | '  .$request->edprog,
                'coa_kredit' => $don->jenis_donatur == "personal" ? $request->edcoa_kreditindi :  $request->edcoa_kreditenti,
                'id_kantor' => $don->id_kantor,
                  ]); 
                  
             }else{
                Transaksi::where('id', $request->id)->update([
                'jumlah' => preg_replace("/[^0-9]/", "", $request->ednom),
                'id_transaksi' => $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas,
                'tanggal' => $request->tanggal,
                'pembayaran' => $request->edpbyr,
                'keterangan' => $request->edket == "null" ? NULL :  $request->edket,
                'id_koleks' => $request->edpetugas,
                'kolektor' => $request->namped,
                'id_program' => $request->edidprg,
                'subprogram' => $request->edprog,
                'id_donatur' => $request->eddon,
                'donatur' => $request->namdon,
                'dp' => $prog->dp,
                'id_sumdan' => $prog->id_sumber_dana == null ? NULL : $prog->id_sumber_dana,
                'akun' => $request->edprog,
                'id_pros' => 0,
                'coa_debet' => $coanon_cash ,
                'id_bank' =>$request->edpbyr != 'transfer' ? NULL :$request->ed_bank ,
                'bukti' => $bukti,
                'bukti2' => $bukti2,
                'ket_penerimaan'=>'an:' . $don->nama . ' | '  .$request->edprog,
                'coa_kredit' => $don->jenis_donatur == "personal" ? $request->edcoa_kreditindi :  $request->edcoa_kreditenti,
                'id_kantor' => $don->id_kantor,
            ]);    
             }
        
        }
        $sumtran1 = 0;
        $sumtran1 = Transaksi::where('id_transaksi', $transaksi->id_transaksi)->where('approval', '>', 0)->sum('jumlah');
        Transaksi::where('id_transaksi', $transaksi->id_transaksi)->where('approval', '>', 0)->update([
            'subtot' => $sumtran1,
             ]);
        Donatur::where('id', $transaksi->id_donatur )->update([
            'setoran' => $sumtran1,
             ]);
        
        if($transaksi->id_transaksi != $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas){     
            $sumtran = 0;
            $sumtran = Transaksi::where('id_transaksi', $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas)->where('approval', '>', 0)->sum('jumlah');
            Transaksi::where('id_transaksi', $request->eddon . date('dmY', strtotime($request->tanggal)) .$request->edpetugas)->where('approval', '>', 0)->update([
                'subtot' => $sumtran,
                 ]);
            Donatur::where('id', $request->eddon )->update([
                'setoran' => $sumtran,
             ]);
        }
        
        // \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Transaksi , dengan id'. $request->id,$perbedaanString,'transaksi','update',' $request->id');
        \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Transaksi , dengan id'. $request->id,$perbedaanString,'transaksi','update',$request->id);


           return response()->json(['success' => 'Data is successfully added']);
     
          
       
    
       
    
   }
    
            public function get_nm1(Request $request)
    {
        $q = $request->search;
        $data = Donatur::where(function ($query) use ($q) {
            $query->where('nama', 'LIKE', '%' . $q . '%')
                ->orWhere('no_hp', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%'.$q.'%');
        })->get();
        if (count($data) > 0) {
            //  $list = array();
            foreach ($data as $key => $val) {
                $list[] = [
                    "text" => $val->nama.' - '.$val->alamat ,
                    "no_hp" => $val->no_hp,
                    "kota" => $val->kota,
                    "email" => $val->email,
                    "alamat" => $val->alamat,
                    "nama" => $val->nama,
                    "id" => $val->id,

                ];
            }
            return response()->json($list);

            // return json_encode($list);
        } else {
            return "hasil kosong";
        }
    }
    
        public function getcoanoncash(Request $request){
        $q = $request->search;
        $data = COA::where('grup', 'like', '%5%')->where(function ($query) use ($q) {
            $query->where('coa', 'LIKE', '%' . $q . '%')
                ->orWhere('nama_coa', 'LIKE', '%' . $q . '%');
                // ->orWhere('email', 'LIKE', '%'.$q.'%');
        })->orderBy('coa', 'ASC')->get();
        if (count($data) > 0) {
            //  $list = array();
            foreach ($data as $key => $val) {
                $list[] = [
                "text" => $val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa,

                ];
            }
            return response()->json($list);

            // return json_encode($list);
        } else {
            return "hasil kosong";
        }
     
    }
    
    public function detail(Request $request, $id)
    {
        $response = Transaksi::select('transaksi.*','jabatan.jabatan','users.id_jabatan')->where('id_transaksi',$id)->join('users','users.id','=','transaksi.id_koleks')->join('jabatan','jabatan.id','=','users.id_jabatan')->first();
        $tabel = Transaksi::where('id_transaksi',$id)->get();
        
        return view('transaksi.detail', compact('response','tabel'));
    }
    
    public function transaksi_rutin(Request $request)
    {
        if ($request->ajax()) {
            
            $tahun = $request->bulan == '' ? Carbon::now()->format('Y') : $request->bulan;
            
            $prog = $request->prog == '' ? "id_program IS NOT NULL" : "id_program = '$request->prog'";
            
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kntr = Auth::user()->id_kantor;
            
            $cari = $request->cari;
            
            if($request->tab == 'tab1'){
                
                $transaksi = Transaksi::selectRaw("id_donatur, donatur, '$tahun' as t,
                                SUM(IF(YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi', transaksi.jumlah, 0 )) AS jumlah,
                                SUM(IF(MONTH(transaksi.tanggal) = '1' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah1,
                                SUM(IF(MONTH(transaksi.tanggal) = '2' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah2,
                                SUM(IF(MONTH(transaksi.tanggal) = '3' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah3,
                                SUM(IF(MONTH(transaksi.tanggal) = '4' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah4,
                                SUM(IF(MONTH(transaksi.tanggal) = '5' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah5,
                                SUM(IF(MONTH(transaksi.tanggal) = '6' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah6,
                                SUM(IF(MONTH(transaksi.tanggal) = '7' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah7,
                                SUM(IF(MONTH(transaksi.tanggal) = '8' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah8,
                                SUM(IF(MONTH(transaksi.tanggal) = '9' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah9,
                                SUM(IF(MONTH(transaksi.tanggal) = '10' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah10,
                                SUM(IF(MONTH(transaksi.tanggal) = '11' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah11,
                                SUM(IF(MONTH(transaksi.tanggal) = '12' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah12")
                                
                            ->whereRaw("via_input = 'transaksi' AND YEAR(tanggal) = '$tahun' AND jumlah > 0  AND $prog")
                            ->groupBy('id_donatur', 'donatur')
                            ->where(function($query) use ($request) {
                                if(isset($request->kota)){
                                    $query->whereIn('transaksi.id_kantor', $request->kota);
                                }
                            })
                            
                            ->where(function($query) use ($request) {
                                if(isset($request->bln)){
                                    $query->whereRaw('MONTH(transaksi.tanggal) IN (' . implode(',', $request->bln) . ')');
                                }
                            })
                            
                            ->where(function($query) use ($request, $cari) {
                                if(isset($request->cari) && $request->cari != ''){
                                    $query->where('donatur','LIKE','%'.$cari.'%');
                                }
                            })
                            
                            ->where(function ($query) use ($k, $kntr) {
                                if(Auth::user()->kolekting == 'admin'){
                                    $query->whereRaw("transaksi.id_kantor IS NOT NULL");
                                }else if(Auth::user()->kolekting == 'kacab'){
                                    if($k == null){
                                        $query->whereRaw("transaksi.id_kantor = '$kntr'");
                                    }else{
                                        $query->whereRaw("transaksi.id_kantor = '$kntr'")
                                                ->orWhereRaw("(transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')");
                                    }
                                }
                            });
                
                $ha = $transaksi->get();
            
                $ajn = [];
                $tot = 0;
                $jan = 0;
                $feb = 0;
                $mar = 0;
                $apr = 0;
                $mei = 0;
                $jun = 0;
                $jul = 0;
                $ags = 0;
                $sep = 0;
                $okt = 0;
                $nov = 0;
                $des = 0;
                
                
                foreach($ha as $t){
                    $tot += $t->jumlah;
                    $jan += $t->jumlah1;
                    $feb += $t->jumlah2;
                    $mar += $t->jumlah3;
                    $apr += $t->jumlah4;
                    $mei += $t->jumlah5;
                    $jun += $t->jumlah6;
                    $jul += $t->jumlah7;
                    $ags += $t->jumlah8;
                    $sep += $t->jumlah9;
                    $okt += $t->jumlah10;
                    $nov += $t->jumlah11;
                    $des += $t->jumlah12;
                }
                
                $ajn = [
                    'tot' => $tot,
                    'tot1' => $jan,
                    'tot2' => $feb,
                    'tot3' => $mar,
                    'tot4' => $apr,
                    'tot5' => $mei,
                    'tot6' => $jun,
                    'tot7' => $jul,
                    'tot8' => $ags,
                    'tot9' => $sep,
                    'tot10' => $okt,
                    'tot11' => $nov,
                    'tot12' => $des   
                ];
                    
                return $ajn;
            }
            
            $transaksi = Transaksi::selectRaw("id_donatur, donatur, '$tahun' as t,
                            SUM(IF(YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi', transaksi.jumlah, 0 )) AS jumlah,
                            SUM(IF(MONTH(transaksi.tanggal) = '1' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah1,
                            SUM(IF(MONTH(transaksi.tanggal) = '2' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah2,
                            SUM(IF(MONTH(transaksi.tanggal) = '3' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah3,
                            SUM(IF(MONTH(transaksi.tanggal) = '4' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah4,
                            SUM(IF(MONTH(transaksi.tanggal) = '5' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah5,
                            SUM(IF(MONTH(transaksi.tanggal) = '6' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah6,
                            SUM(IF(MONTH(transaksi.tanggal) = '7' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah7,
                            SUM(IF(MONTH(transaksi.tanggal) = '8' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah8,
                            SUM(IF(MONTH(transaksi.tanggal) = '9' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah9,
                            SUM(IF(MONTH(transaksi.tanggal) = '10' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah10,
                            SUM(IF(MONTH(transaksi.tanggal) = '11' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah11,
                            SUM(IF(MONTH(transaksi.tanggal) = '12' AND YEAR(transaksi.tanggal) = '$tahun' AND via_input = 'transaksi' AND $prog, transaksi.jumlah, 0 )) AS jumlah12")
                            ->whereRaw("via_input = 'transaksi' AND YEAR(tanggal) = '$tahun' AND jumlah > 0  AND $prog"); 
            
            $transaksi->groupBy('id_donatur', 'donatur')
                        ->where(function($query) use ($request) {
                            if(isset($request->kota)){
                                $query->whereIn('transaksi.id_kantor', $request->kota);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->bln)){
                                $query->whereRaw('MONTH(transaksi.tanggal) IN (' . implode(',', $request->bln) . ')');
                            }
                        })
                        
                        ->where(function ($query) use ($k, $kntr) {
                            if(Auth::user()->kolekting == 'admin'){
                                $query->whereRaw("transaksi.id_kantor IS NOT NULL");
                            }else if(Auth::user()->kolekting == 'kacab'){
                                if($k == null){
                                    $query->whereRaw("transaksi.id_kantor = '$kntr'");
                                }else{
                                    $query->whereRaw("transaksi.id_kantor = '$kntr'")
                                            ->orWhereRaw("(transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')");
                                }
                            }
                        });
            
            return DataTables::of($transaksi)
            ->addIndexColumn()
            ->make(true);
        }
        
        return view('transaksi.transaksi_rutin');
    }
    
    public function transaksi_rutin_ekspor(Request $request){
        
        $tahun = $request->bulan == '' ? Carbon::now()->format('Y') : $request->bulan;
        $jdl = 'tahun-'.$tahun;
        
        if(isset($request->bln)){
            $kotay = $request->bln;
            $dd = [];
            foreach($kotay as $aaa){
                $dd[] = $aaa;
            }
            $kotit = 'bulan-'.strtolower(implode('-',$dd));
        }else{
            $kotit = 'semua-bulan';
        }
        
        if(isset($request->kota)){
            $kota = $request->kota;
            $aa = Kantor::select('unit')->whereIn('id', $kota)->get();
            $bb = [];
            foreach($aa as $aaa){
                $bb[] = $aaa->unit;
            }
            $koti = 'unit-'.strtolower(implode('-',$bb));
            
        }else{
            $koti = 'semua-unit';
        }
        
        if($request->tombol == 'xls'){
            $response =  Excel::download(new RutinExport( $request ), 'transaksi-rutin-'.$koti.'-'.$kotit.'-'.$jdl.'.xlsx');
        }else{
            $response =  Excel::download(new RutinExport( $request ), 'transaksi-rutin-'.$koti.'-'.$kotit.'-'.$jdl.'.csv');
        }
        ob_end_clean();
        
        return $response;
    }
    
    public function transaksi_rutin_detail (Request $request)
    {
        $tahun = $request->bulan == '' ? Carbon::now()->format('Y') : $request->bulan;
        $prog = $request->prog == '' ? "transaksi.id_program != 'dfdbfbd'" : "transaksi.id_program = '$request->prog'";
        $id = explode("-", $request->id);
        
        $data = Transaksi::selectRaw("kolektor, donatur, id_transaksi, jumlah, pembayaran, status, tanggal, prog.program")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->whereRaw("MONTH(tanggal) = '$id[0]' AND YEAR(tanggal) = '$id[2]' AND id_donatur = '$id[1]'  AND $prog AND jumlah > 0 ")
                            ->where(function($query) use ($request) {
                                if(isset($request->kota)){
                                    $query->whereIn('transaksi.id_kantor', $request->kota);
                                }
                            })
                            ->get();
        
        return $data;
    }
    
         public function buktisetor_zakat(Request $request)
    {
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
            $petugas = User::where('aktif', 1)->where('id_com', Auth::user()->id_com)->get();
            $progbsz = ProgBSZ::where('aktif','1')->get();
            $kntr = $request->kantor == '' ? "transaksi.id_kantor IS NOT NULL" : "transaksi.id_kantor = '$request->kantor'";
            $tahun = date('Y');
            $bulan = date('m');
            $thn = $request->thn == '' ? "YEAR(transaksi.tanggal) =  '$tahun'" : "YEAR(transaksi.tanggal) = '$request->thn'";
            $bln = $request->bln == '' ? "MONTH(transaksi.tanggal) =  '$bulan'" : "MONTH(transaksi.tanggal) = '$request->bln'";
            $progs = Prog::whereNotNull('id_bsz')->get();
            // $jenis = $request->jenis_zakat == '' ? "IS NOT NULL" :  '$request->jenis_zakat';
            $jenis = $request->jenis_zakat;
            
        if($request->ajax()){
            if($jenis == '1'){
            $data = Transaksi::selectRaw("transaksi.*,donatur.penghasilan,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(jumlah) as jumlah ")
            ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
            ->whereRaw("$kntr AND $thn AND $bln")
            ->whereIn('id_program',$progs->pluck('id_program'))->groupBy('id_donatur')->get();
            }else{
            $data = Transaksi::selectRaw("transaksi.*,donatur.penghasilan,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(jumlah) as jumlah ")
            ->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')
            ->whereRaw("$kntr AND $thn AND $bln")
            ->groupBy('id_donatur')->get();
            
            }
            return DataTables::of($data)
            
            ->make(true);
        }
        return view('transaksi.bukti_setor_zakat', compact('kantor','petugas','progbsz')); 
    }
    
    
        public function buktiBy(Request $request, $id){
        $data['ui'] = Transaksi::whereRaw("id_donatur = '$id'")->first();
        return $data;
    }
    
        public function eksbukti(Request $request){
            
    
        $tahun = date('Y');
        $bulan = date('m');
        $thn = $request->thn == '' ? "YEAR(tanggal) =  '$tahun'" : "YEAR(tanggal) = '$request->thn'";
        $bln = $request->bln == '' ? "MONTH(tanggal) =  '$bulan'" : "MONTH(tanggal) = '$request->bln'";
        $jenis = $request->status;
        $ttdz = \App\Models\Profile::first();
        $don = Donatur::whereRaw("id = '$request->id'")->first();
        $petugas = Donatur::whereRaw("id = '$don->id_koleks'")->first();
        $progs = Prog::whereNotNull('id_bsz')->get();
        $id_karyawan = Tunjangan::first();
        if ($id_karyawan->diterima_bsz != '') {
                $ttd = Karyawan::selectRaw("id_karyawan,nama,ttd")->where('id_karyawan', $id_karyawan->diterima_bsz)->first();
            } else {
        $ttd = null; 
        }
        dd($ttd); 
       if($request->per == 'bulan' && $jenis == '0'){
        $data = Transaksi::selectRaw("transaksi.*,DATE_FORMAT(tanggal,'%Y') as tahun ")->whereRaw("id_donatur = '$request->id' AND $thn AND $bln AND transaksi.jumlah > 0")->get();
        $tot = Transaksi::whereRaw("id_donatur = '$request->id' AND $thn AND $bln AND transaksi.jumlah > 0")->select(DB::raw('SUM(jumlah) as total'))->first();
        $pdf = PDF::loadView('eksportbukti', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'ttd' => $ttd]);
        return $pdf->stream('Bukti Setor Data.pdf');
        
       }else if($request->per == 'tahun' && $jenis == '0'){
            $data = Transaksi::selectRaw("transaksi.*,DATE_FORMAT(tanggal,'%Y') as tahun,SUM(transaksi.jumlah)as jumlah ")
            ->whereRaw("id_donatur = '$request->id' AND $thn AND transaksi.jumlah > 0")->get();
            $tot = Transaksi::whereRaw("id_donatur = '$request->id' AND $thn  AND transaksi.jumlah > 0")->select(DB::raw('SUM(jumlah) as total'))->first();
            $pdf = PDF::loadView('eksportbukti', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Data.pdf');
            
    
       }else if($request->per == 'tahun' && $jenis == '1'){
          
                $data = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                           ->whereRaw("$thn AND transaksi.id_donatur = '$request->id' AND jumlah > 0");
                })
                ->selectRaw("prog_bsz.nama, transaksi.id_program, DATE_FORMAT(transaksi.tanggal, '%Y') as tahun, SUM(transaksi.jumlah) as jumlah")
                ->groupBy('transaksi.id_program', 'tahun', 'prog_bsz.nama')
                ->get();
          
            $tot = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                           ->whereRaw("$thn AND transaksi.id_donatur = '$request->id' AND transaksi.jumlah > 0");
                })->select(DB::raw('SUM(transaksi.jumlah) as total'))->first();
            $bsz = ProgBSZ::get();
            $pdf = PDF::loadView('eksportzakat', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'bsz'=>$bsz,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Zakat.pdf');
       }else if($request->per == 'bulan' && $jenis == '1'){
           
                $data = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn,$bln) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                         ->whereRaw("$thn AND $bln AND transaksi.id_donatur = '$request->id' AND jumlah > 0 ");
                })
                ->selectRaw("transaksi.id_donatur,prog_bsz.nama, transaksi.id_program, DATE_FORMAT(transaksi.tanggal, '%m') as bulan, 
                DATE_FORMAT(transaksi.tanggal, '%Y') as tahun, SUM(transaksi.jumlah) as jumlah")
                ->groupBy('transaksi.id_program')
                ->get();
                
            $tot = Prog::whereNotNull('id_bsz')
                ->leftjoin('prog_bsz', 'prog_bsz.id', '=', 'prog.id_bsz')
                ->leftjoin('transaksi', function ($join) use ($request, $thn,$bln) {
                    $join->on('transaksi.id_program', '=', 'prog.id_program')
                         ->whereRaw("$thn AND $bln AND transaksi.id_donatur = '$request->id' AND transaksi.jumlah > 0 ");
                })
                ->select(DB::raw('SUM(transaksi.jumlah) as total'))->first();
            $bsz = ProgBSZ::get();
            $pdf = PDF::loadView('eksportzakat', ['data' => $data,'ttdz' => $ttdz,'don'=>$don,'tot'=>$tot,'bsz'=>$bsz,'ttd' => $ttd]);
            return $pdf->stream('Bukti Setor Zakat.pdf');
       }
    }
    
}
