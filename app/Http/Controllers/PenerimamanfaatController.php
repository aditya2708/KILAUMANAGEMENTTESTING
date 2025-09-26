<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Transaksi;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\User;
use App\Models\Prog;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Bank;
use App\Models\Asnaf;
use App\Models\Penerimaan;
use App\Models\PenerimaManfaat;
use Auth;
use DB;
use Carbon\Carbon;
use DataTables;
use App\Exports\PenerimaManfaatExport;

use Excel;
use PDF;
use App\Exports\PenerimaanExport;

class PenerimamanfaatController extends Controller
{
    public function index (Request $request, Donatur $donatur)
    {
        if(Auth::user()->level === 'admin'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            $a = Auth::user()->id_com;
            $b = Auth::user()->id_kantor;
            $kantor = Kantor::whereRaw("id_com = '$a' AND id = '$b' OR kantor_induk = '$b'  ")->get();
        }
        
        $bank = Bank::where('id_kantor',Auth::user()->id_kantor)->get();

        $approve = $request->approve;
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        if ($request->bulan != '') {
            $tgl = explode('-', $request->bulan);
            $t = date($tgl[1]);
            $b = date($tgl[0]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        $selectPriode = $request->periode;
        $searchFoot = null;
        if($request->tab == 'tab1'){
            $searchFoot = $request->cari ?? null;
        }
        $search = $request->search['value'] ?? $searchFoot;
        
        if($selectPriode == 'p'){
            $prd = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
        }else if($selectPriode == 'b'){
            $prd = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
        }
        
        $via = $request->via != '' ? $request->via : '';
        // $via = $request->via ;
        $unit = $request->kantt != '' ? "transaksi.id_kantor = $request->kantt" : "transaksi.id_kantor IS NOT NULL ";
        
        $stts = $request->status != '' ? "transaksi.approval = $request->status" : "transaksi.approval IS NOT NULL";
        $viewnya = $request->view != '' ? $request->view : '';
        
        $columnsToSearch = ['transaksi.jumlah', 'transaksi.akun', 'transaksi.ket_penerimaan', 'transaksi.qty', 'transaksi.jumlah', 'us_ap.name', 'us_in.name', 'transaksi.donatur', 'transaksi.program', 'tambahan.unit', 'transaksi.coa_debet', 'transaksi.coa_kredit'];
        
        $like = '(';
        foreach ($columnsToSearch as $column) {
            $like .= "$column LIKE ? OR ";
        }
        $like = rtrim($like, 'OR '); // Menghapus 'OR' yang berlebihan di akhir.
        
        $query = '%' . $search . '%';

        // $periode = $request->periode;
        
        if($request->ajax()){
            $pilihan = $request->pembayaran;
            $pembayaran = function ($query) use ($pilihan) {
                if($pilihan != '' && !empty($pilihan)){
                    if (in_array('mutasi', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'mutasi');
                    }
                    if (in_array('noncash', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'noncash');
                    }
                    if (in_array('bank', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'transfer')
                              ->orWhere('transaksi.pembayaran', 'bank');
                    }
                    if (in_array('cash', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'dijemput')
                              ->orWhere('transaksi.pembayaran', 'teller')
                              ->orWhere('transaksi.pembayaran', 'cash');
                    }
                }
            };
            
            // $pembayaran = function($q) use ($request) {
            //         if($request->pembayaran != '' && !empty($request->pembayaran)){ $q->whereIn('transaksi.pembayaran', $request->pembayaran); }
            // };
          
            if($request->via == ''){
                if($viewnya == 'dp'){ 
                     $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                     ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                     ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                     ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                     ->selectRaw("transaksi.*,
                    (transaksi.dp/100 * transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("$prd AND $unit AND $stts AND transaksi.jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query))
                     ->get();
                     if($request->tab == 'tab1'){
                        //  dd($request->cari);
                        $dey = 0;
                        foreach($data as $x){
                            $dey += $x->tot;
                        }
                        return $dey;
                    }
                }else{
                    $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                    ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                    ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->selectRaw("transaksi.*,
                    (transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("$prd AND $unit AND $stts  AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));
                    if($request->tab == 'tab1'){
                        $dey = 0;
                        foreach($data->get() as $x){
                            $dey += $x->tot;
                        }
                        return $dey;
                    }
                }
            }else{
                if($viewnya == 'dp'){
                   $dp = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                        ->leftjoin('coa','coa.coa','=','prog.coa1')
                        ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                         ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                         ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                        ->selectRaw("transaksi.id,transaksi.tanggal,  coa.nama_coa as akun   ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                        transaksi.user_insert,transaksi.user_approve,transaksi.donatur , (transaksi.dp/100 * transaksi.jumlah) as tot ,transaksi.qty,transaksi.ket_penerimaan,transaksi.program,prog.coa1 as coa_debet,prog.coa2 as coa_kredit,coa.parent,'n' as wow,coa.level,transaksi.id_program  ")
                      ->orderBy('transaksi.id')
                      ->where($pembayaran)
                      ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));

                    $joss = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                     ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                     ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->leftjoin('coa','coa.coa','=','prog.coa2')
                    ->selectRaw("transaksi.id,transaksi.tanggal, coa.nama_coa as akun ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                    transaksi.user_insert,transaksi.user_approve,transaksi.donatur ,(transaksi.jumlah - transaksi.dp/100 * transaksi.jumlah  ) as tot,transaksi.qty ,transaksi.ket_penerimaan,transaksi.program,prog.coa1 as coa_debet,prog.coa2 as coa_kredit,coa.parent, 'n' as wow,coa.level,transaksi.id_program ")
                    ->orderBy('transaksi.id')
                    ->where($pembayaran)
                  ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts  AND jumlah > 0");
                   
                        $normal = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                         ->leftjoin('coa','coa.coa','=','prog.coa1')
                         ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                         ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                         ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                        ->selectRaw("transaksi.id,transaksi.tanggal,  transaksi.subprogram as akun ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                        transaksi.user_insert,transaksi.user_approve,transaksi.donatur , (transaksi.jumlah) as tot ,transaksi.qty,transaksi.ket_penerimaan,transaksi.program,coa_debet,coa_kredit,coa.parent,'y' as wow,coa.level,transaksi.id_program ")
                        ->unionAll($dp)
                        ->unionAll($joss)
                        ->where($pembayaran)
                       ->orderBy('transaksi.id')
                      ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));
               
                 $data = $normal->get();
                    if($request->tab == 'tab1'){
                        $dey = 0;
                        foreach($data as $x){
                            $dey += $x->tot;
                        }
                        return $dey;
                    }
                }else{
                   $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                    ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                    ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->selectRaw("transaksi.*,
                    (transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts  AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));
                    if($request->tab == 'tab1'){
                        $dey = 0;
                        foreach($data->get() as $x){
                            $dey += $x->tot;
                        }
                        return $dey;
                    }
                }
            }

            $data = $data;
            
            
            
            foreach ($data as $datas) {
                if($request->tab == 'approveAll'){
                   $datas->update(['approval' => 1]);
                }
            }
            
            
            return DataTables::of($data)
            ->addIndexColumn()
            //paling atas 3 y
             // Penyaluran Dana Infaq/Sedekah untuk Amil 3 n
             // Bagian Amil dari Dana Infaq/Sedekah Tidak Terikat 3 n
             
            ->addColumn('akunn', function ($data) {
             
                if($data->wow == 'y' ){  
                    $c = '<b>'. $data->akun. '</b>';
                }else if($data->wow == 'n'){
                    $c = '&nbsp;&nbsp;'.$data->akun;
                }else{
                    $c = '&nbsp;'.$data->akun;
                }
            
                return $c;
            })
            ->addColumn('donaturr', function($data){
                if($data->via_input == 'penerimaan') {
                    $ttr = '';
                }else{
                    $ttr = $data->donatur;
                }
                return $ttr;
            })
            
            ->addColumn('kantorr', function($data){
                $trr = Kantor::select('unit')->where('id', $data->id_kantor)->first();
                return $trr->unit;
            })
            
            ->addColumn('user_i', function($data){
                $ppp = User::select('name')->where('id', $data->user_insert)->first();
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
                
                 if($data->approval == 1){
                            $button = '<span class="badge badge-success">Approve<span class="ms-1 fa fa-check"></span></span>';
                            // <label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                        }elseif($data->approval == 0){
                            $button = '<span class="badge badge-secondary">Reject<span class="ms-1 fa fa-ban"></span></span>';
                            // <label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-clock-o"></i></label>';
                        }else{
                            $button = '<span class="badge badge-warning">Pending<span class="ms-1 fas fa-stream"></span></span>';
                            // <label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-xmark"></i></label>';
                        }
                        return $button;
            })
            
            ->addColumn('jml', function($data) {
                // $jost = $request->view != '' ? $request->view : '';
                // return $jost;
                // if($viewnya == 'dp'){
                //       $jml = number_format($data->dp/100 * $data->jumlah, 0, ',', '.');
                //         return $jml;
                // }else{
                //       $jml = number_format($data->jumlah, 0, ',', '.');
                //         return $jml;
                // }
                        $jml = number_format($data->tot, 0, ',', '.');
                        return $jml;
                    })
                    

            
            
            

            ->rawColumns(['kantorr','user_a', 'user_i','donaturr','apr','jml','akunn'])
            ->make(true);
        }
        return view('fins.penerimaan', compact('kantor','bank')); 
    }
    
    public function ekspor(Request $request){;
        // return($request);
        
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        $darib = $request->darib != '' ? $request->darib : date('Y-m');
        $sampaib = $request->sampaib != '' ? $request->sampaib : $darib;
        $via = $request->via != '' ? $request->via : 'kosong';
        $unit = $request->kantt != '' ? "transaksi.id_kantor = $request->kantt" : "transaksi.id_kantor IS NOT NULL";
        $stts = $request->status != '' ? "approval = $request->status" : "approval IS NOT NULL";
        $viewnya = $request->view != '' ? $request->view : 'kosong';
        $periode = $request->periodenya == '' ? 'kosong' : $request->periodenya;
        
        if($request->periodenya == ''){
            $j_period =  'hari-ini-'.$dari;
            $k_period =  'Hari ini '.$dari;
        }else if($request->periodenya == 'harian'){
            $j_period =  'periode-'.$dari.'-'.$sampai;
            $k_period =  'Periode '.$dari.' - '.$sampai;
        }else if($request->periodenya == 'bulan'){
            $j_period =  'bulan-'.$darib.'-'.$sampaib;
            $k_period =  'Bulan '.$darib.' - '.$sampaib;
        }
        
        if($request->tombol == 'xls'){
            $response =  Excel::download(new PenerimaanExport($request, $j_period), 'penerimaan-'.$j_period.'.xlsx');
        }else{
            $response =  Excel::download(new PenerimaanExport($request, $j_period), 'penerimaan-'.$j_period.'.csv');
        }
        ob_end_clean();
        
        return $response;
    }
    
    public function index_pm(Request $request)
    {
        if(Auth::user()->level === 'admin'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
             $petugas = User::where('aktif', 1)->get();
        }else{
            $kantor = Kantor::where('id',Auth::user()->id_kantor)->get();
            $petugas = User::where('aktif', 1)->where('id_kantor', Auth::user()->id_kantor)->get();
        }
        
        $bank = Bank::where('id_kantor',Auth::user()->id_kantor)->get();
        $getasnaf = Asnaf::get();

        if($request->ajax()){
            $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
            $tgl_akhir = $request->sampai != '' ? $request->sampai : date('Y-m-d');
            // $jenis = $request->jenis != '' ? "penerima_manfaat.jenis_pm = $request->jenis" : "penerima_manfaat.jenis_pm IS NOT NULL";
            
            $jenis = $request->jenis != '' ? "jenis_pm = '$request->jenis'" : "jenis_pm IS NOT NULL";
            $jk = $request->jk != '' ? "penerima_manfaat.jk = '$request->jk'" : "penerima_manfaat.jk IS NOT NULL";
            $status = $request->status != '' ? "penerima_manfaat.status = '$request->status'" : "penerima_manfaat.status IS NOT NULL";
            // $jk = $request->jk;
              $kantor = $request->kantor != '' ? "kantor = '$request->kantor'" : "kantor IS NOT NULL";
            $asnaf = $request->asnaf;
            // $kantor = $request->kantor;
            $pj = $request->pj;
            $no_hp = $request->nohp;
            $data = PenerimaManfaat::leftjoin('users', 'users.id_karyawan', '=', 'penerima_manfaat.nama_pj')
            ->selectRaw("penerima_manfaat.*,users.name")
            ->whereRaw("DATE_FORMAT(tgl_reg , '%Y-%m-%d') >= '$tgl_awal' AND DATE_FORMAT(tgl_reg , '%Y-%m-%d') <= '$tgl_akhir' AND $jenis  AND $status AND $kantor")
                    ->where(function($query) use ($request, $asnaf) {
                        if(isset($request->asnaf)){
                            $query->whereIn('asnaf', $asnaf);
                        }
                    })
                    // ->where(function($query) use ($request, $kantor) {
                    //     if(isset($request->kantor)){
                    //         $query->whereIn('kantor', $kantor);
                    //     }
                    // })
                    ->where(function($query) use ($request, $pj) {
                        if(isset($request->pj)){
                            $query->whereIn('nama_pj', $pj);
                        }
                    })
                    // ->where('hp', 'like', '%$no_hp%')
                    // ->where(function($query) use ($request) {
                    //     $no_hp = $request->input('no_hp'); // Mengambil nomor HP dari permintaan
                    //     if(isset($no_hp)){
                    //         $query->where('hp', 'LIKE', '$no_hp%');
                    //     }
                    // })
                    ->where('hp', 'like',$no_hp . '%')
                    ;
            
            return DataTables::of($data)
            ->addIndexColumn()
            
            ->addColumn('asnaff', function($data){
                $ttr = Asnaf::where('id',$data->asnaf)->first()->asnaf;
                return $ttr;
            })
            
            ->addColumn('kantorr', function($data){
                $ttr = Kantor::where('id',$data->kantor)->first()->unit;
                return $ttr;
            })
            ->addColumn('st', function ($data) {
                if ($data->status == 1){
                        $c = 'checked';
                    }else{
                        $c = '';
                    } 
                    $button = 
                    '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" status="'.$data->status.'" data-id="'. $data->id . '"  data-value="'. $data->status . '" type="checkbox" '.( $data->status  == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';

                    return $button;
            })
            
            ->addColumn('hapus', function ($data) {
                $status = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Hapus"><button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></span>';
                return $status;
            })
            ->addColumn('editpm', function ($data) {
                    $slug = $data->id;
                    $link = url('edit-pm/'.$slug);
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit"><div class="btn-group">
                                        <a class="btn btn-success btn-sm"  id="' . $data->id . '" target="blank_"  href="' . url('/edit-pm/' . $data->id) . '"><i class="fa fa-edit"></i></a>
                                   </div></span>';
                    return $button;
                })
            
            
            ->rawColumns(['kantorr','asnaff','st','hapus','editpm'])
            ->make(true);
        }
        return view('penerima-manfaat.penerima_manfaat', compact('kantor','bank','getasnaf','petugas')); 
    }
    
    public function add_penerimaan(Request $request){
            
            $bank = [];
            $noncash = [];
            $coa = [];
            $id_kantor  = [];
            $jenis_trans = [];
            $nominal = [];
            $qty = [];
            $tgl = [];
            $keterangan = [];
            $pembayaran = [];
            $debet = [];
            foreach($request->arr as $val){
                
                if($val['pembayaran'] == 'bank'){
                    $p = Bank::where('id_bank',$val['bank'])->first();
                    $debet[] = $p['id_coa'];
                }else if($val['pembayaran'] == 'noncash'){
                    $debet[] = $val['non_cash'];
                }else {
                    $p = Kantor::where('id',$val['id_kantor'])->first();
                    $debet[] = $p['id_coa'];
                }
                
                $bank[] = $val['bank'];
                $noncash[] = $val['non_cash'];
                $coa[] = $val['coa'];
                $id_kantor[] = $val['id_kantor'];
                $jenis_trans[] = $val['jenis_trans'];
                $nominal[] = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
                $qty[] = $val['qty'];
                $tgl[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
                $keterangan[] = $val['keterangan'];
                $pembayaran[] = $val['pembayaran'];
                
            }
        
        for($i = 0; $i< count($request->arr); $i++){
            
                $prog = Prog::where('coa_individu',  $coa[$i]);
                if(count($prog->get()) > 0){
                    $mm = $prog->first()->dp;
                }else{
                    $mm = null;
                }
            
                $data = new Transaksi;
                $data->coa_debet = $debet[$i];
                $data->coa_kredit = $coa[$i];
                
                $data->akun = $jenis_trans[$i];
                $data->qty = $qty[$i];
                
                $data->jumlah = $nominal[$i];
                $data->pembayaran = $pembayaran[$i];
                
                $data->id_bank = $bank[$i];
                $data->ket_penerimaan = $keterangan[$i];
                $data->id_kantor = $id_kantor[$i];
                $data->tanggal = $tgl[$i];
                $data->approval = 1;
                $data->dp = $mm;
                $data->via_input = 'penerimaan';
                $data->user_insert = Auth::user()->id;
                $data->user_approve = Auth::user()->id;
                
                $data->save();
        }
       
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function add_pm(){
        if (Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang')) {
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $datdon =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->where('id_com', Auth::user()->id_com)->get();
        $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                    ->select('jabatan.jabatan', 'users.*')->where('aktif', 1)->where('users.kota', Auth::user()->kota)->where('users.id_com', Auth::user()->id_com)->get();
        $asnaf = Asnaf::where('aktif','y')->get();
        }else{
        $petugas = User::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                    ->select('jabatan.jabatan', 'users.*')->where('users.id_com', Auth::user()->id_com)->where('aktif', 1)->get();
        $datdon =  Kantor::select('unit', 'id')->get();
        $asnaf = Asnaf::where('aktif','y')->get();
        }
        return view('penerima-manfaat.add_pm', compact('petugas', 'datdon','asnaf'));
    }
    
    public function post_pm(Request $request){
            
            // $bank = [];
            // $noncash = [];
            // $coa = [];
            // $id_kantor  = [];
            // $jenis_trans = [];
            // $nominal = [];
            // $qty = [];
            // $tgl = [];
            // $keterangan = [];
            // $pembayaran = [];
            // $debet = [];
            // foreach($request->arr as $val){
                
            //     if($val['pembayaran'] == 'bank'){
            //         $p = Bank::where('id_bank',$val['bank'])->first();
            //         $debet[] = $p['id_coa'];
            //     }else if($val['pembayaran'] == 'noncash'){
            //         $debet[] = $val['non_cash'];
            //     }else {
            //         $p = Kantor::where('id',$val['id_kantor'])->first();
            //         $debet[] = $p['id_coa'];
            //     }
                
            //     $bank[] = $val['bank'];
            //     $noncash[] = $val['non_cash'];
            //     $coa[] = $val['coa'];
            //     $id_kantor[] = $val['id_kantor'];
            //     $jenis_trans[] = $val['jenis_trans'];
            //     $nominal[] = $val['nominal'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['nominal']);
            //     $qty[] = $val['qty'];
            //     $tgl[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
            //     $keterangan[] = $val['keterangan'];
            //     $pembayaran[] = $val['pembayaran'];
                
            // }
        
        // for($i = 0; $i< count($request->arr); $i++){
                $data = new PenerimaManfaat;
                $data->jenis_pm = $request->jenis;
                
                if($request->jenis == 'personal'){
                    $data->penerima_manfaat = $request->nama;
                    $data->tgl_lahir = $request->tgl_lahir;
                    $data->alamat = $request->alamat;
                    $data->hp = $request->nohp;
                    $data->nik = $request->nik;
                    $data->email = $request->email;
                    $data->jk = $request->jk;
                    $data->latitude = $request->latitude;
                    $data->longitude = $request->longitude;
                    $data->id_prov = $request->provinsi;
                    $data->kota = $request->kota;
                    
                }else{
                    $data->penerima_manfaat = $request->lembaga;
                    $data->alamat = $request->alamat;
                    $data->hp = $request->nohap;
                    $data->email = $request->email1;
                    $data->latitude = $request->latitude1;
                    $data->longitude = $request->longitude1;
                    $data->id_prov = $request->provinsii;
                    $data->kota = $request->kotaa;
                }
                
                $data->status = 1;
                $data->nama_pj = $request->pj;
                $data->asnaf = $request->asnaf;
                $data->tgl_reg = date('Y-m-d H:i:s');
                $data->kantor = $request->id_kantor;
                $data->foto_pm = $request->foto;
                
                $data->save();
        // }
       
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function get_info_pm($id){
        $data = PenerimaManfaat::where('penerima_manfaat.id', $id)
                ->join('asnaf','asnaf.id','=','penerima_manfaat.asnaf')
                ->join('tambahan','penerima_manfaat.kantor','=','tambahan.id')
                ->select('*','penerima_manfaat.alamat as alay','penerima_manfaat.id as idtot')
                ->first();
        return $data;
    }
    
    public function nama_pm(Request $request){
        // dd($request->all());
        $q = $request->search;
        $data = PenerimaManfaat::where(function($query) use ($q) {
                    $query->where('penerima_manfaat', 'LIKE', '%'.$q.'%')
                        ->orWhere('hp', 'LIKE', '%'.$q.'%');
                })->get();
        if (count($data) > 0) {
            //  $list = array();
             foreach($data as $key => $val){
                 $list[] = [
                        "text" => $val->penerima_manfaat,
                        "no_hp" => $val->hp,
                        "kota" => $val->kota,
                        "alamat" => $val->alamat,
                        "nama" => $val->penerima_manfaat,
                        "id" => $val->id,
                        
                    ];
             }
             return json_encode($list);
         } else {
             return "hasil kosong";
         }
    }
    
    public function penerimaanBy(Request $request, $id){
        $aw = Transaksi::whereRaw("id = '$id'")->first();
        $aw->user_insert = User::where('id', $aw->user_insert)->first()->name;
        $aw->user_approve = $aw->user_approve == null ? null : User::where('id', $aw->user_approve)->first()->name;
        return $aw;
    }
    
    
        public function aksipenerimaan(Request $request)
    {

        $p = Transaksi::findOrFail($request->id);
        $cek = Transaksi::where('id', $request->id)->first();
        $create_at = Carbon::parse($cek->created_at)->format('Y-m-d');
        $akses = Auth::user()->keuangan;
        
        $cektrans = Pengeluaran::where('hapus_token', $cek->hapus_token)->first();
        
        //   Transaksi::whereIn('hapus_token', $hapus_token)->update([
        //          'approval' => 1,
        //          'user_approve' => Auth::user()->id,
        //     ]);
        
        
        if($request->aksi == 'acc'){
            if($akses == 'admin' || $akses == 'keuangan pusat' ){
                
                
            if($cek->via_input == 'mutasi' && Auth::user()->keuangan == 'keuangan pusat' || $cek->via_input == 'mutasi' && Auth::user()->keuangan == 'admin'){
            
                Pengeluaran::where('hapus_token', $cek->hapus_token )->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            
              Transaksi::where('id',$request->id)->update([
                     'approval' => 1,
                     'user_approve' => Auth::user()->id,
                ]);
            
            }
            
                // Pengeluaran::where('id', $request->id)->update([
                //     'acc' => 1,
                //     'user_approve' => Auth::user()->id,
                // ]);
              
                \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data  ' . $p->id );
                return response()->json(['success' => 'Data is successfully updated']);
                
            }else if ($cek->tgl == $create_at && $akses == 'kacab' || $cek->tgl == $create_at && $akses == 'keuangan cabang' ){
                
            if($cek->via_input == 'mutasi'){
              Pengeluaran::where('hapus_token', $cek->hapus_token )->update([
                    'acc' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            
              Transaksi::where('id ',$request->id )->update([
                     'approval' => 1,
                     'user_approve' => Auth::user()->id,
                ]);
            }
                
                //     Pengeluaran::where('id', $request->id)->update([
                //     'acc' => 1,
                //     'user_approve' => Auth::user()->id,
                // ]);
              
                \LogActivity::addToLog(Auth::user()->name . ' Aprrove Data ' . $p->id );
                return response()->json(['success' => 'Data is successfully updated']);
                
            }else if($cek->tgl != $create_at && $akses != 'admin' ){
                return response()->json(['gagal' => 'Data is Failed updated']);
                }
                
        }else{
            
              Pengeluaran::where('hapus_token', $cek->hapus_token )->update([
                'approval' => 0,
                'user_approve' => Auth::user()->id,
                'notif' => 1
                ]);
            
              Transaksi::where('id ',$request->id )->update([
                'acc' => 0,
                'user_approve' => Auth::user()->id,
                'notif' => 1
                ]);
            
            \LogActivity::addToLog(Auth::user()->name . ' Rejected Data  ' . $p->id);
            return response()->json(['success' => 'Data is successfully updated']);
        }
        
    }
    
     public function acc_semua_penerimaan(Request $request)
    {

        $via = $request->via ;
        $kntr = $request->kntr;
        $stts = $request->stts;
        $filt = $request->filt;
        
        $darit = $request->tgld != '' ? $request->tgld : date('Y-m-d');
        $sampait = $request->tglk != '' ? $request->tglk : $darit;
        
        $darib = $request->blnd != '' ? $request->blnd : date('Y-m');
        $sampaib = $request->blnk != '' ? $request->blnk : $darib;      
        
        if($filt == 'bulan'){
            $cek = Transaksi::selectRaw("transaksi.*")->whereRaw("via_input = '$via' AND id_kantor = '$kntr' AND approval = 2 AND DATE_FORMAT(tanggal,'%Y-%m') >= '$darib' AND DATE_FORMAT(tanggal,'%Y-%m') <= '$sampaib' ")->get();
        }else{
            $cek = Transaksi::selectRaw("transaksi.*")->whereRaw("via_input = '$via' AND id_kantor = '$kntr' AND tanggal >= '$darit' AND tanggal <= '$sampait' AND approval = 2")->get();
        }
 
         $ketlog = $request->filt == 'bulan' ? 'dari bulan '.$darib.' sampai '.$sampaib   :'dari tanggal '.$dari.' sampai '.$sampai  ;

        foreach($cek as $val){
            if($val['via_input'] == 'mutasi' && $val['hapus_token'] != null && (Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->keuangan == 'admin')){    
                Transaksi::where('hapus_token', $val['hapus_token'])->where('via_input', 'mutasi')->update([
                    'approval' => 1,
                    'user_approve' => Auth::user()->id,
                ]);
            }
            
            Pengeluaran::where('id', $val['id'])->update([
                'acc' => 1,
                'user_approve' => Auth::user()->id,
            ]);
            
        }
        
         \LogActivity::addToLog(Auth::user()->name . ' Aprrove Semua Data Pengeluaran '.$ketlog );

        
        return response()->json(['success' => 'Data is successfully updated']);

    }
    
        public function infor_pm(Request $request,$id)
    {
        
        $asnaf = Asnaf::get();
        $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        $data = PenerimaManfaat::where('id', $request->id)->first();
        $provinces = Provinsi::get();
        
        $kota = Kota::where('province_id', $request->id_prov)->get();
           $pass = Karyawan::where('karyawan.aktif', 1)->where('karyawan.id_com', Auth::user()->id_com)
                            ->join('jabatan','jabatan.id', '=','karyawan.jabatan')->get();
                           
                                 foreach($pass as $key => $val){
                                    $h1[] = [
                                        "text" => $val->nama,
                                        "nama" => $val->nama,
                                        "id" => $val->id_karyawan,
                                        "unit_kerja" => $val->unit_kerja,
                                        "jabatan" => $val->jabatan,
                                    ];
                                }
                   
        return view('penerima-manfaat.edit_pm', compact('data','asnaf','kantor','h1','provinces','kota'));
        
    }
    
        public function editstat(Request $request)
    {
        if($request->value == 1){
            $data = PenerimaManfaat::where('id', $request->id)->update([
                'status' => 0,
                ]);;
      
        }else{
            $data = PenerimaManfaat::where('id', $request->id)->update([
                'status' => 1,
                ]);;
        }
        
            return response()->json(['success' => 'Data is successfully updated']);

    }
    
        public function hapuspm($id)
    {
        $data = PenerimaManfaat::findOrFail($id);
        $data->delete();
        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Penerima Manfaat ' . $data->penerima_manfaat);
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    
        public function get_salur($id,Request $request)
    {
       if ($request->ajax()) {
            $data = Pengeluaran::whereRaw("id_pm = '$request->id' AND via_input = 'penyaluran'");
            return DataTables::of($data)
                ->addColumn('tanggal', function ($data) {
                    if($data->tgl_salur == null){
                       $trr = 'Penyaluran Masih di Proses'; 
                    }
                    $trr = $data->tgl_salur;
                    return $trr;
                })
                ->make(true);
       }
        return view('penerima-manfaat.edit_pm');


    }
    
        public function pmexport(Request $request){
            $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
            $tgl_akhir = $request->sampai != '' ? $request->sampai : date('Y-m-d');
            
         $dari = ' Dari Tanggal ' . $tgl_awal;
         $sampai = ' Sampai ' . $tgl_akhir;
         if($request->tombol == 'xls'){
            $r = Excel::download(new PenerimaManfaatExport($request), 'Penerima Manfaat '.$dari. $sampai .'.xlsx');
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new PenerimaManfaatExport($request), 'Penerima Manfaat '.$dari. $sampai .'.csv');
            ob_end_clean();
            return $r;
        }
    }
    
     public function editpm(Request $request)
   {
        $data = PenerimaManfaat::findOrFail($request->id);
        // if($request->edjenis =='personal'){
        //     $keyMapping = [
        //         'ednama' => 'penerima_manfaat',
        //         'edpj' => 'nama_pj',
        //         'edhp' => 'hp',
        //         'edasnaf' => 'asnaf',
        //         'edttl' =>'tgl_lahir',
        //         'edkantor' => 'kantor',
        //         'ednik' => 'nik' ,
        //         'edemail' => 'email',
        //         'edjenis' =>'jenis_pm',
        //         'edjk' => 'jk',
        //         'latitude' => 'latitude',
        //         'longitude' => 'longitude',
        //         'alamat' => 'alamat',

        //     ];
        //     }else if($request->edjenislebaga == 'entitas'){
        //     $keyMapping = [
        //         'edpjlembaga' => 'penerima_manfaat',
        //         'edpjlembaga' => 'nama_pj',
        //         'edhplembaga' => 'hp',
        //         'edasnaflembaga' => 'asnaf',
        //         'emaillembaga' => 'email',
        //         'edjenislebaga' =>'jenis_pm',
        //         'latitude' => 'latitude',
        //         'longitude' => 'longitude',
        //         'alamat' => 'alamat',

        //     ];
        //     }
          
        //         $perbedaan = [];
        //         foreach ($keyMapping as $kunciRequest => $kunciCari) {
        //             $nilaiRequest = $request->all()[$kunciRequest];
        //             $nilaiCari = $data[$kunciCari];
                
        //             if ($nilaiRequest !== $nilaiCari && $nilaiRequest !== null && $nilaiCari !== $nilaiRequest ) {
        //                 $perbedaan[$kunciRequest] = [
        //                     'lama' => $nilaiCari,
        //                     'baru' => $nilaiRequest,
        //                 ];
        //             }
        //         }
        //     $perbedaan = array_filter($perbedaan);

        //     $perbedaanString = '';
        //     foreach ($perbedaan as $kunci => $nilai) {
        //         if ($nilai['lama'] != $nilai['baru']) {
        //             $perbedaanString .= "$kunci: Lama = {$nilai['lama']} , Baru = {$nilai['baru']} \n";
        //         }
        //     }
            
        //     $perbedaanString = rtrim($perbedaanString);
            
            

   
            if($request->edjenis == 'personal'){
            PenerimaManfaat::where('id', $request->id)->update([
                'penerima_manfaat' => $request->ednamas,
                'nama_pj' => $request->edpj,
                'hp' => $request->edhp,
                'asnaf' => $request->edasnaf,
                'tgl_lahir' => $request->edttl,
                'kantor' => $request->edkantor,
                'nik' =>$request->ednik ,
                'email' => $request->edemail,
                'jenis_pm' =>$request->edjenis,
                'jk' => $request->edjk ,
                'latitude' => $request->latitude ,
                'longitude' => $request->longitude ,
                'alamat' => $request->alamat ,
                
            ]); 
            }else if($request->edjenis == 'entitas'){
                  PenerimaManfaat::where('id', $request->id)->update([
                'penerima_manfaat' => $request->ednamalembaga,
                'nama_pj' => $request->edpjlembaga,
                'hp' => $request->edhplembaga,
                'asnaf' => $request->edasnaflembaga,
                'tgl_lahir' =>null,
                'nik' => null ,
                'email' => $request->emaillembaga,
                'jenis_pm' => $request->edjenislebaga,
                'jk' =>null ,
                'latitude' => $request->latitude ,
                'longitude' => $request->longitude ,
                'alamat' => $request->alamat ,
                
            ]); 
            }   
                  
            // \LogActivity::addToLoghfm(Auth::user()->name . ' Edit Data Dari Halaman Penerima Manfaat , dengan id'. $request->id,$perbedaanString,'PM','update',$request->id);
            \LogActivity::addToLog(Auth::user()->name . ' Edit Data Dari Halaman Penerima Manfaat , dengan id ' . $request->id );

           return response()->json(['success' => 'Data is successfully added']);

   }
    
    
}
