<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rencana;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\User;
use App\Models\Targets;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Prosp;
use App\Models\RencanaThn;
use App\Models\RencanaBln;
use Auth;
use DB;
use DataTables;
use Excel;
use DateTime;
use App\Imports\RencanaImport;

use PDF;

use App\Exports\DetailRencanaExport;
use App\Exports\DetailAllRencanaExport;
use App\Exports\DetailAllKarRencanaExport;

class PerencanaanController extends Controller
{
    
    public function index(Request $request)
    {
        // if($request->tanggal != ''){
        //     $tgl = explode(' s.d. ', $request->tanggal);
        //     $dari = $tgl[0];
        //     $sampai = $tgl[1];
        // }else{
        //     $dari = date('Y-m-d');
        //     $sampai = date('Y-m-d');
        // }
        
        $kan = Auth::user()->id_kantor;
        // var_dump($kan);
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        if ($request->ajax()) {
            $kantor = Kantor::select('level')->where('id', Auth::user()->id_kantor)->first();
            
            // $startDate = new \DateTime($dari);
            // $endDate = new \DateTime(date('Y-m-d', strtotime('+1 day',strtotime($sampai))));
            // $interval = new \DateInterval('P1D');
    
            // $tgls = [];
            // $dateRange = new \DatePeriod($startDate, $interval, $endDate);
            // $countDays = iterator_count($dateRange);
                
            $data = [];
            
            if($request->periode == 'tahun'){
                $month = $request->tahun == '' ? date('Y') : $request->tahun;
                $full = "tahun = '$month'";
            }else if($request->periode == 'bulan'){
                $bb = $request->bulan == '' ? date('Y-m-01') : $request->bulan.'-01';   
                $month = $request->bulan == '' ? date('Y-m') : $request->bulan;
                $full ="rencana_bln.bulan = '$bb'";
            }else{
                // if($request->tanggal != ''){
                //     $tgl = explode(' s.d. ', $request->tanggal);
                //     $dari = $tgl[0];
                //     $sampai = $tgl[1];
                // }else{
                //     $dari = date('Y-m-d');
                //     $sampai = date('Y-m-d');
                // }
                
                // if(strtotime($dari) == strtotime($sampai)){
                //     $month = $dari;
                // }else{
                //     $month = $dari.' s.d. '.$sampai;
                // }
                
                // $full = "DATE(tgl_awal) >= '$dari' AND DATE(tgl_awal) <= $sampai";
                
                $bb = $request->tanggal == '' ? date('Y-m') : $request->tanggal;   
                $month = $request->tanggal == '' ? date('Y-m') : $request->tanggal;
                // $full = "DATE_FORMAT(bulan, '%Y-%m') = '$month'";
                $full = "DATE_FORMAT(tgl_awal, '%Y-%m') = '$month'";
                
            }
            
            if($request->periode == 'tanggal'){
                $datas = Karyawan::selectRaw("rencana.*, karyawan.nama, karyawan.id_karyawan as id_kar, COUNT(IF($full AND marketing = 0 AND rencana.aktif = 1, id, null)) as jumlah")
                            ->leftJoin('rencana','karyawan.id_karyawan','=','rencana.id_karyawan')
                            ->where('karyawan.aktif', 1)
                            ->where('id_com', Auth::user()->id_com)
                            ->where(function($query) use ($kantor, $kan, $k, $request){
                                if($request->unit == ''){
                                    if(Auth::user()->level == 'kacab'){
                                        if($k == null){
                                            $query->where('karyawan.id_kantor', $kan);
                                        }else{
                                            $query->whereRaw("karyawan.id_kantor = '$kan' OR karyawan.id_kantor = '$k->id'");
                                        }
                                    }
                                }else{
                                    $query->where('karyawan.id_kantor', $request->unit);
                                }
                            })
                            ->orderBy('karyawan.nama','ASC')
                            ->groupBy('id_kar')
                            ->get();
                
            }else{
                $datas = Kantor::selectRaw("tambahan.*")
                            ->where('id_com', Auth::user()->id_com)
                            ->where(function($query) use ($kantor, $kan, $k, $request){
                                if(Auth::user()->kepegawaian == 'kacab'){
                                    if($k == null){
                                        $query->where('tambahan.id', $kan);
                                    }else{
                                        $query->whereRaw("tambahan.id = '$kan' OR tambahan.id = '$k->id'");
                                    }
                                }else{
                                    $query->whereRaw("tambahan.id IS NOT NULL");
                                }
                            })
                            ->orderBy('tambahan.unit','ASC')
                            ->get();
            }
            
            if($request->periode == 'tanggal'){
                
                $mergedData = [];
                foreach($datas as $d){
                    $cobain = Rencana::select('tgl_awal')->join('users','users.id','=','rencana.user_insert')->join('rencana_bln','rencana.id_rb','=','rencana_bln.id')->whereRaw("rencana.marketing = 0  AND $full AND rencana.id_karyawan = '$d->id_kar'")->pluck('tgl_awal')->toArray();
                    $lagi = Rencana::select('tgl_awal')->whereRaw("marketing = 1 AND $full AND id_karyawan = '$d->id_kar'")->pluck('tgl_awal')->toArray();
                    
                    $mergedData = array_reduce($cobain, function ($carry, $date) {
                        $carry[$date][] = $date;
                        return $carry;
                    }, []);
                    
                    $mergedData = array_values($mergedData);
                    
                    $mergedData2 = array_reduce($lagi, function ($carrye, $datee) {
                        $carrye[$datee][] = $datee;
                        return $carrye;
                    }, []);
                    
                    $mergedData2 = array_values($mergedData2);
                    
                    $data[] = [
                        'id' => '',
                        'id_karyawan' => $d->id_kar,
                        'nama' => $d->nama,
                        'tgl' => $month,
                        'jumlah_hari' => count($mergedData). ' Tugas Umum dan '.count($mergedData2).' Merketing',
                        'has' => 0,
                        'id_kan' => $d->id,
                        'pros' => 0,
                        'tugass'=> count($cobain),
                    ];
                }
            }else{
                foreach($datas as $key => $d){
                    if($request->periode == 'tahun'){
                        $rencana = RencanaThn::selectRaw("COUNT(id) as jumlah, 0 as hasil, 0 as proses")->whereRaw("id_kantor =  '$d->id' AND $full")->get();
                    }else{
                        $rencana = RencanaBln::selectRaw("COUNT(rencana_bln.id) as jumlah, 
                                        (SELECT COUNT(rencana_bln.id) FROM rencana_bln INNER JOIN rencana_bln as hasilnya ON hasilnya.id = rencana_bln.id_hasil  WHERE $full AND rencana_bln.jenis_target = 'proses' AND rencana_bln.id_kantor = '$d->id') as proses, 
                                        COUNT(IF($full AND rencana_bln.jenis_target = 'hasil', rencana_bln.id, null)) as hasil
                                    ")
                                    ->whereRaw("rencana_bln.id_kantor =  '$d->id' AND $full")
                                    ->get();
                                    
                    }
                    
                    $data[] = [
                            'id' => $d->id,
                            'id_karyawan' => $key+1,
                            'nama' => $d->unit,
                            'tgl' => $month,
                            'id_kan' => $d->id,
                            'jumlah_hari' => $rencana[0]->jumlah,
                            'has' => $rencana[0]->hasil,
                            'pros' => $rencana[0]->proses,
                            'tugass' => 0
                    ]; 
                }
            }
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('proses', function ($data) {
                    $jml = '<button class="btn btn-xs btn-rounded btn-success proseses" id="proses" data-kantor="'.$data['id'].'">'.$data['pros'].'</button>';
                    return $jml;
                })
                ->addColumn('hasil', function ($data) {
                    $jml = '<button class="btn btn-xs btn-rounded btn-info proseses " id="hasil" data-kantor="'.$data['id'].'">'.$data['has'].'</button>';
                    return $jml;
                })
                
                ->addColumn('tugas', function ($data) {
                    $jml = '<button class="btn btn-xs btn-rounded btn-primary karyawannn" data-nama="'.$data['nama'].'" data-tgl="'.$data['tgl'].'" data-id="'.$data['id_karyawan'].'">'.$data['tugass'].'</button>';
                    return $jml;
                })
                
                ->addColumn('akss', function ($data) {
                    $jml = '<button class="btn btn-xs btn-rounded btn-primary lihattt" data-nama="'.$data['nama'].'" data-tgl="'.$data['tgl'].'" data-kan="'.$data['id_kan'].'" data-id="'.$data['id_karyawan'].'"><i class="fa fa-list"></i></button>';
                    return $jml;
                })
                
                ->addColumn('set_target', function ($data) {
                    $tgl = $data['tgl'];
                    $kan = $data['id_kan'];
                    $gett =Targets::whereRaw("jenis_target = 'prog' AND periode = 'bulan' AND DATE_FORMAT(tanggal, '%Y-%m') = '$tgl' AND id_kantor = '$kan'")->get();
                    if(count($gett) > 0){
                        $jml = '<button class="btn btn-xxs btn-rounded btn-success progser" data-nama="'.$data['nama'].'" data-tgl="'.$data['tgl'].'" data-id="'.$data['id_karyawan'].'">Lihat</button>';
                    }else{
                        $jml = '<button class="btn btn-xxs btn-rounded btn-danger kalogada" data-nama="'.$data['nama'].'" data-tgl="'.$data['tgl'].'" data-id="'.$data['id_karyawan'].'">Kosong</button>';
                    }
                    return $jml;
                })
                
                ->rawColumns(['hasil', 'proses', 'tugas','set_target','akss'])
                ->make(true);
        }
        
        if(Auth::user()->kepegawaian == 'kacab'){
           if($k == null) {
                $kota =  Kantor::where('id_com', Auth::user()->id_com)->where('id', $kan)->get();
           }else{
                $kota =  Kantor::whereRaw("(id = '$kan' OR id = '$k->id')")->where('id_com', Auth::user()->id_com)->get();
           }
        }else{
            $kota =  Kantor::where('id_com', Auth::user()->id_com)->get();
        }
        // return $k;
        
        
        return view('perencanaan.index', compact('kota'));
    }
    
    public function getRencanaThn(Request $request){
        if($request->tab == 'tab1'){
            $datas = RencanaThn::whereRaw("id_kantor = '$request->id_kan' AND tahun = '$request->tahun' AND jenis_target = '$request->jt'")->get();
            return $datas;
        }
        
        if($request->tab == 'tab2'){
            $unt = $request->unit == '' || $request->unit == 'all' ? 0 : $request->unit;
            
            $datay = RencanaThn::selectRaw("rencana_thn.*, tambahan.unit as kota, '' as aksi")->join('tambahan','tambahan.id','=','rencana_thn.id_kantor')->whereRaw("tipe = 'bagian' AND id_kantor = '$unt'")->get();
            return $datay;
        }
        
        if($request->tab == 'tab3'){
            
            $kan = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kantor = Kantor::select('level')->where('id', Auth::user()->id_kantor)->first();
            
            $tipe   = 'satuan';
            $jenis  = $request->jt;
            
            $unitt = $request->unit;
            $dataay = RencanaThn::selectRaw("rencana_thn.*, tambahan.unit as kota, '' as aksi")->join('tambahan','tambahan.id','=','rencana_thn.id_kantor')
                        // ->whereRaw("tipe = 'satuan' AND jenis_target = '$request->jt'")
                        ->where(function($q) use($tipe, $jenis) {
                                if($tipe != 'all'){
                                    if($tipe == 'satuan' && $jenis != 'all'){
                                        $q->where('tipe', $tipe)->where('jenis_target', $jenis);
                                    }else{
                                        $q->where('tipe', $tipe);   
                                    }
                                }else{
                                    $q->where('tipe', 'satuan')->where(function($que) use($tipe, $jenis){
                                                                    if($jenis != 'all'){
                                                                        $que->where('jenis_target', $jenis);
                                                                    }
                                                                })
                                        ->orWhere('tipe', 'bagian');
                                } 
                            })
                            ->where(function($q) use($unitt) {
                                    $q->where('rencana_thn.id_kantor', $unitt)
                                        ->orWhere('rencana_thn.id_kantor', '0');
                            })
                        // ->where(function($query) use ($kantor, $kan, $k, $unitt){
                        //         if($unitt == ''){
                        //             if(Auth::user()->level == 'kacab'){
                        //                 if($k == null){
                        //                     $query->where('rencana_thn.id_kantor', $kan);
                        //                 }else{
                        //                     $query->whereRaw("rencana_thn.id_kantor = '$kan' OR rencana_thn.id_kantor = '$k->id'");
                        //                 }
                        //             }else if(Auth::user()->level == 'admin' || Auth::user()->level == 'operator pusat'){
                        //                 $query->whereRaw("rencana_thn.id_kantor iS NOT NULL");
                        //             }
                        //         }else{
                        //             $query->where('rencana_thn.id_kantor', $unitt);
                        //         }
                        //     })
                        
                        ->get();
            return $dataay;
        }
        
        if($request->tab == 'tab4'){
            $dataay = RencanaThn::find($request->id);
            return $dataay;
        }
        
        if($request->tab == 'tab5'){
            $dataay = RencanaThn::where('bagian', $request->id_bagian)->where('jenis_target','hasil')->get();
            return $dataay;
        }
        
        if($request->tab == 'tab6'){
            $dataay = RencanaThn::where('bagian', $request->id_bagian)->where('jenis_target','proses')->get();
            return $dataay;
        }
        
        // $bagians = $request->bagian; 
        $id_kan = $request->unit; 
        $tipe   = $request->tipe;
        $jenis  = $request->jenis;
        $renkan = RencanaThn::selectRaw("rencana_thn.*, (SELECT unit FROM tambahan WHERE rencana_thn.id_kantor = tambahan.id) as kota, '' as aksi, (SELECT tugas FROM rencana_thn as rtt WHERE rencana_thn.bagian = rtt.id) as bagians")
                            // ->join('tambahan','tambahan.id','=','rencana_thn.id_kantor')
                            ->where(function($q) use($id_kan) {
                                    $q->where('id_kantor', $id_kan)
                                        ->orWhere('id_kantor', '0');
                            })
                            
                            // ->where(function($q) use($bagians) {
                            //     if($bagians != 'all'){
                            //         $q->where('bagian', $bagians);
                            //     }
                            // })
                            
                            ->where(function($q) use($tipe, $jenis) {
                                if($tipe != 'all'){
                                    if($tipe == 'satuan' && $jenis != 'all'){
                                        $q->where('tipe', $tipe)->where('jenis_target', $jenis);
                                    }else{
                                        $q->where('tipe', $tipe);   
                                    }
                                }else{
                                    $q->where('tipe', 'satuan')
                                        ->where(function($que) use($tipe, $jenis){
                                            if($jenis != 'all'){
                                                $que->where('jenis_target', $jenis);
                                            }
                                        })
                                        ->orWhere('tipe', 'bagian');
                                }
                                
                                // if($bagians != 'all'){
                                    
                                //     $q->where('bagian', $bagians);
                                // }
                            })
                            ->orderBy('jeniS_target','ASC')
                            ->orderBy('id_kantor','ASC')
                            ->get();
        
        return $renkan;
        
        // $data = RencanaThn::whereRaw("tipe = 'bagian'")->get();
        // return $data;
    }
    
    public function getRencanaBln(Request $request){
        
        
        // return $request;
        $tahun = $request->tahun == '' ? date('Y-m') : $request->tahun;
        
        if($request->tab == 'tab1'){
            $data1 = RencanaBln::selectRaw("rencana_bln.*, rencana_bln.program as prog, rencana_bln.jenis as cash, DATE_FORMAT(rencana_bln.bulan, '%Y-%m') as tahun, DATE_FORMAT(rencana_bln.bulan_akhir, '%Y-%m') as tahun2, rencana_bln.id_hasil, rencana_thn.tugas as tgs, satuan_thn.tugas as satuan_text, hasilnya.tugas as hasill, satuan_thn.rumus as rums,DATE_FORMAT(hasilnya.bulan, '%Y-%m') as bulano")
                    ->join('rencana_bln as hasilnya','hasilnya.id','=','rencana_bln.id_hasil')
                    ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                    ->join('rencana_thn','rencana_thn.id','=','rencana_bln.id_rt')
                    ->whereRaw("rencana_bln.id_kantor = '$request->id_kan' AND DATE_FORMAT(rencana_bln.bulan, '%Y-%m') = '$tahun' AND rencana_bln.jenis_target = '$request->jt'")->get();
            return $data1;
        }
        
        if($request->tab == 'hasil'){
            $data1x = RencanaBln::selectRaw("rencana_bln.*, rencana_bln.program as prog, rencana_bln.jenis as cash, DATE_FORMAT(rencana_bln.bulan, '%Y-%m') as tahun, DATE_FORMAT(rencana_bln.bulan_akhir, '%Y-%m') as tahun2, rencana_bln.id_hasil, rencana_thn.tugas as tgs, satuan_thn.tugas as satuan_text, satuan_thn.rumus as rums, '' as bulano")
                    ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                    ->join('rencana_thn','rencana_thn.id','=','rencana_bln.id_rt')
                    ->whereRaw("rencana_bln.id_kantor = '$request->id_kan' AND DATE_FORMAT(rencana_bln.bulan, '%Y-%m') = '$tahun' AND rencana_bln.jenis_target = '$request->jt'")->get();
            return $data1x;
        }
        
        if($request->tab == 'tab2'){
            $bulan = $request->bulan == '' ? date('Y-m') : $request->bulan;
            $daaa = RencanaBln::whereRaw("rencana_bln.id_kantor = '$request->id_kantor' AND DATE_FORMAT(bulan, '%Y-%m') = '$bulan' AND rencana_bln.jenis_target = 'hasil'")->get();
            return $daaa;
        }
        
        if($request->tab == 'tab5'){
            
            $dataa = RencanaBln::select('rencana_bln.*','rumus')->join('rencana_thn','rencana_thn.id','=','rencana_bln.satuan')->where('rencana_bln.id',$request->id)->first();
            $itung = Rencana::whereRaw("rencana.id_rb = '$request->id' AND rencana.tgl_awal = rencana.tgl_akhir AND rencana.aktif = 1")->sum('target');
            $dataa['sisa']  = $dataa == null ? 0 : $dataa->target - $itung;
            return $dataa;
        }
        
        $now = date('Y-m');
        // $data = RencanaBln::whereRaw("id_kantor = '$request->id_kan' AND DATE_FORMAT(bulan, '%Y-%m') = '$now' ")->get();
        
        $tgll = $request->tgl == '' ? date('Y-m') : $request->tgl;
        
        $jenis  = 'proses';
        $id_kan = $request->id_kan;
        // $bln    = date('Y-m-01');
        $bln = $tgll.'-01';
        
        $sel    = $jenis == 'proses' ? "rencana_bln.*,
                                        (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian, 
                                        (SELECT tugas FROM rencana_bln AS hasil WHERE hasil.id = rencana_bln.id_hasil) AS thasil" 
                                        : "rencana_bln.*, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian";
                                        
        $getren = RencanaBln::selectRaw($sel)
                            ->whereDate('rencana_bln.bulan_akhir', '>=', $bln)
                            ->whereDate('rencana_bln.bulan', '<=', $bln) 
                            ->where('rencana_bln.jenis_target', $jenis)
                            ->where('rencana_bln.aktif', 1)
                            ->where('rencana_bln.id_kantor', $id_kan)
                            ->where('tgl_selesai', NULL)
                            ->get();
                            
        // return $getren;
        
        $data = [];
        
        foreach($getren as $v){
            $totar  = Rencana::whereRaw("rencana.id_rb = $v->id AND rencana.tgl_awal = rencana.tgl_akhir AND rencana.aktif = 1")->sum('target');
            $sat    = RencanaThn::where('rencana_thn.id', $v->satuan)->first();
            // return $sat;
            if($v->target - $totar > 0){
                $data[] = [
                            'id'            => $v->id,
                            'id_rt'         => $v->id_rt,
                            'id_hasil'      => $v->id_hasil,
                            'bulan_hasil'   => $v->bulan_hasil,
                            'id_kantor'     => $v->id_kantor,
                            'tugas'         => $v->tugas,
                            'bulan'         => $v->bulan,
                            'bulan_akhir'   => $v->bulan_akhir,
                            'target'        => $v->target,
                            'satuan'        => $v->satuan,
                            'jenis_target'  => $v->jenis_target,
                            'aktif'         => $v->aktif,
                            'capaian'       => $v->capaian,
                            'tgl_selesai'   => $v->tgl_selesai,
                            'acc'           => $v->acc,
                            'marketing'     => $v->marketing,
                            'alasan'        => $v->alasan,
                            'alasan_reject' => $v->alasan_reject,
                            'sisa_target'   => $v->target - $totar,
                            'bagian'        => $v->bagian,
                            'sat'           => $sat->tugas ?? null,
                            'rumus'         => $sat->rumus ?? null,
                            'thasil'        => $v->thasil,
                        ];
            }
        }
        
        return $data;
    }
    
    public function addRencanaT(Request $request){
        
        // return($request);
        
        foreach($request->renTa as $val){
            
            $id_kan = $val['id_kantor'];
            $tugas = $val['tugas'];
            $target = $val['target'];
            $tahun = $val['tahun'];
            
            if($val['id'] == ''){
                
                $input['id_kantor'] = $id_kan;
                $input['tugas'] = $tugas;
                $input['target'] = $target;
                $input['tahun'] = $tahun;
                $input['user_insert'] = Auth::user()->id;
                
                RencanaThn::create($input);
            }else{
                
                $input['tugas'] = $tugas;
                $input['target'] = $target;
                $input['tahun'] = $tahun;
                $input['user_update'] = Auth::user()->id;
                
                RencanaThn::where('id', $val['id'])->update($input);
            }
            
        }
        
        // return $request;   
        return response()->json(['success' => 'Berhasil!']);   
    }
    
    public function addRencanaTP (Request $request){
        $id1 = [];
        $id2 = [];
        $aeh = RencanaThn::where('id_kantor', $request->id_kan)->where('tipe','bagian')->pluck('id')->toArray();
        foreach($request->renTP as $val){
            
            $aksi = $val['aksi'];
            
            $id_kan = $val['id_kantor'];
            $tugas = $val['tugas'];
            
            if($val['id'] == ''){
                
                $input['id_kantor'] = $id_kan;
                $input['tugas'] = $tugas;
                $input['tipe'] = 'bagian';
                $input['user_insert'] = Auth::user()->id;
                
                RencanaThn::create($input);
            }else{
                
                // $idArray = array_map(function($item) {
                //     return (int) $item['id'];
                // }, $request->renTP);
                
                // $id1 = $aeh;
                // $id2 = $idArray;
                
                // $hasil1 = array_diff($id1, $id2);
                
                // foreach ($hasil1 as $index => $value) {}
                
                
                if($aksi == 'hapus'){
                    RencanaThn::where('id', $val['id'])->delete();
                    // return('n');
                }else{
                    $input['tugas'] = $tugas;
                    $input['user_update'] = Auth::user()->id;
                    RencanaThn::where('id', $val['id'])->update($input);
                    // return('y');
                }
                
            }
            
        }
        
        return response()->json(['success' => 'Berhasil!']);
    }
    
    public function addRencanaS (Request $request){
        // return $request;
        $id1 = [];
        $id2 = [];
        $aeh = RencanaThn::where('id_kantor', $request->id_kan)->where('tipe','satuan')->pluck('id')->toArray();
        foreach($request->renS as $val){
            
            $id_kan = $val['id_kantor'];
            $tugas = $val['tugas'];
            $jenis_target = $val['jenis_target'];
            // $rumus = $val['rumus'];
            $aksi = $val['aksi'];
            $bagian = $val['bagian'];
            
            if($val['id'] == ''){
                
                $input['jenis_target'] = $jenis_target;
                $input['id_kantor'] = $id_kan;
                $input['tugas'] = $tugas;
                $input['tipe'] = 'satuan';
                // $input['rumus'] = $rumus;
                $input['bagian'] = $bagian;
                $input['user_insert'] = Auth::user()->id;
                
                RencanaThn::create($input);
            }else{
                
                // $idArray = array_map(function($item) {
                //     return (int) $item['id'];
                // }, $request->renS);
                
                // $id1 = $aeh;
                // $id2 = $idArray;
                
                // $hasil1 = array_diff($id1, $id2);
                
                // foreach ($hasil1 as $index => $value) {}
                
                
                if($aksi == 'hapus'){
                    RencanaThn::where('id', $val['id'])->delete();
                }else{
                    $input['tugas'] = $tugas;
                    // $input['rumus'] = $rumus;
                    $input['bagian'] = $bagian;
                    $input['jenis_target'] = $jenis_target;
                    $input['user_update'] = Auth::user()->id;
                    RencanaThn::where('id', $val['id'])->update($input);
                }
                
            }
            
        }
        
        return response()->json(['success' => 'Berhasil!']);
    }
    
    public function addRencanaM(Request $request){
        if(Auth::user()->name == 'Akhmad Sugandi'){
            // return($request);
            
        }
        
        
        $bulan = $request->bulan == '' ? date('Y-m') : $request->bulan;
        // $aeh = RencanaBln::where('id_kantor', $request->id_kan)->whereRaw("DATE_FORMAT(bulan, '%Y-%m') = '$bulan'")->pluck('id')->toArray();
        foreach($request->renBu as $val){
            
            $aksi = $val['aksi'];
            
            $id_kan = $val['id_kantor'];
            $tugas = $val['tugas'];
            $target = $val['target'];
            $tahun = $val['tahun'];
            $parent = $val['parent'];
            // $metode = 
            $satuan = $val['satuan'];
            
            $parent_text = $val['parent_text'];
            
            if($parent_text == 'Marketing'){
                $prog = $val['prog'];
                $cash = $val['cash'];
                
            }else{
                $prog = null;
                $cash = null;
            }
            
            $tahun2 = $val['tahun2'];
            $jenis_target = $val['jenis_target'];
            $id_hasil = $val['id_hasil'];
            
            $yes = $val['bulano'] != null ? $val['bulano'] : null;
            
            $hasil_bulan= $val['bulano'];
            
            if($val['id'] == ''){ 
                $input['id_kantor'] = $id_kan;
                $input['id_rt'] = $parent;
                $input['tugas'] = $tugas;
                $input['target'] = $target;
                $input['bulan_hasil'] = $yes;
                $input['satuan'] = $satuan;
                $input['program'] = $prog;
                $input['jenis'] = $cash;
                $input['metode'] = $val['metode'];
                $input['jenis_target'] = $jenis_target;
                $input['bulan'] = $tahun.'-01';
                $input['id_hasil'] = $id_hasil;
                $input['bulan_akhir'] = $tahun2.'-01';
                $input['user_insert'] = Auth::user()->id;
                
                RencanaBln::create($input);
            }else{
                
                // $idArray = array_map(function($item) {
                //     return (int) $item['id'];
                // }, $request->renBu);
                
                // $id1 = $aeh;
                // $id2 = $idArray;
                
                // $hasil1 = array_diff($id1, $id2);
                
                // foreach ($hasil1 as $index => $value) {}
                
                // if(count($request->renBu) < count($aeh)){
                if($aksi == 'hapus'){
                    RencanaBln::where('id', $val['id'])->delete();
                    // return('n');
                }else if($aksi == 'edit'){
                    $input['id_rt'] = $parent;
                    $input['tugas'] = $tugas;
                    $input['satuan'] = $satuan;
                    $input['target'] = $target;
                    $input['metode'] = $val['metode'];
                    $input['bulan_hasil'] = $yes;
                    $input['bulan'] = $tahun.'-01';
                    $input['program'] = $prog;
                    $input['jenis'] = $cash;
                    $input['id_hasil'] = $id_hasil;
                    $input['jenis_target'] = $jenis_target;
                    $input['bulan_akhir'] = $tahun2.'-01';
                    $input['user_update'] = Auth::user()->id;
                    RencanaBln::where('id', $val['id'])->update($input);
                }
            }
        }
        
        // return $request; 
        return response()->json(['success' => 'Berhasil!']);   
    }
    
    public function exportRencana(Request $request){
        $tahun = $request->bulan != '' ? $request->bulan : date('Y-m');
        $kntr =  $request->kntr;
        
        if($request->tombol == 'xls'){
            $r = Excel::download(new RencanaExport($request), 'Data Rencana Unit '.$kntr.' '.$tahun.'.xls');
            ob_end_clean();
            return $r;
        }else{
            $r = Excel::download(new RencanaExport($request), 'Data Rencana Unit '.$kntr.' '.$tahun.'.csv');
            ob_end_clean();
            return $r;
        }
    }
    
    public function form(Request $request){
        
        if($request->daterange != ''){
            if(strpos($request->daterange, 's.d.') > 0){
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = $tgl[0];
                $sampai = $tgl[1];
            }else{
                $dari = $request->daterange;
                $sampai = $request->daterange;
            }
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }
        
        $bulan = $request->daterange == '' ? date('Y-m-d') : $request->daterange;
        $data = Rencana::selectRaw("DATE(tgl_awal) as bul, id_karyawan, COUNT(IF(aktif= 1, id, NULL)) as jumlah, COUNT(IF(aktif= 0, id, NULL)) as non, COUNT(IF(durasi = 'daily'  AND aktif= 1, id, NULL)) as daily, COUNT(IF(durasi = 'range'  AND aktif= 1, id, NULL)) as jarak, COUNT(IF(tgl_selesai IS NOT NULL AND acc = 1, id, NULL)) as selesai")->where('id_karyawan', $request->id)->whereRaw("DATE(tgl_awal) >= '$dari' AND DATE(tgl_awal) <= '$sampai' AND marketing = '0'")->groupBy('bul')->orderBy('bul', 'asc')->get();

        return response()->json($data);
    }
    
    public function laporanBy(Request $request){
        
        $coy = [];
        
        if($request->tab == 'lain'){
            $coy = Rencana::where('id',$request->id)->first();
            
            return $coy;
        }
        
        if($request->tab == 'tab1'){
            // perbaiki
            $day = Laporan::find($request->id);
            
            // $day = Rencana::where('id_laporan', $request->id)->orderBy('tgl_awal','asc')->first();
            // $dats = Rencana::where('id_laporan', $request->id)->orderBy('tgl_awal','asc')->get();
            // $bln = date('m', strtotime($day->tgl_awal));
            // $thn = date('Y', strtotime($day->tgl_awal));
                
            // ini tanggal laporan
            $u = User::where('id_karyawan', $day->id_karyawan)->first();
            
            $tgl = date('Y-m-d', strtotime($day->created_at));    
            $tgl_d = date('Y-m-01', strtotime($tgl));
                
            
            $r_mrkt = Rencana::where('id_laporan', $request->id)->where('marketing', 1)->whereDate('tgl_awal', $tgl)->first();
            $r_umum = Rencana::where('id_laporan', $request->id)->where('marketing', 0)->get(); 
            $r_dana = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $day->id_karyawan)->where('jenis_target', 'kar')->first();
                
            $min    = $r_dana != null ? $r_dana->minimal : 0;
            $omset  = Transaksi::whereDate('tanggal', '>=', $tgl_d)->whereDate('tanggal', '<=', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->sum('jumlah');
            $tran   = Transaksi::selectRaw("COUNT(id_donatur) AS knj, COUNT(IF(subtot > $min, id_donatur, NULL)) AS tdm")->whereDate('tanggal', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->first();
            $prosp  = Prosp::selectRaw("COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
                            ->where(function($q) use ($tgl) {
                                $q->whereDate('tgl_fol', $tgl)->orWhereDate('created_at', $tgl);
                            })
                            ->where('id_peg', $u->id)->first();
                
            $crit = [];
            
            // return $r_dana== null ? 0: 1;
                
            if($r_dana != null){
                $v = $r_dana;
                $crit[0]   =  [
                                'id'        => 0,
                                'tugas'     => 'Target Dana',
                                'target'    => $v->target,
                                'tgl_awal'  => $tgl,
                                'akhir'     => date('Y-m-t'),
                                'capaian'   => $omset,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'dana'      => 1,
                                'telat'     => 0,
                                'alasan'    => '',
                                'acc'       => 1,
                                'task'      => '',
                                'id_laporan' => ''
                            ];
            }
                
            foreach($r_umum as $key => $v){
                $i = $r_dana != null ? $key+1 : $key;
                $crit[$i]  =   [
                                'id'        => $v->id,
                                'tugas'     => $v->tugas,
                                'tgl_awal'  => $v->tgl_awal,
                                'target'    => $v->target,
                                'akhir'     => $v->tgl_akhir,
                                'capaian'   => $v->capaian,
                                'marketing' => 0,
                                'aktif'     => $v->aktif,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => $v->alasan,
                                'acc'       => $v->acc,
                                'task'       => 'umum',
                                'id_laporan' => $v->id_laporan
                            ];
            }
                
            if($r_mrkt != null | $tran != null | $prosp != null){
                $v = $r_mrkt;
                $i = $r_dana != null ? count($r_umum) + 1 :  count($r_umum);
                $crit[$i] =  [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Total Kunjungan',
                                'target'    => $r_mrkt != null ? $v->kunjungan : 0,
                                'tgl_awal'  => $tgl,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                                'capaian'   => $tran != null ? $tran->knj : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'acc'       => 1,
                                'task'      => '',
                                'id_laporan' => ''
                                ];
                $crit[$i+1] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Transaksi diatas minimal',
                                'tgl_awal'  => $tgl,
                                'target'    => $r_mrkt != null ? $v->transaksi : 0,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                                'capaian'   => $tran != null ? $tran->tdm : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'acc'       => 1,
                                'task'      => '',
                                'id_laporan' => ''
                                ];
                $crit[$i+2] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Total Penawaran',
                                'tgl_awal'  => $tgl,
                                'target'    => $r_mrkt != null ? $v->penawaran : 0,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                                'capaian'   => $prosp != null ? $prosp->pnw + $prosp->cls : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'acc'       => 1,
                                'task'      => '',
                                'id_laporan' => ''
                                ];
                $crit[$i+3] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Prospek Closing',
                                'tgl_awal'  => $tgl,
                                'target'    => $r_mrkt != null ? $v->closing : 0,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                                'capaian'   => $prosp != null ? $prosp->cls : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'acc'       => 1,
                                'task'      => '',
                                'id_laporan' => ''
                                ];
            }
            
            return response()->json($crit);
        }
        
        $data = Rencana::where('id',$request->id)->first();
        $det = Laporan::find($data->id_laporan);
            
        $tgl = date('Y-m-d', strtotime($det->created_at));
            
        $tgl_d = date('Y-m-01', strtotime($tgl));
            
        $u = User::where('id_karyawan', $data->id_karyawan)->first();
        
        $r_mrkt = Rencana::where('id_laporan', $det->id_laporan)->where('marketing', 1)->whereDate('tgl_awal', $tgl)->first(); 
        $r_umum = Rencana::where('id_laporan', $det->id_laporan)->where('marketing', 0)->get(); 
        $r_dana = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $det->id_karyawan)->where('jenis_target', 'kar')->first();
            
        $min    = $r_dana != null ? $r_dana->minimal : 0;
        $omset  = Transaksi::whereDate('tanggal', '>=', $tgl_d)->whereDate('tanggal', '<=', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->sum('jumlah');
        $tran   = Transaksi::selectRaw("COUNT(id_donatur) AS knj, COUNT(IF(subtot > $min, id_donatur, NULL)) AS tdm")->whereDate('tanggal', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->first();
        $prosp  = Prosp::selectRaw("COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
                            ->where(function($q) use ($tgl) {
                                $q->whereDate('tgl_fol', $tgl)->orWhereDate('created_at', $tgl);
                            })
                            ->where('id_peg', $u->id)->first();
            
        $coy = [];
        
        // return $r_dana== null ? 0: 1;
            
        if($r_dana != null){
            $v = $r_dana;
            $coy[0]   =  [
                            'id'        => 0,
                            'tugas'     => 'Target Dana',
                            'target'    => $v->target,
                            'tgl_awal'  => $tgl,
                            'akhir'     => date('Y-m-t'),
                            'capaian'   => $omset,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 1,
                            'telat'     => 0,
                            'alasan'    => '',
                            'acc'       => 1,
                            'task'      => ''
                        ];
        }
            
        foreach($r_umum as $key => $v){
            $i = $r_dana != null ? $key+1 : $key;
            $coy[$i]  =   [
                            'id'        => $v->id,
                            'tugas'     => $v->tugas,
                            'tgl_awal'  => $v->tgl_awal,
                            'target'    => $v->target,
                            'akhir'     => $v->tgl_akhir,
                            'capaian'   => $v->capaian,
                            'marketing' => 0,
                            'aktif'     => $v->aktif,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => $v->alasan,
                            'acc'       => $v->acc,
                            'task'       => 'umum'
                        ];
        }
            
        if($r_mrkt != null | $tran != null | $prosp != null){
            $v = $r_mrkt;
            $i = $r_dana != null ? count($r_umum) + 1 :  count($r_umum);
            $coy[$i] =  [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Kunjungan',
                            'target'    => $r_mrkt != null ? $v->kunjungan : 0,
                            'tgl_awal'  => $tgl,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->knj : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            'acc'       => 1,
                            'task'      => ''
                            ];
            $coy[$i+1] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Transaksi diatas minimal',
                            'tgl_awal'  => $tgl,
                            'target'    => $r_mrkt != null ? $v->transaksi : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->tdm : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            'acc'       => 1,
                            'task'      => ''
                            ];
            $coy[$i+2] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Penawaran',
                            'tgl_awal'  => $tgl,
                            'target'    => $r_mrkt != null ? $v->penawaran : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->pnw + $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            'acc'       => 1,
                            'task'      => ''
                            ];
            $coy[$i+3] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Prospek Closing',
                            'tgl_awal'  => $tgl,
                            'target'    => $r_mrkt != null ? $v->closing : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            'acc'       => 1,
                            'task'      => ''
                            ];
        }

        return response()->json($coy);
    }
    
