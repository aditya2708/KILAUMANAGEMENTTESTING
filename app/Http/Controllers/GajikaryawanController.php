<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Tunjangan;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use App\Models\Profile;
use DataTables;
use Auth;
use Carbon\Carbon;
use DB;
use PDF;

use App\Exports\GajiQueryExport;
use Maatwebsite\Excel\Facades\Excel;

class GajikaryawanController extends Controller
{
    public function index (Request $request) 
    {
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $id = Auth::user()->id_kantor;
        
        $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $kntr = [];
        if(Auth::user()->level == 'admin' || Auth::user()->level == 'hrd' || Auth::user()->level == 'keuangan pusat' ||  Auth::user()->level == 'operator pusat'){
            $kntr = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang'){
            if($k == null){
                $kntr = Kantor::where('id', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
            }else{
                $kntr = Kantor::whereRaw("(id = '$id' OR id = '$k->id')")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        if($request->ajax())
        {
             $id_com = $request->com;
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;   
            $month = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $year = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            
            $jabats = $request->jab != '' ? "gaji.id_jabatan = '$request->jab'" : "gaji.id_jabatan IS NOT NULL";
            $kantora = $request->kan != '' ? "gaji.id_kantor = '$request->kan'" : "gaji.id_kantor IS NOT NULL";
            
            $kot = Auth::user()->id_kantor;
            $data = [];
            if(Auth::user()->level == 'admin' || Auth::user()->level == 'hrd' || Auth::user()->level == 'keuangan pusat' ||  Auth::user()->level == 'operator pusat'){
                $data = Gaji::select('gaji.*','jabatan.jabatan','tambahan.unit')->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                ->whereIn('gaji.id_karyawan', function($query) use ($id_com){
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
                                                $query->where('karyawan.id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('karyawan.id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->whereRaw("$jabats AND MONTH(gaji.created_at) = '$month' AND YEAR(gaji.created_at) = '$year' AND $jabats AND $kantora ");
            }else if(Auth::user()->level == 'kacab'){
                if($k == null){
                    $data = Gaji::select('gaji.*','jabatan.jabatan','tambahan.unit')->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                        ->whereIn('gaji.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->gaji == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else if($id_com == '0'){
                                                $query->whereIn('id_com', function($q) {
                                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                });
                                            }else{
                                                $query->where('karyawan.id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('karyawan.id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                        ->whereRaw("$jabats AND MONTH(gaji.created_at) = '$month' AND YEAR(gaji.created_at) = '$year' AND $jabats AND gaji.id_kantor = '$kot' ");
                }else{
                    if($request->kantor != ''){
                        $data = Gaji::select('gaji.*','jabatan.jabatan','tambahan.unit')
                        ->join('jabatan','jabatan.id','=','gaji.id_jabatan')
                        ->join('tambahan','tambahan.id','=','gaji.id_kantor')
                        ->whereIn('gaji.id_karyawan', function($query) use ($id_com){
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
                                                $query->where('karyawan.id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('karyawan.id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                        ->whereRaw("$jabats AND MONTH(gaji.created_at) = '$month' AND YEAR(gaji.created_at) = '$year' AND $jabats AND $kantora");
                    }else{
                        $data = Gaji::select('gaji.*','jabatan.jabatan','tambahan.unit')
                        ->join('jabatan','jabatan.id','=','gaji.id_jabatan')
                        ->join('tambahan','tambahan.id','=','gaji.id_kantor')
                        ->whereIn('gaji.id_karyawan', function($query) use ($id_com){
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
                                                $query->where('karyawan.id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('karyawan.id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                        ->whereRaw("$jabats AND MONTH(gaji.created_at) = '$month' AND YEAR(gaji.created_at) = '$year' AND $jabats AND ($kantora OR gaji.id_kantor = '$k->id')");
                    }
                }
            }
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('tgl', function($data){
                        $tgl = ($data->created_at)->isoFormat('MMMM');
                        return $tgl;
                    })
                    ->addColumn('gajpok', function($data){
                        $gajpok = 'Rp.'.number_format($data->gapok, 0, ',', '.');
                        return $gajpok;
                    })
                    ->addColumn('tjjabatan', function($data){
                        $tjj = 'Rp.'.number_format($data->tj_jabatan, 0, ',', '.');
                        return $tjj;
                    })
                    ->addColumn('tjd', function($data){
                        $tjd = 'Rp.'.number_format($data->tj_daerah, 0, ',', '.');
                        return $tjd;
                    })
                    ->addColumn('tja', function($data){
                        $tjd = 'Rp.'.number_format($data->tj_anak, 0, ',', '.');
                        return $tjd;
                    })
                    ->addColumn('tjp', function($data){
                        $tjd = 'Rp.'.number_format($data->tj_pasangan, 0, ',', '.');
                        return $tjd;
                    })
                    ->addColumn('tp', function($data){
                        $tjd = 'Rp.'.number_format($data->transport, 0, ',', '.');
                        return $tjd;
                    })
                    ->addColumn('tjberas', function($data){
                        $tjb = 'Rp.'.number_format($data->tj_beras, 0, ',', '.');
                        return $tjb;
                    })
                    ->addColumn('tot', function($data){
                        $tot = 'Rp.'.number_format($data->total, 0, ',', '.');
                        return $tot;
                    })
                    ->rawColumns(['tgl'])
                    ->make(true);
        }
        return view ('fins.gaji_karyawan',compact('jabat','kntr'));
    }
    
    public function edit($id){
        if(request()->ajax())
        {
            $data = Golongan::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request) {
        $form_data = array(
            'kenaikan'     =>  $request->kenaikan,
        );
        Golongan::where('id_gol', $request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    // public function getgaji($id){
    
    //     $today = new DateTime("today");
        
    //     $kar = Karyawan::where('id_karyawan', $id)->first();
    //     // dd($kar->nm_pasangan);
    //     $tot_pasangan = 0;
    //     if($kar->status_nikah == 'Menikah'){
    //         $istri = unserialize($kar->nm_pasangan);
    //         foreach($istri as $key => $value){
    //             $tot_pasangan += 1;
    //         }
    //     }
        
    //     $anak = unserialize($kar->nm_anak); 
    //     $tgl = unserialize($kar->tgl_lahir_anak);
    //     $sts = unserialize($kar->status_anak);
        
    //     $tot_anak = 0;
    //     if($kar->nm_anak != null){
    //     foreach($anak as $key => $value){
    //         $tt = new DateTime($tgl[$key]);
    //         if($today->diff($tt)->y <= 21 && $sts[$key] == 'Belum Menikah'){
    //             $tot_anak += 1;
    //         }
    //     }
    //     }
    //     $tgl_gaji = $kar->tgl_gaji;
    //     $tj = Tunjangan::first();
    //     if($kar->jabatan == $tj->kolektor){
    //         $kolek = Transaksi_Perhari::leftjoin('users', 'transaksi_perhari.id', '=', 'users.id')
    //                 ->select(\DB::raw("
    //                     SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.jumlah, 0)) AS Omset,
    //                     SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.honor, 0)) AS honor,
    //                     SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.bonus_cap, 0)) AS boncap
    //                     "))
    //                 ->where('users.id_karyawan',$id)->first();
    //         $honor = $kolek != null ? $kolek->honor : 0;
    //         $boncap = $kolek != null ? $kolek->boncap : 0;
    //         if ($kolek->Omset <= 10000000){
    //             $bon = $kolek->Omset * 4/100;
    //         }elseif ($kolek->Omset > 10000000 && $kolek->Omset <= 20000000){
    //             $bon = ($kolek->Omset - 10000000) * 5/100 + 400000; 
    //         }elseif ($kolek->Omset > 20000000){
    //             $bon = ($kolek->Omset - 20000000) * 6/100 + 900000;
    //         }else{
    //             $bon = 0;
    //         }
    //         $bonkol = $boncap + $bon;
    //     }
    //     // dd($honor);
    //     // $transport = 35000;
    //     $gol = $kar->golongan;
    //     $datass = Karyawan::leftjoin('gapok', 'karyawan.masa_kerja', '=', 'gapok.th')->leftjoin('jabatan', 'karyawan.jabatan', '=', 'jabatan.id')
    //             ->leftjoin('daerah', 'karyawan.id_daerah', '=', 'daerah.id_daerah')
    //             ->leftjoin('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
    //             ->leftjoin("set_terlambat",function($join) use ($id){
    //                         $join->on("presensi.keterlambatan",">=","set_terlambat.awal")
    //                              ->on("presensi.keterlambatan","<=","set_terlambat.akhir")
    //                              ->where('presensi.id_karyawan', $id);
    //                         })
    //             ->select(\DB::raw("karyawan.nama, gapok.$gol, jabatan.tj_jabatan , daerah.tj_daerah, 
    //                             SUM(IF(presensi.status = 'Hadir' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
    //                             OR presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
    //                             , presensi.jumlah,0)) AS jumlah,
    //                             SUM(IF(MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji'), set_terlambat.potongan, 0)) AS potongan"))
    //             ->groupBy('karyawan.nama','gapok.'.$gol, 'jabatan.tj_jabatan', 'daerah.tj_daerah')
    //             ->where('karyawan.id_karyawan',$id)->get();
    //     // dd($datass);
    //     $total = $tot_anak + $tot_pasangan + 1;
    //     // dd($potongbpjs);
    //     $jkk = Bpjs::where('nama_jenis', 'JKK')->first();
    //     $jkm = Bpjs::where('nama_jenis', 'JKM')->first();
    //     $jht = Bpjs::where('nama_jenis', 'JHT')->first();
    //     $jpn = Bpjs::where('nama_jenis', 'JPN')->first();
    //     $sht = Bpjs::where('nama_jenis', 'KESEHATAN')->first();
        
    //     $profil = Profile::where('id', 1)->first();
        
    //     $pjkk = $profil->jkk == 1 && $kar->jkk == 1 ? $jkk->perusahaan : 0;
    //     $pjkm = $profil->jkm == 1 && $kar->jkm == 1 ? $jkm->perusahaan : 0;
    //     $pjht = $profil->jht == 1 && $kar->jht == 1 ? $jht->perusahaan : 0;
    //     $pjpn = $profil->jpn == 1 && $kar->jpn == 1 ? $jpn->perusahaan : 0;
    //     $psht = $profil->kesehatan == 1 && $kar->kesehatan == 1 ? $sht->perusahaan : 0;
        
    //     $kjkk = $kar->jkk == 1 ? $jkk->karyawan : 0;
    //     $kjkm = $kar->jkm == 1 ? $jkm->karyawan : 0;
    //     $kjht = $kar->jht == 1 ? $jht->karyawan : 0;
    //     $kjpn = $kar->jpn == 1 ? $jpn->karyawan : 0;
    //     $ksht = $kar->kesehatan == 1 ? $sht->karyawan : 0;
        
    //     $potongbpjs = $tj->umr * (($kjkk + $kjkm + $kjht + $kjpn + $ksht)/100);
        
    //     $databpjs = [
    //         'jkk' => number_format($tj->umr * ($pjkk/100),0, ',' , '.'),
    //         'jkm' => number_format($tj->umr * ($pjkm/100),0, ',' , '.'),
    //         'jht' => number_format($tj->umr * ($pjht/100),0, ',' , '.'),
    //         'jpn' => number_format($tj->umr * ($pjpn/100),0, ',' , '.'),
    //         'sht' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
    //         // 'total' => number_format($tj->umr * (($jkk->perusahaan + $jkm->perusahaan + $jht->perusahaan + $jpn->perusahaan)/100),0, ',' , '.'),
    //         ];
            
    //     // return($databpjs);    
    //     $data = [];
    //     foreach($datass as $x => $v){
    //         if($kar->status_kerja == 'Contract'){
    //             $data['data'][] = [
    //                 'nama' => $v->nama,
    //                 'masa_kerja' => $kar->masa_kerja,
    //                 'golongan' => $kar->golongan,
    //                 'id_jabatan' => $kar->jabatan,
    //                 'id_kantor' => $kar->id_kantor,
    //                 // 'id_daerah' => $kar->id_daerah,
    //                 'gapok' => number_format($v->$gol,0, ',' , '.'),
    //                 'tj_jabatan' => number_format($v->tj_jabatan,0, ',' , '.'),
    //                 'tj_daerah' => number_format($v->tj_daerah,0, ',' , '.'),
    //                 'jml_hari' => $v->jumlah,
    //                 'tj_anak' => number_format($tot_anak * ($tj->tj_anak/100 * $v->$gol),0, ',' , '.'),
    //                 'tj_pasangan' => number_format($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol),0, ',' , '.'),
    //                 'tj_beras' => number_format($tj->tj_beras*$tj->jml_beras*$total,0, ',' , '.'),
    //                 'transport' => number_format($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport,0, ',' , '.'),
    //                 'total' => number_format($v->$gol + $v->tj_jabatan + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
    //                             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
    //                             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
    //                 'thp' => number_format(($v->$gol + $v->tj_jabatan + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
    //                             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
    //                             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport)) - $potongbpjs ,0, ',' , '.'),
    //                 'thp2' => round(($v->$gol + $v->tj_jabatan + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
    //                             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
    //                             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport)) - $potongbpjs),
    //                 'tgl_gaji' => $tgl_gaji,
    //                 'bpjs' => number_format($potongbpjs,0, ',' , '.'),
    //                 'tot_tj_bpjs' => number_format($tj->umr * (($pjkk + $pjkm + $pjht + $pjpn)/100),0, ',' , '.'),
    //                 'tj_bpjs' => $databpjs,
    //                 'bonus' => $kar->jabatan == $tj->kolektor ? $bonkol : 0,
    //                 'potongan' => number_format($v->potongan,0, ',' , '.')
    //             ];
    //         }else{
    //             $data['data'][] = [
    //                 'nama' => $v->nama,
    //                 'masa_kerja' => $kar->masa_kerja,
    //                 'golongan' => $kar->golongan,
    //                 'id_jabatan' => $kar->jabatan,
    //                 'id_kantor' => $kar->id_kantor,
    //                 // 'id_daerah' => $kar->id_daerah,
    //                 'gapok' => number_format($v->$gol,0, ',' , '.'),
    //                 'tj_jabatan' => '0',
    //                 'tj_daerah' => '0',
    //                 'jml_hari' => $v->jumlah,
    //                 'tj_anak' => '0',
    //                 'tj_pasangan' => '0',
    //                 'tj_beras' => '0',
    //                 'transport' => number_format($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport,0, ',' , '.'),
    //                 'total' => number_format($v->$gol + 
    //                             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
    //                 'thp' => number_format($v->$gol + 
    //                             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
    //                 'tgl_gaji' => $tgl_gaji,
    //                 'bpjs' => '0',
    //                 'tot_tj_bpjs' => '0',
    //                 'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
    //                 'bonus' => $kar->jabatan == $tj->kolektor ? $bonkol : 0,
    //                 'potongan' => number_format($v->potongan,0, ',' , '.')
    //             ];
    //         }
    //     }
    //     return($data);
    // }

// public function accgaji($id, Request $request)
// {
//     $datkar = Karyawan::where('id_karyawan',$id)->update(['tgl_gaji' => date('Y-m-d')]);

//         $data = new Gaji;
//         $data->id_karyawan = $id;
//         $data->nama = $request->nama;
//         $data->masa_kerja = $request->masa_kerja;
//         $data->golongan = $request->golongan;
//         $data->id_jabatan = $request->id_jabatan;
//         $data->id_kantor = $request->id_kantor;
//         $data->gapok = preg_replace("/[^0-9]/", "", $request->gapok);
//         $data->tj_jabatan = preg_replace("/[^0-9]/", "", $request->tj_jabatan);
//         $data->tj_daerah = preg_replace("/[^0-9]/", "", $request->tj_daerah);
//         $data->tj_anak = preg_replace("/[^0-9]/", "", $request->tj_anak);
//         $data->tj_pasangan = preg_replace("/[^0-9]/", "", $request->tj_pasangan);
//         $data->tj_beras = preg_replace("/[^0-9]/", "", $request->tj_beras);
//         $data->transport = preg_replace("/[^0-9]/", "", $request->transport);
//         $data->jml_hari = $request->jml_hari;
//         $data->total = preg_replace("/[^0-9]/", "", $request->total);
//         $data->thp = preg_replace("/[^0-9]/", "", $request->thp);
//         $data->bpjs = preg_replace("/[^0-9]/", "", $request->bpjs);
//         $data->created_at = $request->tgl_gaji;
//         $data->created = date('Y-m-d H:i:s');
        
//         $data->save();
        
//     return response()->json(
//         [
//             "status"=>"sukses",
//             "data"=>$data
//         ]
//     );
// }
    public function cekdata(Request $request){
        // return($request);
        $tgl = $request->tgl;
        
        $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
        
        $data = Gaji::whereRaw("MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->get();
        return $data; 
    }

    public function ekspay(Request $request){
        $tgl = $request->tgl;
        $kntr = $request->unit;
        $stts = $request->status;
        
        $getkntr = $request->unit != '' ? Kantor::where('id',$request->unit)->first() : '';
        $nmunit = $request->unit != '' ? $getkntr->unit :'semua-unit-kerja';
        // $unit = $request->unit != '' ? $request->unit :'semua-unit-kerja';
        $nmstatus = $request->status != '' ? $request->status :'semua-status-kerja';
        
        $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
        $kantor = $request->unit != '' ? "id_kantor = '$request->unit'" : "id_kantor != ''";
        $status = $request->status != '' ? "status_kerja = '$request->status'" : "status_kerja != ''";
        
        $data = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->orderBy('nama', 'asc')->get();
        $tot = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->select(DB::raw('SUM(thp) as total'))->first();
        $blnx = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->select(DB::raw('created_at'))->groupBy('created_at')->first();
        $malas = \App\Models\Profile::first();
        
        // if(count($data) > 0){
            $pdf = PDF::loadView('eksporpay', ['data' => $data, 'tgl' => $tgl, 'tot' => $tot, 'unit' => $kntr, 'blnxs' => $blnx, 'malas' => $malas])->setPaper('a4', 'potrait');
            return $pdf->stream($tgl.'-payroll-'.$nmunit.'-'.$nmstatus.'.pdf');
        // }else{
        //     return response()->json(['data' => 'kosong']);
        // }
        
    }
    
    public function eksbpjs(Request $request){
       $tgl = $request->tgl;
       $kntr = $request->unit; 
       $bpjs = $request->bpjs;
       
       $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
       $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
       $kantor = $request->unit != '' ? "id_kantor = '$request->unit'" : "id_kantor != ''";
       $status = $request->status != '' ? "status_kerja = '$request->status'" : "status_kerja != ''";
        
       $data = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->get();
       $tot = Gaji::selectRaw('SUM(thp) as total ')->whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->first();
       
       if($bpjs == "kesehatan"){
        //   $data = Gaji::where(DB::raw("(DATE_FORMAT(created_at,'%m-%Y'))"),$tgl)->get();
        //   $tot = Gaji::selectRaw('SUM(kesehatan) as total')->where(DB::raw("(DATE_FORMAT(created_at,'%m-%Y'))"),$tgl)->first();
        
           $data = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->get();
           $tot = Gaji::selectRaw('SUM(kesehatan) as total')->whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->first();
            
           $pdf = PDF::loadView('eksporbpjs', ['data' => $data, 'tgl' => $tgl, 'tot' => $tot, 'bpjs' => $bpjs, 'unit' => $kntr]);
           
       }else{
           $data = Gaji::whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->get();
           $tot = Gaji::selectRaw('SUM(ketenagakerjaan) as total')->whereRaw("$kantor AND $status AND MONTH(created_at) = '$bulan' AND YEAR(created_at) = '$tahun' ")->first();
           
        //   $data = Gaji::where('id_kantor', $kntr)->where(DB::raw("(DATE_FORMAT(created_at,'%m-%Y'))"),$tgl)->get();
        //   $tot = Gaji::selectRaw('SUM(ketenagakerjaan) as total')->where('id_kantor', $kntr)->where(DB::raw("(DATE_FORMAT(created_at,'%m-%Y'))"),$tgl)->first();
           $getkntr = Kantor::where('id',$kntr)->first();
           $pdf = PDF::loadView('eksporbpjs', ['data' => $data, 'tgl' => $tgl, 'tot' => $tot, 'bpjs' => $bpjs, 'unit' => $kntr]);
           
       }
       
       if($kntr == ''){
            return $pdf->download($tgl.'-BPJS-'.$bpjs.'-Semua.pdf');
        }else{
            $getkntr = Kantor::where('id',$kntr)->first();
            return $pdf->download($tgl.'-BPJS-'.$bpjs.'-'.$getkntr->unit.'.pdf');
        }
    }
    
    public function eksdata(Request $request) 
    {
        $getkntr = $request->unit != '' ? Kantor::where('id',$request->unit)->first() : '';
        $tgl = $request->tgl;
        $nmunit = $request->unit != '' ? $getkntr->unit :'semua-unit-kerja';
        $unit = $request->unit != '' ? $request->unit :'semua-unit-kerja';
        $status = $request->status != '' ? $request->status :'semua-status-kerja';
        $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
        $header =  $tgl.'-Gaji-'.$nmunit.'-'.$status;
        $perusahaan = DB::table('company')->selectRaw('name')->where('id_com', $request->com?? Auth::user()->id_com)->first()->name;
        // dd($perushaan);
        $response =  Excel::download(new GajiQueryExport($unit, $status, $bulan, $tahun, $header,$perusahaan), $tgl.'-Gaji-'.$nmunit.'-'.$status.'.xlsx');
        ob_end_clean();
        return $response;
    }
}