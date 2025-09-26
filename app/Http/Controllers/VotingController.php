<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use DB;
use Carbon\Carbon;
use DataTables;
use Auth;
use App\Models\Kantor;
use App\Models\Voting;
use App\Models\Jabatan;

class VotingController extends Controller
{
    public function index(Request $req){
        
        $id_com = Auth::user()->id_com;
        
        $kantor = Kantor::select('unit','id','kantor_induk')
                ->where('id_com', Auth::user()->id_com)
                ->where(function($query) {
                    if(Auth::user()->kepegawaian != 'admin'){
                        $query->where('id', Auth::user()->id_kantor)
                        ->orWhere('kantor_induk', Auth::user()->id_kantor);
                    }
                })
                ->get();
        $jabatan = Jabatan::where('id_com', $id_com)->get();
        
        if($req->ajax()){
            
            $data = Voting::all();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kantor', function ($data) {
                    $o = [];
                    $p = Kantor::select('unit')->whereIn('id', unserialize($data->id_kantor))->get();
                    if(count($p) > 0){
                        foreach($p as $x){
                            $o[] = $x->unit; 
                        }
                    }else{
                        $o = '';
                    }
                    return $o;
                }) 
                ->addColumn('jabatan', function ($data) {
                    $o = [];
                    $p = Jabatan::select('jabatan')->whereIn('id', unserialize($data->ditunjukan))->get();
                    if(count($p) > 0){
                        foreach($p as $x){
                            $o[] = $x->jabatan; 
                        }
                    }else{
                        $o = '';
                    }
                    return $o;
                }) 
                ->addColumn('vote', function ($data) {
                    // $ah = [];
                    // $o = [];
                    $p = unserialize($data->voting);
                    if(count($p) > 0){
                        $listHtml = '<ul>';
        
                        foreach ($p as $item) {
                            $listHtml .= '<li>- ' . $item . '</li>';
                        }
                        
                        $listHtml .= '</ul>';
                        
                    }
                    
                    return $listHtml;
                })
                
                ->addColumn('onoff', function($data){
                    $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class"  data-id="'. $data->id . '"  data-value="'. $data->aktif . '" type="checkbox" '.($data->aktif == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                    return $button;
                })
                
                ->addColumn('aksi', function($data){
                    $roar = '';
                    return $roar;
                })
                
