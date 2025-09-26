<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Transaksi;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\User;
use App\Models\Program;
use App\Models\SaldoAw;
use App\Models\COA;
use App\Models\Bank;
use App\Models\Penerimaan;
use App\Models\Pengeluaran;
use App\Models\HapusPengeluaran;
use App\Models\HapusTransaksi;
use App\Models\Anggaran;
use App\Models\Jabatan;
use Auth;
use DB;
use Excel;
use Carbon\Carbon;
use DataTables;
use Intervention\Image\Facades\Image;
use App\Exports\PengeluaranExport;


class PengeluaranController extends Controller
{
    public function index (Request $request, Donatur $donatur)
    {
        
        $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
        $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        $bank = Bank::all();
   
        $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
        $via = $request->via != '' ? "via_input = '$request->via'": "via_input IS NOT NULL";
        $stts = $request->stts != '' ? "acc = '$request->stts'": "acc IS NOT NULL";
        $kntr = $request->kntr != '' ? "kantor = '$request->kntr'": "kantor IS NOT NULL";
        
        if($request->ajax()){
            
            $pilihan = $request->pembayaran;
            $pembayaran = function ($query) use ($pilihan) {
                if($pilihan != '' && !empty($pilihan)){
                    if (in_array('mutasi', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'mutasi');
                    }
                    if (in_array('noncash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'noncash');
                    }
                    if (in_array('bank', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'transfer')
                              ->orWhere('pengeluaran.pembayaran', 'bank');
                    }
                    if (in_array('cash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'dijemput')
                              ->orWhere('pengeluaran.pembayaran', 'teller')
                              ->orWhere('pengeluaran.pembayaran', 'cash');
                    }
                }
            };
            
            
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $filt = $request->filt;
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE(tgl) >= '$now' AND DATE(tgl) <= '$now'";
            $bln = "MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'";
            
            if(Auth::user()->keuangan == 'admin' ||  Auth::user()->keuangan == 'keuangan pusat' ){
                if($filt == 'p'){
                   $data = Pengeluaran::whereRaw("$via AND $tgls AND $kntr AND $stts")->where($pembayaran);
                }else{
                    $data = Pengeluaran::whereRaw("$via AND $bln AND $kntr AND $stts")->where($pembayaran);
                }
            }else{
                if($cek == null){
                    if($filt == 'p'){
                        $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$kz' AND $stts")->where($pembayaran);
                    }else{
                        $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$kz' AND $stts")->where($pembayaran);
                    }
                }else{
                    if($request->kntr != ''){
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$request->kntr' AND $stts")->where($pembayaran);
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$request->kntr' AND $stts")->where($pembayaran);
                        }
                    }else{
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran);
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran);
                        }
                    }
                }
            }
            
            if($request->tab == 'tab1'){
                    
                    $cari = $request->cari;
                    
                    $aha = $data->where(function($q) use ($cari){
                        if(isset($cari)){
                            $q->where('jenis_transaksi', 'LIKE', '%' . $cari . '%')->orWhere('keterangan', 'LIKE', '%' . $cari . '%');
                        }
                    });
                    
                    $sip = $aha->get();
                    $ini = [];
                    
                    $sum = 0;
                    
                    foreach($sip as $s){
                        $sum += $s->nominal;
                    }
                    
                    $ini = [
                        'sum' => $sum,
                        'itung' => $data
                    ];
                    
                    return $ini;
                }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('donatur', function($data){
                if($data->referensi == null) {
                    $ttr = '';
                }else{
                    $ttr = $data->referensi;
                }
                return $ttr;
            })
            
            ->addColumn('kantorr', function($data){
                $trr = Kantor::select('unit')->where('id', $data->kantor)->first();
                return $trr->unit;
            })
            
            ->addColumn('user_i', function($data){
                $ppp = User::select('name')->where('id', $data->user_input)->first();
                if($ppp != null) {
                    $ttr = $ppp->name;
                }else{
                    $ttr = '';
                }
                return $ttr;
            })
            
            ->addColumn('user_a', function($data){
                $ppp = User::select('name')->where('id', $data->user_approve)->first();
                if($ppp != null) {
                    $ttr = $ppp->name;
                }else{
                    $ttr = '';
                }
                return $ttr;
            })
            
            ->addColumn('apr', function($data){
                if($data->acc == 1){
                            $button = '<label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                        }elseif($data->acc == 0){
                            $button = '<label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-ban"></i></label>';
                        }else{
                            $button = '<label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-stream"></i></label>';
                        }
                
                return $button;
            })
            
            ->addColumn('jml', function($data){
                $jml = number_format($data->nominal, 0, ',', '.');
                return $jml;
            })
            
            ->addColumn('tgll', function($data){
                
                $ttr = date('Y-m-d',strtotime($data->tgl));
                
                return $ttr;
            })
            
            ->addColumn('hapus', function($data){
                
                $button = '<div class="btn-group">';
                $button .= '<button class="btn btn-danger btn-sm btn-rounded" id="hapus" data-toggle="tooltip" data-placement="top" title="Hapus" style="margin-left: 5%"><i class="fa fa-trash"></i></button></div>';
                
                return $button;
            })
            
