<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rencana;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Targets;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Prosp;
use App\Models\RencanaThn;
use App\Models\RencanaBln;
use App\Models\KPIH;
use Auth;
use DB;
use DataTables;
use Excel;
use DateTime;

class KPIController extends Controller
{
    
    public function index(Request $req)
    {
        
        $kan = Auth::user()->id_kantor;
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        $kantor = Kantor::select('level')->where('id', Auth::user()->id_kantor)->first();
        
        if(Auth::user()->kepegawaian == 'kacab'){
            $kk = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            
            if($kk == null){
                $units = Kantor::whereRaw("id = '$kan'")->get();
            }else{
                $units = Kantor::whereRaw("id = '$kk->id' OR id = '$kan'")->get();
            }
            
        }else{
            $units = Kantor::where('id_com', Auth::user()->id_com)->get();
        }
        
        if ($req->ajax()) {
            
            $tanggal = $req->bln == '' ? date('Y-m-01') : $req->bln.'-01';
            $kntr = $req->unit == '' ? Auth::user()->id_kantor : $req->unit;
            
            $real = KPIH::selectRaw("perhitungan_kpi.*, karyawan.nama")->leftjoin('karyawan', 'karyawan.id_karyawan','=','perhitungan_kpi.id_karyawan')
                        ->whereRaw("status_kerja = 'Contract' AND Aktif= 1 AND tanggal = '$tanggal' AND perhitungan_kpi.id_kantor = '$kntr'");
                        
            // return $real;
           
            return DataTables::of($real)
                ->addIndexColumn()
                ->addColumn('attitude', function ($real) {
                        return $real->kehadiran + $real->sikap;
                    })
                ->addColumn('output', function ($real) {
                        $jml = '<a href="javascript:void(0)" class="fungss" id="hasil" data-id="'.$real->id_karyawan.'">'.$real->hasil.'</a>';
                        return $jml;
                    })
                ->addColumn('proses', function ($real) {
                        $jml = '<a href="javascript:void(0)" class="fungss" id="proses" data-id="'.$real->id_karyawan.'">'.$real->proses.'</a>';
                        return $jml;
                    })
                ->addColumn('total', function ($real) {
                        $jml = '<a href="javascript:void(0)" class="fungss" id="total" data-id="'.$real->id_karyawan.'">'.$real->total_kpi.'</a>';
                        return $jml;
                    })
                ->rawColumns(['attitude', 'output', 'proses','total'])
                ->make(true);
                
        }
                    
        return view('kpi.index', compact('units'));
    }
    
    public function kpi_kar(Request $req){
        
        $bulan = $req->date == '' ? date('Y-m-01') : $req->date.'-01';
        
        $m = date('m', strtotime($bulan));
        $y = date('Y', strtotime($bulan));
        
        // $data = Rencana::selectRaw("users.name, rencana.id_karyawan")
        //         ->join('users','users.id_karyawan','=','rencana.id_karyawan')
        //         ->whereRaw("users.aktif = 1 AND MONTH(tgl_awal) = '$m' AND YEAR(tgl_awal) = '$y' AND users.id_kantor = '$req->unit'")
        //         ->groupBy('rencana.id_karyawan')
        //         ->get();
        $unit = $req->unit == '' ? Auth::user()->id_kantor : $req->unit;
        
        $data = User::select('name','id_karyawan')->whereRaw("users.aktif = 1 AND status_kerja = 'Contract' AND users.id_kantor = '$unit' ")->get();
        return $data;
    }
    