    public function konfirmasi_rencana(Request $request){
        
        $data = Rencana::find($request->id);
        if($request->jenis == 'konfirmasi'){
            
            $data->acc = $request->value;
            $data->alasan_reject = $request->alasan;
            $data->user_update = Auth::user()->id;
            $data->update();
            
            \LogActivity::addToLog(Auth::user()->name . ' '. $request->jaer .' ID Rencana ' . $request->id );
        }else{
            $data->aktif = $request->value;
            $data->user_update = Auth::user()->id;
            $data->alasan = $request->alasan;
            $data->update();
            \LogActivity::addToLog(Auth::user()->name . ' '. $request->jaer .' ID Rencana ' . $request->id );
        }
        
        
        return response()->json(['success' => 'Berhasil!']);   
    }
    
    public function ubah_aktif_rencana(Request $request){
        // return $request;
        
        if($request->tab == 'tt'){
            $ingfo = $request->acc == 0 ? "Menonaktifkan" : "Mengaktifkan";
            $datay = Rencana::find($request->id);
                
                if($datay->durasi == 'range'){
                    $datat = [
                        'aktif' => $request->acc,
                        'user_update' => Auth::user()->id
                    ];
                    Rencana::where('id_range', $datay->id_range)->update($datat);
                }else{
                    $datay->aktif = $request->acc;
                    $datay->user_update = Auth::user()->id;
                    $datay->update();
                }
                
                // return($data);
        \LogActivity::addToLog(Auth::user()->name . ' '. $ingfo .' Rencana untuk ID ' . $request->id );
        return response()->json(['success' => 'Berhasil!']);
        }
        
        $info = $request->acc == 0 ? "Menonaktifkan" : "Mengaktifkan";
        
        $data = Rencana::find($request->id);
                
                if($data->durasi == 'range'){
                    $datat = [
                        'aktif' => $request->acc,
                        'user_update' => Auth::user()->id
                    ];
                    Rencana::where('id_range', $data->id_range)->update($datat);
                }else{
                    $data->aktif = $request->acc;
                    $data->user_update = Auth::user()->id;
                    $data->update();
                }
                
                // return($data);
        \LogActivity::addToLog(Auth::user()->name . ' '. $info .' Rencana untuk ID ' . $request->id );
        return response()->json(['success' => 'Berhasil!']);
    }
    