                ->rawColumns(['kantor', 'jabatan', 'vote', 'aksi', 'onoff'])
                ->make(true);
        }
        
        return view('voting.index', compact('kantor','jabatan'));
    }
    
    public function post_voting(Request $req){
        $data = [
            'judul' => $req->ket,
            'voting' => serialize($req->vote),
            'tgl_awal' => $req->date1,
            'tgl_akhir' => $req->date2,
            'id_kantor' => serialize($req->kantor),
            'ditunjukan' => serialize($req->jabatan),
            'id_com' => Auth::user()->id_com,
            'aktif' => 1,
            'jumlah_voting' => serialize($req->jumvot),
        ];
        Voting::create($data);
        return response()->json(['success' => 'Berhasil tambah data']);
    }
    
    public function haha($id){
        $today = new DateTime("today");
    
        $day    = date('d'); 
        $tg     = date('Y-m-01', strtotime(date('Y-m-d')));
        // $kar = Karyawan::selectRaw("karyawan.*, IF(($day >= 20 AND karyawan.tgl_gaji >= $tg AND MONTH(karyawan.tgl_gaji) <= MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) <= YEAR(NOW())) OR (karyawan.tgl_gaji < $tg), 0, 1) AS kondisi")->where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
        $kar = Karyawan::selectRaw("karyawan.*, 
                                    IF('$day' >= 25, 
                                        IF(karyawan.tgl_gaji >= DATE(NOW()),1,0), 
                                        IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0)
                                        ) AS kondisi
                                    ")
                ->where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
        // dd($kar->nm_pasangan);
        $tot_pasangan = 0;
        if($kar->status_nikah == 'Menikah' && $kar->nm_pasangan != null){
            $istri = unserialize($kar->nm_pasangan);
            foreach($istri as $key => $value){
                $tot_pasangan += 1;
            }
        }
        
        $anak = unserialize($kar->nm_anak); 
        $tgl = unserialize($kar->tgl_lahir_anak);
        $sts = unserialize($kar->status_anak);
        
        $tot_anak = 0;
        if($kar->nm_anak != null){
        foreach($anak as $key => $value){
            $tt = new DateTime($tgl[$key]);
            if($today->diff($tt)->y <= 21 && $sts[$key] == 'Belum Menikah'){
                $tot_anak += 1;
            }
        }
        }
        $tgl_gaji = $kar->tgl_gaji;
        
        // return $tgl_gaji;
        $month = date("m",strtotime($tgl_gaji));
        $year = date("Y",strtotime($tgl_gaji));
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        $drh = Daerah::where('id_daerah', $kar->id_daerah)->first();
        
        // return $id;
        
        if($kar->jabatan == $tj->kolektor || $kar->jabatan == $tj->spv_kol || $kar->jabatan == $tj->sokotak){
            $kolek = Transaksi_Perhari::leftjoin('users', 'transaksi_perhari.id', '=', 'users.id')
                    ->select(\DB::raw("
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.honor, 0)) AS honor,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.bonus_cap, 0)) AS boncap
                        "))
                    ->where('users.id_karyawan',$id)->first();
                    
            // return $kolek;
            
            if($month == date('m') && $year == date('Y')){
                $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                    ->select(\DB::raw("
                    COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput' 
                            AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totkun,
                    COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput'
                            AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totup"))
                    ->where('users.id_karyawan',$id)->first();
            }else{
                $datkol = Rinbel::select('totkun', 'totup')
                    ->where('id_karyawan',$id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->first();
            // return($datkol);
            }  
            
                    
            if($kar->jabatan == $tj->kolektor){
                $honor = $kolek != null ? $kolek->honor : 0;
                $boncap = $kolek != null ? $kolek->boncap : 0;
                if ($kolek->Omset <= 10000000){
                    $bon = $kolek->Omset * 4/100;
                }elseif ($kolek->Omset > 10000000 && $kolek->Omset <= 20000000){
                    $bon = ($kolek->Omset - 10000000) * 5/100 + 400000; 
                }elseif ($kolek->Omset > 20000000){
                    $bon = ($kolek->Omset - 20000000) * 6/100 + 900000;
                }else{
                    $bon = 0;
                }
            // $rinbon= [
            //             'bonus' => ['Bonus Capaian Target', 'Bonus Omset'],
            //             'jumlah' => [$boncap, $bon]
            //          ];
            // return response()->json(['data', $tj]);
            if($kar->status_kerja == 'Magang'){
                $rinbon= [
                            ['bonus' => 'Omset',
                             'jumlah' => round($bon)],
                            ['bonus'=> 'Capaian Target',
                             'jumlah' => round($boncap)],
                         ];
                     
                $potongan = [
                                ['nampot' => 'Potongan',
                                 'jumlah' => 0],
                            ];
                $bonkol = $boncap + $bon;
            }else{   
                $rinbon= [
                            ['bonus' => 'Omset',
                             'jumlah' => round($bon)],
                            ['bonus'=> 'Capaian Target',
                             'jumlah' => round($boncap)],
                         ];     
                         
                $potongan = [
                                ['nampot' => 'Belum Dikunjungi',
                                 'jumlah' => is_object($datkol)  ? $datkol->totkun * $tj->potongan : 0 ] ,
                                ['nampot'=> 'Tutup 1x',
                                 'jumlah' => is_object($datkol) ? $datkol->totup * $tj->potongan : 0],
                            ];
                $bonkol = $boncap + $bon;
            }
            $totpot = is_object($datkol) ? ($datkol->totkun * $tj->potongan) + ($datkol->totup * $tj->potongan) : 0;
            
            }elseif($kar->jabatan == $tj->spv_kol){
                $kol = Transaksi::
                    select(\DB::raw("
                        SUM(IF( MONTH(created_at) = MONTH('$tgl_gaji') AND YEAR(created_at) = YEAR('$tgl_gaji') AND via_input = 'transaksi' 
                            AND id_koleks IN (SELECT id FROM users WHERE id_jabatan = $tj->kolektor AND id_spv = $id), jumlah, 0)) AS Omtim
                        "))->first();
            if($month == date('m') && $year == date('Y')){
                $datkolspv = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                    ->select(\DB::raw("
                    COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                            AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS belkun,
                    COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                            AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS beltup"))
                    // ->where('users.id_karyawan',$id)
                    ->whereIn('users.id_karyawan', function($query) use ($id) {
                        $query->select('id_karyawan')->from('users')->where('id_spv', $id);
                    })->first();
            }else{
                $datkolspv = Rinbel::select(\DB::raw("
                    SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), belkun, 0)) AS belkun,
                    SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), beltup, 0)) AS beltup"))
                    ->first();
            }
                    // dd($datkolspv);
                // $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                //     ->select(\DB::raw("
                //     COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
                //     COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup"))
                //     ->where('users.id_karyawan',$id)->first();
            // $honor = $kolek != null ? $kolek->honor : 0;
            $bontim = $kol != null ? $kol->Omtim * 1/100 : 0; 
            $bon = $kolek != null ? $kolek->Omset * 1/100 : 0;
            $rinbon= [
                        // ['bonus' => 'Omset',
                        //  'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Tim',
                         'jumlah' => round($bontim) + round($bon)],
                     ];
            $bonkol = $bontim + $bon;
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => $datkolspv->belkun * $tj->potongan],
                            ['nampot'=> 'Tutup 1x',
                             'jumlah' => ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan)],
                            ['nampot' => 'Belum Dikunjungi',
                             'jumlah' => $datkol->totkun * $tj->potongan],
                            // ['nampot'=> 'Tutup 1x',
                            //  'jumlah' => $datkol->totup * $tj->potongan],
                        ];
            // dd($bon, $bontim);
            $totpot = ($datkolspv->belkun * $tj->potongan) + ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan) + ($datkol->totkun * $tj->potongan);
            // ini bonus sales
            }elseif($kar->jabatan == $tj->sokotak){
                $tgl_awal = date('Y-m-01', strtotime('-4 month', strtotime($kar->tgl_gaji)));
        $tgl_trans = date('Y-m-01', strtotime($kar->tgl_gaji));
        $tanggal = date('Y-m-t', strtotime($kar->tgl_gaji));
        
        $idprosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                    ->select(\DB::raw("id_don"))
                    ->where(function($query) use ($tgl_trans, $tanggal) {
                        $query->where('prosp.ket', '!=', 'open')->whereDate('prosp.tgl_fol', '>=', $tgl_trans)->whereDate('prosp.tgl_fol', '<=', $tanggal)
                              ->orWhere('prosp.ket', 'open')->whereDate('prosp.updated_at', '>=', $tgl_trans)->whereDate('prosp.updated_at', '<=', $tanggal);
                    })
                    ->whereRaw("prosp.created_at IS NOT NULL")
                    ->where('users.id_karyawan',$id)
                    ->get();
        
        $id_prosdon = [];
        
        if(count($idprosdon) > 0){
            foreach($idprosdon as $x => $v){
                $id_prosdon[] = $v->id_don;
            }
        }
        // return(count($id_prosdon));            
        $prosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                    ->select(\DB::raw("
                            COUNT(DISTINCT IF(prosp.ket = 'closing' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS closing,
                            COUNT(DISTINCT IF(prosp.ket = 'open' AND prosp.created_at IS NOT NULL AND DATE(prosp.created_at) >= '$tgl_trans' AND DATE(prosp.created_at) <= '$tanggal', id_don, NULL)) AS open,
                            COUNT(DISTINCT IF(prosp.ket = 'cancel' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS cancel
                        "))
                    ->where('users.id_karyawan',$id)
                    ->first();
        // return($prosdon);            
        $prosp = Transaksi::
                join('prog', 'transaksi.id_program', '=', 'prog.id_program')->join('prosp', 'transaksi.id_pros','=', 'prosp.id')
                    ->select(\DB::raw("
                        transaksi.id_donatur, transaksi.donatur, DATE_FORMAT(transaksi.tanggal, '%Y-%m') AS bulan, 
                        transaksi.id_program, transaksi.subprogram, prosp.tgl_fol,
                        SUM(transaksi.jumlah) AS omset, transaksi.pembayaran
                        "))
                    ->whereIn('transaksi.id_pros', function($pr) use ($id, $tgl_awal) {
                    $pr->select('id')->from('prosp')->whereIn('id_peg', function($query) use ($id) {
                            $query->select('id')->from('users')->where('id_karyawan', $id);
                        })->whereDate('tgl_fol','>=', $tgl_awal)->where('ket', 'closing')->where('status', 1)->where('created_at','!=',NULL)
                        // ->where('konprog', 0)
                        ;
                    })
                    ->whereDate('transaksi.tanggal','>=', $tgl_trans)
                    ->groupBy('transaksi.id_donatur', 'transaksi.donatur', 'bulan', 'transaksi.subprogram', 'transaksi.id_program', 'prosp.tgl_fol', 'transaksi.pembayaran')
                    // ->whereMonth('transaksi.created_at', 4)->whereYear('transaksi.created_at', date('Y'))
                    ->get();
                    
        
        $totbonpo = 0;
        $tothonpo = 0;
        $totbonset = 0;
        $totpo = 0;
            $poin = 0;
            $honpo = 0;
            $bonpo = 0;
            $bonset = 0;
        $data = [];
        $total = [];
        $id_don = [];
        if(count($prosp) > 0){
            foreach($prosp as $x => $v){
                
                $bln = date_diff(date_create($tanggal), date_create($v->tgl_fol));
                $b = $bln->m;
                $p = Prog::where('id_program', $v->id_program)->first();
                $omst = $v->pembayaran == 'noncash' ? $v->omset*($p->prenoncash/100) : $v->omset;         
                $prog = $p->tes === 'b:0;' || @unserialize($p->tes) !== false ? unserialize($p->tes) : [];
                $honp = $p->honpo === 'b:0;' || @unserialize($p->honpo) !== false ? unserialize($p->honpo) : [];
                $bonp = $p->bonpo === 'b:0;' || @unserialize($p->bonpo) !== false ? unserialize($p->bonpo) : [];
                $bons = $p->bonset === 'b:0;' || @unserialize($p->bonset) !== false ? unserialize($p->bonset) : [];
                $konb = $p->konbon === 'b:0;' || @unserialize($p->konbon) !== false ? unserialize($p->konbon) : [];
                $inhonp = $p->inhonpo === 'b:0;' || @unserialize($p->inhonpo) !== false ? unserialize($p->inhonpo) : [];
                $inbonp = $p->inbonpo === 'b:0;' || @unserialize($p->inbonpo) !== false ? unserialize($p->inbonpo) : [];
                $inbons = $p->inbonset === 'b:0;' || @unserialize($p->inbonset) !== false ? unserialize($p->inbonset) : [];
                $bons2 = $p->bonset2 === 'b:0;' || @unserialize($p->bonset2) !== false ? unserialize($p->bonset2) : [];
                $minp2 = $p->minpo2 === 'b:0;' || @unserialize($p->minpo2) !== false ? unserialize($p->minpo2) : [];
                // dd($bons);
                   
                if($inhonp[$b] == 1){
                    $honpo = $omst < $p->minpo ? 0: floatval($honp[$b]);
                    $poin = $omst < $p->minpo ? 0 : 1;
                }else if($inhonp[$b] == 2){
                    $honpo = round(($omst/$p->minpo)*$honp[$b]);
                    $poin = round($omst/$p->minpo);
                }else{
                    $honpo = 0;
                    $poin = 0;
                }
                
                if($inbonp[$b] == 1){
                    $bonpo = $omst < $p->minpo ? 0 : floatval($bonp[$b]);
                }else if($inbonp[$b] == 2){
                    $bonpo = round(($omst/$p->minpo)*$bonp[$b]);
                }else{
                    $bonpo = 0;
                }
                
                if($inbons[$b] == 1){
                    $bonset = $omst < $p->minpo ? 0 : floatval($bons[$b]);
                }else if($inbons[$b] == 2){
                    $bonset = round($omst*($bons[$b]/100)); 
                }else if($inbons[$b] == 3){
                    if($omst >= $p->minpo){
                        $bonset = floatval($bons[$b]);
                    }else if($omst < $p->minpo && $omst >= floatval($minp2[$b])){
                        $bonset = floatval($bons2[$b]);
                    }else{
                        $bonset = 0;
                    }
                }else{
                    $bonset = 0;
                    // $bonset = $inbons[$b] == 1 ? round($v->omset*($bons[$b]/100)) : floatval($bons[$b]);
                }
                
                if($poin > 0 | $honpo > 0 | $bonpo > 0 | $bonset > 0){
                $id_don[] = $v->id_donatur;
                $data[] = [
                    'b' => $b,
                    'bulan' => $v->bulan,
                    'id_donatur' => $v->id_donatur,
                    'donatur' => $v->donatur,
                    'subprogram' => $v->subprogram,
                    'tgl_fol' => $v->tgl_fol,
                    'omset' => $omst,
                    'minpo' => $p->minpo,
                    'poin' => $poin,
                    'honpo' => $honpo,
                    'bonpo' => $bonpo,
                    'bonset' => $bonset,
                    'totbon' => $honpo + $bonpo + $bonset
                    // 'kolektif' => $p->kolektif,
                ];
                }
            }
        }else{
          $data = []; 
        }
        
        $tgl_gaji = $kar->tgl_gaji;
        $month = date("m",strtotime($tgl_gaji));
        $year = date("Y",strtotime($tgl_gaji));
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        if($kar->jabatan == $tj->sokotak){
            $kolek = Transaksi::leftjoin('users', 'transaksi.id_koleks', '=', 'users.id')
                    ->select(\DB::raw("transaksi.tanggal, users.id, 
                        SUM(jumlah) AS jumlah,
                        COUNT(DISTINCT IF(transaksi.subtot >= users.minimal AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) * users.honor AS honor
                        "))
                    ->whereMonth('transaksi.tanggal', $month)->whereYear('transaksi.tanggal', $year)
                    ->whereNotIn('transaksi.id_donatur', $id_don)
                    ->where('users.id_karyawan',$id)
                    ->groupBy('tanggal', 'id')
                    ->get();
            
            $omset  = 0;
            $honor  = 0;
            for($i=0; $i<count($kolek); $i++){
            $omset  += $kolek[$i]['jumlah'];
            $honor  += $kolek[$i]['honor'];
            }
            
            // return($omset);        
            if($kar->jabatan == $tj->sokotak){
                $honor = $kolek != null ? $honor : 0;
                // $boncap = $kolek != null ? $kolek->boncap : 0;
                if ($omset <= 10000000){
                    $bon = $omset * 4/100;
                }elseif ($omset > 10000000 && $omset <= 20000000){
                    $bon = ($omset - 10000000) * 5/100 + 400000; 
                }elseif ($omset > 20000000){
                    $bon = ($omset - 20000000) * 6/100 + 900000;
                }else{
                    $bon = 0;
                }
            }
        }
        // dd($id_don, $data);
        for($i=0; $i<count($data); $i++){
        $totpo  += $data[$i]['poin'];
        $tothonpo  += $data[$i]['honpo'];
        $totbonpo  += $data[$i]['bonpo'];
        $totbonset  += $data[$i]['bonset'];
        }
        
        if($data != []){
            
            if($kar->jabatan == $tj->sokotak){
                $total = [
                        ['nambon' => 'Total Poin',
                         'nominal'=> $totpo],
                        ['nambon' => 'Total Honor Poin',
                         'nominal'=> $tothonpo],
                        ['nambon' => 'Total Bonus Poin',
                         'nominal'=> $totbonpo],
                        ['nambon' => 'Total Bonus Omset',
                         'nominal'=> $totbonset],
                        ['nambon' => 'Bonus Omset Kolekting',
                         'nominal'=> round($bon)],
                        ['nambon' => 'Honor Kolekting',
                         'nominal'=> $honor],
                        ['nambon' => 'Total Bonus',
                         'nominal'=> $tothonpo + $totbonpo + $totbonset + round($bon) + $honor],
                    ];
            }else{
                $total = [
                        ['nambon' => 'Total Poin',
                         'nominal'=> $totpo],
                        ['nambon' => 'Total Honor Poin',
                         'nominal'=> $tothonpo],
                        ['nambon' => 'Total Bonus Poin',
                         'nominal'=> $totbonpo],
                        ['nambon' => 'Total Bonus Omset',
                         'nominal'=> $totbonset],
                        ['nambon' => 'Total Bonus',
                         'nominal'=> $tothonpo + $totbonpo + $totbonset],
                    ];
            }
            
        }else{
          $total = []; 
        }
        
        // $datfin = [];
        // $datfin['jumlah'] = count($data);
        // $datfin['id_don'] = $id_don;
        // $datfin['data'] = $data;
        // $datfin['total'] = $total;
        // $datfin['prosdon'] = [
        //                         ['nampros'  => 'Donatur Closing',
        //                          'jumlah'   => $prosdon['closing']],
        //                         ['nampros'  => 'Donatur Open',
        //                          'jumlah'   => $prosdon['open']],
        //                         ['nampros'  => 'Donatur Cancel',
        //                          'jumlah'   => $prosdon['cancel']],
        //                         ['nampros'  => 'Total Prospek',
        //                          'jumlah'   => $prosdon['closing'] + $prosdon['open'] + $prosdon['cancel']],
        //                     ]; 
            $rinbon= [
                    //   ['bonus' => 'Total Poin',
                    //      'jumlah'=> $totpo],
                        ['bonus' => 'Total Honor Poin',
                         'jumlah'=> $tothonpo],
                        ['bonus' => 'Total Bonus Poin',
                         'jumlah'=> $totbonpo],
                        ['bonus' => 'Total Bonus Omset',
                         'jumlah'=> $totbonset],
                        ['bonus' => 'Bonus Omset Kolekting',
                         'jumlah'=> round($bon)],
                        ['bonus' => 'Total Bonus',
                         'jumlah'=> $tothonpo + $totbonpo + $totbonset + round($bon)],
                     ];
            $bonkol = $tothonpo + $totbonpo + $totbonset;
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => 0],
                        ];
            $totpot = 0;
            }
        }else{
            $honor = 0;
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => 0],
                     ];
                     
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => 0],
                        ];
            $totpot = 0;
        }
        // dd($honor);
        // $transport = $tj->potongan0;
        $gol = $kar->status_kerja == 'Magang' ? 'IA' : $kar->golongan;
        $datass = Karyawan::leftjoin('gapok', 'karyawan.masa_kerja', '=', 'gapok.th')->leftjoin('jabatan', 'karyawan.jabatan', '=', 'jabatan.id')
                ->leftjoin('daerah', 'karyawan.id_daerah', '=', 'daerah.id_daerah')
                ->leftjoin('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
                ->leftjoin("set_terlambat",function($join) use ($id){
                            $join->on("presensi.keterlambatan",">=","set_terlambat.awal")
                                 ->on("presensi.keterlambatan","<=","set_terlambat.akhir")
                                 ->where('presensi.id_karyawan', $id);
                            })
                ->select(\DB::raw("karyawan.nama, gapok.$gol, jabatan.tj_fungsional, jabatan.tj_jabatan , daerah.tj_daerah, jabatan.tj_training, jabatan.kon_tj_plt, jabatan.tj_plt,
                                SUM(IF(presensi.status = 'Hadir' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                                OR presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                                , presensi.jumlah,0)) AS jumlah,
                                SUM(IF(MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji'), set_terlambat.potongan, 0)) AS potongan"))
                ->groupBy('karyawan.nama','gapok.'.$gol, 'jabatan.tj_jabatan', 'jabatan.tj_training', 'daerah.tj_daerah', 'jabatan.tj_fungsional', 'jabatan.tj_training', 'jabatan.kon_tj_plt', 'jabatan.tj_plt')
                ->where('karyawan.id_karyawan',$id)->get();
        // dd($datass);
        $total = $tot_anak + $tot_pasangan + 1;
        // dd($potongbpjs);
        $jkk = Bpjs::where('nama_jenis', 'JKK')->first();
        $jkm = Bpjs::where('nama_jenis', 'JKM')->first();
        $jht = Bpjs::where('nama_jenis', 'JHT')->first();
        $jpn = Bpjs::where('nama_jenis', 'JPN')->first();
        $sht = Bpjs::where('nama_jenis', 'KESEHATAN')->first();
        
        $profil = Profile::where('id_com', Auth::user()->id_com)->first();
        
        $pjkk = $profil->jkk == 1 && $kar->jkk == 1 ? $jkk->perusahaan : 0;
        $pjkm = $profil->jkm == 1 && $kar->jkm == 1 ? $jkm->perusahaan : 0;
        $pjht = $profil->jht == 1 && $kar->jht == 1 ? $jht->perusahaan : 0;
        $pjpn = $profil->jpn == 1 && $kar->jpn == 1 ? $jpn->perusahaan : 0;
        $psht = $profil->kesehatan == 1 && $kar->kesehatan == 1 ? $sht->perusahaan : 0;
        
        $kjkk = $kar->jkk == 1 ? $jkk->karyawan : 0;
        $kjkm = $kar->jkm == 1 ? $jkm->karyawan : 0;
        $kjht = $kar->jht == 1 ? $jht->karyawan : 0;
        $kjpn = $kar->jpn == 1 ? $jpn->karyawan : 0;
        $ksht = $kar->kesehatan == 1 ? $sht->karyawan : 0;
        
        $potongbpjs = $tj->umr * (($kjkk + $kjkm + $kjht + $kjpn + $ksht)/100);
        
        $databpjs = [
            'jkk' => number_format($tj->umr * ($pjkk/100),0, ',' , '.'),
            'jkm' => number_format($tj->umr * ($pjkm/100),0, ',' , '.'),
            'jht' => number_format($tj->umr * ($pjht/100),0, ',' , '.'),
            'jpn' => number_format($tj->umr * ($pjpn/100),0, ',' , '.'),
            // 'sht' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
            // 'total' => number_format($tj->umr * (($jkk->perusahaan + $jkm->perusahaan + $jht->perusahaan + $jpn->perusahaan)/100),0, ',' , '.'),
            ];
            
        // return($databpjs);
        
        $rekdis = Rekdis::selectRaw("id_karyawan, COUNT(IF(stat = 1, tanggal, NULL)) AS lap, COUNT(IF(stat = 2, tanggal, NULL)) AS pul, COUNT(IF(stat = 3, tanggal, NULL)) AS lappul")
                ->where('id_karyawan', $kar->id_karyawan)
                ->groupBy('id_karyawan')->first();
                
        if($rekdis == null){        
        $rekdis = [
                    'id_karyawan'   => $kar->id_karyawan,
                    'lap'           => 0,
                    'pul'           => 0,
                    'lappul'         => 0
                ];
        }
        
        $potpul = ($rekdis['lap'] * $tj->pul) + ($rekdis['pul'] * $tj->pul);
        $potlappul = $rekdis['lappul'] * $tj->lappul;
        
        $data = [];
        foreach($datass as $x => $v){
            
        $potlaptab = [
                    ['nampot' => 'Keterlambatan', 'jumlah'  => round($v->potongan)],
                    ['nampot' => 'Tidak Laporan atau Presensi Pulang', 'jumlah'  => $potpul],
                    ['nampot' => 'Tidak Laporan dan Presensi Pulang', 'jumlah'  => $potlappul]
                ];
        
        // dd($rekdis, $potlaptab);
        
            // if($id == 8911041807102){
            //     $tjjab = ($kar->plt == 1 ? $tj->tj_plt/100 * $v->tj_jabatan : $v->tj_jabatan) + $v->tj_fungsional + 400000;
            // }else{
                $tjjab = ($kar->plt == 1 ? $v->tj_jabatan * ($v->tj_plt/100) : $v->tj_jabatan) + $v->tj_fungsional;
            // }
            
            // if($kar->plt == 1){
            //     $tjjab = ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) + $v->tj_fungsional;
            //     $tjjabtrain = $v->tj_training == 1 ? ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) : 0;
            // }else{
            //     $tjjab = $v->tj_jabatan + $v->tj_fungsional;
            //     $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
            // }
            
                
            $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
            
            if($kar->status_kerja == 'Contract'){
                $data['data'][] = [
                    'kondisi' => $kar->kondisi,
                    'nama' => $v->nama,
                    'no_rek' => $kar->no_rek,
                    'nik' => $kar->nik,
                    'status_kerja' => $kar->status_kerja,
                    'masa_kerja' => $kar->masa_kerja,
                    'golongan' => $kar->golongan,
                    'id_jabatan' => $kar->jabatan,
                    'id_kantor' => $kar->id_kantor,
                    // 'id_daerah' => $kar->id_daerah,
                    'gapok' => number_format($v->$gol,0, ',' , '.'),
                    'tj_jabatan' => number_format($tjjab,0, ',' , '.'),
                    'tj_daerah' => number_format($v->tj_daerah,0, ',' , '.'),
                    'tj_p_daerah' => number_format($kar->jab_daerah == 1 ? $drh->tj_jab_daerah : 0,0, ',' , '.'),
                    'jml_hari' => $v->jumlah,
                    'tj_anak' => number_format($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol),0, ',' , '.'),
                    'tj_pasangan' => number_format($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol),0, ',' , '.'),
                    'tj_beras' => number_format($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total,0, ',' , '.'),
                    'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor :  $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                    'total' => number_format($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                                ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                    'thp' => round(($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                                ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport))),
                    // 'thp' => number_format(($v->$gol + $tjjab + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                    //             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
                    //             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport)) - $potongbpjs ,0, ',' , '.'),
                    'tgl_gaji' => $tgl_gaji,
                    'bpjs' => round($potongbpjs),
                    'tot_tj_bpjs' => number_format($tj->umr * (($pjkk + $pjkm + $pjht + $pjpn)/100),0, ',' , '.'),
                    'bpjs_sehat' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
                    'tj_bpjs' => $databpjs,
                    'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                    'rinbon' => $rinbon,
                    'potlaptab' => $potlaptab,
                    'potab' => round($v->potongan),
                    'potdis' => '0',
                    'totpot' => $totpot,
                    'potongan' => $potongan,
                    'bokin' => '0'
                ];
            }else if($kar->status_kerja == 'Training'){
                // $gapok = $kar->id_karyawan == '9812062405101' ? $v->$gol * (50/100) : $v->$gol * (80/100);
                $gapok = $v->$gol * (80/100);
                $data['data'][] = [
                    'kondisi' => $kar->kondisi,
                    'nama' => $v->nama,
                    'no_rek' => $kar->no_rek,
                    'nik' => $kar->nik,
                    'status_kerja' => $kar->status_kerja,
                    'masa_kerja' => $kar->masa_kerja,
                    'golongan' => $kar->golongan,
                    'id_jabatan' => $kar->jabatan,
                    'id_kantor' => $kar->id_kantor,
                    // 'id_daerah' => $kar->id_daerah,
                    'gapok' => number_format($gapok,0, ',' , '.'),
                    'tj_jabatan' => number_format($tjjabtrain,0, ',' , '.'),
                    'tj_daerah' => '0',
                    'tj_p_daerah' => '0',
                    'jml_hari' => $v->jumlah,
                    'tj_anak' => '0',
                    'tj_pasangan' => '0',
                    'tj_beras' => '0',
                    'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                    'total' => number_format(($gapok) + $tjjabtrain +
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                    'thp' => round(($gapok) + $tjjabtrain +
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport)),
                    'tgl_gaji' => $tgl_gaji,
                    'bpjs' => 0,
                    'tot_tj_bpjs' => '0',
                    'bpjs_sehat' => '0',
                    'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                    'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                    'rinbon' => $rinbon,
                    'potab' => round($v->potongan),
                    'potlaptab' => $potlaptab,
                    'potdis' => '0',
                    'totpot' => $totpot,
                    'potongan' => $potongan,
                    'bokin' => '0'
                ];
            }else{
                $tjmag = $tj->tj_magang_mhs;
                $data['data'][] = [
                    'kondisi' => $kar->kondisi,
                    'nama' => $v->nama,
                    'no_rek' => $kar->no_rek,
                    'nik' => $kar->nik,
                    'status_kerja' => $kar->status_kerja,
                    'masa_kerja' => $kar->masa_kerja,
                    'golongan' => $kar->golongan,
                    'id_jabatan' => $kar->jabatan,
                    'id_kantor' => $kar->id_kantor,
                    // 'id_daerah' => $kar->id_daerah,
                    'gapok' => 0,
                    'tj_jabatan' => 0,
                    'tj_daerah' => '0',
                    'tj_p_daerah' => '0',
                    'jml_hari' => $v->jumlah,
                    'tj_anak' => '0',
                    'tj_pasangan' => '0',
                    'tj_beras' => '0',
                    'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag,0, ',' , '.'),
                    'total' => number_format(
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag),0, ',' , '.'),
                    'thp' => round(
                                ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag)),
                    'tgl_gaji' => $tgl_gaji,
                    'bpjs' => 0,
                    'tot_tj_bpjs' => '0',
                    'bpjs_sehat' => '0',
                    'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                    'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                    'rinbon' => $rinbon,
                    'potab' => 0,
                    'potdis' => '0',
                    'totpot' => 0,
                    'potongan' => $potongan,
                    'bokin' => '0'
                ];
            }
        }
        return($data);
    }
}