            ->rawColumns(['kantorr','user_a', 'user_i','donatur','apr', 'tgll','jml','hapus'])
            ->make(true);
        }
        return view('fins.pengeluaran', compact('kantor','bank','jabat','saldo')); 
    }
    
     public function index1 (Request $request, Donatur $donatur)
    {
        
        $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
        $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        $bank = Bank::all();
   
        $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
        $via = $request->via != '' ? "via_input = '$request->via'": "via_input IS NOT NULL";
        $stts = $request->stts != '' ? "acc = '$request->stts'": "acc IS NOT NULL";
        $kntr = $request->kntr != '' ? "kantor = '$request->kntr'": "kantor IS NOT NULL";
        
        if($request->ajax()){
            
            $pilihan = $request->pembayaran;
            $pembayaran = function ($query) use ($pilihan) {
                if($pilihan != '' && !empty($pilihan)){
                    if (in_array('mutasi', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'mutasi');
                    }
                    if (in_array('noncash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'noncash');
                    }
                    if (in_array('bank', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'transfer')
                              ->orWhere('pengeluaran.pembayaran', 'bank');
                    }
                    if (in_array('cash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'dijemput')
                              ->orWhere('pengeluaran.pembayaran', 'teller')
                              ->orWhere('pengeluaran.pembayaran', 'cash');
                    }
                }
            };
            
            
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $filt = $request->filt;
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE(tgl) >= '$now' AND DATE(tgl) <= '$now'";
            $bln = "MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'";
            
            if(Auth::user()->keuangan == 'admin' ||  Auth::user()->keuangan == 'keuangan pusat' ){
                if($filt == 'p'){
                   $data = Pengeluaran::whereRaw("$via AND $tgls AND $kntr AND $stts")->where($pembayaran);
                }else{
                    $data = Pengeluaran::whereRaw("$via AND $bln AND $kntr AND $stts")->where($pembayaran);
                }
            }else{
                if($cek == null){
                    if($filt == 'p'){
                        $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$kz' AND $stts")->where($pembayaran);
                    }else{
                        $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$kz' AND $stts")->where($pembayaran);
                    }
                }else{
                    if($request->kntr != ''){
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$request->kntr' AND $stts")->where($pembayaran);
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$request->kntr' AND $stts")->where($pembayaran);
                        }
                    }else{
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran);
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran);
                        }
                    }
                }
            }
            
            if($request->tab == 'tab1'){
                    
                    $cari = $request->cari;
                    
                    $aha = $data->where(function($q) use ($cari){
                        if(isset($cari)){
                            $q->where('jenis_transaksi', 'LIKE', '%' . $cari . '%')->orWhere('keterangan', 'LIKE', '%' . $cari . '%');
                        }
                    });
                    
                    $sip = $aha->get();
                    $ini = [];
                    
                    $sum = 0;
                    
                    foreach($sip as $s){
                        $sum += $s->nominal;
                    }
                    
                    $ini = [
                        'sum' => $sum,
                        'itung' => $data
                    ];
                    
                    return $ini;
                }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('donatur', function($data){
                if($data->referensi == null) {
                    $ttr = '';
                }else{
                    $ttr = $data->referensi;
                }
                return $ttr;
            })
            
            ->addColumn('kantorr', function($data){
                $trr = Kantor::select('unit')->where('id', $data->kantor)->first();
                return $trr->unit;
            })
            
            ->addColumn('user_i', function($data){
                $ppp = User::select('name')->where('id', $data->user_input)->first();
                if($ppp != null) {
                    $ttr = $ppp->name;
                }else{
                    $ttr = '';
                }
                return $ttr;
            })
            
            ->addColumn('user_a', function($data){
                $ppp = User::select('name')->where('id', $data->user_approve)->first();
                if($ppp != null) {
                    $ttr = $ppp->name;
                }else{
                    $ttr = '';
                }
                return $ttr;
            })
            
            ->addColumn('apr', function($data){
                if($data->acc == 1){
                            $button = '<label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                        }elseif($data->acc == 0){
                            $button = '<label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-ban"></i></label>';
                        }else{
                            $button = '<label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-stream"></i></label>';
                        }
                
                return $button;
            })
            
            ->addColumn('jml', function($data){
                $jml = number_format($data->nominal, 0, ',', '.');
                return $jml;
            })
            
            ->addColumn('tgll', function($data){
                
                $ttr = date('Y-m-d',strtotime($data->tgl));
                
                return $ttr;
            })
            
            ->addColumn('hapus', function($data){
                
                $button = '<div class="btn-group">';
                $button .= '<button class="btn btn-danger btn-sm btn-rounded" id="hapus" data-toggle="tooltip" data-placement="top" title="Hapus" style="margin-left: 5%"><i class="fa fa-trash"></i></button></div>';
                
                return $button;
            })
            
            ->rawColumns(['kantorr','user_a', 'user_i','donatur','apr', 'tgll','jml','hapus'])
            ->make(true);
        }
        return view('fins.pengeluaran1', compact('kantor','bank','jabat','saldo')); 
    }
    
    public function Pexport(Request $request)
    {
        $now = date('Y-m-d');
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
            
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
        $filt = $request->tt;
            
        if($filt == 'p'){
            if($request->daterange != ''){
                $jdl = '_periode_'.$dari.'_sd_'.$sampai;
                $txt = 'Periode '.$dari.' s.d '.$sampai;
            }else{
                $jdl = '_periode_'.$now.'_sd_'.$now;
                $txt = 'Periode '.$now.' s.d '.$now;
            }
        }else{
            $jdl = '_bulan_'.$bulan.'_tahun_'.$tahun;
            $txt = 'Bulan '.$bulan.' Tahun '.$tahun;
        }
        
        if($request->tombol == 'xsl'){
            $response = Excel::download(new PengeluaranExport($request, $jdl), 'pengeluaran_'.$jdl.'.xlsx');
            ob_end_clean();
        }else{
            $response = Excel::download(new PengeluaranExport($request, $jdl), 'pengeluaran_'.$jdl.'.csv');
            ob_end_clean();
        }
        return $response;
    }
   
    //yang lama
    // public function post_pengeluaran(Request $request){
    
    //         foreach($request->arr as $val){
                
    //             if($val['pembayaran'] == 'bank'){
    //                 $p = Bank::where('id_coa',$val['bank'])->first();
    //                 $kredit = $p->id_coa;
    //             }else if($val['pembayaran'] == 'noncash'){
    //                 $kredit = $val['non_cash'];
    //             }else {
    //                 $p = Kantor::where('id',$val['id_kantor'])->first();
    //                 $kredit = $p->id_coa;
    //             }
                 
    //             $bank = $val['bank'];
    //             $noncash = $val['non_cash'];
    //             $coa = $val['coa'];
    //             $anggaran= $val['id_anggaran'];
    //             $id_kantor = $val['id_kantor'];
    //             $jenis_trans = $val['jenis_trans'];
    //             $nominal = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
    //             $qty = $val['qty'];
    //             $tgl= $val['tgl'] == '' ? date('Y-m-d H:i:s') : $val['tgl'].' '.date('H:i:s');
                
    //             $format = date('ymdHis',strtotime($tgl));
    //             $keterangan= $val['keterangan'];
    //             $pembayaran = $val['pembayaran'];
    //             $depart = $val['jabatan'] == '' ? NULL : $val['jabatan'];
    //             $saldo_dana= $val['saldo'];
    //             $resi = '3'.$format.''.$id_kantor.''.Auth::user()->id;
    //             // $hps = $val['hps'];
                
    //             $input = $request->all();
    //             $input['coa_debet'] = $val['coa'];
    //             $input['coa_kredit'] = $kredit;
    //             $input['id_anggaran'] = $anggaran;
    //             $input['jenis_transaksi'] = $jenis_trans;
    //             $input['qty'] = $qty;
    //             $input['nominal'] = $nominal;
    //             $input['pembayaran'] = $pembayaran;
    //             $input['bank'] = $bank;
    //             $input['keterangan'] = $keterangan;
    //             $input['kantor'] = $id_kantor;
    //             $input['tgl'] = $tgl;
    //             $input['non_cash'] = $noncash;
    //             $input['department'] = $depart;
    //             $input['no_resi'] = $resi;
    //             $input['saldo_dana'] = $saldo_dana;
    //             // $input['user_input'] = Auth::user()->name;
    //             $input['acc'] = 2;
    //             $input['user_input'] = Auth::user()->id;
    //             $input['via_input'] = 'pengeluaran';
    //             // $input['hapus_token'] = $hps;
                
    //             if (!empty($val['foto'])) {
    //                 $folderPath = "/home/kilauindonesia/public_html/kilau/bukti/";
    //                 $image_parts = explode(";base64,", $val['foto']);
    //                 $image_type_aux = explode("image/", $image_parts[0]);
    //                 $image_type = $image_type_aux[1];
    //                 $image_base64 = base64_decode($image_parts[1]);
    //                 $image_name = $val['namafile'];
    //                 $file = $folderPath . $image_name;
    //                 file_put_contents($file, $image_base64);
                    
    //                 $input['bukti'] = $image_name;
    //             }
                
    //             Pengeluaran::create($input);
    //         }
        
   
        
    //     return response()->json(['success' => 'Data is successfully added']);
    // }
    
    public function post_pengeluaran(Request $request){
    
            foreach($request->arr as $val){
                
                if($val['pembayaran'] == 'bank'){
                    $p = Bank::where('id_coa',$val['bank'])->first();
                    $kredit = $p->id_coa;
                    $bank = $p->id_bank;
                }else if($val['pembayaran'] == 'noncash'){
                    $kredit = $val['non_cash'];
                    $bank = null;
                }else {
                    $p = Kantor::where('id',$val['id_kantor'])->first();
                    $kredit = $p->id_coa;
                    $bank = null;
                }
                 
                
                $noncash = $val['non_cash'];
                $coa = $val['coa'];
                $anggaran= $val['id_anggaran'];
                $nominal= $val['nominal'];
                
                $id_kantor = $val['id_kantor'];
                $jenis_trans = $val['jenis_trans'];
                $nominal = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
                $qty = $val['qty'];
                $tgl= $val['tgl'] == '' ? date('Y-m-d H:i:s') : $val['tgl'].' '.date('H:i:s');
                
                $format = date('ymdHis',strtotime($tgl));
                $keterangan= $val['keterangan'];
                $pembayaran = $val['pembayaran'];
                $depart = $val['jabatan'] == '' ? null : $val['jabatan'];
                $saldo_dana= $val['saldo'];
                $resi = '3'.$format.''.$id_kantor.''.Auth::user()->id;
                // $hps = $val['hps'];
                
                $input = $request->all();
                $input['coa_debet'] = $val['coa'];
                $input['coa_kredit'] = $kredit;
                $input['id_anggaran'] = $anggaran;
                $input['jenis_transaksi'] = $jenis_trans;
                $input['qty'] = $qty;
                $input['nominal'] = $nominal;
                $input['pembayaran'] = $pembayaran;
                $input['bank'] = $bank;
                $input['keterangan'] = $keterangan;
                $input['kantor'] = $id_kantor;
                $input['tgl'] = $tgl;
                $input['non_cash'] = $noncash;
                $input['department'] = $depart;
                $input['no_resi'] = $resi;
                $input['saldo_dana'] = $saldo_dana;
                // $input['user_input'] = Auth::user()->name;
                $input['acc'] = 2;
                $input['user_input'] = Auth::user()->id;
                $input['via_input'] = 'pengeluaran';
                // $input['hapus_token'] = $hps;
                
                        
                // if(!empty($val['foto'])){
                //     $data->bukti = $request->file('bukti')->getClientOriginalName();
                //     $request->file('bukti')->move('gambarUpload',$data->bukti);
                // }
                
                
                if (!empty($val['foto'])) {
                    $folderPath = "/home/kilauindonesia/public_html/kilau/bukti/";
                    $image_parts = explode(";base64,", $val['foto']);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $image_name = $val['namafile'];
                    $file = $folderPath . $image_name;
                    file_put_contents($file, $image_base64);
                    
                    $input['bukti'] = $image_name;
                }
                
                if (!empty($val['foto2'])) {
                    $folderPath = "/home/kilauindonesia/public_html/kilau/bukti/";
                    $image_parts = explode(";base64,", $val['foto2']);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1]; 
                    $image_base64 = base64_decode($image_parts[1]);
                    $image_name = $val['namafile2'];
                    $file = $folderPath . $image_name;
                    file_put_contents($file, $image_base64);
                    
                    $input['bukti_kegiatan'] = $image_name;
                } 
                
                if (!empty($val['foto3'])) {
                    $folderPath = "/home/kilauindonesia/public_html/kilau/bukti/";
                    $image_parts = explode(";base64,", $val['foto3']);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $image_name = $val['namafile3'];
                    $file = $folderPath . $image_name;
                    file_put_contents($file, $image_base64);
                    
                    $input['berita_acara'] = $image_name;
                }
                
                
            // $z = Anggaran::findOrFail($anggaran)->select('anggaran.uang_pengeluaran')->get();
            //   if($z = 0 ){
            //           Anggaran::where('id_anggaran',$anggaran)->update([
            //               'uang_pengeluaran' => $nominal
            //               ]);
            //     }else{
            //           Anggaran::where('id_anggaran',$anggaran)
            //               ->increment('uang_pengeluaran', $nominal);
                       
            //     }
                
                Pengeluaran::create($input);
                
               

            }
    
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function get_saldo_pengirim(Request $request){
        
        // $b = date('m');
        // $t = date('Y');
        
        // $cari_coa = Bank::where('id_coa',$request->coa)->get();
        // $cari_kota = Kantor::where('id_coa',$request->coa);
        
        // $saldo = COA::where('coa',$request->coa)->selectRaw("SUM(konak) as jumlah")->get();
        // $transaksi = Transaksi::where('coa_debet',$request->coa)->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->selectRaw("SUM(jumlah) as jumlah")->get();
        // $pengeluaran = Pengeluaran::where('coa_kredit',$request->coa)->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->selectRaw("SUM(nominal) as jumlah")->get();
        
        // $ngitung = ($saldo[0]->jumlah + $transaksi[0]->jumlah) - $pengeluaran[0]->jumlah;
        
        $d = date('d');
        $b = date('m');
        $t = date('Y');
        $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $bulan = date('m', strtotime($waktu));
        $tahun = date('Y', strtotime($waktu));
        
        $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $sampai1 = date('Y-m-d');
        
        $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->coa)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ")->get();
        $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval >= 1 AND coa_debet = '$request->coa'")->get();
        $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc >= 1 AND coa_kredit = '$request->coa'")->get();
        
        $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$request->coa'")->get();
        $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->coa'")->get();
        
        if(date('Y-m-01') == date('Y-m-d')){
            $saldo_awal = $saldo[0]->saldo; //saldo awal 361
        }else{
            $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal; //saldo awal 365
        }
        
        $wow = $saldo_awal + $trans1[0]->jumlah - $peng1[0]->nominal;
        
        
        // $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal;
        
        // $wow = $saldo_awal + $trans[0]->jumlah - $peng[0]->nominal;
        
        
        return $wow;
    }
    
    public function get_saldo_penerima(Request $request){
        // $b = date('m');
        // $t = date('Y');
        
        // $cari_coa = Bank::where('id_coa',$request->coa)->get();
        // $cari_kota = Kantor::where('id_coa',$request->coa);
        
        // $saldo = COA::where('coa',$request->coa)->selectRaw("SUM(konak) as jumlah")->get();
        // $transaksi = Transaksi::where('coa_debet',$request->coa)->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->selectRaw("SUM(jumlah) as jumlah")->get();
        // $pengeluaran = Pengeluaran::where('coa_kredit',$request->coa)->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->selectRaw("SUM(nominal) as jumlah")->get();
        
        // $ngitung = ($saldo[0]->jumlah + $transaksi[0]->jumlah) - $pengeluaran[0]->jumlah;
        
        // return $ngitung;
        
        // $d = date('d');
        // $b = date('m');
        // $t = date('Y');
        // $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-t'))));
        // $bulan = date('m', strtotime($waktu));
        // $tahun = date('Y', strtotime($waktu));
        
        // $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        // $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        
        // $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->coa)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ")->get();
        // $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval >= 1 AND coa_debet = '$request->coa'")->get();
        // $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc >= 1 AND coa_kredit = '$request->coa'")->get();
        
        // $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal;
        
        // $wow = $saldo_awal + $trans[0]->jumlah - $peng[0]->nominal;
        
        
        // return $wow;
        
        $d = date('d');
        $b = date('m');
        $t = date('Y');
        $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $bulan = date('m', strtotime($waktu));
        $tahun = date('Y', strtotime($waktu));
        
        $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $sampai1 = date('Y-m-d');
        
        $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->coa)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ")->get();
        $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval >= 1 AND coa_debet = '$request->coa'")->get();
        $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc >= 1 AND coa_kredit = '$request->coa'")->get();
        
        $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$request->coa'")->get();
        $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->coa'")->get();
        
        if(date('Y-m-01') == date('Y-m-d')){
            $saldo_awal = $saldo[0]->saldo; //saldo awal 361
        }else{
            $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal; //saldo awal 365
        }
        
        $wow = $saldo_awal + $trans1[0]->jumlah - $peng1[0]->nominal;
        
        
        return $wow;
    }
    
    public function get_saldo_pengeluaran(Request $request){
        // $b = date('m') ;
        // $t = date('Y');
        
        // if($request->via === 'cash') {
        //     $saldo = COA::selectRaw("SUM(konak) as jumlah")->where('id_kantor', $request->kantor)->where('grup', 3)->get();
        //     $cari = COA::selectRaw("coa")->where('id_kantor', $request->kantor)->where('grup', 3)->get();
        //     $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->whereIn('coa_kredit', $cari)->get();
        //     $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->whereIn('coa_debet', $cari)->get();
        // }else if($request->via === 'bank'){
        //         $saldo = COA::selectRaw("SUM(konak) as jumlah")->where('coa', $request->bank)->get();
        //         $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' AND coa_kredit = '$request->bank'")->get();
        //         $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND coa_debet = '$request->bank'")->get();
        // }else if($request->via === 'noncash'){
        //     $saldo = Transaksi::where('id_kantor',$request->kantor)
        //         ->select(\DB::raw("0 as jumlah"))
        //         ->get();
        //         $p = Pengeluaran::selectRaw("0 as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->get();
        //         $t = Transaksi::selectRaw("0 as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->get();
        // }
        
        
        // $wow = 0;
        
        // $wow = $saldo[0]->jumlah + $t[0]->jumlah - $p[0]->saldo;
        
        // return $wow;
        
        $d = date('d');
        $b = date('m');
        $t = date('Y');
        $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $bulan = date('m', strtotime($waktu));
        $tahun = date('Y', strtotime($waktu));
        
        $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $sampai1 = date('Y-m-d');
        
        // return([$dari, $sampai]);
        
        if($request->via === 'cash') {
            
            $cari = COA::selectRaw("coa, nama_coa")->where('id_kantor', $request->kantor)->where('grup', 3)->first();
            
            $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $cari->coa)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ")->get();
            $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$cari->coa'")->get();
            $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$cari->coa'")->get();
            
            $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$cari->coa'")->get();
            $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$cari->coa'")->get();
            
            // $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->whereIn('coa_kredit', $cari)->get();
            // $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->whereIn('coa_debet', $cari)->get();
        }else if($request->via === 'bank'){
                // $saldo = COA::selectRaw("SUM(konak) as jumlah")->where('coa', $request->bank)->get();
                $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->bank)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
                // $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' AND coa_kredit = '$request->bank'")->get();
                // $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND coa_debet = '$request->bank'")->get();
                
                $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$request->bank'")->get();
                $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$request->bank'")->get();
                
                $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$request->bank'")->get();
                $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->bank'")->get();
        }else if($request->via === 'noncash'){
            
            $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->noncash)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
            
            $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$request->noncash'")->get();
            $peng = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$request->noncash'")->get();
            
            $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) =  date(now()) AND approval = 1 AND coa_debet = '$request->noncash'")->get();
            $peng1 = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->noncash'")->get();
        }
        
        
        // $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal;
        if(date('Y-m-01') == date('Y-m-d')){
            $saldo_awal = $saldo[0]->saldo; //saldo awal 361
        }else{
            $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal; //saldo awal 365
        }
        
        $wow = $saldo_awal + $trans1[0]->jumlah - $peng1[0]->nominal;
        
        return [$saldo_awal, $trans1, $peng1, $wow];
    }
    
    public function get_saldox_pengeluaran(Request $request){
        $d = date('d');
        $b = date('m');
        $t = date('Y');
        $waktu = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-01'))));
        $bulan = date('m', strtotime($waktu));
        $tahun = date('Y', strtotime($waktu));
        
        $dari = date('Y-m-01', strtotime(date('Y-m-d')));
        $sampai = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $sampai1 = date('Y-m-d');
        
        // return([$dari, $sampai]);
        
        if($request->via === 'cash') {
            
            $cari = COA::selectRaw("coa, nama_coa")->where('id_kantor', $request->kantor)->where('grup', 3)->first();
            
            $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $cari->coa)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' ")->get();
            
            $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$cari->coa'")->get();
            $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$cari->coa'")->get();
            
            $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$cari->coa'")->get();
            $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$cari->coa'")->get();
            
            // $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t'")->whereIn('coa_kredit', $cari)->get();
            // $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t'")->whereIn('coa_debet', $cari)->get();
        }else if($request->via === 'bank'){
                // $saldo = COA::selectRaw("SUM(konak) as jumlah")->where('coa', $request->bank)->get();
                $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->bank)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
                // $p = Pengeluaran::selectRaw("SUM(nominal) as saldo")->whereRaw("MONTH(tgl) = '$b' AND YEAR(tgl) = '$t' AND coa_kredit = '$request->bank'")->get();
                // $t = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$t' AND coa_debet = '$request->bank'")->get();
                
                $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$request->bank'")->get();
                $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$request->bank'")->get();
                
                $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) = date(now()) AND approval = 1 AND coa_debet = '$request->bank'")->get();
                $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->bank'")->get();
        }else if($request->via === 'noncash'){
            $saldo = SaldoAw::selectRaw("SUM(saldo_awal) as saldo")->where('coa', $request->noncash)->whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun'")->get();
            
            $trans = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$request->noncash'")->get();
            $peng = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$request->noncash'")->get();
            
            $trans1 = Transaksi::selectRaw("SUM(jumlah) as jumlah")->whereRaw("DATE(tanggal) =  date(now()) AND approval = 1 AND coa_debet = '$request->noncash'")->get();
            $peng1 = Pengeluaran::selectRaw("SUM(nominal) as nominal")->whereRaw("DATE(tgl) = date(now()) AND acc = 1 AND coa_kredit = '$request->noncash'")->get();
        }
        
        if(date('Y-m-d') == date('Y-m-01')){
            $saldo_awal = $saldo[0]->saldo;
        }else{
            $saldo_awal = $saldo[0]->saldo + $trans[0]->jumlah - $peng[0]->nominal;
        }
        
        $wow = $saldo_awal + $trans1[0]->jumlah - $peng1[0]->nominal;
        
        // if(Auth::user()->name == 'Management'){
        //     $transt = Transaksi::selectRaw("*")->whereRaw("DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND approval = 1 AND coa_debet = '$request->noncash'")->get();
        //     $pengt = Pengeluaran::selectRaw("*")->whereRaw("DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai' AND acc = 1 AND coa_kredit = '$request->noncash'")->get();
            
        //     return [$transt, $pengt];
        // }
        
        
        return $wow;
    }
    
    public function getcoapengeluaranbank(Request $request){
        
        if(Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat' ){
            $coa_parent= COA::where('parent','n')->where('id_kantor', $request->kantor)
            ->where(function($query) {
                $query->where('grup', 'like', '%4%');
            })->orderBy('coa', 'ASC')->get();
        
        }else{
            
            $coa_parent= COA::where('parent','n')->where('id_kantor',$request->kantor)
            ->where(function($query) {
                $query->where('grup', 'like', '%4%');
            })->orderBy('coa', 'ASC')->get();
        }
        
        if(count($coa_parent) >0) {
            
            foreach($coa_parent as $key => $val){
                $h1[] = [
                    "text" => $val->nama_coa,
                    "coa" => $val->coa,
                    "id" => $val->coa,
                    "parent" => $val->parent,
                    "nama_coa" => $val->nama_coa,
                ];
            }
        }else{
            $h1[] = [
                 "text" => '',
                    "coa" => '',
                    "id" => '',
                    "parent" => '',
                    "nama_coa" => '',
                ];
        }
        
        return response()->json($h1);
    }
    
    public function post_mutasi(Request $request){
            
            $bank = [];
            $pengirim = [];
            $penerima = [];
            $id_kantor  = [];
            $nominal = [];
            $qty = [];
            $tgl = [];
            $keterangan = [];
            $saldo_dana = [];
            $depart = [];
            $pembayaran = [];
            $resi = [];
            $hps = [];
            $gmbr = [];
            
            foreach($request->arr_mut as $val){
                
               
                $p = Bank::where('id_coa', $val['coa_pengirim']);
                
                // return($val['coa_kredit']);
                
                if(count($p->get()) > 0){
                    $ah = $p->first()->id_bank;
                }else{
                    $ah = NULL;
                }
                
                
                if (!empty($val['foto_mut'])) {
                    $folderPath = "/home/kilauindonesia/public_html/kilau/bukti/";
                    $image_parts = explode(";base64,", $val['foto_mut']);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $image_name = $val['nama_file_mut'];
                    $file = $folderPath . $image_name;
                    file_put_contents($file, $image_base64);
                    
                    $gmbr[] = $image_name;
                }else{
                    $gmbr[] = null;
                }
                
                $bank[] = $ah;
                $id_kantor[] = $val['id_kantor'];
                $jenis_trans_p[] = $val['penerima'];
                $jenis_trans_t[] = $val['pengirim'];
                $nominal[] = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
                $qty[] = $val['qty'];
                $tgl[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
                $keterangan[] = $val['keterangan'];
                // $pembayaran[] = $val['pembayaran'];
                $pengirim[] = $val['coa_pengirim'];
                $penerima[] = $val['coa_penerima'];
                
                // return(COA::where('nama_coa', $val['penerima'])->first()->id_kantor);
                
                $kntr_p[] = COA::where('coa', $val['coa_penerima'])->first()->id_kantor;
                
                $baru = $val['tgl'] == '' ? date('Y-m-d H:i:s') : $val['tgl'].' '.date('H:i:s');
                
                $format = date('ymdHis', strtotime($baru));
                $resi[] = '7'.''.$format.''.$val['id_kantor'].''.Auth::user()->id;
                 
                $hps[] = $val['hps'];
            }
        
        
        if(Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->keuangan == 'admin' ){
               for($i = 0; $i< count($request->arr_mut); $i++){
                    $data = new Pengeluaran;
                    $data->coa_debet = $penerima[$i];
                    $data->coa_kredit = $pengirim[$i];
                    $data->jenis_transaksi = $jenis_trans_p[$i];
                    $data->qty = $qty[$i];
                    $data->nominal = $nominal[$i];
                    $data->pembayaran = 'mutasi';
                    $data->bank = count($p->get()) > 0 ? $bank[$i] : null;
                    $data->keterangan = $keterangan[$i];
                    $data->kantor = $id_kantor[$i];
                    $data->tgl = $tgl[$i];
                    $data->hapus_token = $hps[$i];
                    // $data->department = $gmbr[$i];
                    // $data->saldo_dana = $saldo_dana[$i];
                    $data->bukti = $gmbr[$i];
                    $data->acc = 1;
                    $data->no_resi = $resi[$i];
                    $data->via_input = 'mutasi';
                    $data->user_input = Auth::user()->id;
                    $data->user_approve = Auth::user()->id;
                    $data->save();
                    
                    $data2 = new Transaksi;
                    $data2->coa_debet = $penerima[$i];
                    $data2->coa_kredit = $pengirim[$i];
                    $data2->akun = $jenis_trans_t[$i];
                    $data2->qty = $qty[$i];
                    $data2->jumlah = $nominal[$i];
                    $data2->pembayaran = 'mutasi';
                    $data2->hapus_token = $hps[$i];
                    $data2->id_bank = count($p->get()) > 0 ? $bank[$i] : null;
                    $data2->ket_penerimaan = $keterangan[$i];
                    $data2->id_kantor = $kntr_p[$i];
                    $data2->tanggal = $tgl[$i];
                    $data2->bukti = $gmbr[$i];
                    $data2->approval = 1;
                    $data2->id_transaksi = $resi[$i];
                    $data2->via_input = 'mutasi';
                    $data2->user_insert = Auth::user()->id;
                    $data2->user_approve = Auth::user()->id;
                    
                    $data2->save();
            }
            
            // for($i = 0; $i< count($request->arr_mut); $i++){
            //         $data = new Transaksi;
            //         $data->coa_debet = $penerima[$i];
            //         $data->coa_kredit = $pengirim[$i];
            //         $data->akun = $jenis_trans_t[$i];
            //         $data->qty = $qty[$i];
            //         $data->jumlah = $nominal[$i];
            //         $data->pembayaran = 'mutasi';
            //         $data->hapus_token = $hps[$i];
            //         $data->id_bank = count($p->get()) > 0 ? $bank[$i] : null;
            //         $data->ket_penerimaan = $keterangan[$i];
            //         $data->id_kantor = $kntr_p[$i];
            //         $data->tanggal = $tgl[$i];
            //         $data->bukti = $gmbr[$i];
            //         $data->approval = 1;
            //         $data->id_transaksi = $resi[$i];
            //         $data->via_input = 'mutasi';
            //         $data->user_insert = Auth::user()->id;
            //         $data->user_approve = Auth::user()->id;
                    
            //         $data->save();
            // }
        }else{
        for($i = 0; $i< count($request->arr_mut); $i++){
                $data = new Pengeluaran;
                $data->coa_debet = $penerima[$i];
                $data->coa_kredit = $pengirim[$i];
                $data->jenis_transaksi = $jenis_trans_p[$i];
                $data->qty = $qty[$i];
                $data->nominal = $nominal[$i];
                $data->pembayaran = 'mutasi';
                $data->bank = count($p->get()) > 0 ? $bank[$i] : null;
                $data->keterangan = $keterangan[$i];
                $data->kantor = $id_kantor[$i];
                $data->tgl = $tgl[$i];
                $data->hapus_token = $hps[$i];
                $data->bukti = $gmbr[$i];
                $data->acc = 2;
                $data->no_resi = $resi[$i];
                $data->via_input = 'mutasi';
                $data->user_input = Auth::user()->id;
                // $data->user_approve = Auth::user()->id;
                $data->save();
                
                $data2 = new Transaksi;
                $data2->coa_debet = $penerima[$i];
                $data2->coa_kredit = $pengirim[$i];
                $data2->akun = $jenis_trans_t[$i];
                $data2->qty = $qty[$i];
                $data2->jumlah = $nominal[$i];
                $data2->pembayaran = 'mutasi';
                $data2->hapus_token = $hps[$i];
                $data2->id_bank = count($p->get()) > 0 ? $bank[$i] : null;
                $data2->ket_penerimaan = $keterangan[$i];
                $data2->id_kantor = $kntr_p[$i];
                $data2->tanggal = $tgl[$i];
                $data2->bukti = $gmbr[$i];
                $data2->approval = 2;
                $data2->id_transaksi = $resi[$i];
                $data2->via_input = 'mutasi';
                $data2->user_insert = Auth::user()->id;
                // $data2->user_approve = Auth::user()->id;
                $data2->save();
        }
        // for($i = 0; $i< count($request->arr_mut); $i++){
        //         $data = new Transaksi;
        //         $data->coa_debet = $penerima[$i];
        //         $data->coa_kredit = $pengirim[$i];
        //         $data->akun = $jenis_trans_t[$i];
        //         $data->qty = $qty[$i];
        //         $data->jumlah = $nominal[$i];
        //         $data->pembayaran = 'mutasi';
        //         $data->hapus_token = $hps[$i];
        //         $data->id_bank = count($p->get()) > 0 ? $bank[$i] : null;
        //         $data->ket_penerimaan = $keterangan[$i];
        //         $data->id_kantor = $kntr_p[$i];
        //         $data->tanggal = $tgl[$i];
        //         $data->bukti = $gmbr[$i];
        //         $data->approval = 2;
        //         $data->id_transaksi = $resi[$i];
        //         $data->via_input = 'mutasi';
        //         $data->user_insert = Auth::user()->id;
        //         // $data->user_approve = Auth::user()->id;
        //         $data->save();
        // }
        }
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function pengeluaranBy(Request $request, $id){
        
        $data['ui'] = Pengeluaran::join('users','users.id', '=', 'pengeluaran.user_input')->select('users.name', 'pengeluaran.*')->where('pengeluaran.id', $id)->first();
        $data['ua'] = Pengeluaran::join('users','users.id', '=', 'pengeluaran.user_approve')->select('users.name', 'pengeluaran.*')->where('pengeluaran.id', $id)->first();
        return $data;
    }
    
    public function pengEdBy(Request $request, $id){
        $find = Pengeluaran::find($id);
        $coa = COA::where('coa', $find->coa_debet)->first();
        
        // return($find);
        
        $data['ui'] = Pengeluaran::join('users','users.id', '=', 'pengeluaran.user_input')->select('users.name', 'pengeluaran.*')->where('pengeluaran.id', $id)->first();
        $data['ua'] = Pengeluaran::join('users','users.id', '=', 'pengeluaran.user_approve')->select('users.name', 'pengeluaran.*')->where('pengeluaran.id', $id)->first();
        return $data;
    }
    
    // public function aksipeng(Request $request)
    // {
    //     $p = Pengeluaran::findOrFail($request->id);

    //     if($request->aksi == 'acc'){
    //         Pengeluaran::where('id', $request->id)->update([
    //             'acc' => 1,
    //             'user_approve' => Auth::user()->id,
    //         ]);
    //         \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data Pengeluaran ' . $p->jenis_transaksi);
            
    //     }else{
    //         Pengeluaran::where('id', $request->id)->update([
    //             'acc' => 0,
    //             'user_approve' => Auth::user()->id,
    //             'note' => $request->alasan,
    //             'notif' => 1
    //         ]);
    //         \LogActivity::addToLog(Auth::user()->name . ' Rejected Data Pengeluaran ' . $p->jenis_transaksi);
    //     }
        
    //     return response()->json(['success' => 'Data is successfully updated']);
    // }
    
    public function aksipeng(Request $request)
    {

        $p = Pengeluaran::findOrFail($request->id);
        $cek = Pengeluaran::where('id', $request->id)->first();
        $create_at = Carbon::parse($cek->created_at)->format('Y-m-d');
        $akses = Auth::user()->keuangan;
        
        $cektrans = Transaksi::where('hapus_token', $cek->hapus_token)->first();
        
        //   Transaksi::whereIn('hapus_token', $hapus_token)->update([
        //          'approval' => 1,
        //          'user_approve' => Auth::user()->id,
        //     ]);
        
        

        if($request->aksi == 'acc'){
            if( $akses == 'admin' || $akses == 'keuangan pusat' ){
            if($cek->via_input == 'mutasi' && Auth::user()->keuangan == 'keuangan pusat' || $cek->via_input == 'mutasi' &&  Auth::user()->keuangan == 'admin'){
              $cektrans = Transaksi::where('hapus_token', $cek->hapus_token)->update([
                     'approval' => 1,
                     'user_approve' => Auth::user()->id,
                ]);
                Pengeluaran::where('id', $request->id)->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            
            }
            
                Pengeluaran::where('id', $request->id)->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
              
                \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data Pengeluaran ' . $p->jenis_transaksi );
                return response()->json(['success' => 'Data is successfully updated']);
                
            }else if ($cek->tgl == $create_at && $akses == 'kacab' || $cek->tgl == $create_at && $akses == 'keuangan cabang' ){
                
            if($cek->via_input == 'mutasi'){
              $cektrans = Transaksi::where('hapus_token', $cek->hapus_token)->update([
                     'approval' => 1,
                     'user_approve' => Auth::user()->id,
                ]);
                Pengeluaran::where('id', $request->id)->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            
            }
                
                    Pengeluaran::where('id', $request->id)->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
              
                \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data Pengeluaran ' . $p->jenis_transaksi );
                return response()->json(['success' => 'Data is successfully updated']);
                
            }else if($cek->tgl != $create_at && $akses != 'admin' ){
                return response()->json(['gagal' => 'Data is Failed updated']);
                }
                
        }else{
            Pengeluaran::where('id', $request->id)->update([
                'acc' => 0,
                'user_approve' => Auth::user()->id,
                'note' => $request->alasan,
                'notif' => 1
            ]);
            
            if($request->id_anggaran == null || $request->id_anggaran == 0){
             Anggaran::where('id_anggaran',$request->id_anggaran)
                          ->decrement('uang_pengeluaran', $request->nominal);
            }
                           
            \LogActivity::addToLog(Auth::user()->name . ' Rejected Data Pengeluaran ' . $p->jenis_transaksi);
            return response()->json(['success' => 'Data is successfully updated']);
        }
        
    }
    
    
    public function editspeng(Request $request)
    {
          $cek = Pengeluaran::where('id', $request->id)->first();
          $create_at = Carbon::parse($cek->created_at)->format('Y-m-d');
          $akses = Auth::user()->keuangan;
          
     
                $keyMapping = [
                    'ket' => 'keterangan',
                    'nominal' => 'nominal',
                ];
      
                $perbedaan = [];
                foreach ($keyMapping as $kunciRequest => $kunciCari) {
                    $nilaiRequest = $request->all()[$kunciRequest];
                    $nilaiCari = $cek[$kunciCari];
                
                    if ($nilaiRequest != $nilaiCari && $nilaiRequest !== null) {
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
          
        if($cek->tgl != $create_at && $akses == 'admin' ||$cek->tgl != $create_at && $akses == 'keuangan cabang'){
               Pengeluaran::where('id', $request->id)->update([
                'keterangan' => $request->ket == null ? $cek->keterangan :$request->ket,
                'nominal' => $request->nominal === null ?$cek->nominal : $request->nominal
            ]);
            
            if($cek->via_input == 'mutasi'){
                Transaksi::where('hapus_token', $cek->hapus_token)->update([
                'ket_penerimaan' => $request->ket == null ? $cek->keterangan :$request->ket,
                'jumlah' => $request->nominal === null ?$cek->nominal : $request->nominal
            ]);
            }
            
            
        \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Pengeluaran , dengan id'. $request->id,$perbedaanString,'pengeluaran','update',$request->id);
        // \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Pengeluaran , dengan id'. $request->id,$perbedaanString,' Dari Halaman Pengeluaran');
            

        return response()->json(['success' => 'Data is successfully updated']);
        }else if ($cek->tgl == $create_at && $akses != 'admin' ||$cek->tgl == $create_at && $akses != 'keuangan pusat' ){
               Pengeluaran::where('id', $request->id)->update([
                'keterangan' => $request->ket == null ? $cek->keterangan :$request->ket,
                'nominal' => $request->nominal === null ?$cek->nominal : $request->nominal
            ]);
            
            if($cek->via_input == 'mutasi'){
                Transaksi::where('hapus_token', $cek->hapus_token)->update([
                'ket_penerimaan' => $request->ket == null ? $cek->keterangan :$request->ket,
                'jumlah' => $request->nominal === null ?$cek->nominal : $request->nominal
            ]);
            }
            
        \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Pengeluaran , dengan id'. $request->id,$perbedaanString,'pengeluaran','update',$request->id);
        
            // \LogActivity::addToLog(Auth::user()->name . ' Mengubah Data Pengeluaran, dengan id'. $request->id);
            
            return response()->json(['success' => 'Data is successfully updated']);
        }else if($cek->tgl != $create_at && $akses != 'admin' ){
           return response()->json(['gagal' => 'Data is Failed updated']);
    }
     
    }
    
    
           
    

    // public function hapus_pengeluaran(Request $request)
    // {
    //     $cari = Pengeluaran::where('id', $request->id);
        
    //     if(count($cari->get())){
    //         if($cari->first()->via_input == 'pengeluaran'){
    //             $aw = Pengeluaran::find($request->id);
    //             $input = $aw;
    //             $input['hapus_alasan'] = $request->alasan;
    //             $input['user_delete'] = Auth::user()->id;
                
    //             $data = HapusPengeluaran::create($input);
                
    //             Pengeluaran::where('id', $request->id)->delete();
    //                 \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Pengeluaran, dengan id'. $request->id);
    //             return response()->json(['success' => 'Data is successfully deleted']);
    //         }else if($cari->first()->via_input == 'mutasi'){
    //             $p = $cari->first()->hapus_token;
                
    //             $peng = Pengeluaran::find($request->id);
    //             $inpeng = $aw->first();
    //             $inpeng['hapus_alasan'] = $request->alasan;
    //             $inpeng['user_delete'] = Auth::user()->id;
    //             $data = HapusPengeluaran::create($inpeng);
                
    //             $tran = Transaksi::where('hapus_token', $p);
    //             $intran = $tran->first();
    //             $intran['hapus_alasan'] = $request->alasan;
    //             $intran['user_delete'] = Auth::user()->id;
    //             $data = HapusPengeluaran::create($intran);
                
    //             $Peng->delete();
    //             $tran->delete();
    //             \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Pengeluaran, dengan id'. $request->id);
    //             return response()->json(['success' => 'Data is successfully deleted']);
    //         }
    //     }else{
    //         return response()->json(['failed' => 'Data is unsuccessful deleted', 'code' => 500]);
    //     }
        
    // }
    
    public function acc_semua(Request $request)
    {
        // return $request;
        $via = $request->via != '' ? "pengeluaran.via_input = '$request->via'" : "pengeluaran.via_input IS NOT NULL";
        $stts = $request->status != '' ? "pengeluaran.acc = '$request->status'" : "pengeluaran.acc IS NOT NULL";
        // $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        
        if(Auth::user()->keuangan == 'admin'){
            $kntr = $request->kntr != '' ? "pengeluaran.kantor = '$request->kntr'" : "pengeluaran.kantor IS NOT NULL";
        }else{
            $ka = Auth::user()->id_kantor;
            $kntr = $request->kntr != '' ? "pengeluaran.kantor = '$request->kntr'" : "pengeluaran.kantor '$ka'";
        }
        
        // return $ka;
        // $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }
        
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        $now = date('Y-m-d');
        
        $tgls = $request->filt == 'p' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE_FORMAT(tgl,'%m-%Y') = '$bln'";
        
        $ketlog = $request->filt == 'p' ? 'dari tanggal '.$dari.' sampai '.$sampai : 'bulan '.$bln;
       
        $cek = Pengeluaran::whereRaw("$via AND $kntr AND $tgls AND acc = 2")->get();
      
        foreach($cek as $val){
            if($val['via_input'] == 'mutasi' && $val['hapus_token'] != null && (Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->keuangan == 'admin')){    
                Transaksi::where('hapus_token', $val['hapus_token'])->where('via_input', 'mutasi')->update([
                    'approval' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            }
            
            Pengeluaran::where('id', $val['id'])->whereRaw("pembayaran != 'noncash'")->update([
                'acc' => 1,
                'user_approve' => Auth::user()->id,
            ]);
            
        }
        
        \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengeluaran '.$ketlog );
            
        //  $cektreans =  Transaksi::whereIn('hapus_token', $hapus_token)->get();

        // $filt = $request->filt;
        
        // if($filt == 'p'){
            
        //     if($via == 'mutasi' && Auth::user()->keuangan == 'keuangan pusat' ||$via == 'mutasi' &&  Auth::user()->keuangan == 'admin'){    
        //         Transaksi::whereIn('hapus_token', $hapus_token)->update([
        //              'approval' => 1,
        //              'user_approve' => Auth::user()->id,
        //         ]);
        //          Pengeluaran::whereRaw("$via AND $kntr AND $tgls AND acc = 2")->update([
        //             'acc' => 1,
        //             'user_approve' => Auth::user()->id,
        //     ]);
            
        //     }
        //     Pengeluaran::whereRaw("$via AND $kntr AND $tgls AND acc = 2")->update([
        //             'acc' => 1,
        //             'user_approve' => Auth::user()->id,
        //     ]);
            
        //     \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengeluaran ' + 'dari tanggal' + $dari + 'sampai' + $sampai );
        // }else{
            
        //     if($via == 'mutasi' && Auth::user()->keuangan == 'keuangan pusat' ||$via == 'mutasi' && Auth::user()->keuangan == 'admin'){    
        //       Transaksi::whereIn('hapus_token', $hapus_token)->update([
        //          'approval' => 1,
        //          'user_approve' => Auth::user()->id,
        //     ]);
            
        //       Pengeluaran::whereRaw("$via AND $kntr AND $bln AND acc = 2")->update([
        //             'acc' => 1,
        //             'user_approve' => Auth::user()->id,
        //     ]);
        //     }
            
        //     Pengeluaran::whereRaw("$via AND $kntr AND $bln AND acc = 2")->update([
        //             'acc' => 1,
        //             'user_approve' => Auth::user()->id,
        //     ]);
        //     \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengeluaran ' + 'dari bulan ' + $bulan + ' tahun ' + $tahun );
        // }
      
        
        return response()->json(['success' => 'Data is successfully updated']);

    }
    
}