    public function hapus_rencana(Request $req){
        
        // $data = Rencana::find($request->id);
                
        //     if($data->durasi == 'range'){
        //         Rencana::where('id_range', $data->id_range)->delete();
        //     }else{
        //         $data->delete();
        //     }
        
        // return $req;
        
        $id_ren = $req->id > 0 ? $req->id : 0;
        $ren    = Rencana::findOrFail($id_ren);
            
        if($ren->durasi == 'range'){
            $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
        }
            
        $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
        if($id_lap > 0){
            return response()->json([ "status"=> "GAGAL" ]);
        }else{
            \LogActivity::addToLog(Auth::user()->name.' Menghapus rencana '.$ren->tugas.' untuk '.$ren->id_karyawan);
            if($ren->durasi == 'range'){
                $del_range  = Rencana::where('id_range', $ren->id_range)->delete();
            }else{
                $ren->delete();
            }
                
            return response()->json([ "status"=> "SUKSES" ]);
        }
            
        // \LogActivity::addToLog(Auth::user()->name . ' Menghapus Rencana untuk ID ' . $request->id );
        // return response()->json(['success' => 'Berhasil!']);
    }
    
    public function getDetail(Request $request){
        $rek = $request->tgl == '' ? date('Y-m-01') : $request->tgl.'-01';
        
        $m = date('m', strtotime($rek));
        $y = date('Y', strtotime($rek));
        
        if($request->tab == 'export'){
            if($request->type == 'all'){
                $bulan = $request->tgl == '' ? date('Y-m-01') : $request->tgl.'-01';
                if($request->periode == 'tanggal'){
                    // return 'halo';    
                    $data = RencanaBln::selectRaw("rencana.id, rencana_bln.tugas as rencana_proses, renhas.tugas as rencana_hasil, 
                                renhas.id as id_renhas, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.metode, rencana_bln.satuan, 
                                renhas.target, rencana_thn.tugas as bagian, renhas.target as target_hasil, rencana_bln.target as target_proses,
                                renhas.metode as metode_hasil, rencana_bln.metode as metode_proses, satuan_thn.tugas as satuannya, 
                                (SELECT unit FROM tambahan WHERE id = rencana_bln.id_kantor) as kantor, 
                                (SELECT name FROM users WHERE id_karyawan = rencana.id_karyawan) as namaku
                            ")
                            ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                            ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
                            ->join('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
                            ->join('rencana','rencana_bln.id','=','rencana.id_rb')
                            ->where('rencana_bln.id_kantor', $request->unit)
                            ->whereRaw("DATE(rencana_bln.bulan) = '$rek' AND rencana.marketing = '0'")
                            ->get();
                    
                    $newStructure = [];

                    // Iterate through the data
                    foreach ($data as $entry) {
                        $bagian = $entry['bagian'];
                        $id_renhas = $entry['id_renhas'];
                        $proses = $entry['rencana_proses'];
                        $hasil = $entry['rencana_hasil'];
                        $tgl_awal = $entry['tgl_awal'];
                        $satuan = $entry['satuannya'];
                        $tgl_akhir = $entry['tgl_akhir'];
                        $target_hasil = $entry['target_hasil'];
                        $target_proses = $entry['target_proses'];
                        $m_hasil = $entry['metode_hasil'];
                        $m_proses = $entry['metode_proses'];
                        $kantor = $entry['kantor'];
                        $namaku = $entry['namaku'];
                        
                        if (!isset($newStructure[$kantor])) {
                            $newStructure[$kantor] = [];
                        }
                        
                        if (!isset($newStructure[$kantor][$namaku])) {
                            $newStructure[$kantor][$namaku] = [];
                        }
                        
                        if (!isset($newStructure[$kantor][$namaku][$bagian])) {
                            $newStructure[$kantor][$namaku][$bagian] = [];
                        }
                        
                        if (!isset($newStructure[$kantor][$namaku][$bagian][$id_renhas])) {
                            $newStructure[$kantor][$namaku][$bagian][$id_renhas] = ['proses' => [], 'hasil' => []];
                        }
                    
                        // Update process data
                        if (!isset($newStructure[$kantor][$namaku][$bagian][$id_renhas]['proses'][$proses])) {
                            $newStructure[$kantor][$namaku][$bagian][$id_renhas]['proses'][$proses] = [
                                'start_date' => date('d-m-Y', strtotime($tgl_awal)),
                                'end_date' =>  date('d-m-Y', strtotime($tgl_akhir)),
                                'target' => $target_proses,
                                'metode' => $m_proses,
                                'satuan' => $satuan
                            ];
                        } else {
                            // Update end date if current end date is later
                            $current_end_date = $newStructure[$kantor][$namaku][$bagian][$id_renhas]['proses'][$proses]['end_date'];
                            if ($tgl_akhir > $current_end_date) {
                                $newStructure[$kantor][$namaku][$bagian][$id_renhas]['proses'][$proses]['end_date'] = date('d-m-Y', strtotime($tgl_akhir));
                            }
                        }
                    
                        // Update result data
                        if (!isset($newStructure[$kantor][$namaku][$bagian][$id_renhas]['hasil'][$hasil])) {
                            $newStructure[$kantor][$namaku][$bagian][$id_renhas]['hasil'][$hasil] = [
                                'start_date' => date('m Y', strtotime($tgl_awal)),
                                'end_date' => date('m Y', strtotime($tgl_akhir)),
                                'target' => $target_hasil,
                                'metode' => $m_hasil,
                                'satuan' => $satuan
                            ];
                        } else {
                            // Update end date if current end date is later
                            $current_end_date = $newStructure[$kantor][$namaku][$bagian][$id_renhas]['hasil'][$hasil]['end_date'];
                            if ($tgl_akhir > $current_end_date) {
                                $newStructure[$kantor][$namaku][$bagian][$id_renhas]['hasil'][$hasil]['end_date'] = date('m Y', strtotime($tgl_akhir));
                            }
                        }
                    }
                    
                    $kantornya = Kantor::where('id', $request->unit)->first()->unit;
                    
                    $response = Excel::download(new DetailAllKarRencanaExport($newStructure, $bulan, $kantornya), 'Detail Rencana Semua Karyawan Unit '.$kantornya.' Bulan '.date('m-Y', strtotime($bulan)).'.xlsx');
                    ob_end_clean();
                            
                    return $response;
                }else{
                    $data = RencanaBln::selectRaw("rencana_bln.*, rencana_thn.tugas as bagian, renhas.tugas as rencana_hasil, satuan_thn.tugas as satuannya")
                            ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
                            ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                            ->leftjoin('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
                            ->whereRaw("DATE(rencana_bln.bulan) = '$rek'")
                            ->orderBy('rencana_bln.id_kantor','ASC')
                            ->get();
                            
                    $output = [];
                            
                    $parentChildMapping = [];
            
                        // First, initialize parents and children
                    foreach ($data as $item) {
                        // $parentChildMapping[$item->id_kantor] = [];
                        $kantorr = Kantor::where('id', $item->id_kantor)->first()->unit;
                        if ($item->id_hasil === null) {
                            $parentChildMapping[$kantorr][$item->bagian][$item->id] = [
                                'hasil' => $item,
                                'proses' => []
                            ];
                        }
                    }
                        
                        // Then, add children to their respective parents
                    foreach ($data as $item) {
                        $kantorr = Kantor::where('id', $item->id_kantor)->first()->unit;
                        if ($item->id_hasil !== null) {
                            if (isset($parentChildMapping[$kantorr][$item->bagian][$item->id_hasil])) {
                                $parentChildMapping[$kantorr][$item->bagian][$item->id_hasil]['proses'][] = $item;
                            }
                        }
                    }
                    
                    $output = []; 
                    
                    foreach ($parentChildMapping as $index => $tasks) {
                        foreach ($tasks as $index2 => $taskDetails) {
                            foreach ($taskDetails as $index3 => $aw) {
                                $output[$index][$index2][$index3] = [
                                    'proses' => array_map(function($proses) {
                                        return [
                                            $proses['tugas'] => [
                                                'bulan' => date('m Y', strtotime($proses['bulan'])),
                                                'metode' => $proses['metode'],
                                                'target' => $proses['target'],
                                                'satuan' => $proses['satuannya'],
                                                'selesai' => date('m Y', strtotime($proses['bulan_akhir'])),
                                            ]
                                        ];
                                    }, $aw['proses']),
                                    'hasil' => [
                                        $aw['hasil']['tugas'] => [
                                            'bulan' => date('m Y', strtotime($aw['hasil']['bulan'])),
                                            'metode' => $aw['hasil']['metode'],
                                            'target' => $aw['hasil']['target'],
                                            'satuan' => $aw['hasil']['satuannya'],
                                            'selesai' => date('m Y', strtotime($aw['hasil']['bulan_akhir'])),
                                        ]
                                    ]
                                ];
                            }
                        }
                    }
                    
                    $response = Excel::download(new DetailAllRencanaExport($output, $bulan), 'Detail Rencana Semua Unit Bulan '.date('m-Y', strtotime($bulan)).'.xlsx');
                    ob_end_clean();
                            
                    return $response;
                }
            }else{
                    $bulan = $request->tgl == '' ? date('Y-m-01') : $request->tgl.'-01';
                    if($request->periode == 'tanggal'){
                        $user = User::where('id_karyawan', $request->id)->first()->name;
                        $data = RencanaBln::selectRaw("rencana.id, rencana_bln.tugas as rencana_proses, renhas.tugas as rencana_hasil, 
                                    renhas.id as id_renhas, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.metode, rencana_bln.satuan, 
                                    renhas.target, rencana_thn.tugas as bagian, renhas.target as target_hasil, rencana_bln.target as target_proses,
                                    renhas.metode as metode_hasil, rencana_bln.metode as metode_proses, satuan_thn.tugas as satuannya
                                ")
                                ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                                ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
                                ->join('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
                                ->join('rencana','rencana_bln.id','=','rencana.id_rb')
                                ->where('rencana.id_karyawan', $request->id)
                                ->whereRaw("DATE(rencana_bln.bulan) = '$rek' AND rencana.marketing = '0'")
                                ->get();
                        
                        // return $data;
                                
                        $newStructure = [];
                
                        // Iterate through the data
                        foreach ($data as $entry) {
                            $bagian = $entry['bagian'];
                            $id_renhas = $entry['id_renhas'];
                            $proses = $entry['rencana_proses'];
                            $hasil = $entry['rencana_hasil'];
                            $tgl_awal = $entry['tgl_awal'];
                            $satuan = $entry['satuannya'];
                            $tgl_akhir = $entry['tgl_akhir'];
                            $target_hasil = $entry['target_hasil'];
                            $target_proses = $entry['target_proses'];
                            $m_hasil = $entry['metode_hasil'];
                            $m_proses = $entry['metode_proses'];
                        
                            // Initialize if not set
                            if (!isset($newStructure[$bagian])) {
                                $newStructure[$bagian] = [];
                            }
                            if (!isset($newStructure[$bagian][$id_renhas])) {
                                $newStructure[$bagian][$id_renhas] = ['proses' => [], 'hasil' => []];
                            }
                        
                            // Update process data
                            if (!isset($newStructure[$bagian][$id_renhas]['proses'][$proses])) {
                                $newStructure[$bagian][$id_renhas]['proses'][$proses] = [
                                    'start_date' => date('d-m-Y', strtotime($tgl_awal)),
                                    'end_date' =>  date('d-m-Y', strtotime($tgl_akhir)),
                                    'target' => $target_proses,
                                    'metode' => $m_proses,
                                    'satuan' => $satuan
                                ];
                            } else {
                                // Update end date if current end date is later
                                $current_end_date = $newStructure[$bagian][$id_renhas]['proses'][$proses]['end_date'];
                                if ($tgl_akhir > $current_end_date) {
                                    $newStructure[$bagian][$id_renhas]['proses'][$proses]['end_date'] = $tgl_akhir;
                                }
                            }
                        
                            // Update result data
                            if (!isset($newStructure[$bagian][$id_renhas]['hasil'][$hasil])) {
                                $newStructure[$bagian][$id_renhas]['hasil'][$hasil] = [
                                    'start_date' => date('m Y', strtotime($tgl_awal)),
                                    'end_date' => date('m Y', strtotime($tgl_akhir)),
                                    'target' => $target_hasil,
                                    'metode' => $m_hasil,
                                    'satuan' => $satuan
                                ];
                            } else {
                                // Update end date if current end date is later
                                $current_end_date = $newStructure[$bagian][$id_renhas]['hasil'][$hasil]['end_date'];
                                if ($tgl_akhir > $current_end_date) {
                                    $newStructure[$bagian][$id_renhas]['hasil'][$hasil]['end_date'] = date('m Y', strtotime($tgl_akhir));
                                }
                            }
                        }
                        
                        if($request->ex == 'pdf'){
                            $pdf = PDF::loadView('rencanapdf', ['data' => $newStructure, 'user' => $user, 'bulan' => $bulan, 'ahha' => 'karyawan'])->setPaper('a4', 'landscape');
                            return $pdf->stream('Detail Rencana '.$user.' Bulan'.date('m-Y', strtotime($bulan)).'.pdf');
                        }else{
                            $ahha = 'karyawan';
                            $response = Excel::download(new DetailRencanaExport($newStructure, $user, $bulan, $ahha), 'Detail Rencana '.$user.' Bulan '.date('m-Y', strtotime($bulan)).'.xlsx');
                            ob_end_clean();
                            
                            return $response;
                        }
                        
                    }else{
                        
                        $user = Kantor::where('id', $request->id)->first()->unit;
                        $data = RencanaBln::selectRaw("rencana_bln.*, rencana_thn.tugas as bagian, renhas.tugas as rencana_hasil, satuan_thn.tugas as satuannya")
                            ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
                            ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                            ->leftjoin('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
                            ->where('rencana_bln.id_kantor', $request->id)
                            ->whereRaw("DATE(rencana_bln.bulan) = '$rek'")
                            ->get();
                        
                        $parentChildMapping = [];
            
                        // First, initialize parents and children
                        foreach ($data as $item) {
                            if ($item->id_hasil === null) {
                                $parentChildMapping[$item->id] = [
                                    'hasil' =>$item,
                                    'proses' => []
                                ];
                            }
                        }
                        
                        // Then, add children to their respective parents
                        foreach ($data as $item) {
                            if ($item->id_hasil !== null) {
                                if (isset($parentChildMapping[$item->id_hasil])) {
                                    $parentChildMapping[$item->id_hasil]['proses'][] = $item;
                                }
                            }
                        }
                        
                        
                        $transformedData = [];
            
                        // Process each item in the data
                        foreach ($parentChildMapping as $id => $item) {
                            $bagian = $item['hasil']['bagian'];
                            
                            if (!isset($transformedData[$bagian])) {
                                $transformedData[$bagian] = [];
                            }
                        
                            // Add 'hasil' data
                            $transformedData[$bagian][$id]['hasil'][$item['hasil']['tugas']] = [
                                // 'tugas' => $item['hasil']['tugas'],
                                'bulan' => date('m Y', strtotime($item['hasil']['bulan'])),
                                'selesai' => date('m Y', strtotime($item['hasil']['bulan_akhir'])),
                                'target' => $item['hasil']['target'],
                                'satuan' => $item['hasil']['satuannya'],
                                'metode' => $item['hasil']['metode'],
                            ];
                        
                            // Add 'proses' data
                            foreach ($item['proses'] as $proses) {
                                $transformedData[$bagian][$id]['proses'][$proses['tugas']][] = [
                                    // 'tugas' => $proses['tugas'],
                                    'bulan' => date('m Y', strtotime($proses['bulan'])),
                                    'selesai' => date('m Y', strtotime($proses['bulan_akhir'])),
                                    'target' => $proses['target'],
                                    'satuan' => $proses['satuannya'],
                                    'metode' => $proses['metode'],
                                ];
                            }
                        }
                        
                        if($request->ex == 'pdf'){
                            $pdf = PDF::loadView('rencanapdf', ['data' => $transformedData, 'user' => $user, 'bulan' => $bulan, 'ahha' => 'kantor'])->setPaper('a4', 'landscape');
                            return $pdf->stream('Detail Rencana '.$user.' Bulan'.date('m-Y', strtotime($bulan)).'.pdf');
                        }else{
                            $ahha = 'kantor';
                            $response = Excel::download(new DetailRencanaExport($transformedData, $user, $bulan, $ahha), 'Detail Rencana '.$user.' Bulan '.date('m-Y', strtotime($bulan)).'.xlsx');
                            ob_end_clean();
                            
                            return $response;
                        }
                    }
                // }
                // else{
                //     $response = Excel::download(new DetailRencanaExport($request, $tgltext1, $blntext1, $thntext1), 'Detail Rencana '.$user.' Bulan'.date('m-Y', strtotime($bulan)).'.xlsx');
                //     ob_end_clean();
                    
                //     return $response;
                // }
            }
            
        }
        
        if($request->tab == 'tab1'){
            $data = RencanaBln::selectRaw("rencana_bln.*, rencana_thn.tugas as bagian, renhas.tugas as rencana_hasil, satuan_thn.tugas as satuannya")
                ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
                ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
                ->leftjoin('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
                ->where('rencana_bln.id_kantor', $request->id)
                ->whereRaw("DATE(rencana_bln.bulan) = '$rek'")
                ->get();
            
            $parentChildMapping = [];

            // First, initialize parents and children
            foreach ($data as $item) {
                if ($item->id_hasil === null) {
                    $parentChildMapping[$item->id] = [
                        'hasil' =>$item,
                        'proses' => []
                    ];
                }
            }
            
            // Then, add children to their respective parents
            foreach ($data as $item) {
                if ($item->id_hasil !== null) {
                    if (isset($parentChildMapping[$item->id_hasil])) {
                        $parentChildMapping[$item->id_hasil]['proses'][] = $item;
                    }
                }
            }
            
            
            $transformedData = [];

            // Process each item in the data
            foreach ($parentChildMapping as $id => $item) {
                $bagian = $item['hasil']['bagian'];
                
                if (!isset($transformedData[$bagian])) {
                    $transformedData[$bagian] = [];
                }
            
                // Add 'hasil' data
                $transformedData[$bagian][$id]['hasil'][$item['hasil']['tugas']] = [
                    // 'tugas' => $item['hasil']['tugas'],
                    'bulan' => date('m Y', strtotime($item['hasil']['bulan'])),
                    'selesai' => date('m Y', strtotime($item['hasil']['bulan_akhir'])),
                    'target' => $item['hasil']['target'],
                    'satuan' => $item['hasil']['satuannya'],
                    'metode' => $item['hasil']['metode'],
                ];
            
                // Add 'proses' data
                foreach ($item['proses'] as $proses) {
                    $transformedData[$bagian][$id]['proses'][$proses['tugas']][] = [
                        // 'tugas' => $proses['tugas'],
                        'bulan' => date('m Y', strtotime($proses['bulan'])),
                        'selesai' => date('m Y', strtotime($proses['bulan_akhir'])),
                        'target' => $proses['target'],
                        'satuan' => $proses['satuannya'],
                        'metode' => $proses['metode'],
                    ];
                }
            }
            
            return $transformedData;
            
            
        }
        
        // $data = RencanaBln::selectRaw("rencana.id, rencana_bln.tugas as rencana_proses, renhas.tugas as rencana_hasil, 
        //             renhas.id as id_renhas, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.metode, rencana_bln.satuan, 
        //             rencana.target, rencana_thn.tugas as bagian, renhas.target as target_hasil, rencana_bln.target as target_proses,
        //             renhas.metode as metode_hasil, rencana_bln.metode as metode_proses, satuan_thn.tugas as satuannya
        //         ")
        //         ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
        //         ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
        //         ->join('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
        //         ->join('rencana','rencana_bln.id','=','rencana.id_rb')
        //         ->where('rencana.id_karyawan', $request->id)
        //         ->whereRaw("DATE(rencana_bln.bulan) = '$rek' AND rencana.marketing = '0'")
        //         ->get();
        $data = Rencana::selectRaw("rencana.id_rb,rencana.id, rencana.id_range, rencana.tugas, (SELECT tugas FROM rencana_thn WHERE rencana_bln.id_rt = rencana_thn.id) as bagians, rencana.target, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.tugas as parent, (SELECT tugas FROM rencana_thn WHERE rencana_bln.satuan = rencana_thn.id) as satuan, rencana_bln.metode")
                ->join('rencana_bln','rencana_bln.id','=','rencana.id_rb')
                ->whereRaw("MONTH(rencana.tgl_awal) = '$m' AND YEAR(rencana.tgl_awal) = '$y' AND rencana.marketing = '0' AND rencana.id_karyawan = '$request->id'")
                ->get();
        
        $summedTargets = [];

        // Loop through each item in the data
        foreach ($data as $item) {
            $idd = $request->id;
            $id_rb = $item['id_rb'];
            $id_range = $item['id_range'];
            $target = $item['target'];
            $tugas = $item['tugas'];
            $satuan = $item['satuan'];
            $metode = $item['metode'];
            $tgl = $item['tgl_awal'];
            $tgl_akhir = $item['tgl_akhir'];
            $bagians = $item['bagians'];
        
            // Initialize the id_rb in summedTargets if it doesn't exist
            if (!isset($summedTargets[$id_rb])) {
                $summedTargets[$id_rb] = [
                    'target' => 0,
                    'id_ranges' => []
                ];
            }
        
            // Check if id_range is already counted for this id_rb
            if ($id_range === null || !in_array($id_range, $summedTargets[$id_rb]['id_ranges'])) {
                // Add the target to the sum for this id_rb
                $summedTargets[$id_rb]['target'] += $target;
        
                // If id_range is set, add it to the list to avoid duplicates
                if ($id_range !== null) {
                    $summedTargets[$id_rb]['id_ranges'][] = $id_range;
                }
                
            }
            
            $summedTargets[$id_rb]['tugas'] = $tugas;
            $summedTargets[$id_rb]['satuan'] = $satuan;
            $summedTargets[$id_rb]['metode'] = $metode;
            $summedTargets[$id_rb]['bagians'] = $bagians;
            $summedTargets[$id_rb]['tanggal'] = date('m Y', strtotime($tgl));
            $summedTargets[$id_rb]['tanggall'] = date('Y-m', strtotime($tgl_akhir));
            $summedTargets[$id_rb]['id_kar'] = $idd;
            
            
            // $summedTargets[$id_rb]['hasil'] = [
            //     'tugas' => $tugas,
            //     'target' =>  $summedTargets[$id_rb]['sum']
            // ];
            
            // $summedTargets['proses'][] = [
            //     $item
            // ];
        }
        
        $real = [];
        
        foreach($summedTargets as $id => $data){
            
            
            $real[$id]['hasil'] = $data;
            
            // return $data;
            $id_kar = $data['id_kar'];
            $datak = Rencana::selectRaw("rencana.tugas, DATE_FORMAT(rencana.tgl_awal, '%d %m %Y') as tgl_awal, rencana.tgl_akhir, rencana.target")
                                ->whereRaw("id_karyawan = '$id_kar'")
                                ->where('id_rb', $id)->groupBy('id_range')
                                ->get();
            
            $real[$id]['proses'] = $datak;
        }
        
        return $real;
        
        // return $summedTargets;
        // $updatedData = [];
        // foreach ($data as $item) {
        //     $idRange = $item['id_range'];
        
        //     // Jika ada id_range, gabungkan tgl_awal dan tgl_akhir
        //     if ($idRange !== null) {
        //         // Jika sudah ada id_range yang sama, gabungkan tanggalnya
        //         if (isset($updatedData[$idRange])) {
        //             $updatedData[$idRange]['tgl_awal'] = min($updatedData[$idRange]['tgl_awal'], $item['tgl_awal']);
        //             $updatedData[$idRange]['tgl_akhir'] = max($updatedData[$idRange]['tgl_akhir'], $item['tgl_akhir']);
        //         } else {
        //             // Jika belum ada, masukkan data baru
        //             $updatedData[$idRange] = $item;
        //         }
        //     } else {
        //         // Jika id_range null, simpan data seperti biasa
        //         $updatedData[] = $item;
        //     }
        // }
        
        // // Convert the updated data into an array of items to print
        // $finalData = array_values($updatedData);
        
        // return $finalData;
                
        // $newStructure = [];

        // // Iterate through the data
        // foreach ($data as $entry) {
        //     $bagian = $entry['bagian'];
        //     $id_renhas = $entry['id_renhas'];
        //     $proses = $entry['rencana_proses'];
        //     $hasil = $entry['rencana_hasil'];
        //     $tgl_awal = $entry['tgl_awal'];
        //     $satuan = $entry['satuannya'];
        //     $tgl_akhir = $entry['tgl_akhir'];
        //     $target_hasil = $entry['target_hasil'];
        //     $target_proses = $entry['target_proses'];
        //     $m_hasil = $entry['metode_hasil'];
        //     $m_proses = $entry['metode_proses'];
        
        //     // Initialize if not set
        //     if (!isset($newStructure[$bagian])) {
        //         $newStructure[$bagian] = [];
        //     }
        //     if (!isset($newStructure[$bagian][$id_renhas])) {
        //         $newStructure[$bagian][$id_renhas] = ['proses' => [], 'hasil' => []];
        //     }
        
        //     // Update process data
        //     if (!isset($newStructure[$bagian][$id_renhas]['proses'][$proses])) {
        //         $newStructure[$bagian][$id_renhas]['proses'][$proses] = [
        //             'start_date' => date('d-m-Y', strtotime($tgl_awal)),
        //             'end_date' =>  date('d-m-Y', strtotime($tgl_akhir)),
        //             'target' => $target_proses,
        //             'metode' => $m_proses,
        //             'satuan' => $satuan
        //         ];
        //     } else {
        //         // Update end date if current end date is later
        //         $current_end_date = $newStructure[$bagian][$id_renhas]['proses'][$proses]['end_date'];
        //         if ($tgl_akhir > $current_end_date) {
        //             $newStructure[$bagian][$id_renhas]['proses'][$proses]['end_date'] = $tgl_akhir;
        //         }
        //     }
        
        //     // Update result data
        //     if (!isset($newStructure[$bagian][$id_renhas]['hasil'][$hasil])) {
        //         $newStructure[$bagian][$id_renhas]['hasil'][$hasil] = [
        //             'start_date' => date('m Y', strtotime($tgl_awal)),
        //             'end_date' => date('m Y', strtotime($tgl_akhir)),
        //             'target' => $target_hasil,
        //             'metode' => $m_hasil,
        //             'satuan' => $satuan
        //         ];
        //     } else {
        //         // Update end date if current end date is later
        //         $current_end_date = $newStructure[$bagian][$id_renhas]['hasil'][$hasil]['end_date'];
        //         if ($tgl_akhir > $current_end_date) {
        //             $newStructure[$bagian][$id_renhas]['hasil'][$hasil]['end_date'] = date('m Y', strtotime($tgl_akhir));
        //         }
        //     }
        // }
        // return $newStructure;
    }
    
    public function getBytanggal (Request $request){
        $rek = $request->tgl == '' ? date('Y-m') : $request->tgl;
        // lama
        // $data = Rencana::selectRaw("rencana.*, users.name, rencana_bln.id as id_rb, rencana_bln.tugas as parent_rencana")->join('users','users.id','=','rencana.user_insert')->join('rencana_bln','rencana.id_rb','=','rencana_bln.id')->where('rencana.id_karyawan', $request->id)->whereRaw("DATE_FORMAT(tgl_awal, '%Y-%m') = '$rek' AND rencana.marketing = '0'")->get();
        // baru
        
        $data = Rencana::selectRaw("rencana.aktif, rencana.id, rencana.durasi, rencana.id_range, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.tugas as tugas, rencana.capaian, users.name, rencana_bln.id as id_rb, rencana_bln.tugas as parent_rencana")
            ->join('rencana_bln','rencana_bln.id','=','rencana.id_rb')
            ->join('rencana_thn','rencana_bln.satuan','=','rencana_thn.id')
            ->join('users','users.id','=','rencana.user_insert')
            ->where('rencana.id_karyawan', $request->id)
            ->whereRaw("DATE_FORMAT(tgl_awal, '%Y-%m') = '$rek' AND rencana.marketing = '0'")
            ->get();
        $ko = [];
        
        // if(Auth::user()->name == 'Management'){
            
            $awww = [];
            $hasil = [];
            $groups = [];
            
            // Loop pertama untuk memproses data 'daily' dan mengumpulkan id_range
            foreach($data as $day) {
                if($day->durasi == 'daily') {
                    $hasil[] = [
                        'color' => $day->color == null ? '#8196e9' : $day->color,
                        'aktif' => $day->aktif,
                        'id' => $day->id,
                        'title' => $day->tugas,
                        'start' => $day->tgl_awal.'T23:59:00',
                        'end' => null
                    ];
                } else {
                    if ($day['id_range'] == $day['id']) {
                        $awww[] = $day['id_range'];
                    }
                }
            }
            
            // Hanya lakukan query jika ada id_range yang dikumpulkan
            if (!empty($awww)) {
                $coba = Rencana::whereIn('id_range', $awww)
                        ->orderBy('id_range','asc')
                        ->orderBy('tgl_awal','asc')
                        ->get();
            
                $separatedData = [];
                foreach ($coba as $item) {
                    $separatedData[$item['id_range']][] = $item;
                }
            
                foreach ($separatedData as $key => $dates) {
                    $start = $dates[0]->tgl_awal;
                    $end = $dates[0]->tgl_awal;
                    $sissh = Rencana::selectRaw('rencana_bln.tugas, rencana.color, rencana.aktif, rencana.id, rencana.tgl_awal, rencana.tgl_akhir')->join('rencana_bln','rencana_bln.id','=','rencana.id_rb')->where('rencana.id', $key)->first();
            
                    for ($i = 1; $i < count($dates); $i++) {
                        $currentDate = $dates[$i]->tgl_awal;
                        $previousDate = date('Y-m-d', strtotime($end . ' +1 day'));
            
                        if ($currentDate === $previousDate) {
                            $end = $currentDate;
                        } else {
                            $groups[] = [
                                'start' => $start.'T23:59:00',
                                'end' => $end.'T23:59:00',
                                'color' => $sissh->color == null ? '8196e9' : $sissh->color,
                                'aktif' => $sissh->aktif,
                                'id' => $sissh->id,
                                'title' => $sissh->tugas
                            ];
                            $start = $currentDate;
                            $end = $currentDate;
                        }
                    }
            
                    $groups[] = [
                        'start' => $start.'T23:59:00',
                        'end' => $end.'T23:59:00',
                        'color' => $sissh->color == null ? '8196e9' : $sissh->color,
                        'aktif' => $sissh->aktif,
                        'id' => $sissh->id,
                        'title' => $sissh->tugas
                    ];
                }
            }
            
            $result = array_merge($hasil, $groups);
            return response()->json($result);
        // }else{
        //     foreach($data as $day){
        //         if($day->durasi == 'daily'){
        //             $color = $day->aktif == 0 ? '#a9a9a9' : 'text-primary';
        //             $aktif = $day->aktif;
        //             $id = $day->id;
        //             $tugas = $day->tugas;
        //             $tgl_awal = $day->tgl_awal;
        //             $tgl_akhir = null;
        //         }else{
                    
        //             $coba = Rencana::where('id_range', $day->id_range)->orderBy('tgl_awal','asc')->first();
                    
        //             if($day->id == $coba->id_range){
        //                 $color = $coba->aktif == 0 ? '#a9a9a9' : 'text-primary';
        //                 $aktif = $coba->aktif;
        //                 $id = $coba->id;
        //                 $tugas = $coba->tugas;
        //                 $tgl_awal = $coba->tgl_awal;
        //                 $tgl_akhir = $coba->tgl_akhir;
        //             }else{
        //                 $color = null;
        //                 $aktif = null;
        //                 $id= null;
        //                 $tugas = null;
        //                 $tgl_awal = null;
        //                 $tgl_akhir = null;
        //             }
                    
        //             // $color = $day->aktif == 0 ? '#a9a9a9' : 'text-primary';
        //             // $aktif = $day->aktif;
        //             // $id = $day->id;
        //             // $tugas = $day->tugas;
        //             // $tgl_awal = $day->tgl_awal;
        //             // $tgl_akhir = $day->tgl_akhir;
                    
        //         }
                
        //         $ko[] = [
        //             'color' => $color,
        //             'aktif' => $aktif,
        //             'id' => $id,
        //             'title' => $tugas,
        //             // 'daysOfWeek' => [1, 2, 3, 4, 5],
        //             'start' => $tgl_awal,
        //             'end' => $tgl_akhir
        //         ];                
        //     }
            
        //     return response()->json($ko);   
        // }
    }
    
    public function get_rencana_id (Request $request){
        
        // return($request); 
        $data = Rencana::select('rencana.*','rencana_bln.target as target_kita', 'rencana_thn.rumus','rencana_thn.tugas as satuann', 'rencana_bln.tugas as tugasnya')->join('rencana_bln','rencana_bln.id','=','rencana.id_rb')->join('rencana_thn','rencana_thn.id','=','rencana_bln.satuan')->where('rencana.id', $request->id_p)->first();
        // if($data->durasi == 'range'){
        //     $data = Rencana::where('id_range', $data->id_range)->orderBy('tgl_awal','asc')->first();
        // }
        $itung = Rencana::whereRaw("rencana.id_rb = $data->id_rb AND rencana.tgl_awal = rencana.tgl_akhir AND rencana.aktif = 1")->sum('target');
        $data['sisa']  = $data->target_kita - $itung;
        
        // return $itung;
        
        return response()->json($data);
    }
    
    public function tambah_rencana(Request $req)
    {
        // return $req;
        
        // if($request->durasi == 'daily'){
        //     $dari = $request->tgl_awal;
        //     $sampai = $request->tgl_awal;
        // }else{
        //     $dari = $request->tgl_awal;
        //     $sampai = $request->tgl_akhir;
        // }
        
        // $tanggalAwal = new DateTime($dari);

        // $tanggalAkhir = new DateTime($sampai);
        
        // $selisihHari = 0;
        
        // $dateList = [];
        
        // while ($tanggalAwal <= $tanggalAkhir) {
        //     $selisihHari++;
        //     $dateList[] = $tanggalAwal->format('Y-m-d');
        //     $tanggalAwal->modify('+1 day');
        // }
        
        // $data = [];
        
        // $created = date('Y-m-d H:i:s');
        // for($i = 0; $i < $selisihHari; $i++){
        
        //     if($i > 0){    
        //         $last = Rencana::where('created_at', $created)->where('id_karyawan', $request->id)->first()->id;
        //         if($i == 1){
        //             $upren = Rencana::where('id', $last)->update(['id_range' => $last]);
        //         }
        //     }
            
        //     Rencana::create(
        //         $data[$i] = [
        //             'id_karyawan' => $request->id,
        //             'id_rb' => $request->tugas_bl,
        //             'tugas' => $request->tugas,
        //             'target' => $request->target,
        //             'durasi' => $request->durasi,
        //             'tgl_awal' =>  $dateList[$i],
        //             'tgl_akhir' =>  $sampai,
        //             'user_insert' => Auth::user()->id,
        //             'marketing' => 0,
        //             'id_range' => $request->durasi == 'daily' ? 0 : ($i > 0 ? $last : 0),
        //             'created_at' => $i > 0 ? $created : date('Y-m-d H:i:s')
        //         ]
        //     );
        // }
        
        
        if($req->durasi == 'daily'){
            $dari = $req->tgl_awal == '' ? date('Y-m-d') : $req->tgl_awal;
            $sampai = $dari;
        }else{
            $datey = explode(",", $req->daterangeVal);
            $itung = count($datey);
            $dari = $datey[0];
            $sampai = $datey[$itung-1];
            
        }
        
        // return [$dari, $sampai];
        
        $tanggalAwal = new DateTime($dari);

        $tanggalAkhir = new DateTime($sampai);
        
        $selisihHari = 0;
        $dateList = [];
        
        while ($tanggalAwal <= $tanggalAkhir) {
            $selisihHari++;
            $dateList[] = $tanggalAwal->format('Y-m-d');
            $tanggalAwal->modify('+1 day');
        }
            
        $durasi = $req->durasi;
            
        $last = 0; 
        
        if($req->durasi == 'daily'){
            $itungs = $selisihHari;
            $list = $dateList;
        }else{
            $itungs = $itung; 
            $list = $datey;
        }
        
        for($i = 0; $i < $itungs; $i++){
            if($durasi == 'range' && $list[$i] == $dari){
                $get_idr = Rencana::insertGetId([
                                'id_karyawan'   => $req->id,
                                'id_rb'         => $req->tugas_bl,
                                'tugas'         => $req->tugas,
                                'durasi'        => $durasi,
                                'tgl_awal'      => $list[$i],
                                'tgl_akhir'     => $sampai,
                                'color'         => $req->warna,
                                'target'        => $req->target,
                                // 'bobot'         => $req->bobot,
                                'user_insert'   => Auth::user()->id,
                                'marketing'     => 0,
                                'id_range'      => null,
                            ]);
                $last = $get_idr;
            }else{
                if($durasi == 'range' && $i == 1){
                    Rencana::where('id', $last)->update(['id_range' => $last]);
                }
                Rencana::create([
                                'id_karyawan'   => $req->id,
                                'id_rb'         => $req->tugas_bl,
                                'tugas'         => $req->tugas,
                                'durasi'        => $durasi,
                                'tgl_awal'      => $list[$i],
                                'tgl_akhir'     => $sampai,
                                'color'         => $req->warna,
                                'target'        => $req->target,
                                // 'bobot'         => $req->bobot,
                                'user_insert'   => Auth::user()->id,
                                'marketing'     => 0,
                                'id_range'      => $durasi == 'daily' ? null : $last,
                            ]);
            }
        }
            
        // \LogActivity::addToLog(Auth::user()->name . ' Membuat Rencana untuk ID' . Auth::user()->id_karyawan);
        
        
        \LogActivity::addToLog(Auth::user()->name . ' Membuat Rencana untuk ID' . $req->id);
        return response()->json(['success' => 'Berhasil!']);
        
    }
    
    public function edit_rencana(Request $req)
    {
        $id_ren = $req->id > 0 ? $req->id : 0;
        $ren    = Rencana::findOrFail($id_ren);
        $user   = Auth::user()->id;
            
        if($ren->durasi == 'range'){
            $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
        }
            
        $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
        if($id_lap > 0){
            return response()->json(
                [
                    "status"=> "GAGAL",
                ]
            );
        }else{
            $dari = date('Y-m-d', strtotime($req->tgl_awal));
            
            if($req->durasi == 'daily'){
                $sampai = $dari;
            }else{
                $sampai = date('Y-m-d', strtotime($req->tgl_akhir)) <= $dari ? $dari : date('Y-m-d', strtotime($req->tgl_akhir));
            }
            
            // $durasi = $dari == $sampai ? 'daily' : 'range';
            
            $tanggalAwal = new DateTime($dari);
            $tanggalAkhir = new DateTime($sampai);
            
            $selisihHari = 0;
            $dateList = [];
            
            while ($tanggalAwal <= $tanggalAkhir) {
                $selisihHari++;
                $dateList[] = $tanggalAwal->format('Y-m-d');
                $tanggalAwal->modify('+1 day'); 
            }
                
            $last = 0;
            
            if($req->durasi == 'range'){
                if($ren->durasi == 'range'){
                    $range = Rencana::where('id_range', $ren->id_range)->get();
                }else{
                    $range = Rencana::where('id', $ren->id)->get();
                }
                
                if(count($range) > $selisihHari){
                    Rencana::where('id_range', $ren->id_range)->where('id', '>', $range[$selisihHari-1]['id'])->delete();
                }
                
                for($i = 0; $i < $selisihHari; $i++){
                    if(count($range)-1 >= $i){
                        $upren  = Rencana::findOrFail($range[$i]['id'])
                                ->update([
                                            'id_rb'         => $req->id_proses,
                                            'tugas'         => $req->nama,
                                            'durasi'        => $req->durasi,
                                            'tgl_awal'      => $dateList[$i],
                                            'tgl_akhir'     => $sampai,
                                            'target'        => $req->target,
                                            'user_update'   => Auth::user()->id,
                                            'id_range'      => $range[0]['id'],
                                        ]);
                        
                    }else if($i > (count($range)-1)){
                        Rencana::create([
                                        'id_karyawan'   => Auth::user()->id_karyawan,
                                        'id_rb'         => $req->id_proses,
                                        'tugas'         => $req->nama,
                                        'durasi'        => $req->durasi,
                                        'tgl_awal'      => $dateList[$i],
                                        'tgl_akhir'     => $sampai,
                                        'target'        => $req->target,
                                        'user_insert'   => Auth::user()->id,
                                        'marketing'     => 0,
                                        'id_range'      => $range[0]['id'],
                                    ]);
                    }
                }
                
            }else{
                if($ren->durasi == 'range'){
                    $range = Rencana::where('id_range', $ren->id_range)->first();
                    
                    Rencana::where('id_range', $ren->id_range)->where('id', '>', $range->id)->delete();
                    
                    $upren  = Rencana::findOrFail($range->id)
                            ->update([
                                        'id_rb'         => $req->id_proses,
                                        'tugas'         => $req->nama,
                                        'durasi'        => $req->durasi,
                                        'tgl_awal'      => $dari,
                                        'tgl_akhir'     => $sampai,
                                        'target'        => $req->target,
                                        'user_update'   => Auth::user()->id,
                                        'id_range'      => NULL,
                                    ]);
                }else{
                    $upren  = Rencana::findOrFail($id_ren)
                            ->update([
                                        'id_rb'         => $req->id_proses,
                                        'tugas'         => $req->nama,
                                        'durasi'        => $req->durasi,
                                        'tgl_awal'      => $dari,
                                        'tgl_akhir'     => $sampai,
                                        'target'        => $req->target,
                                        'user_update'   => Auth::user()->id,
                                    ]);
                }
            }
                
            \LogActivity::addToLog(Auth::user()->name.' Mengubah rencana '.$ren->id.' untuk '.$ren->id_karyawan); 
            
            return response()->json(
                [
                    "status"=> "SUKSES",
                ]
            );
        }
            
        return response()->json([
            'status' => 'SUKSES',
            ]); 
        
        // $data = Rencana::find($request->id);
        
        // if($data->durasi != $request->durasi){
        //     if($request->durasi == 'daily'){
                
        //         $dari = $request->tgl_awal;
        //         $sampai = $request->tgl_akhir;
                
        //         $glati = Rencana::where('id_range', $data->id_range)->get();
                
        //         foreach($glati as $g){
        //             if($g->id != $data->id){
        //                 Rencana::where('id', $g->id)->delete();
        //             }
        //         }
                
        //         Rencana::where('id', $data->id)->update([
        //             'durasi' => $request->durasi,
        //             'tugas' => $request->tugas,
        //             'target' => $request->target,
        //             'id_rb' => $request->tugas_ble,
        //             'tgl_awal' =>  $dari,
        //             'tgl_akhir' =>  $sampai,
        //             'user_update' => Auth::user()->id,
        //             'id_range' => 0
        //         ]);
                
        //     }else{
        //         // return($data);
        //         $dari = $request->tgl_awal;
        //         $sampai = $request->tgl_akhir;
                
        //         $tanggalAwal = new DateTime($dari);

        //         $tanggalAkhir = new DateTime($sampai);
                
        //         $selisihHari = 0;
                
        //         $dateList = [];
                
        //         while ($tanggalAwal <= $tanggalAkhir) {
        //             $selisihHari++;
        //             $dateList[] = $tanggalAwal->format('Y-m-d');
        //             $tanggalAwal->modify('+1 day');
        //         }
                
        //         $datax = [];
                
        //         $created = date('Y-m-d H:i:s');
        //         for($i = 0; $i < $selisihHari; $i++){
                    
        //             $datax[$i] = [
        //                 'id_karyawan' => $data->id_karyawan,
        //                 'tugas' => $request->tugas,
        //                 'durasi' => 'range',
        //                 'tgl_awal' =>  $dateList[$i],
        //                 'target' => $request->target,
        //                 'id_rb' => $request->tugas_ble,
        //                 'tgl_akhir' =>  $sampai,
        //                 'user_insert' => Auth::user()->id,
        //                 'marketing' => 0,
        //                 'id_range' => $data->id,
        //                 'created_at' => $i > 0 ? $created : date('Y-m-d H:i:s')
        //             ];
        //         }
        //         foreach($datax as $xx){
        //             if($data->tgl_awal == $xx['tgl_awal']){
        //                 Rencana::where('id', $data->id)->update(['durasi' => 'range', 'tgl_awal' => $data->tgl_awal, 'tgl_akhir' =>  $sampai, 'id_range' => $data->id, 'user_update' => Auth::user()->id]);
        //             }else{
        //                 $data = new Rencana;
        //                 $data->id_karyawan = $xx['id_karyawan'];
        //                 $data->tugas = $xx['tugas'];
        //                 $data->durasi = $xx['durasi'];
        //                 $data->tgl_awal = $xx['tgl_awal'];
                        
        //                 $data->target = $xx['target'];
        //                 $data->id_rb = $xx['tugas_ble'];
        //                 $data->tgl_akhir = $xx['tgl_akhir'];
        //                 $data->user_insert = $xx['user_insert'];
        //                 $data->id_range = $xx['id_range'];
        //                 $data->created_at = $xx['created_at'];
        //                 $data->save();
        //             }
        //         }
                
        //     }
        // }else{
        //     $data->tugas = $request->tugas;
            
        //     if($data->tgl_awal != $request->tgl_awal){
        //         $dari = $request->tgl_awal;
        //         $sampai = $request->tgl_akhir;
                
        //         if($request->durasi == 'range'){
                    
        //             $tanggalAwal = new DateTime($dari);

        //             $tanggalAkhir = new DateTime($sampai);
                    
        //             $selisihHari = 0;
                    
        //             $dateList = [];
                    
        //             while ($tanggalAwal <= $tanggalAkhir) {
        //                 $selisihHari++;
        //                 $dateList[] = $tanggalAwal->format('Y-m-d');
        //                 $tanggalAwal->modify('+1 day');
        //             }
                    
        //             $datax = [];
                    
        //             $created = date('Y-m-d H:i:s');
        //             for($i = 0; $i < $selisihHari; $i++){
                    
        //                 if($i > 0){    
        //                     $lat = Rencana::where('created_at', $created)->where('id_karyawan', $request->id_kar);
        //                     if(count($lat->get()) > 0){
        //                         $last = $lat->first()->id;
        //                         if($i == 1){
        //                             $upren = Rencana::where('id', $last)->update(['id_range' => $last]);
        //                         }
        //                     }
        //                 }else{
        //                     $last = 0;
        //                 }
                        
        //                 Rencana::create(
        //                     $datax[$i] = [
        //                         'id_karyawan' => $request->id_kar,
        //                         'tugas' => $request->tugas,
        //                         'durasi' => $request->durasi,
        //                         'tgl_awal' =>  $dateList[$i],
        //                         'tgl_akhir' =>  $sampai,
        //                         'user_insert' => Auth::user()->id,
        //                         'marketing' => 0,
        //                         'id_range' => $request->durasi == 'daily' ? 0 :$last,
        //                         'created_at' => $i > 0 ? $created : date('Y-m-d H:i:s')
        //                     ]
        //                 );
        //             }
                    
        //             Rencana::where('id_range', $data->id_range)->delete();
        //         }else{
        //             $data->tgl_awal = $dari;
        //             $data->tgl_akhir = $dari;
        //         }
                
        //     }else if($data->tgl_akhir != $request->tgl_akhir){
        //         $dari = $request->tgl_awal;
        //         $sampai = $request->tgl_akhir;
                
        //         $tanggalAwal = new DateTime($dari);

        //         $tanggalAkhir = new DateTime($sampai);
                    
        //         $selisihHari = 0;
                    
        //         $dateList = [];
                    
        //         while ($tanggalAwal <= $tanggalAkhir) {
        //             $selisihHari++;
        //             $dateList[] = $tanggalAwal->format('Y-m-d');
        //             $tanggalAwal->modify('+1 day');
        //         }
                    
        //         $datax = [];
                    
        //         $created = date('Y-m-d H:i:s');
        //         for($i = 0; $i < $selisihHari; $i++){
                
        //             if($i > 0){    
        //                 $lat = Rencana::where('created_at', $created)->where('id_karyawan', $request->id_kar);
        //                 if(count($lat->get()) > 0){
        //                     $last = $lat->first()->id;
        //                     if($i == 1){
        //                         $upren = Rencana::where('id', $last)->update(['id_range' => $last]);
        //                     }
        //                 }
        //             }else{
        //                 $last = 0;
        //             }
                    
        //             Rencana::create(
        //                 $datax[$i] = [
        //                   'id_karyawan' => $request->id_kar,
        //                     'tugas' => $request->tugas,
        //                     'durasi' => $request->durasi,
        //                     'tgl_awal' =>  $dateList[$i],
        //                     'tgl_akhir' =>  $sampai,
        //                     'user_insert' => Auth::user()->id,
        //                     'marketing' => 0,
        //                     'id_range' => $request->durasi == 'daily' ? 0 : $last,
        //                     'created_at' => $i > 0 ? $created : date('Y-m-d H:i:s')
        //                 ]
        //             );
        //         }
                
        //         Rencana::where('id_range', $data->id_range)->delete();
        //     }else{
        //         $data->tgl_awal = $request->tgl_awal;
        //         $data->tgl_akhir = $request->tgl_akhir;
        //     }
            
        //     $data->durasi = $request->durasi;
        //     $data->user_update = Auth::user()->id;
    
        //     $data->update();
        // }
        
                
        // \LogActivity::addToLog(Auth::user()->name . ' Edit Rencana untuk ID ' . $request->id );
        // return response()->json(['success' => 'Berhasil!']);
    }
    
    public function get_marketing (Request $request){
        $bulan = $request->bulan == '' ? date('Y-m') : $request->bulan;
        $data = Rencana::whereRaw("id_karyawan = '$request->id' AND DATE_FORMAT(tgl_awal, '%Y-%m') = '$bulan' AND marketing = '1'")->get();
        return $data;
    }
    
    public function tambah_marketing (Request $request){
        if($request->tgl != ''){
            $tgl = explode('_', $request->tgl);
            $dari = $tgl[0];
            $sampai = $tgl[1];
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }
        
        
        $tanggalAwal = new DateTime($dari);

        $tanggalAkhir = new DateTime($sampai);
        
        $selisihHari = 0;
        
        $dateList = [];
        
        while ($tanggalAwal <= $tanggalAkhir) {
            $selisihHari++;
            $dateList[] = $tanggalAwal->format('Y-m-d');
            $tanggalAwal->modify('+1 day');
        }
        
        $data = [];
        
        for($i = 0; $i < $selisihHari; $i++){
            Rencana::create(
                $data[$i] = [
                    'durasi' => 'daily',
                    'id_karyawan' => $request->id,
                    'tgl_awal' =>  $dateList[$i],
                    'tgl_akhir' =>  $dateList[$i],
                    'kunjungan' => $request->knjngn,
                    'transaksi' => $request->tr,
                    'penawaran' => $request->pnwrn,
                    'closing' => $request->cl,
                    'user_insert' => Auth::user()->id,
                    'marketing' => 1
                ] 
            );
        }
        
        \LogActivity::addToLog(Auth::user()->name . ' Membuat Rencana Marketing untuk ID' . $request->id);
        return response()->json(['success' => 'Berhasil!']);
    }
    
    public function edit_marketing(Request $request){
        
        $data = Rencana::find($request->id);
                $data->tgl_awal = $request->tgl;
                $data->tgl_akhir = $request->tgl;
                $data->transaksi = $request->tr;
                $data->kunjungan = $request->knjngn;
                $data->penawaran = $request->pnwrn;
                $data->closing = $request->cl;
                $data->user_update = Auth::user()->id;

                $data->update();
                
        \LogActivity::addToLog(Auth::user()->name . ' Edit Rencana Marketing untuk ID ' . $request->id );
        return response()->json(['success' => 'Berhasil!']);
    }
    
    public function edit_get_marketing(Request $request){
        $data = Rencana::whereRaw("id = '$request->id'")->first();
        return $data;
    }
    
    public function getajasih (Request $request){
        $id     = $request->id;
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d');
        $tgl_d  = date('Y-m-01', strtotime($tgl));
        $bln    = date('m', strtotime($tgl));
        $thn    = date('Y', strtotime($tgl));
        
        $r_late = Rencana::whereDate('tgl_akhir', '<', $tgl)->where('tgl_selesai', null)
                            ->where('id_karyawan', $id)->where('marketing', 0)
                            ->get();
        $r_umum = Rencana::whereDate('tgl_awal', '<=', $tgl)->whereDate('tgl_akhir', '>=', $tgl)
                            ->where('id_karyawan', $id)->where('marketing', 0)
                            ->get();
                            
        $r_mrkt = Rencana::whereDate('tgl_awal', $tgl)->whereDate('tgl_akhir', $tgl)
                            ->where('id_karyawan', $id)->where('marketing', 1)
                            ->first();
        
        $r_dana = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $id)->where('jenis_target', 'kar')->first();
        
        $min    = $r_dana != null ? $r_dana->minimal : 0;
        $omset  = Transaksi::whereMonth('tanggal', $bln)->whereYear('tanggal', $thn)->where('id_koleks', $u->id)->where('approval', '>', 0)->sum('jumlah');
        $tran   = Transaksi::selectRaw("COUNT(id_donatur) AS knj, COUNT(IF(subtot > $min, id_donatur, NULL)) AS tdm")->whereDate('tanggal', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->first();
        $prosp  = Prosp::selectRaw("COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
                        ->where(function($q) use ($tgl) {
                            $q->whereDate('tgl_fol', $tgl)->orWhereDate('created_at', $tgl);
                        })
                        ->where('id_peg', $u->id)->first();
        $tugas  = [];
        if($r_dana != null){
            $v = $r_dana;
            $tugas[0]   =  [
                            'id'        => 0,
                            'tugas'     => 'Target Dana',
                            'target'    => $v->target,
                            'akhir'     => date('Y-m-t'),
                            'capaian'   => $omset,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 1,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
        }
        
        // return($r_late);
        foreach($r_late as $key => $v){
            $i = $r_dana != null ? $key + 1 : $key;
            $tugas[$i]  =   [
                            'id'        => $v->id,
                            'tugas'     => $v->tugas,
                            'target'    => $v->target,
                            'akhir'     => $v->tgl_akhir,
                            'capaian'   => $v->capaian,
                            'marketing' => 0,
                            'aktif'     => $v->aktif,
                            'dana'      => 0,
                            'telat'     => 1,
                            'alasan'    => $v->alasan,
                            ];
        }
        
        foreach($r_umum as $key => $v){
            $i = $r_dana != null ? count($r_late) + 1 : count($r_late);
            $tugas[$i]  =   [
                            'id'        => $v->id,
                            'tugas'     => $v->tugas,
                            'target'    => $v->target,
                            'akhir'     => $v->tgl_akhir,
                            'capaian'   => $v->capaian,
                            'marketing' => 0,
                            'aktif'     => $v->aktif,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => $v->alasan,
                            ];
        }
        
        if($r_mrkt != null | $tran != null | $prosp != null){
            $v = $r_mrkt;
            $i = $r_dana != null ? count($r_umum) + count($r_late) + 1 : count($r_umum) + count($r_late);
            $tugas[$i] =  [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Kunjungan',
                            'target'    => $r_mrkt != null ? $v->kunjungan : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->knj : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+1] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Transaksi diatas minimal',
                            'target'    => $r_mrkt != null ? $v->transaksi : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->tdm : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+2] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Penawaran',
                            'target'    => $r_mrkt != null ? $v->penawaran : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->pnw + $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+3] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Prospek Closing',
                            'target'    => $r_mrkt != null ? $v->closing : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
        }
        
        return $tugas;
    }
    
    public function import()
    {
        Excel::import(new RencanaImport, 'import.xls');
    
        return redirect('perencanaan'); // Redirect to a specific route after import
    }
    
    public function getBaganHasil(Request $request){
        // $data= RencanaBln::find($request->val);
        // $data = RencanaBln::select('rencana_thn.tugas','rencana_thn.id')->join('rencana_thn','rencana_thn.id','=','rencana_bln.id_rt')->where('rencana_bln.id', $request->val)->get();
        $data = RencanaBln::select('rencana_thn.tugas','rencana_thn.id')->join('rencana_thn','rencana_thn.id','=','rencana_bln.id_rt')->where('rencana_bln.id', $request->val)->first();
        
        return $data;
    }
    
    public function rencana_id_modal(Request $req){
            $r_umum = Rencana::leftjoin('rencana_bln', 'rencana.id_rb', '=', 'rencana_bln.id')
                    ->select('rencana.*', 'rencana_bln.satuan AS sat_bln')
                    ->where('rencana.id', $req->id)
                    ->where('rencana.marketing', 0)
                    ->get();
            
            $tugas  = [];
            
            foreach($r_umum as $key => $v){
            $sat    = RencanaThn::find($v->sat_bln);
            
                $tugas  =   [
                                'id'            => $v->id,
                                'tugas'         => $v->tugas,
                                'deskripsi'     => $v->deskripsi,
                                'bukti'         => $v->bukti,
                                'jam_awal'      => $v->jam_awal,
                                'jam_akhir'     => $v->jam_akhir,
                                'durasi'        => $v->durasi,
                                'target'        => $v->target,
                                'tgl_awal'      => $v->tgl_awal,
                                'tgl_akhir'     => $v->tgl_akhir,
                                'tgl_selesai'   => $v->tgl_selesai,
                                'capaian'       => $v->capaian,
                                'satuan'        => $sat != null ? $sat->rumus : null,
                                'aktif'         => $v->aktif,
                                'acc'           => $v->acc,
                                'alasan'        => $v->alasan,
                                'alasan_r'      => $v->alasan_reject,
                                'id_kar'        => $v->id_karyawan
                                ];
            }
            
            
        return($tugas);
    }
    
}