    public function kpi_det(Request $req){
        $bulan = $req->date == '' ? date('Y-m-01') : $req->date.'-01';
        
        $m = date('m', strtotime($bulan));
        $y = date('Y', strtotime($bulan));
        $id = $req->id;
        
        $tgls = "MONTH(created_at) = '$m' AND YEAR(created_at) = '$y'";
        
        $data = [];
        
        $data['presensi'] = Presensi::selectRaw("SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                        SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                        SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                        SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                        SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                        SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                        SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting
                ")
                ->whereRaw("MONTH(created_at) = '$m' AND YEAR(created_at) = '$y' AND id_karyawan = '$id'")
                ->groupBy('id_karyawan')
                ->get();
                
        
        $date = $bulan;
        
        $dataa = Rencana::selectRaw("rencana.id_rb,rencana.id, rencana.id_range, rencana.tugas, (SELECT tugas FROM rencana_thn WHERE rencana_bln.id_rt = rencana_thn.id) as bagians, rencana.target, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.tugas as parent, (SELECT tugas FROM rencana_thn WHERE rencana_bln.satuan = rencana_thn.id) as satuan, rencana_bln.metode")
                ->join('rencana_bln','rencana_bln.id','=','rencana.id_rb')
                ->whereRaw("MONTH(rencana.tgl_awal) = '$m' AND YEAR(rencana.tgl_awal) = '$y' AND rencana.marketing = '0' AND rencana.id_karyawan = '$id'")
                ->get();
                
        // return $dataa;
        
        $summedTargets = [];

        // Loop through each item in the data
        foreach ($dataa as $item) {
            $id_rb = $item['id_rb'];
            $id_range = $item['id_range'];
            $target = $item['target'];
            $tugas = $item['tugas'];
            $satuan = $item['satuan'];
            $metode = $item['metode'];
            $tgl = $item['tgl_awal'];
            $tgl_akhir = $item['tgl_akhir'];
            $bagians = $item['bagians'];
        
            // Initialize the bagians and id_rb in summedTargets if they don't exist
            if (!isset($summedTargets[$bagians])) {
                $summedTargets[$bagians] = [];
            }
            if (!isset($summedTargets[$bagians][$id_rb])) {
                $summedTargets[$bagians][$id_rb] = [
                    'target' => 0,
                    'id_ranges' => []
                ];
            }
        
            // Check if id_range is already counted for this id_rb
            if ($id_range === null || !in_array($id_range, $summedTargets[$bagians][$id_rb]['id_ranges'])) {
                // Add the target to the sum for this id_rb
                $summedTargets[$bagians][$id_rb]['target'] += $target;
        
                // If id_range is set, add it to the list to avoid duplicates
                if ($id_range !== null) {
                    $summedTargets[$bagians][$id_rb]['id_ranges'][] = $id_range;
                }
            }
        
            // Assign other attributes to the corresponding id_rb
            $summedTargets[$bagians][$id_rb]['tugas'] = $tugas;
            $summedTargets[$bagians][$id_rb]['satuan'] = $satuan;
            $summedTargets[$bagians][$id_rb]['metode'] = $metode;
            $summedTargets[$bagians][$id_rb]['bagians'] = $bagians;
            $summedTargets[$bagians][$id_rb]['tanggal'] = date('m Y', strtotime($tgl));
            $summedTargets[$bagians][$id_rb]['tanggall'] = date('Y-m', strtotime($tgl_akhir));
            $summedTargets[$bagians][$id_rb]['id_kar'] = $id;
        }
        
        $real = [];
        $cappros = [];
        
        // return $summedTargets;
        
        foreach($summedTargets as $bagian => $dataaa){
            
            $real['hasil'][$bagian] = $dataaa;
            
            foreach($dataaa as $id => $dayta){
                // $real = $id;
                
                $id_kar = $dayta['id_kar'];
                $metode = $dayta['metode'];
                $bagians = $dayta['bagians'];
                $satuan = $dayta['satuan'];
                // '$bagians' as bagians, 
                $datak = Rencana::selectRaw("id_karyawan as id_kar,  '$metode' as metode, '$satuan' as satuan, rencana.tugas, id, id_range, DATE_FORMAT(rencana.tgl_awal, '%d/%m/%Y') as tgl_awal, DATE_FORMAT(rencana.tgl_akhir, '%d/%m/%Y') as tgl_akhir, rencana.target")
                            ->whereRaw("id_karyawan = '$id_kar'")
                            ->where('id_rb', $id)
                            ->groupBy('id_range')
                            ->get();
                
                $real['proses'][$bagian][$id] = $datak;
                
                $cappros[] = $datak;
            }
        }
        
        $resultes = [];
        $indexs = 0;
        
        foreach ($cappros as $subArray) {
            foreach ($subArray as $item) {
                $resultes[$indexs] = $item;
                $indexs++;
            }
        }
        
        $d = [];
        foreach($resultes as $dd){
            
            $cawal = DateTime::createFromFormat('d/m/Y', $dd->tgl_awal);
            $awal =  $cawal->format('Y-m-d');
            
            $cakhir = DateTime::createFromFormat('d/m/Y', $dd->tgl_akhir);
            $akhir =  $cakhir->format('Y-m-d');
            
            $user = User::where('id_karyawan', $dd->id_kar)->first() == null ? 0 : User::where('id_karyawan', $dd->id_kar)->first()->id ;

            
            $haystack = $dd->satuan;
            $needle1 = "kegiatan";
            $needle2 = "Kunjungan";
            $needle3 = "Penawaran / FUP";
            $position1 = stripos($haystack, $needle1);
            $position2 = stripos($haystack, $needle2);
            $position3 = stripos($haystack, $needle3);
            
            if ($position1 !== false) {
                $d['kegiatan'][] = 1;
            } else if($position2 !== false){
                
                $cari = Transaksi::selectRaw("count(id) as hitung")->whereRaw("id_koleks = '$user' AND tanggal BETWEEN '$awal' AND '$akhir' ")->get();
                
                $d['kunjungan'][] = $cari;
            }else if($position3 !== false){
                $d['penawaran'][] = 3;
            }else{
                $d['lainnya'][] = '-';
            }
        }
        
        // return $d;
        
              
        // $datas = RencanaBln::selectRaw("rencana.id, rencana_bln.tugas as rencana_proses, renhas.tugas as rencana_hasil, 
        //                 renhas.id as id_renhas, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.metode, rencana_bln.satuan, 
        //                 renhas.target, rencana_thn.tugas as bagian, renhas.target as target_hasil, rencana_bln.target as target_proses,
        //                 renhas.metode as metode_hasil, rencana_bln.metode as metode_proses, satuan_thn.tugas as satuannya, rencana.capaian,
        //                 rencana_bln.id as id_rbb
        //             ")
        //         ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
        //         ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
        //         ->join('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
        //         ->join('rencana','rencana_bln.id','=','rencana.id_rb')
        //         ->where('rencana.id_karyawan', $req->id)
        //         ->whereRaw("DATE(rencana_bln.bulan) = '$date' AND rencana.marketing = '0'")
        //         ->get();
                    
        // $newStructure = [];
        
        // $itungcap = 0;
            
        // foreach ($datas as $entry) {
        //     $bagian = $entry['bagian'];
        //     $id_renhas = $entry['id_renhas'];
        //     $id_rbb = $entry['id_rbb'];
        //     $proses = $entry['rencana_proses'];
        //     $hasil = $entry['rencana_hasil'];
        //     $tgl_awal = $entry['tgl_awal'];
        //     $satuan = $entry['satuannya'];
        //     $tgl_akhir = $entry['tgl_akhir'];
        //     $target_hasil = $entry['target_hasil'];
        //     $target_proses = $entry['target_proses'];
        //     $m_hasil = $entry['metode_hasil'];
        //     $m_proses = $entry['metode_proses'];
        //     $capaian = $entry['capaian'];
            
            
        //     if (!isset($newStructure[$id_rbb])) {
        //         $newStructure[$id_rbb] = [
        //             'capaian' => 0
        //         ];
        //     }
            
        //     $kopid = $newStructure[$id_rbb]['capaian'] += (int)$capaian;
            
        //     if (!isset($newStructure['proses'])) {
        //         $newStructure['proses'] = [];
        //     }
            
        //     if (!isset($newStructure['hasil'])) {
        //         $newStructure['hasil'] = [];
        //     }
            
        //     if (!isset($newStructure['proses'][$bagian][$id_rbb])) {
        //         $newStructure['proses'][$bagian][$id_rbb] = [];
        //     }
            
            
        //     if (!isset($newStructure['hasil'][$bagian][$id_renhas])) {
        //         $newStructure['hasil'][$bagian][$id_renhas] = [];
        //     }
            
        //     $newStructure['proses'][$bagian][$id_rbb] = [
        //         'tugas' => $proses,
        //         'target' => $target_proses,
        //         'metode' => $m_proses,
        //         'satuan' => $satuan,
        //         'capaian' => $newStructure[$id_rbb]['capaian']
        //     ];
            
        //     $newStructure['hasil'][$bagian][$id_renhas] = [
        //         'tugas' => $hasil,
        //         'target' => $target_hasil,
        //         'metode' => $m_hasil,
        //         'satuan' => $satuan,
        //         'capaian' => null
        //     ];
        // }
        
        // $data['proses'] = $newStructure['proses'];
        // $data['hasil'] = $newStructure['hasil'];
        $data['task'] = $real;
        
        return $data;
    }
    
    public function getrendetbul(Request $req){
        
        $cawal = DateTime::createFromFormat('d/m/Y', $req->awal);
        $cakhir = DateTime::createFromFormat('d/m/Y', $req->akhir);
        $awal = $cawal->format('Y-m-d');
        $akhir = $cakhir->format('Y-m-d');
        $did = $req->did;
        $idkar = $req->id_kar;
        // AND id_laporan IS NOT NULL
        $cari = Rencana::select('id_laporan')->whereRaw("id_karyawan = '$idkar' AND id_rb = '$did' AND tgl_awal BETWEEN '$awal' AND '$akhir'")->pluck('id_laporan')->toArray();
        
        if(count($cari) > 0){
            $data = Laporan::selectRaw("DATE(laporan.created_at) as tanggalll, laporan.ket, rencana.tugas")->join('rencana','rencana.id_laporan','=','laporan.id_laporan')->whereIn('laporan.id_laporan', $cari)->get();
        }else{
            $data = [];
        }
        
        // $data = Laporan::selectRaw("DATE(laporan.created_at) as tanggalll, laporan.ket, rencana.*")->join('rencana','rencana.id_laporan','=','laporan.id_laporan')->where('id_rb', $req->id)->get();
        
        return $data;
    }
    
    public function postkpii(Request $req){
        
        $id_kar = $req->id_kar;
        $tunjangan = Karyawan::selectRaw("tj_jabatan")->where('id_karyawan', $req->id_kar)->join('jabatan','jabatan.id','=','karyawan.jabatan')->first()->tj_jabatan;
        $hadir = $req->hadir;
        $sikap = $req->sikap;
        $proses = $req->proses;
        $hasil = $req->hasil;
        $bln = $req->bln.'-01';
        $unit = $req->unit;
        
        $total = $sikap + $hadir + $proses + $hasil;
        $perhitungan = $tunjangan * ($total/100);
        
        $data = new KPIH;
        $data->id_karyawan = $id_kar;
        $data->id_kantor = $unit;
        $data->tanggal = $bln;
        $data->sikap = $sikap;
        $data->kehadiran = $hadir;
        $data->proses = $proses;
        $data->hasil = $hasil;
        $data->total_kpi = $total;
        $data->tunjangan = $tunjangan;
        $data->perhitungan = $perhitungan;
        $data->potongan = $tunjangan - $perhitungan;
        $data->acc = 2;
        $data->id_com = Auth::user()->id_com;
            
        $data->save();
        
        return response()->json(["data" => "successs"]); 
    }
    
    public function detail(Request $req){
        
        $date = '2024-09-01';
        $m = date('m', strtotime($date));
        $y = date('Y', strtotime($date));
            
        if($req->tab != 'attitude'){
                    
            $data = RencanaBln::selectRaw("rencana.id, rencana_bln.tugas as rencana_proses, renhas.tugas as rencana_hasil, 
                    renhas.id as id_renhas, rencana.tgl_awal, rencana.tgl_akhir, rencana_bln.metode, rencana_bln.satuan, 
                    renhas.target, rencana_thn.tugas as bagian, renhas.target as target_hasil, rencana_bln.target as target_proses,
                    renhas.metode as metode_hasil, rencana_bln.metode as metode_proses, satuan_thn.tugas as satuannya
                ")
            ->join('rencana_thn as satuan_thn','satuan_thn.id','=','rencana_bln.satuan')
            ->join('rencana_thn','rencana_bln.id_rt','=','rencana_thn.id')
            ->join('rencana_bln as renhas','renhas.id','=','rencana_bln.id_hasil')
            ->join('rencana','rencana_bln.id','=','rencana.id_rb')
            ->where('rencana.id_karyawan', $req->id)
            ->whereRaw("DATE(rencana_bln.bulan) = '$date' AND rencana.marketing = '0'")
            ->get();
                    
            $newStructure = [];
        
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
            return $newStructure;
        }else{
            $dataku = Presensi::whereRaw("MONTH(created_at) = '$m' AND YEAR(created_at) = '$y' AND id_karyawan = '$req->id'")->get();
            return $dataku;
        }
        
    }
    
}
