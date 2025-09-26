<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Karyawan;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use App\Models\User;
use App\Models\Tunjangan;
use Carbon\Carbon;
use Auth;
use DB;
use App\Exports\AnalisisExport;
use App\Exports\AnalisisDetailExport;
use App\Exports\DetailAnalisisDonaturExport;
use Excel;
use PDF;

use DataTables;

class ReportController extends Controller
{
    public function analis_transaksi(Request $request){
        $kantor = Kantor::all();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        $analisnya = 'Analis';
        if($request->ajax()){
            $thn1 = $request->tahun == '' ? $y : $request->tahun;
            $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
            
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
            $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
            
            $bul1 = '01-'.$b1;
            $bul2 = '01-'.$b2;
            
            $bula1 = date('Y-m', strtotime($bul1));
            $bula2 = date('Y-m', strtotime($bul2));
            
            $bayar = $request->bay;
            $pembayaran = $request->bay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN ('" . implode("', '", $bayar) . "')";
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            if($request->approv == 2){
                $approve = "approval = '2'";
            }else if($request->approv == 1){
                $approve = "approval = '1'";
            }else if($request->approv == 3){
                $approve = "approval = '0'";
            }else{
                $approve = "approval IS NOT NULL";
            }
            
            $prd = $request->plhtgl;
            
            if($prd == 0){
                $waktu = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
            }else if($prd == 1){
                $waktu = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
            }else if($prd == 2){
                $waktu = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            }
                 
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kntr = Auth::user()->id_kantor;
            if(Auth::user()->kolekting == 'admin'){
                $kondisiQuery = null;
            }else{
                if($k == null){
                    $kondisiQuery = "AND transaksi.id_kantor = '$kntr'";
                }else{
                    $kondisiQuery = "AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')";
                }
            }
            
            if(isset($request->kotal)){
                $kadal = "AND transaksi.id_kantor IN ('" . implode("', '", $request->kotal) . "')";
            }else{
                $kadal = null;
            }
            
            if($request->petugas != ''){
                $pee = "id_koleks = '$request->petugas'";
            }else{
                $pee = "id_koleks IS NOT NULL"; 
            }

            $sel_bank = "transaksi.id_bank, sum(jumlah) AS data, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, bank.no_rek, bank.nama_bank, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yuuu
                ";
            $sel_kantor = "transaksi.id_kantor, sum(jumlah) AS data,  SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi, sum(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, unit, '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT COUNT(DISTINCT(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
            $sel_jam = "sum(jumlah) AS data, HOUR(created_at) AS jam, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
            $sel_tanggal = "sum(jumlah) AS data, DAY(tanggal) as tgl, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
            $sel_bulan = "sum(jumlah) AS data, MONTH(tanggal) AS bln, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
            $sel_tahun = "sum(jumlah) AS data, YEAR(tanggal) as tgl, kolektor,SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
            $sel_donatur = "sum(jumlah) AS data, donatur, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , id_donatur, '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
            $sel_program = "sum(jumlah) AS data, prog.program, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,transaksi.id_program, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";  
            $sel_status = "sum(jumlah) AS data, status, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuuu";
            $sel_petugas = "sum(jumlah) AS data, id_koleks, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu"; 
            $sel_bayar = "sum(jumlah) AS data, pembayaran, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuuu";
                
            $sel_user = "sum(jumlah) AS data, users.name, transaksi.user_insert, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, transaksi.kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
                
            $whereRawBank = "$waktu AND $pembayaran AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawKantor = "$waktu AND $pembayaran AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawJTBT = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawDonatur = "$waktu AND $pembayaran AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawProgram = "$waktu AND $pembayaran AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawStatus = "$waktu AND $pembayaran AND $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawPetugas = "$waktu AND $pembayaran AND $pee AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawBayar = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawUser = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";
            
            // if($request->tab == 'tab1'){
                if(Auth::user()->kolekting == 'admin'){
                    if($request->analis == 'bank'){
                        $data = Transaksi::selectRaw($sel_bank)
                            ->join('bank','bank.id_bank','=','transaksi.id_bank')
                            ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                            ->whereRaw($whereRawBank);
                    }else if($request->analis == 'kantor'){
                        $data = Transaksi::selectRaw($sel_kantor) 
                                ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
                                ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
                                ->whereRaw($whereRawKantor);
                    }else if($request->analis == 'jam'){
                        $data = Transaksi::selectRaw($sel_jam)
                                ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'tanggal'){
                        $data = Transaksi::selectRaw($sel_tanggal)
                                ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'bulan'){
                        $data = Transaksi::selectRaw($sel_bulan)
                                ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'tahun'){
                        $data = Transaksi::selectRaw($sel_tahun)
                                ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }
                    else if($request->analis == 'donatur'){
                        $data = Transaksi::selectRaw($sel_donatur)
                                ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawDonatur);
                    }
                    else if($request->analis == 'program'){
                        $data = Transaksi::selectRaw($sel_program)
                                ->join('prog','prog.id_program','=','transaksi.id_program')
                                ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                ->whereRaw($whereRawProgram);
                    }else if($request->analis == 'status'){
                        $data = Transaksi::selectRaw($sel_status)
                                ->groupBy('status','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawStatus);
                    }else if($request->analis == 'petugas'){
                        $data = Transaksi::selectRaw($sel_petugas)
                                ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawPetugas);
                    }else if($request->analis == 'bayar'){
                        $data = Transaksi::selectRaw($sel_bayar)
                                ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawBayar);
                    }else if($request->analis == 'user'){
                        $data = Transaksi::selectRaw($sel_user)
                                ->join('users','users.id','=','transaksi.user_insert')
                                ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawUser);
                    }
                }else{
                    if($k == null){
                       if($request->analis == 'bank'){
                            $data = Transaksi::selectRaw($sel_bank)
                                    ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                    ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                                    ->whereRaw($whereRawBank);
                                            
                        }else if($request->analis == 'kantor'){
                            $data = Transaksi::selectRaw($sel_kantor) 
                                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
                                    ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
                                    ->whereRaw($whereRawKantor);
                        }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw($sel_jam)
                                    ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw($sel_tanggal)
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw($sel_bulan)
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw($sel_tahun)
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'donatur'){
                            $data = Transaksi::selectRaw($sel_donatur)
                                    ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawDonatur);
                        }else if($request->analis == 'program'){
                            $data = Transaksi::selectRaw($sel_program)
                                    ->join('prog','prog.id_program','=','transaksi.id_program')
                                    ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                    ->whereRaw($whereRawProgram);
                        }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw($sel_status)
                                    ->groupBy('status','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawStatus);
                        }else if($request->analis == 'petugas'){
                            $data = Transaksi::selectRaw($sel_petugas)
                                    ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawPetugas);
                        }else if($request->analis == 'bayar'){
                            $data = Transaksi::selectRaw($sel_bayar)
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawBayar);
                        }else if($request->analis == 'user'){
                                    $data = Transaksi::selectRaw($sel_user)
                                            ->join('users','users.id','=','transaksi.user_insert')
                                            ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                            ->whereRaw($whereRawUser);
                        }
                    }else{
                       if($request->analis == 'bank'){
                            $data = Transaksi::selectRaw($sel_bank)
                                ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                                ->whereRaw($whereRawBank);
                                                
                        }else if($request->analis == 'kantor'){
                            $data = Transaksi::selectRaw($sel_kantor) 
                                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
                                    ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
                                    ->whereRaw($whereRawKantor);
                        }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw($sel_jam)
                                    ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw($sel_tanggal)
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw($sel_bulan)
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw($sel_tahun)
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawJTBT);
                        }else if($request->analis == 'donatur'){
                            $data = Transaksi::selectRaw($sel_donatur)
                                    ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawDonatur);
                        }else if($request->analis == 'program'){
                            $data = Transaksi::selectRaw($sel_program)
                                    ->join('prog','prog.id_program','=','transaksi.id_program')
                                    ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                    ->whereRaw($whereRawProgram);
                        }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw($sel_status)
                                    ->groupBy('status','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawStatus);
                        }else if($request->analis == 'petugas'){
                            $data = Transaksi::selectRaw($sel_petugas)
                                    ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawPetugas);
                        }else if($request->analis == 'bayar'){
                            $data = Transaksi::selectRaw($sel_bayar)
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawBayar);
                        }else if($request->analis == 'user'){
                            $data = Transaksi::selectRaw($sel_user)
                                    ->join('users','users.id','=','transaksi.user_insert')
                                    ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw($whereRawUser);
                        }
                    }
                }
               
                
                $kotah = $request->kotal;
                
                $data->where(function($query) use ($request, $kotah) {
                    if(isset($request->kotal)){
                        $query->whereIn('transaksi.id_kantor', $kotah);
                    }
                });
                
                if($request->tab == 'tab55'){
                    
                    $data_real = [];
                    $transaksi = 0;
                    $nontransaksi = 0;
                    $don = 0;
                    $don1 = 0;
                    $datal = 0;
                    
                    $yu = 0;
                    $yuu = 0;
                    $yuuu = 0;
                    
                    if(count($data->get()) > 0){
                        foreach($data->get() as $k){
                            $transaksi += $k->transaksi;
                            $nontransaksi += $k->nontransaksi;
                            $datal += $k->data;
                            $don += $k->don;
                            $don1 += $k->don1;
                            $yu = $k->yu;
                            $yuu = $k->yuu;
                            $yuuu = $k->yuuu;
                        }
                        
                        
                         
                        $data_real = [
                            'persen_dn' => round(($datal/$yu)*100, 2),
                            'persen_nm' => round(($don/$yuu)*100, 2),
                            'persen_tr' => round(($transaksi/$yuuu)*100, 2),
                            'yu' => $yu,
                            'yuu' => $yuu,
                            'yuuu' => $yuuu,
                            'transaksi' => $transaksi,
                            'nontransaksi' => $nontransaksi,
                            'don1' => $don1,
                            'don' => $don,
                            'data' => $datal,
                        ];
                        
                    }else{
                        $data_real = [
                            'persen_dn' => 0,
                            'persen_nm' => 0,
                            'persen_tr' => 0,
                            'yu' => 0,
                            'yuu' => 0,
                            'yuuu' => 0,
                            'nontransaksi' =>0,
                            'don1' => 0,
                            'transaksi' => 0,
                            'don' => 0,
                            'data' => 0,
                        ];
                    }
                    
                    
                    $data1 = $data_real;
                    return $data1;
                }
                
                // return($data->get());
                    
                return DataTables::of($data)
                ->addColumn('nama', function($data){
                    if($data->analis == 'bank'){
                        $trr = '<a data-bs-toggle="modal"  class="dalwar" id="'.$data->id_bank.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->nama_bank .' ('.$data->no_rek.')">'.$data->nama_bank .' ('.$data->no_rek.')</a>';
                        return $trr;
                    }else if($data->analis == 'bulan'){
                        $bulan = array (1 => 'Januari', 'Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->bln.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$bulan[$data->bln].'">'.$bulan[$data->bln].'</a>';
                        return $trr;
                    }else if($data->analis == 'kantor'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->id_kantor.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->unit.'">'.$data->unit.'</a>';
                        return $trr;
                    }else if($data->analis == 'jam'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->jam.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="Jam '.$data->jam.'">Jam '.$data->jam.'</a>';
                        return $trr;
                    }else if($data->analis == 'tanggal'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->tgl.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="Tanggal '.$data->tgl.'">Tanggal '.$data->tgl.'</a>';
                        return $trr;
                    }else if($data->analis == 'tahun'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->tgl.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->tgl.'">'.$data->tgl.'</a>';
                        return $trr;
                    }else if($data->analis == 'donatur'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->id_donatur.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->donatur .'">'.$data->donatur .'</a>';
                        return $trr;
                    }else if($data->analis == 'program'){
                        $trr ='<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->id_program.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'. $data->program.'">'. $data->program.'</a>';
                        return $trr;
                    }else if($data->analis == 'status'){
                        $trr = '<a data-bs-toggle="modal"  class="dalwar" id="'.$data->status.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->status.'">'.$data->status.'</a>';
                        return $trr;
                    }else if($data->analis == 'petugas'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->id_koleks.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->kolektor.'">'.$data->kolektor.'</a>';
                        return $trr;
                    }else if($data->analis == 'bayar'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->pembayaran.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.ucfirst($data->pembayaran).'">'.ucfirst($data->pembayaran).'</a>';
                        return $trr;
                    }else if($data->analis == 'user'){
                        $trr = '<a data-bs-toggle="modal" tab="" class="dalwar" id="'.$data->user_insert.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa">'.$data->name.'</a>';
                        return $trr;
                    }
                })
                ->addColumn('nontransaksi', function($data){
                     if($data->analis == 'bank'){
                            if($data->nontransaksi > 0){                         
                        $trr = '<a data-bs-toggle="modal"  class="dalwar" id="'.$data->id_bank.'" tab="123" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->nama_bank .' ('.$data->no_rek.')">'.$data->nontransaksi .'</a>';
                            }else{
                              $trr  = $data->nontransaksi;
                            }
                        return $trr;
                    }else if($data->analis == 'bulan'){
                        $bulan = array (1 => 'Januari', 'Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
                        if($data->nontransaksi > 0){  
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->bln.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'kantor'){
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->id_kantor.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->unit.'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'jam'){
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->jam.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="Jam '.$data->jam.'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'tanggal'){
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  class="dalwar" id="'.$data->tgl.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="Tanggal '.$data->tgl.'">Tanggal '.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        
                        return $trr;
                    }else if($data->analis == 'tahun'){
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->tgl.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->tgl.'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'donatur'){ 
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->id_donatur.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->donatur .'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'program'){ 
                        if($data->nontransaksi > 0){
                            $trr ='<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->id_program.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'. $data->program.'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'status'){ 
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->status.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->status.'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'petugas'){ 
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->id_koleks.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.$data->kolektor.'">'.$data->kolektor.'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'bayar'){ 
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->pembayaran.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa" data="'.ucfirst($data->pembayaran).'">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }else if($data->analis == 'user'){ 
                        if($data->nontransaksi > 0){
                            $trr = '<a data-bs-toggle="modal"  tab="123"  class="dalwar" id="'.$data->user_insert.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa">'.$data->nontransaksi .'</a>';
                        }else{
                            $trr  = $data->nontransaksi;
                        }
                        return $trr;
                    }
                })
                ->addColumn('npersen', function($data){
                    if($data->persen == 'nominal'){
                        $trr = ($data->data/$data->yu)*100;
                    }else if($data->persen == 'donatur'){
                        $trr = ($data->don/$data->yuu)*100;
                    }else if($data->persen == 'transaksi'){
                        $trr = ($data->transaksi/$data->yuuu)*100;
                    }
                    return round($trr,2);
                })
                ->rawColumns(['nama','nontransaksi'])
                ->make(true);
                
            // }
            
            // if($request->tab == 'tab55'){
            //     if(Auth::user()->kolekting == 'admin'){
            //         if($request->analis == 'bank'){
            //             $data = Transaksi::selectRaw($sel_bank)
            //                 ->join('bank','bank.id_bank','=','transaksi.id_bank')
            //                 ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
            //                 ->whereRaw($whereRawBank);
            //         }else if($request->analis == 'kantor'){
            //             $data = Transaksi::selectRaw($sel_kantor) 
            //                     ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
            //                     ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
            //                     ->whereRaw($whereRawKantor);
            //         }else if($request->analis == 'jam'){
            //             $data = Transaksi::selectRaw($sel_jam)
            //                     ->groupBy('jam','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawJTBT);
            //         }else if($request->analis == 'tanggal'){
            //             $data = Transaksi::selectRaw($sel_tanggal)
            //                     ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawJTBT);
            //         }else if($request->analis == 'bulan'){
            //             $data = Transaksi::selectRaw($sel_bulan)
            //                     ->groupBy('bln','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawJTBT);
            //         }else if($request->analis == 'tahun'){
            //             $data = Transaksi::selectRaw($sel_tahun)
            //                     ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawJTBT);
            //         }
            //         else if($request->analis == 'donatur'){
            //             $data = Transaksi::selectRaw($sel_donatur)
            //                     ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawDonatur);
            //         }
            //         else if($request->analis == 'program'){
            //             $data = Transaksi::selectRaw($sel_program)
            //                     ->join('prog','prog.id_program','=','transaksi.id_program')
            //                     ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawProgram);
            //         }else if($request->analis == 'status'){
            //             $data = Transaksi::selectRaw($sel_status)
            //                     ->groupBy('status','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawStatus);
            //         }else if($request->analis == 'petugas'){
            //             $data = Transaksi::selectRaw($sel_petugas)
            //                     ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawPetugas);
            //         }else if($request->analis == 'bayar'){
            //             $data = Transaksi::selectRaw($sel_bayar)
            //                     ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawBayar);
            //         }else if($request->analis == 'user'){
            //             $data = Transaksi::selectRaw($sel_user)
            //                     ->join('users','users.id','=','transaksi.user_insert')
            //                     ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawUser);
            //         }
            //     }else{
            //         if($k == null){
            //           if($request->analis == 'bank'){
            //                 $data = Transaksi::selectRaw($sel_bank)
            //                         ->join('bank','bank.id_bank','=','transaksi.id_bank')
            //                         ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawBank);
                                            
            //             }else if($request->analis == 'kantor'){
            //                 $data = Transaksi::selectRaw($sel_kantor) 
            //                         ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
            //                         ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
            //                         ->whereRaw($whereRawKantor);
            //             }else if($request->analis == 'jam'){
            //                 $data = Transaksi::selectRaw($sel_jam)
            //                         ->groupBy('jam','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'tanggal'){
            //                 $data = Transaksi::selectRaw($sel_tanggal)
            //                         ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'bulan'){
            //                 $data = Transaksi::selectRaw($sel_bulan)
            //                         ->groupBy('bln','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'tahun'){
            //                 $data = Transaksi::selectRaw($sel_tahun)
            //                         ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'donatur'){
            //                 $data = Transaksi::selectRaw($sel_donatur)
            //                         ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawDonatur);
            //             }else if($request->analis == 'program'){
            //                 $data = Transaksi::selectRaw($sel_program)
            //                         ->join('prog','prog.id_program','=','transaksi.id_program')
            //                         ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawProgram);
            //             }else if($request->analis == 'status'){
            //                 $data = Transaksi::selectRaw($sel_status)
            //                         ->groupBy('status','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawStatus);
            //             }else if($request->analis == 'petugas'){
            //                 $data = Transaksi::selectRaw($sel_petugas)
            //                         ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawPetugas);
            //             }else if($request->analis == 'bayar'){
            //                 $data = Transaksi::selectRaw($sel_bayar)
            //                         ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawBayar);
            //             }else if($request->analis == 'user'){
            //                         $data = Transaksi::selectRaw($sel_user)
            //                                 ->join('users','users.id','=','transaksi.user_insert')
            //                                 ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
            //                                 ->whereRaw($whereRawUser);
            //             }
            //         }else{
            //           if($request->analis == 'bank'){
            //                 $data = Transaksi::selectRaw($sel_bank)
            //                     ->join('bank','bank.id_bank','=','transaksi.id_bank')
            //                     ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
            //                     ->whereRaw($whereRawBank);
                                                
            //             }else if($request->analis == 'kantor'){
            //                 $data = Transaksi::selectRaw($sel_kantor) 
            //                         ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
            //                         ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
            //                         ->whereRaw($whereRawKantor);
            //             }else if($request->analis == 'jam'){
            //                 $data = Transaksi::selectRaw($sel_jam)
            //                         ->groupBy('jam','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'tanggal'){
            //                 $data = Transaksi::selectRaw($sel_tanggal)
            //                         ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'bulan'){
            //                 $data = Transaksi::selectRaw($sel_bulan)
            //                         ->groupBy('bln','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'tahun'){
            //                 $data = Transaksi::selectRaw($sel_tahun)
            //                         ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawJTBT);
            //             }else if($request->analis == 'donatur'){
            //                 $data = Transaksi::selectRaw($sel_donatur)
            //                         ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawDonatur);
            //             }else if($request->analis == 'program'){
            //                 $data = Transaksi::selectRaw($sel_program)
            //                         ->join('prog','prog.id_program','=','transaksi.id_program')
            //                         ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawProgram);
            //             }else if($request->analis == 'status'){
            //                 $data = Transaksi::selectRaw($sel_status)
            //                         ->groupBy('status','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawStatus);
            //             }else if($request->analis == 'petugas'){
            //                 $data = Transaksi::selectRaw($sel_petugas)
            //                         ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawPetugas);
            //             }else if($request->analis == 'bayar'){
            //                 $data = Transaksi::selectRaw($sel_bayar)
            //                         ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawBayar);
            //             }else if($request->analis == 'user'){
            //                 $data = Transaksi::selectRaw($sel_user)
            //                         ->join('users','users.id','=','transaksi.user_insert')
            //                         ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
            //                         ->whereRaw($whereRawUser);
            //             }
            //         }
            //     }
                
            //     $kotah = $request->kotal;
                
            //     $data->where(function($query) use ($request, $kotah) {
            //         if(isset($request->kotal)){
            //             $query->whereIn('transaksi.id_kantor', $kotah);
            //         }
            //     });
                
            //     $data_real = [];
            //     $transaksi = 0;
            //     $nontransaksi = 0;
            //     $don = 0;
            //     $don1 = 0;
            //     $datal = 0;
                
            //     $yu = 0;
            //     $yuu = 0;
            //     $yuuu = 0;
                
            //     // return($data->get());
                
            //     if(count($data->get()) > 0){
            //         foreach($data->get() as $k){
            //             $transaksi += $k->transaksi;
            //             $nontransaksi += $k->nontransaksi;
            //             $datal += $k->data;
            //             $don += $k->don;
            //             $don1 += $k->don1;
            //             $yu = $k->yu;
            //             $yuu = $k->yuu;
            //             $yuuu = $k->yuuu;
            //         }
                    
                    
                     
            //         $data_real = [
            //             'persen_dn' => round(($datal/$yu)*100, 2),
            //             'persen_nm' => round(($don/$yuu)*100, 2),
            //             'persen_tr' => round(($transaksi/$yuuu)*100, 2),
            //             'yu' => $yu,
            //             'yuu' => $yuu,
            //             'yuuu' => $yuuu,
            //             'transaksi' => $transaksi,
            //             'nontransaksi' => $nontransaksi,
            //             'don1' => $don1,
            //             'don' => $don,
            //             'data' => $datal,
            //         ];
                    
            //     }else{
            //         $data_real = [
            //             'persen_dn' => 0,
            //             'persen_nm' => 0,
            //             'persen_tr' => 0,
            //             'yu' => 0,
            //             'yuu' => 0,
            //             'yuuu' => 0,
            //             'transaksi' => 0,
            //             'don' => 0,
            //             'data' => 0,
            //         ];
            //     }
                
                
            //     $data1 = $data_real;
            //     return $data1;
            // }
        }
        
        $pem = Transaksi::selectRaw("DISTINCT(pembayaran) as pembayaran")->whereRaw("pembayaran IS NOT NULL AND via_input = 'transaksi'")->get();
        
        return view('report-management.analisis_transaksi', compact('kantor','analisnya','pem'));  
    }
    
    public function getPetugas(Request $request){
        // return $request;
        if($request->plhtgl == 0){
            
            if($request->tgl != '') {
                $tgl = explode(' s.d. ', $request->tgl);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $now = date('Y-m-d');
            $m = date('m');
            $y = date('Y');
            
            $periode = $request->tgl != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        }else if($request->plhtgl == 1){
            $periode = $request->bulan != '' ? "MONTH(transaksi.tanggal) = '$request->bulan'" : "MONTH(transaksi.tanggal) = '$m"; 
        }else if($request->plhtgl == 2){
            $periode = $request->tahun != '' ? "YEAR(transaksi.tanggal) = '$request->tahun'" : "MONTH(transaksi.tanggal) = '$y'"; 
        }
        
        $datas = Transaksi::selectRaw("DISTINCT(id_koleks), kolektor")
                ->where(function($query) use ($request) {
                    if(isset($request->kota)){
                        $query->whereIn('id_kantor', $request->kota);
                    }else{
                        $query->where('id_kantor', Auth::user()->id_kantor);
                    }
                })
                ->whereRaw("via_input = 'transaksi' AND $periode")
                ->orderBy('kolektor', 'asc')
                ->get();
        
        return $datas;
    }
    
    public function chart_transaksi(Request $request){
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        $analisnya = 'Analis';
        
        // $year = $request->tahun == '' ? "YEAR(transaksi.tanggal) = '$y'" : "YEAR(transaksi.tanggal) = '$request->tahun'" ;
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
        $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        if($request->approv == 2){
            $approve = "approval = '2'";
        }else if($request->approv == 1){
            $approve = "approval = '1'";
        }else{
            $approve = "approval IS NOT NULL";
        }
        
        if($request->petugas != ''){
            $pee = "id_koleks = '$request->petugas'";
        }else{
            $pee = "id_koleks IS NOT NULL";
        }
        
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
        $bul1 = '01-'.$b1;
        $bul2 = '01-'.$b2;
        
        $bula1 = date('Y-m', strtotime($bul1));
        $bula2 = date('Y-m', strtotime($bul2));

        $real = "DATE_FORMAT(tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
        
        if($request->persen == 'nominal'){
            $persen_nih = "sum(transaksi.jumlah) as data";
        }else if($request->persen == 'donatur'){
            $persen_nih = "count(distinct(transaksi.id_donatur)) as data";
        }else if($request->persen == 'transaksi'){
            $persen_nih = "count(transaksi.id) as data";
        }
        
        $bayar = $request->bay;
        
        $now = date('Y-m-d');
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        $kotah = $request->kotal;
        
        $prd = $request->plhtgl;
        if($prd == 0){
            $waktu = $tgls;
        }else if($prd == 1){
            $waktu = $real;
        }else if($prd == 2){
            $waktu = $year;
        }
        
        if(Auth::user()->kolekting == 'admin'){
            $que = "transaksi.id_kantor IS NOT NULL";
        }else{
            if($k == null){
                $que = "transaksi.id_kantor = '$kntr'";
            }else{
                $que = "(transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')";
            }
        }
        
        if($request->analis == 'bank'){
            $data = Transaksi::selectRaw("transaksi.id_bank, $persen_nih, nama_bank, count(id)  as transaksi, count(id_donatur)  as don, no_rek")
                    ->join('bank','bank.id_bank','=','transaksi.id_bank')
                    ->groupBy('transaksi.id_bank', 'nama_bank')
                    ->whereRaw("$waktu AND transaksi.id_bank IS NOT NULL AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'kantor'){
            $data = Transaksi::selectRaw("transaksi.id_kantor, $persen_nih, count(transaksi.id)  as transaksi, count(id_donatur)  as don, unit")
                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->groupBy('transaksi.id_kantor' )
                    ->whereRaw("$waktu AND transaksi.id_kantor IS NOT NULL AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'jam'){
            $data = Transaksi::selectRaw("$persen_nih, HOUR(transaksi.created_at) AS jam, count(id)  as transaksi, count(id_donatur)  as don")
                    ->groupBy('jam' )
                    ->whereRaw("$waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'tanggal'){
            $data = Transaksi::selectRaw("$persen_nih, DAY(tanggal) AS tgl, DAY(tanggal) as lit , count(id)  as transaksi, count(id_donatur)  as don")
                    ->groupBy('lit','tgl' )
                    ->whereRaw("$waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'tahun'){
            $data = Transaksi::selectRaw("$persen_nih, YEAR(tanggal) AS tgl, count(id)  as transaksi, count(id_donatur)  as don")
                    ->groupBy('tgl')
                    ->whereRaw("$waktu AND $pee AND  $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'bulan'){
            $data = Transaksi::selectRaw("$persen_nih, MONTH(tanggal) AS month, count(id)  as transaksi, count(id_donatur)  as don")
                    ->groupBy('month')
                    ->orderBy('month', 'asc')
                    ->whereRaw("$waktu AND $pee AND  via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }else if($request->analis == 'donatur'){
            $data = Transaksi::selectRaw("$persen_nih, donatur, count(id)  as transaksi, 1 as don, id_donatur")
                    ->groupBy('id_donatur','donatur' )
                    ->whereRaw("$waktu AND $pee AND  id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que")
                    ->orderBy('data','desc')
                    ->limit(5);
        }else if($request->analis == 'program'){
            $data = Transaksi::selectRaw("$persen_nih, prog.program, count(id)  as transaksi, count(id_donatur) as don")
                    ->join('prog','prog.id_program','=','transaksi.id_program')
                    ->groupBy('program')
                    ->whereRaw("$waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que")
                    ->orderBy('data','desc')
                    ->limit(5);
        }else if($request->analis == 'status'){
            $data = Transaksi::selectRaw("$persen_nih, status, count(id)  as transaksi, count(id_donatur) as don")
                    ->groupBy('status')
                    ->whereRaw("$waktu AND $pee AND  $approve AND via_input = 'transaksi' AND $que");
        }else if($request->analis == 'petugas'){
            $data = Transaksi::selectRaw("$persen_nih, kolektor, id_koleks, count(id)  as transaksi, count(id_donatur) as don")
                    ->groupBy('id_koleks','kolektor')
                    ->whereRaw("$waktu AND  id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que")
                    ->orderBy('data','desc')
                    ->limit(5);
        }else if($request->analis == 'bayar'){
            $data = Transaksi::selectRaw("$persen_nih, pembayaran, count(id)  as transaksi, count(id_donatur)  as don")
                    ->groupBy('pembayaran')
                    ->orderBy('data', 'desc')
                    ->whereRaw("$waktu AND $pee AND $approve AND via_input = 'transaksi' AND $que");
        }else if($request->analis == 'user'){
            $data = Transaksi::selectRaw("$persen_nih, users.name, count(transaksi.id)  as transaksi, count(id_donatur)  as don")
                    ->join('users','users.id','=','transaksi.user_insert')
                    ->groupBy('users.name')
                    ->orderBy('data', 'desc')
                    ->whereRaw("$waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $que");
        }
        
        $data->where(function($query) use ($request, $bayar) {
            if(isset($bayar)){
                $query->whereIn('transaksi.pembayaran', $bayar);
            }
        });
                
        $data->where(function($query) use ($request, $kotah) {
            if(isset($request->kotal)){
                $query->whereIn('transaksi.id_kantor', $kotah);
            }
        });
        
        $da = $data->get();
        
        $bulan = array (1 => 'Januari', 'Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
        
        $datas = [];
        
        if($request->analis == 'bank'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['nama_bank'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'bulan'){
            // return($da);
            foreach($da as $d){
                $datas['categories'][] = [ $bulan[$d['month']] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'kantor'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['unit'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'jam'){
            // return($da);
            foreach($da as $d){
                $datas['categories'][] = [ 'Jam '.$d['jam'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'tanggal'){
            foreach($da as $d){
                $datas['categories'][] = [ 'Tanggal '.$d['tgl'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'tahun'){
            foreach($da as $d){
                $datas['categories'][] = [$d['tgl']];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'donatur'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['donatur'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'program'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['program'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'status'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['status'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'petugas'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['kolektor'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'bayar'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['pembayaran'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }else if($request->analis == 'user'){
            foreach($da as $d){
                $datas['categories'][] = [ $d['name'] ];
                $datas['items'][] = [ $d['data'] ]; 
            }
        }
        
        return $datas;
    }
    
    public function get_data_id($id, Request $request){ 
        $y = date('Y');
        if($request->ajax())
        {
            // $year = $request->tahun == '' ? "YEAR(transaksi.tanggal) = '$y'" : "YEAR(transaksi.tanggal) = '$request->tahun'" ;
            $thn1 = $request->tahun == '' ? $y : $request->tahun;
            $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
            $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            if($request->approv == 2){
                $approve = "approval = '2'";
            }else if($request->approv == 1){
                $approve = "approval = '1'";
            }else{
                $approve = "approval IS NOT NULL";
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            // $b1 = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            // $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
        
            // $real = "DATE_FORMAT(transaksi.tanggal,'%m-%Y') >= '$b1' AND DATE_FORMAT(transaksi.tanggal,'%m-%Y') <= '$b2'";
            $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
            $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
            $bul1 = '01-'.$b1;
            $bul2 = '01-'.$b2;
            
            $bula1 = date('Y-m', strtotime($bul1));
            $bula2 = date('Y-m', strtotime($bul2));

            $real = "DATE_FORMAT(tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
            
            $jumlah = $request->kondisi == '123' ? "transaksi.jumlah = 0" : "transaksi.jumlah > 0";
            
            // if($request->bay == ''){
            //     $bay = "transaksi.pembayaran IS NOT NULL";
            // }else if($request->bay == 'cash'){
            //     $bay = "transaksi.pembayaran != 'noncash'";
            // }else if($request->bay == 'noncash'){
            //     $bay = "transaksi.pembayaran = 'noncash'";
            // }
            
            if(isset($request->kotal)){
                $kaadal = "AND transaksi.id_kantor IN ('" . implode("', '", $request->kotal) . "')";
            }else{
                $kaadal = null;
            }
            
            if($request->petugas != ''){
                $pee = "id_koleks = '$request->petugas'";
            }else{
                $pee = "id_koleks IS NOT NULL"; 
            }
            
            $bay = $request->bay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN ('" . implode("', '", $request->bay) . "')";
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
            
            $kan = Auth::user()->id_kantor;
            $k = Kantor::select('level')->where('kantor_induk', Auth::user()->id_kantor)->first();
            
            $data = [];
            if($request->plhtgl == 0){
                if($request->analis == 'bank'){
                     $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.id_bank = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'tanggal'){
                    $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND DAY(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'tahun'){
                    $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND YEAR(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'bulan'){
                    $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND MONTH(tanggal) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'kantor'){
                    $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.id_kantor = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah AND $pee")
                            ->get();
                }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay $kaadal AND HOUR(created_at) = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah AND $pee")
                            ->get();
                }else if($request->analis == 'donatur'){
                    $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                            ->whereRaw("$tgls AND $bay AND transaksi.id_donatur = '$id'  AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'program'){
                        $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.id_program = '$id'  AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->where(function($query) use ($kan, $k, $request){
                                if(Auth::user()->level == 'kacab'){
                                    if($k == null){
                                        $query->where('transaksi.id_kantor', $kan);
                                    }else{
                                        $query->whereRaw("transaksi.id_kantor = '$kan' OR transaksi.id_kantor = '$k->id'");
                                    }
                                }
                            })
                            ->get();
                }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                            ->whereRaw("$tgls AND $bay AND transaksi.status = '$id' AND $approve AND via_input = 'transaksi' $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'petugas'){
                        $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.id_koleks = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'bayar'){
                        $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.pembayaran = '$id' AND $approve AND via_input = 'transaksi' $kaadal AND $pee")
                            ->get();
                }else if($request->analis == 'user'){
                        $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->join('users','users.id','=','transaksi.user_insert')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$tgls AND $bay AND transaksi.user_insert = '$id' AND $approve AND via_input = 'transaksi' AND $jumlah $kaadal AND $pee")
                            ->get();
                }
            }else if($request->plhtgl == 1){
                if($request->analis == 'bank'){
                     $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.id_bank = '$id' AND $pee AND $bay AND $approve $kaadal AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'tanggal'){
                    $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" DAY(tanggal) = '$id' AND $pee AND $bay AND $approve $kaadal AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'bulan'){
                    $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" MONTH(tanggal) = '$id AND $pee' AND $bay AND $approve $kaadal AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'tahun'){
                    $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" YEAR(tanggal) = '$id' AND $pee AND $bay AND $approve $kaadal AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'kantor'){
                    $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.id_kantor = '$id' AND $pee AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" HOUR(created_at) = '$id' AND $pee  AND $bay $kaadal AND  $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'donatur'){
                    $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.id_donatur = '$id' AND $pee AND  $bay $kaadal AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'program'){
                        $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.id_program = '$id' AND $pee AND $bay $kaadal AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->where(function($query) use ($kan, $k, $request){
                                if(Auth::user()->level == 'kacab'){
                                    if($k == null){
                                       $query->where('transaksi.id_kantor', $kan);
                                    }else{
                                        $query->whereRaw("transaksi.id_kantor = '$kan' OR transaksi.id_kantor = '$k->id'");
                                    }
                                }
                            })
                            ->get();
                }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                            ->whereRaw(" transaksi.status = '$id' AND $pee AND $bay $kaadal AND $approve AND via_input = 'transaksi' AND $real")
                            ->get();
                }else if($request->analis == 'petugas'){
                        $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("transaksi.id_koleks = '$id' AND $pee AND $kaadal $bay AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }else if($request->analis == 'bayar'){
                        $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.pembayaran = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $real")
                            ->get();
                }else if($request->analis == 'user'){
                        $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->join('users','users.id','=','transaksi.user_insert')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$bay AND transaksi.user_insert = '$id' AND $pee $kaadal AND $approve AND via_input = 'transaksi' AND $jumlah AND $real")
                            ->get();
                }
            }else if($request->plhtgl == 2){
                if($request->analis == 'bank'){
                     $data = Transaksi::selectRaw("transaksi.id_bank, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_bank','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND transaksi.id_bank = '$id' AND $pee $kaadal AND  $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'tanggal'){
                    $data = Transaksi::selectRaw("DAY(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND DAY(tanggal) = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                            
                }else if($request->analis == 'tahun'){
                    $data = Transaksi::selectRaw("YEAR(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND YEAR(tanggal) = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'bulan'){
                    $data = Transaksi::selectRaw("MONTH(tanggal) AS tgl, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND MONTH(tanggal) = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'kantor'){
                    $data = Transaksi::selectRaw("transaksi.id_kantor, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_kantor','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND transaksi.id_kantor = '$id' AND $pee  AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("HOUR(created_at) AS jam, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('jam','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND HOUR(created_at) = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'donatur'){
                    $data = Transaksi::selectRaw("transaksi.id_donatur, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_donatur','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND transaksi.id_donatur = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->where(function($query) use ($kan, $k, $request){
                                if(Auth::user()->level == 'kacab'){
                                    if($k == null){
                                        $query->where('transaksi.id_kantor', $kan);
                                    }else{
                                        $query->whereRaw("transaksi.id_kantor = '$kan' OR transaksi.id_kantor = '$k->id'");
                                    }
                                }
                            })
                            ->get();
                }else if($request->analis == 'program'){
                        $data = Transaksi::selectRaw("transaksi.id_program, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_program','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND transaksi.id_program = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("transaksi.status, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->groupBy('transaksi.status','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal')
                            ->whereRaw("$year AND transaksi.status = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi'")
                            ->get();
                }else if($request->analis == 'petugas'){
                        $data = Transaksi::selectRaw("transaksi.id_koleks, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal,transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$year AND transaksi.id_koleks = '$id' AND $pee $kaadal AND $bay AND $approve AND via_input = 'transaksi' AND $jumlah")
                            ->get();
                }else if($request->analis == 'bayar'){
                        $data = Transaksi::selectRaw("transaksi.pembayaran, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw(" transaksi.pembayaran = '$id' AND $pee $kaadal AND $approve AND $bay AND via_input = 'transaksi' AND $year")
                            ->get();
                }else if($request->analis == 'user'){
                        $data = Transaksi::selectRaw("users.name, id_transaksi, prog.program, jumlah, donatur,transaksi.tanggal, transaksi.id_donatur, transaksi.kolektor, status")
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->join('users','users.id','=','transaksi.user_insert')
                            // ->groupBy('transaksi.id_koleks','id_transaksi', 'prog.program', 'jumlah','donatur','transaksi.tanggal','transaksi.id_donatur')
                            ->whereRaw("$bay AND transaksi.user_insert = '$id' AND $pee $kaadal AND $approve AND via_input = 'transaksi' AND $jumlah AND $year")
                            ->get();
                }
            }
            
            $datas = [];
            
            // if(count($data) > 0){
                
            // }else{
                
            // }
            // $datas[] = [
            //     donatur => null,
            //     id_donatur => null,
            //     id_kantor => null,
            //     id_transaksi => null,
            //     jumlah => null,
            //     kolektor => null,
            //     program => null,
            //     tanggal => null
            //     ];
            
            return $data;
        }
    }
    
    public function analis_don(Request $request){
        $kantor = Kantor::all();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        $analisnya = 'Analis';
        if($request->ajax()){
            // $year = $request->tahun == '' ? "YEAR(transaksi.tanggal) = $y" : "YEAR(transaksi.tanggal) = $request->tahun" ;
            $thn1 = $request->tahun == '' ? $y : $request->tahun;
            $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
            $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            if($request->approv == 2){
                $approve = "approval = 2";
            }else if($request->approv == 1){
                $approve = "approval = 1";
            }else{
                $approve = "approval IS NOT NULL";
            }
            
            if($request->bay == ''){
                $bay = "transaksi.pembayaran IS NOT NULL";
            }else if($request->bay == 'cash'){
                $bay = "transaksi.pembayaran != 'noncash'";
            }else if($request->bay == 'noncash'){
                $bay = "transaksi.pembayaran = 'noncash'";
            }
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
            $darik = $request->daterange != '' ? $dari : $now ;
            $sampaik = $request->daterange != '' ? $sampai :  $now;
            
            if($request->plhtgl == 0){
                $data = Donatur::selectRaw("count(donatur.id) as aktif, id_kantor, unit, '$request->persen' as persen, $request->plhtgl as pilih, $request->approv as appr, '$bulan' as bulan, '$tahun' as tahun, '$darik' as dari, '$sampaik' as sampai, 
                        (SELECT count(id) from donatur where status != 'Ditarik' AND status != 'Off' ) as aw,
                        (SELECT count(id_donatur) from transaksi where status != 'Ditarik' AND status != 'Off' ) as aww,
                        (SELECT count(id_donatur) from transaksi where jumlah > 0 ) as awww
                        ")
                        ->join('tambahan','donatur.id_kantor','=','tambahan.id')
                        ->groupBy('id_kantor','unit','aw','awww','aww','persen', 'pilih','appr','bulan','tahun','dari','sampai')
                        ->whereRaw("status != 'Ditarik' AND status != 'Off'");
            //          }  
            }else if($request->plhtgl == 1){
                $data = Donatur::selectRaw("count(donatur.id) as aktif, id_kantor, unit, '$request->persen' as persen, $request->plhtgl as pilih, $request->approv as appr, '$bulan' as bulan, '$tahun' as tahun, '$darik' as dari, '$sampaik' as sampai, 
                        (SELECT count(id) from donatur where status != 'Ditarik' AND status != 'Off' ) as aw,
                        (SELECT count(id_donatur) from transaksi where status != 'Ditarik' AND status != 'Off' ) as aww,
                        (SELECT count(id_donatur) from transaksi where jumlah > 0 ) as awww
                        ")
                        ->join('tambahan','donatur.id_kantor','=','tambahan.id')
                        ->groupBy('id_kantor','unit','aw','awww','aww','persen', 'pilih','appr','bulan','tahun','dari','sampai')
                        ->whereRaw("status != 'Ditarik' AND status != 'Off'");
            }else if($request->plhtgl == 2){
                 $data = Donatur::selectRaw("count(donatur.id) as aktif, id_kantor, unit, '$request->persen' as persen, $request->plhtgl as pilih, $request->approv as appr, '$bulan' as bulan, '$tahun' as tahun, '$darik' as dari, '$sampaik' as sampai,  
                        (SELECT count(id) from donatur where status != 'Ditarik' AND status != 'Off' ) as aw,
                        (SELECT count(id_donatur) from transaksi where status != 'Ditarik' AND status != 'Off' ) as aww,
                        (SELECT count(id_donatur) from transaksi where jumlah > 0 ) as awww
                        ")
                        ->join('tambahan','donatur.id_kantor','=','tambahan.id')
                        ->groupBy('id_kantor','unit','aw','awww','aww','persen','pilih','bulan','tahun','dari','sampai','appr')
                        ->whereRaw("status != 'Ditarik' AND status != 'Off'");
            }
            
            // $dat = Transaksi::selectRaw("count(id_donatur) as ppp")->whereRaw("status != 'Ditarik' AND status != 'Off' AND $bulanan")->groupBy('id_kantor');
                    
            // return($dat->get());
            
            return DataTables::of($data)
            ->addColumn('nonaktif', function($data){
                if($data->appr == 2){
                    $approve = "approval = 2";
                }else if($data->appr == 1){
                    $approve = "approval = 1";
                }else{
                    $approve = "approval IS NOT NULL";
                }
                
                if($data->pilih == 0){
                    $pilihlah = "DATE(transaksi.tanggal) >= '$data->dari' AND DATE(transaksi.tanggal) <= '$data->sampai'";
                }else if($data->pilih == 1){
                    $pilihlah = "MONTH(transaksi.tanggal) = '$data->bulan' AND YEAR(transaksi.tanggal) = '$data->tahun'";
                }else if($data->pilih == 2){
                    $pilihlah = "YEAR(tanggal) = '$data->tahun'";
                }
                
                $trr = Transaksi::selectRaw("count(id_donatur) as ppp")->whereRaw("id_kantor = '$data->id_kantor' AND status != 'Ditarik' AND status != 'Off' AND $pilihlah AND $approve")->groupBy('id_kantor'); 
                if(count($trr->get()) > 0){
                    return $trr->first()->ppp;
                }else{
                    return 0;
                }
            })
            ->addColumn('trans', function($data){
                if($data->appr == 2){
                    $approve = "approval = 2";
                }else if($data->appr == 1){
                    $approve = "approval = 1";
                }else{
                    $approve = "approval IS NOT NULL";
                }
                
                if($data->pilih == 0){
                    $pilihlah =  "DATE(transaksi.tanggal) >= '$data->dari' AND DATE(transaksi.tanggal) <= '$data->sampai'";
                }else if($data->pilih == 1){
                    $pilihlah = "MONTH(transaksi.tanggal) = '$data->bulan' AND YEAR(transaksi.tanggal) = '$data->tahun'";
                }else if($data->pilih == 2){
                    $pilihlah = "YEAR(tanggal) = '$data->tahun'";
                }
                
                $trr = Transaksi::selectRaw("count(id_donatur) as ppp")->whereRaw("id_kantor = '$data->id_kantor' AND jumlah > 0 AND $pilihlah AND $approve")->groupBy('id_kantor'); 
                if(count($trr->get()) > 0){
                    return $trr->first()->ppp;
                }else{
                    return 0;
                }
            })
            
            ->addColumn('persen', function($data){
                if($data->appr == 2){
                    $approve = "approval = 2";
                }else if($data->appr == 1){
                    $approve = "approval = 1";
                }else{
                    $approve = "approval IS NOT NULL";
                }
                
                if($data->pilih == 0){
                    $pilihlah =  "DATE(transaksi.tanggal) >= '$data->dari' AND DATE(transaksi.tanggal) <= '$data->sampai'";
                }else if($data->pilih == 1){
                    $pilihlah = "MONTH(transaksi.tanggal) = '$data->bulan' AND YEAR(transaksi.tanggal) = '$data->tahun'";
                }else if($data->pilih == 2){
                    $pilihlah = "YEAR(tanggal) = '$data->tahun'";
                }
                
                
                if($data->persen == 'ak'){
                    $ppp = ($data->aktif/$data->aw)*100;
                }else if($data->persen == 'non'){
                    $trr = Transaksi::selectRaw("count(id_donatur) as ppp")->whereRaw("id_kantor = '$data->id_kantor' AND status != 'Ditarik' AND status != 'Off' AND $approve AND $pilihlah")->groupBy('id_kantor');  
                    if(count($trr->get()) > 0){
                        $ppp =  ($trr->first()->ppp/$data->aww)*100;
                    }else{
                        $ppp =  0;
                    }
                }else if($data->persen == 'tran'){
                    $trr = Transaksi::selectRaw("count(id_donatur) as ppp")->whereRaw("id_kantor = '$data->id_kantor' AND jumlah > 0 AND $approve AND $pilihlah")->groupBy('id_kantor'); 
                    // dd($trr->get());
                    if(count($trr->get()) > 0){
                        $ppp =  ($trr->first()->ppp/$data->awww)*100;
                    }else{
                        $ppp =  0;
                    }
                }
                    return round($ppp,3);
            })
            
            ->rawColumns(['nonaktif','trans'])
            ->make(true);
        }
        
        return view('reportmanagement.analis_transaksi', compact('kantor','analisnya'));  
    }
    
    public function analis_kunjungan(Request $request){
        $kantor = Kantor::all();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        $analisnya = 'Analis';
        if($request->ajax()){
            
            $thn1 = $request->tahun == '' ? $y : $request->tahun;
            $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
            $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
            $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
            $bul1 = '01-'.$b1;
            $bul2 = '01-'.$b2;
            
            $bula1 = date('Y-m', strtotime($bul1));
            $bula2 = date('Y-m', strtotime($bul2));

            $real = "DATE_FORMAT(tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
            
            $pembayaran = $request->pembay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN (" . implode(",", $request->pembay) . ")";
            
            if($request->approv == 2){
                $approve = "transaksi.approval = 2";
            }else if($request->approv == 1){
                $approve = "transaksi.approval = 1";
            }else{
                $approve = "transaksi.approval IS NOT NULL";
            }
            
            // if($request->bay == ''){
            //     $bay = "transaksi.pembayaran IS NOT NULL";
            // }else if($request->bay == 'cash'){
            //     $bay = "transaksi.pembayaran != 'noncash'";
            // }else if($request->bay == 'noncash'){
            //     $bay = "transaksi.pembayaran = 'noncash'";
            // }
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
            $darik = $request->daterange != '' ? $dari : $now ;
            $sampaik = $request->daterange != '' ? $sampai :  $now;
            
            if($request->plhtgl == 0){
                $data = Transaksi::join('donatur', 'transaksi.id_donatur', '=', 'donatur.id')->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("transaksi.donatur, donatur.id,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Donasi' AND $pembayaran, transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Tidak Donasi' AND $pembayaran, transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Tutup'  AND $pembayaran, transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Tutup 2x' AND $pembayaran, transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Ditarik' AND $pembayaran, transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( $tgls AND transaksi.status = 'Kotak Hilang' AND $pembayaran, transaksi.id_donatur, NULL)) AS k_hilang"))
                        ->whereRaw("$approve AND $pembayaran AND $tgls")
                        ->groupBy('donatur','id');
            
            }else if($request->plhtgl == 1){
                $data = Donatur::join('transaksi', 'transaksi.id_donatur', '=', 'donatur.id')->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("transaksi.donatur, donatur.id,
                                COUNT(DISTINCT IF(  transaksi.status = 'Donasi' AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS donasi,
                                COUNT(DISTINCT IF(  transaksi.status = 'Tidak Donasi' AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF(  transaksi.status = 'Tutup'  AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS tutup,
                                COUNT(DISTINCT IF(  transaksi.status = 'Tutup 2x' AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF(  transaksi.status = 'Ditarik' AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS ditarik,
                                COUNT(DISTINCT IF(  transaksi.status = 'Kotak Hilang' AND $pembayaran, transaksi.id_donatur AND $real, NULL)) AS k_hilang"))
                        ->whereRaw("$approve AND $pembayaran AND via_input = 'transaksi' AND $real")
                        ->groupBy('donatur','id');
                        
            }else if($request->plhtgl == 2){
                 $data = Donatur::join('transaksi', 'transaksi.id_donatur', '=', 'donatur.id')->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("transaksi.donatur, donatur.id,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Donasi' AND $pembayaran, transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Tidak Donasi' AND $pembayaran, transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Tutup'  AND $pembayaran, transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Tutup 2x' AND $pembayaran, transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Ditarik' AND $pembayaran, transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( $year AND transaksi.status = 'Kotak Hilang' AND $pembayaran, transaksi.id_donatur, NULL)) AS k_hilang"))
                        ->whereRaw("$approve AND $pembayaran AND $year")
                        ->groupBy('donatur','id');
            }
            
            return DataTables::of($data)
            ->addColumn('totol', function ($data) {
                $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                return $cuk1;
            })
            
            ->rawColumns(['totol'])
            ->make(true);
        }
        
        return view('reportmanagement.analis_transaksi', compact('kantor','analisnya'));  
    }
    
    public function kunjungan_by_id(Request $request){
        // return($request);
        
        
        $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $thn1:  $request->tahun2;
        $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        $pembayaran = $request->pembay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran = '$request->pembay'";
        
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
        $bul1 = '01-'.$b1;
        $bul2 = '01-'.$b2;
        
        $bula1 = date('Y-m', strtotime($bul1));
        $bula2 = date('Y-m', strtotime($bul2));

        $real = "DATE_FORMAT(tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
            
        if($request->approv == 2){
            $approve = "transaksi.approval = 2";
        }else if($request->approv == 1){
            $approve = "transaksi.approval = 1";
        }else{
            $approve = "transaksi.approval IS NOT NULL";
        }
        
        // if($request->bay == ''){
        //     $bay = "transaksi.pembayaran IS NOT NULL";
        // }else if($request->bay == 'cash'){
        //     $bay = "transaksi.pembayaran != 'noncash'";
        // }else if($request->bay == 'noncash'){
        //     $bay = "transaksi.pembayaran = 'noncash'";
        // }
        
        $bay = $request->bay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN ('" . implode("', '", $request->bay) . "')";
            
        $now = date('Y-m-d');
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        $darik = $request->daterange != '' ? $dari : $now ;
        $sampaik = $request->daterange != '' ? $sampai :  $now;
        $id = $request->id;
        $jenis = $request->jenis;
            
        if($request->plhtgl == 0){
            $data = Transaksi::whereRaw("id_donatur = '$id' AND $bay AND $tgls AND status = '$jenis' AND $pembayaran  AND via_input = 'transaksi'");
        }else if($request->plhtgl == 1){
            $data = Transaksi::whereRaw("id_donatur = '$id' AND $bay AND status = '$jenis' AND $pembayaran  AND via_input = 'transaksi' AND $real");
        }else if($request->plhtgl == 2){
            $data = Transaksi::whereRaw("id_donatur = '$id' AND $bay AND $year AND status = '$jenis' AND $pembayaran  AND via_input = 'transaksi'");
        }
        
        return $data->get();
    }
    
    public function analis_donatur(Request $request){
        $kan = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if(Auth::user()->kolekting == 'admin'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->kolekting == 'kacab' ){
            if($k == null){
                $kantor = Kantor::where('id', Auth::user()->id_kantor)->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $kan OR id = $k->id)")->get();
            }
        }
        return view('report-management.analisis_donatur', compact('kantor'));  
    }
    
    public function chart_donatur(Request $request){
        $kot = Auth::user()->id_kantor;
        $cari = Kantor::where('kantor_induk', $kot)->first();
        

        if($request->ajax()){
            $nows = $request->tahun == '' ? date('Y') : $request->tahun;
            $now = $nows ;
            $multi =  $request->mulbul;
            $kan = $request->skntr;
            $kntr = "id_kantor IS NOT NULL" ;
            $multikan = $multi == '1' ? $request->mulkntr : $kan;
            $kntr_ku = $request->kntr == '' ? "1" : $request->kntr;
            $bulan = array (1 => 'Januari', 'Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
            
            if(Auth::user()->kolekting == ('admin')){
                if($request->analis == 'kantor'){
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                    // $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now'")
                    //         ->where(function ($query) use ($multikan,$multi) {
                    //             if ($multi == "1") {
                    //                     $query->whereIn(DB::raw('id_kantor'), $multikan);
                    //              }else if($multi == "0" ) {
                    //                     $query->where('id_kantor', $multikan);
                    //         }
                    //         })
                    // ->get();
                }else if($request->analis == 'cara_bayar'){
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(pembayaran)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                    
                    
                    // $datkan = Donatur::selectRaw("DISTINCT(pembayaran)")->whereRaw("YEAR(created_at) = '$now'")->whereIn('id_kantor', $multikan)->get();
                }else if($request->analis == 'status'){
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(status)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                    
                    // $datkan = Donatur::selectRaw("DISTINCT(status)")->whereRaw("YEAR(created_at) = '$now'")->where('id_kantor', $multikan)->get();
                }else if($request->analis == 'jenis'){
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(jenis_donatur)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                    
                    
                    // $datkan = Donatur::selectRaw("DISTINCT(jenis_donatur)")->whereRaw("YEAR(created_at) = '$now'")->where('id_kantor', $multikan)->get();
                }else if($request->analis == 'jalur' || $request->analis == 'kntr'){
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(jalur)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                }else if($request->analis == 'warn'){
                    $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                    $kot = Auth::user()->id_kantor;
                    
                    $mindon = $request->mindon;
                    $jumbul = $request->jumbul;
                    $program = $request->prog;
                    
                    $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$program')";
                    
                    if(Auth::user()->level == 'kacab'){
                        if($k == null){
                            $kan = "donatur.id_kantor = '$kot'";
                        }else{
                            $kan = "(donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id')";
                        }
                    }else if(Auth::user()->level == 'admin'){
                        $kan = "donatur.id_kantor IS NOT NULL";
                    }
                    
                    $now = date('Y-m-d');
                    $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
                    $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
                    $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, donatur.id_kantor,
                            SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia, jumlah, 0)) as ju")
                            
                        ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                            ->whereIn('donatur.id', function($q){
                            $q->select('id_don')->from('prosp')->where('ket','closing');
                        })
                        
                        ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                        
                        ->where(function($q) use ($request, $kot, $k) {
                            if(Auth::user()->level == 'kacab'){
                                
                                if($k == null){
                                    $q->where('id_kantor', $kot);
                                }else{
                                    $q->whereRaw("donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id'");
                                }
                            }else if(Auth::user()->level == 'admin'){
                                $q->whereRaw("donatur.id_kantor IS NOT NULL");
                            }
                        })
                        ->groupBy('id_donatur','bulan')
                        ->get();
                        
                    $targetAmount = $mindon;
                    $jumbul = $jumbul;
                     
                    $result = [];
                    $count = 0;
                    $sepong = [];
                    $coy = [];
                    $result2 = [];
                    $tt = [];
                    
                    $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                    
                    foreach ($groupedData as $donatur => $donaturData) {
                        
                        $kon = count(array_column($donaturData, 'bulan'));
                        $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                                return $item['ju'] <  $targetAmount;
                        }));
                        
                        // $result[] = [
                        //     "bulan" => array_column($donaturData, 'bulan'),
                        //     "id_donatur" => $donaturData[0]['id_donatur'],
                        //     "jumlah" => array_column($donaturData, 'ju'),
                        //     "count_bulan" => $kon,
                        //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                            
                        // ];
                        
                        if($kon == $jumbul){
                            if($hasil == $jumbul){
                                
                                $result2[] = [
                                    "id_donatur" => $donaturData[0]['id_donatur'],
                                    'donasi_kurang_dari_'.$targetAmount =>  $hasil,
                                    'kantor' => $donaturData[0]['id_kantor']
                                ];
                                
                                //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1, 'user_warning' => Auth::user()->id, 'jumbul' => $jumbul, 'mindon' => $mindon, 'user_update' => Auth::user()->id ]);
                            }
                        }
                    }
                    
                    $ehe = collect($result2)->groupBy('kantor')->toArray();
                    
                    foreach ($ehe as $do => $don) {
                        $sek = count(array_column($don, 'donasi_kurang_dari_'.$targetAmount));
                        $tt[] = [
                            'kantor' => Kantor::find($do)->unit,
                            'donasi' => $sek,
                        ];
                    }
                    
                    // return $tt;
                    
                    
                }else{
                    $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now'")->get();
                    $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")
                        ->whereRaw("YEAR(created_at) = '$now'")
                        ->where(function ($query) use ($multikan, $multi,$kan) {
                            if ($multi == "1" && is_array($multikan) && !empty($multikan)) {
                                $query->whereIn('id_kantor', $multikan);
                            } elseif ($multi == "0" && !is_null($multikan)) {
                                $query->where('id_kantor', $kan);
                            }
                        })
                        ->get();
                    // $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now'")->where('id_kantor', $multikan)->get();
                }
                

                
            }else if(Auth::user()->kolekting == ('kacab')){
                if($cari == null){
                    if($request->analis == 'kantor'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                    }else if($request->analis == 'cara_bayar'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(pembayaran)")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                    }else if($request->analis == 'status'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(status)")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                    }else if($request->analis == 'jenis'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(jenis_donatur)")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        // return($datkan);
                    }else if($request->analis == 'jalur' || $request->analis == 'kntr'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(jalur)")->whereRaw("YEAR(created_at) = '$now'")->where('id_kantor', $kot)->get();
                    }else if($request->analis == 'warn'){
                        $kot = Auth::user()->id_kantor;
                        
                        $mindon = $request->mindon;
                        $jumbul = $request->jumbul;
                        $program = $request->prog;
                        
                        $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$program')";
                        
                        $kan = "donatur.id_kantor = '$kot'";
                        
                        $now = date('Y-m-d');
                        $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
                        $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
                        $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, donatur.id_kantor,
                                SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia, jumlah, 0)) as ju")
                                
                            ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                                ->whereIn('donatur.id', function($q){
                                $q->select('id_don')->from('prosp')->where('ket','closing');
                            })
                            
                            ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                            
                            ->where(function($q) use ($request, $kot, $k) {
                                $q->whereRaw("donatur.id_kantor = '$kot'");
                            })
                            ->groupBy('id_donatur','bulan')
                            ->get();
                            
                        $targetAmount = $mindon;
                        $jumbul = $jumbul;
                         
                        $result = [];
                        $count = 0;
                        $sepong = [];
                        $coy = [];
                        $result2 = [];
                        $tt = [];
                        
                        $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                        
                        foreach ($groupedData as $donatur => $donaturData) {
                            
                            $kon = count(array_column($donaturData, 'bulan'));
                            $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                                    return $item['ju'] <  $targetAmount;
                            }));
                            
                            if($kon == $jumbul){
                                if($hasil == $jumbul){
                                    
                                    $result2[] = [
                                        "id_donatur" => $donaturData[0]['id_donatur'],
                                        'donasi_kurang_dari_'.$targetAmount =>  $hasil,
                                        'kantor' => $donaturData[0]['id_kantor']
                                    ];
                                }
                            }
                        }
                        
                        $ehe = collect($result2)->groupBy('kantor')->toArray();
                        
                        foreach ($ehe as $do => $don) {
                            $sek = count(array_column($don, 'donasi_kurang_dari_'.$targetAmount));
                            $tt[] = [
                                'kantor' => Kantor::find($do)->unit,
                                'donasi' => $sek,
                            ];
                        }
                    }else{
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now' AND id_kantor = '$kot'")->get();
                    }
                }else{
                    if($request->analis == 'kantor'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                    }else if($request->analis == 'cara_bayar'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(pembayaran)")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                    }else if($request->analis == 'status'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(status)")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                    }else if($request->analis == 'jenis'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(jenis_donatur)")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        // return($datkan);
                    }else if($request->analis == 'jalur' || $request->analis == 'kntr'){
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(jalur)")->whereRaw("YEAR(created_at) = '$now'")->where('id_kantor', $kot)->get();
                        // return($datkan);
                    }else if($request->analis == 'warn'){
                        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                        $kot = Auth::user()->id_kantor;
                        
                        $mindon = $request->mindon;
                        $jumbul = $request->jumbul;
                        $program = $request->prog;
                        
                        $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$program')";
                        
                        $kan = "(donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id')";
                        
                        $now = date('Y-m-d');
                        $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
                        $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
                        $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, donatur.id_kantor,
                                SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia, jumlah, 0)) as ju")
                                
                            ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                                ->whereIn('donatur.id', function($q){
                                $q->select('id_don')->from('prosp')->where('ket','closing');
                            })
                            
                            ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                            
                            ->where(function($q) use ($request, $kot, $k) {
                                $q->whereRaw("donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id'");
                            })
                            ->groupBy('id_donatur','bulan')
                            ->get();
                            
                        $targetAmount = $mindon;
                        $jumbul = $jumbul;
                         
                        $result = [];
                        $count = 0;
                        $sepong = [];
                        $coy = [];
                        $result2 = [];
                        $tt = [];
                        
                        $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                        
                        foreach ($groupedData as $donatur => $donaturData) {
                            
                            $kon = count(array_column($donaturData, 'bulan'));
                            $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                                    return $item['ju'] <  $targetAmount;
                            }));
                            
                            if($kon == $jumbul){
                                if($hasil == $jumbul){
                                    
                                    $result2[] = [
                                        "id_donatur" => $donaturData[0]['id_donatur'],
                                        'donasi_kurang_dari_'.$targetAmount =>  $hasil,
                                        'kantor' => $donaturData[0]['id_kantor']
                                    ];
                                }
                            }
                        }
                        
                        $ehe = collect($result2)->groupBy('kantor')->toArray();
                        
                        foreach ($ehe as $do => $don) {
                            $sek = count(array_column($don, 'donasi_kurang_dari_'.$targetAmount));
                            $tt[] = [
                                'kantor' => Kantor::find($do)->unit,
                                'donasi' => $sek,
                            ];
                        }
                    }else{
                        $datbul = Donatur::selectRaw("DISTINCT(MONTH(created_at)) AS bulan")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                        $datkan = Donatur::selectRaw("DISTINCT(id_kantor)")->whereRaw("YEAR(created_at) = '$now' AND (id_kantor = '$kot' OR id_kantor = '$cari->id')")->get();
                    }
                }
            }
            
            $datas = [];
            if($request->analis == 'warn'){ 
                foreach($tt as $d){
                    $datas['categories'][] =  $d['kantor'] ;
                }       
            }else{
                foreach($datbul as $d){
                    $datas['categories'][] =  $bulan[$d['bulan']] ;
                }
            }
                    
            if($request->analis == 'kantor'){
                foreach($datkan as $d){
                    if($d['id_kantor'] != null){
                        $un = Kantor::where('id', $d['id_kantor']);
                        $dat = [];
                        foreach($datbul as $b){
                            $data = Donatur::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->where('id_kantor', $d['id_kantor'])->count();
                            $dat[] = $data;
                        }
                        
                        $datas['series'][] = [ 
                            'id' => $d['id_kantor'], 
                            'name' => count($un->get()) > 0 ? $un->first()->unit : '' ,
                            'data' => $dat
                        ];
                    }
                }
            }else if($request->analis == 'cara_bayar'){
                foreach($datkan as $d){
                    $dat = [];
                    foreach($datbul as $b){
                        $data = Donatur::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->where('pembayaran', $d['pembayaran'])->count();
                        $dat[] = $data;
                    }
                        
                            // return($dat);
                                
                    $datas['series'][] = [ 
                        'id' => $d['pembayaran'], 
                        'name' => $d->pembayaran,
                        'data' => $dat
                    ];
                }
            }else if($request->analis == 'status'){
                foreach($datkan as $d){
                    $dat = [];
                    foreach($datbul as $b){
                        $data = Transaksi::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->where('status', $d['status'])->count();
                        $dat[] = $data;
                    }
                            
                    $datas['series'][] = [ 
                        'id' => $d['status'], 
                        'name' => $d->status,
                        'data' => $dat
                    ];
                }
            }else if($request->analis == 'jalur'){
                foreach($datkan as $d){
                    $dat = [];
                    foreach($datbul as $b){
                        $data = Donatur::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->whereRaw("jalur = '$d->jalur' AND id_kantor = '$kntr_ku'")->count();
                        $dat[] = $data;
                    }
                            
                            // return($dat);
                            
                    $datas['series'][] = [ 
                        'id' => $d['jalur'], 
                        'name' => $d->jalur,
                        'data' => $dat
                    ];
                }
            }else if($request->analis == 'jenis'){
                foreach($datkan as $d){
                    $dat = [];
                    foreach($datbul as $b){
                        $data = Donatur::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->where('jenis_donatur', $d['jenis_donatur'])->count();
                        $dat[] = $data;
                    }
                            
                    $datas['series'][] = [ 
                        'id' => $d['status'], 
                        'name' => $d->jenis_donatur,
                        'data' => $dat
                    ];
                }
            }else if($request->analis == 'warn'){
                foreach($tt as $d){
                    $datas['series'][] = [ $d['donasi'] ];
                }
                // $datas['series'] =  $tt;
            }else{
                foreach($datkan as $d){
                    $un = Kantor::find($d['id_kantor']);
                    $dat = [];
                    foreach($datbul as $b){
                        $data = Donatur::whereYear('created_at', $now)->whereMonth('created_at', $b['bulan'])->where('id_kantor', $d['id_kantor'])->count();
                        $dat[] = $data;
                    }
                                
                    $datas['series'][] = [ 
                        'id' => $d['id_kantor'], 
                        'name' => $un->unit,
                        'data' => $dat
                    ];
                }
            }
        }
        
        return $datas;
    }
    
    public function export_an (Request $request){
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
            
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
            
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
        
        $bul1 = '01-'.$b1;
        $bul2 = '01-'.$b2;
            
        $bula1 = date('Y-m', strtotime($bul1));
        $bula2 = date('Y-m', strtotime($bul2));
        
        
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
        
        $bayar = $request->bay;
        $pembayaran = $request->bay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN ('" . implode("', '", $bayar) . "')";
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        $tgltext = $request->daterange == '' ? $now.' s.d '.$now : $dari.' s.d '.$sampai;
        $blntext = $b1.' s.d '.$b2;
        $thntext = $thn1.' s.d '.$thn2;
        
        $tgltext1 = $request->daterange == '' ? $now.'-s.d-'.$now : $dari.'-s.d-'.$sampai;
        $blntext1 = $b1.'-s.d-'.$b2;
        $thntext1 = $thn1.'-s.d-'.$thn2;
        
        if($request->petugas != ''){
            $pee = "AND id_koleks = '$request->petugas'";
        }else{
            $pee = "AND id_koleks IS NOT NULL"; 
        }
        
        $analis = $request->analis;        
        $plhtgl = $request->plhtgl;
        
        if($plhtgl == 0){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            }
        }else if($plhtgl == 1){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            }
        }else if($plhtgl == 2){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            }
        }
        
        if($request->approv == 2){
            $approve = "approval = '2'";
        }else if($request->approv == 1){
            $approve = "approval = '1'";
        }else if($request->approv == 3){
            $approve = "approval = '0'";
        }else{
            $approve = "approval IS NOT NULL";
        }
        
        $prd = $request->plhtgl;
        
        if($prd == 0){
            $waktu = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        }else if($prd == 1){
            $waktu = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
        }else if($prd == 2){
            $waktu = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
        }
             
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        if(Auth::user()->kolekting == 'admin'){
            $kondisiQuery = null;
        }else{
            if($k == null){
                $kondisiQuery = "AND transaksi.id_kantor = '$kntr'";
            }else{
                $kondisiQuery = "AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')";
            }
        }
            
        if(isset($request->kotal)){
            $kadal = "AND transaksi.id_kantor IN ('" . implode("', '", $request->kotal) . "')";
        }else{
            $kadal = null;
        }
        
        if($request->petugas != ''){
            $pee = "id_koleks = '$request->petugas'";
        }else{
            $pee = "id_koleks IS NOT NULL"; 
        }
        
        $sel_bank = "transaksi.id_bank, sum(jumlah) AS data, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, bank.no_rek, bank.nama_bank, bank.nama_bank as awak, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yuuu
                ";
        $sel_kantor = "transaksi.id_kantor, sum(jumlah) AS data,  SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi, sum(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, unit, unit as awak, '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT COUNT(DISTINCT(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
        $sel_jam = "sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
        $sel_tanggal = "sum(jumlah) AS data, DAY(tanggal) as tgl, DAY(tanggal) as awak, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
        $sel_bulan = "sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
        $sel_tahun = "sum(jumlah) AS data, YEAR(tanggal) as tgl, YEAR(tanggal) as awak, kolektor,SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu
                ";
        $sel_donatur = "sum(jumlah) AS data, donatur, donatur as awak, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , id_donatur, '$request->analis' as analis, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
        $sel_program = "sum(jumlah) AS data, prog.program, prog.program as awak, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,transaksi.id_program, '$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";  
        $sel_status = "sum(jumlah) AS data, status, status as awak, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuuu";
        $sel_petugas = "sum(jumlah) AS data, id_koleks, kolektor, kolektor as awak, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu"; 
        $sel_bayar = "sum(jumlah) AS data, pembayaran, pembayaran as awak, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $pee AND $approve AND via_input = 'transaksi' $kadal) as yuuu";
                
        $sel_user = "sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, transaksi.kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen ,
                (SELECT sum(jumlah) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $pembayaran AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kadal) as yuuu";
                
        $whereRawBank = "$waktu AND $pembayaran AND $pee AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
        $whereRawKantor = "$waktu AND $pembayaran AND $pee AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $kondisiQuery";  
        $whereRawJTBT = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
        $whereRawDonatur = "$waktu AND $pembayaran AND $pee AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
        $whereRawProgram = "$waktu AND $pembayaran AND $pee AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $kondisiQuery";  
        $whereRawStatus = "$waktu AND $pembayaran AND $approve AND via_input = 'transaksi' $kondisiQuery";  
        $whereRawPetugas = "$waktu AND $pembayaran AND $pee AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
        $whereRawBayar = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' $kondisiQuery";  
        $whereRawUser = "$waktu AND $pembayaran AND $pee AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";
        
        if($request->tombol == 'xls') {
            $response = Excel::download(new AnalisisExport($request, $tgltext1, $blntext1, $thntext1), $a.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .XLS pada halaman Analisis Transaksi');
            
        }else if($request->tombol == 'csv'){
            $response = Excel::download(new AnalisisExport($request, $tgltext1, $blntext1, $thntext1), $a.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .CSV pada halaman Analisis Transaksi');
            
        }else if($request->tombol == 'pdf'){
            
            if($plhtgl == 0){
                $periode = 'Periode '.$tgltext;
            }else if($plhtgl == 1){
                $periode = 'Bulan '.$blntext;
            }else if($plhtgl == 2){
                $periode = 'Tahun '.$thntext;
            }
            
            $analis = 'Analis Transaksi Berdasarkan '.$analis ;
            
            if($request->analis == 'bank'){
                $data = Transaksi::selectRaw($sel_bank)
                            ->join('bank','bank.id_bank','=','transaksi.id_bank')
                            ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                            ->whereRaw($whereRawBank);
            }else if($request->analis == 'kantor'){
                $data = Transaksi::selectRaw($sel_kantor) 
                            ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
                            ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
                            ->whereRaw($whereRawKantor);
            }else if($request->analis == 'jam'){
                $data = Transaksi::selectRaw($sel_jam)
                            ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawJTBT);
            }else if($request->analis == 'tanggal'){
                $data = Transaksi::selectRaw($sel_tanggal)
                            ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawJTBT);
            }else if($request->analis == 'bulan'){
                $data = Transaksi::selectRaw($sel_bulan)
                            ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawJTBT);
            }else if($request->analis == 'tahun'){
                $data = Transaksi::selectRaw($sel_tahun)
                            ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawJTBT);
            }else if($request->analis == 'donatur'){
                $data = Transaksi::selectRaw($sel_donatur)
                            ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawDonatur);
            }else if($request->analis == 'program'){
                $data = Transaksi::selectRaw($sel_program)
                            ->join('prog','prog.id_program','=','transaksi.id_program')
                            ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                            ->whereRaw($whereRawProgram);
            }else if($request->analis == 'status'){
                $data = Transaksi::selectRaw($sel_status)
                            ->groupBy('status','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawStatus);
            }else if($request->analis == 'petugas'){
                $data = Transaksi::selectRaw($sel_petugas)
                            ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawPetugas);
            }else if($request->analis == 'bayar'){
                $data = Transaksi::selectRaw($sel_bayar)
                            ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawBayar);
            }else if($request->analis == 'user'){
                $data = Transaksi::selectRaw($sel_user)
                            ->join('users','users.id','=','transaksi.user_insert')
                            ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                            ->whereRaw($whereRawUser);
            }
            
            $kotah = $request->kotal;
                    
            $data->where(function($query) use ($request, $kotah) {
                if(isset($request->kotal)){
                    $query->whereIn('transaksi.id_kantor', $kotah);
                }
            });
            
            $pp = $data->get();
            
            $pdf = PDF::loadView('pdfanalis', ['data' => $pp, 'a' => $a, 'periode' => $periode, 'analis' => $analis])->setPaper('a4', 'potrait');
            return $pdf->stream('analisis-transaksi-pdf.pdf');
            
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .PDF pada halaman Analisis Transaksi');
        
        }
        
        return $response;
        
    }
    
    public function eksporanalis(Request $request)
    {
        $kantor = Kantor::all();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');

        
        $thn1 = $request->tahun == '' ? $y : $request->tahun;
        $thn2 = $request->tahun2 == '' ? $request->tahun :  $request->tahun2;
        $year = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
            
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $b2 = $request->bulan2 == '' ? $b1 : $request->bulan2;
            
        
        $bul1 = '01-'.$b1;
        $bul2 = '01-'.$b2;
            
            
        $bula1 = date('Y-m', strtotime($bul1));
        $bula2 = date('Y-m', strtotime($bul2));
            
        $real = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
        
        $tgltext = $request->daterange == '' ? $now.' s.d '.$now : $dari.' s.d '.$sampai;
        $blntext = $b1.' s.d '.$b2;
        $thntext = $thn1.' s.d '.$thn2;
        
        $bay = $request->bay == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran IN ('" . implode("', '", $request->bay) . "')";
            
        $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
        if($request->approv == 2){
            $approve = "approval = '2'";
        }else if($request->approv == 1){
            $approve = "approval = '1'";
        }else if($request->approv == 3){
            $approve = "approval = '0'";
        }else{
            $approve = "approval IS NOT NULL";
        }
            
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        
        $persen = $request->persen == null ? 'kosong' : $request->persen;
        $kotal = $request->kotal == null ? '[]' : $request->kotal;
            
        $now = date('Y-m-d');
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
        
        $tgltext1 = $request->daterange == '' ? $now.'-s.d-'.$now : $dari.'-s.d-'.$sampai;
        $blntext1 = $b1.'-s.d-'.$b2;
        $thntext1 = $thn1.'-s.d-'.$thn2;
        
        if($request->petugas != ''){
            $pee = "AND id_koleks = '$request->petugas'";
        }else{
            $pee = "AND id_koleks IS NOT NULL"; 
        }
        
        $analis = $request->analis;        
        $plhtgl = $request->plhtgl;
         if($plhtgl == 0){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-periode-'.$tgltext1;
            }
        }else if($plhtgl == 1){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-bulan-'.$blntext1;
            }
        }else if($plhtgl == 2){
            $a = 'analis-transaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            if($request->kondisi == ''){
                $b = 'detail-transaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            }else{
                $b = 'detail-nontransaksi-berdasarkan-'.strtolower($analis).'-tahun-'.$thntext1;
            }
        }
        
        if($request->tombol == 'xls') {
            $response = Excel::download(new AnalisisExport($request, $tgltext1, $blntext1, $thntext1), $a.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .XLS pada halaman Analisis Transaksi');
        }else if($request->tombol == 'pdf'){
            
            if($plhtgl == 0){
                $periode = 'Periode '.$tgltext;
            }else if($plhtgl == 1){
                $periode = 'Bulan '.$blntext;
            }else if($plhtgl == 2){
                $periode = 'Tahun '.$thntext;
            }
            
            $analis = 'Analis Transaksi Berdasarkan '.$analis ;
            
                if(Auth::user()->kolekting == 'admin'){
                    if($request->plhtgl == 0){
                        if($request->analis == 'bank'){
                            $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis, bank.no_rek, bank.nama_bank as awak, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                    ->groupBy('transaksi.id_bank','yu','analis','awak','bank.no_rek','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'kantor'){
                            $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, kolektor, count(transaksi.id)  as transaksi, unit as awak, COUNT(DISTINCT(id_donatur)) as don , '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT COUNT(DISTINCT(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                    ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu', 'persen')
                                    ->whereRaw("$tgls AND $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) as tgl, DAY(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) as tgl, YEAR(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'donatur'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, donatur as awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, id_donatur, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                
                                                ")
                                    ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'program'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis, transaksi.id_program, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->join('prog','prog.id_program','=','transaksi.id_program')
                                    ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuuu
                                                ")
                                    ->groupBy('status','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND  $approve AND via_input = 'transaksi' $pee");
                        }else if($request->analis == 'petugas'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                                                ")
                                    ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0");
                        }else if($request->analis == 'bayar'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuuu
                                                ")
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' $pee");
                        }else if($request->analis == 'user'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi') as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi') as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi') as yuuu
                                                ")
                                    ->join('users','users.id','=','transaksi.user_insert')
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0");
                        }
                    }else if($request->plhtgl == 1){
                        if($request->analis == 'bank'){
                            $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis, bank.no_rek, bank.nama_bank as awak, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee) as yuuu
                                                ")
                                    ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                    ->groupBy('transaksi.id_bank','yu','analis','awak','bank.no_rek','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee");
                        }else if($request->analis == 'kantor'){
                            $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, kolektor, count(transaksi.id)  as transaksi, unit as awak, COUNT(DISTINCT(id_donatur)) as don , '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT COUNT(DISTINCT(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                    ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu', 'persen')
                                    ->whereRaw("$bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee");
                        }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real  $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee");
                        }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) as tgl, DAY(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee");
                        }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee");
                        }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) as tgl, YEAR(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee");
                        }else if($request->analis == 'donatur'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, donatur as awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, id_donatur, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                
                                                ")
                                    ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee");
                        }else if($request->analis == 'program'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis, transaksi.id_program, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->join('prog','prog.id_program','=','transaksi.id_program')
                                    ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real $pee");
                        }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('status','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND  $approve AND via_input = 'transaksi' AND $real $pee");
                        }else if($request->analis == 'petugas'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                                                ")
                                    ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0");
                        }else if($request->analis == 'bayar'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yuuu
                                                ")
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' $pee");
                        }else if($request->analis == 'user'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yuuu
                                                ")
                                    ->join('users','users.id','=','transaksi.user_insert')
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $real");
                        }
                    }else if($request->plhtgl == 2){
                        if($request->analis == 'bank'){
                            $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, nama_bank, nama_bank as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, no_rek, '$request->analis' as analis, '$request->persen' as persen,
                                        (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                        (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                        (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                        ")
                                    ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                    ->groupBy('transaksi.id_bank','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) AS awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'kantor'){
                            $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, unit as awak, count(transaksi.id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, unit, '$request->analis' as analis,'$request->persen' as persen,
                                        (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                        (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                        (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                        ")
                                    ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                    ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'jam'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak,count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                        (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yu,
                                        (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                        (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu  $pee")
                                    ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) AS tgl, DAY(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) AS tgl, YEAR(tanggal) AS awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'donatur'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, donatur as awak, count(id)  as transaksi, kolektor, count(distinct(transaksi.id_donatur)) as don, id_donatur, '$request->analis' as analis,'$request->persen' as persen,
                                        (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                        (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                        (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                        ")
                                    ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'program'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak, count(id)  as transaksi, kolektor, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis, transaksi.id_program,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->join('prog','prog.id_program','=','transaksi.id_program')
                                    ->groupBy('program','yu','analis','yuu','yuuu','persen','transaksi.id_program')
                                    ->whereRaw("$year AND $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                        }else if($request->analis == 'status'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' $pee) as yuuu
                                                ")
                                    ->groupBy('status','yu','analis', 'yuuu', 'yuu')
                                    ->whereRaw("$year AND $bay AND $approve AND via_input = 'transaksi' $pee");
                        }else if($request->analis == 'petugas'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor as awak, kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                                                ")
                                    ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu')
                                    ->whereRaw("$year AND $bay AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0");
                        }else if($request->analis == 'bayar'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $year $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $year $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND $year $pee) as yuuu
                                                ")
                                    ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$approve AND $bay AND via_input = 'transaksi' AND $year $pee");
                        }else if($request->analis == 'user'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, count(transaksi.id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND $real) as yuuu
                                                ")
                                    ->join('users','users.id','=','transaksi.user_insert')
                                    ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0  AND $year");
                        }
                    }
                }else{
                    if($k == null){
                        if($request->plhtgl == 0){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, bank.nama_bank as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis, bank.no_rek, bank.nama_bank, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr'  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0  $pee) as yuuu
                                                    ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND transaksi.id_bank IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'bulan'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, unit as awak, count(transaksi.id)  as transaksi, kolektor, unit, count(distinct(id_donatur)) as don , '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND id_kantor = '$kntr' IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu', 'persen')
                                        ->whereRaw("$tgls AND $bay AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr'  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0  $pee) as yuuu
                                                    ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND $approve AND $bay AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0  $pee");
                            }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) AS tgl, DAY(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi'  $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve  AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0  $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) AS tgl, YEAR(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi'  $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $approve AND $bay AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, kolektor, donatur as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don, id_donatur, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    
                                                    ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND id_donatur IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis, transaksi.id_program, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND transaksi.id_program IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis')
                                        ->whereRaw("$tgls AND  $approve AND $bay AND via_input = 'transaksi' AND id_kantor = '$kntr' $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor, kolektor as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' ) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 ) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 ) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND id_koleks IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' ");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND  $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $tgls $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND  $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $tgls $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND  $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $tgls $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND $bay AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $tgls $pee");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, user.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $tgls ) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $tgls ) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $tgls) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $tgls");
                            }
                        }else if($request->plhtgl == 1){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, nama_bank, nama_bank as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, no_rek, '$request->analis' as analis,  '$request->persen' as persen, 
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" transaksi.id_bank IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'bulan'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, unit as awak, kolektor, count(transaksi.id)  as transaksi, count(distinct(id_donatur)) as don, unit, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr'  AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" transaksi.id_kantor = '$kntr' AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) AS tgl, DAY(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'tahun'){
                                    $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) AS tgl, YEAR(tanggal) as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, donatur as awak, count(id)  as transaksi, kolektor, count(distinct(transaksi.id_donatur)) as don, id_donatur, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" id_donatur IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak, count(id)  as transaksi,  kolektor, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis,transaksi.id_program, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','yuu','yuuu','persen','transaksi.id_program')
                                        ->whereRaw(" transaksi.id_program IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, count(id)  as transaksi,  kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis')
                                        ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND transaksi.id_kantor = '$kntr' AND $real $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor, kolektor as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND  id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND $real) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" id_koleks IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND $bay AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $real $pee");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $real");
                            }
                        }else if($request->plhtgl == 2){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, nama_bank, nama_bank as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, no_rek, '$request->analis' as analis, '$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND transaksi.id_bank IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'bulan'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, unit as awak, kolektor, count(transaksi.id)  as transaksi, count(distinct(id_donatur)) as don, unit, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND transaksi.id_kantor IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) AS tgl, DAY(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND id_kantor = '$kntr' AND  via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tahun'){
                                    $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) as awak, YEAR(tanggal) AS tgl, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND $bay AND id_kantor = '$kntr' AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, kolektor, donatur as awak, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, id_donatur, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND id_donatur IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak, kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis, transaksi.id_program,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','yuu','yuuu','persen','transaksi.id_program')
                                        ->whereRaw("$year AND transaksi.id_program IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, kolektor, status as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee ) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' $pee ) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis', 'yuuu', 'yuu')
                                        ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND transaksi.id_kantor = '$kntr' $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor, kolektor as awak, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr') as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu')
                                        ->whereRaw("$year AND id_koleks IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND transaksi.id_kantor = '$kntr' AND transaksi.jumlah > 0");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND id_kantor = '$kntr' AND via_input = 'transaksi' AND $year $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND $year $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND id_kantor = '$kntr' AND $approve AND via_input = 'transaksi' AND $year $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND $bay AND $year $pee");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $year) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $year) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $year) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND id_kantor = '$kntr' AND transaksi.jumlah > 0 AND $year");
                            }
                        } 
                    }else{
                        if($request->plhtgl == 0){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, count(id)  as transaksi, nama_bank as awak,  kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis, bank.no_rek, bank.nama_bank, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  AND transaksi.jumlah > 0  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','bank.nama_bank','bank.no_rek','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND transaksi.id_bank IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'bulan'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, MONTH(tanggal) AS bln, MONTH(tanggal) as awak, count(id)  as transaksi,  kolektor, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi'  AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, sum(jumlah) AS data, unit as awak, kolektor, count(transaksi.id)  as transaksi, unit, count(distinct(id_donatur)) as don , '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') IS NOT NULL AND $approve AND via_input = 'transaksi'  AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu', 'persen')
                                        ->whereRaw("$tgls AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) AS jam, HOUR(created_at) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tanggal'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, DAY(tanggal) AS tgl, DAY(tanggal) as awak, count(id) as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $approve AND via_input = 'transaksi' AND $bay AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tahun'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data,  kolektor, YEAR(tanggal) AS tgl, YEAR(tanggal) as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$tgls AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, donatur, donatur as awak, count(id) as transaksi, count(distinct(id_donatur)) as don, id_donatur, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                    
                                                    ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND id_donatur IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data,  kolektor, prog.program, prog.program as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis, transaksi.id_program, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND transaksi.id_program IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis')
                                        ->whereRaw("$tgls AND  $approve AND $bay AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor, koelktor as awak, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $tgls AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$tgls AND id_koleks IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND  $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $tgls $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $tgls $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $tgls $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $tgls $pee");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $tgls) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $tgls) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $tgls) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $tgls");
                            }
                        }else if($request->plhtgl == 1){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data,  kolektor, nama_bank as awak, nama_bank, count(id)  as transaksi, count(distinct(id_donatur)) as don, no_rek, '$request->analis' as analis,  '$request->persen' as persen, 
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" transaksi.id_bank IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'bulan'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, MONTH(tanggal) as awak, MONTH(tanggal) AS bln, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND  $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND  $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, unit as awak, sum(jumlah) AS data, kolektor, count(transaksi.id)  as transaksi, count(distinct(id_donatur)) as don, unit, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE  $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND  transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, HOUR(created_at) as awak, HOUR(created_at) AS jam, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, DAY(tanggal) AS tgl, DAY(tanggal) as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee");
                            }else if($request->analis == 'tahun'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, YEAR(tanggal) as awak, YEAR(tanggal) AS tgl,  kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND transaksi.jumlah > 0 AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee");
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, donatur as awak, kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, id_donatur, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" id_donatur IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak,  kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis,transaksi.id_program, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','yuu','yuuu','persen','transaksi.id_program')
                                        ->whereRaw(" transaksi.id_program IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, kolektor, status as awak,count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis')
                                        ->whereRaw(" $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks,kolektor as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw(" id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $real");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $real $pee");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year");
                            }
                        }else if($request->plhtgl == 2){
                            if($request->analis == 'bank'){
                                $data = Transaksi::selectRaw("transaksi.id_bank, sum(jumlah) AS data, nama_bank as awak, kolektor, nama_bank, count(id)  as transaksi, count(distinct(id_donatur)) as don, no_rek, '$request->analis' as analis, '$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->join('bank','bank.id_bank','=','transaksi.id_bank')
                                        ->groupBy('transaksi.id_bank','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND transaksi.id_bank IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'bulan'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, MONTH(tanggal) as awak, MONTH(tanggal) AS bln, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('bln','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'kantor'){
                                $data = Transaksi::selectRaw("transaksi.id_kantor, kolektor, unit as awak, sum(jumlah) AS data, count(transaksi.id)  as transaksi, count(distinct(id_donatur)) as don, unit, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->join('tambahan','tambahan.id','=','transaksi.id_kantor')
                                        ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND transaksi.id_kantor IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'jam'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, HOUR(created_at) as awak, HOUR(created_at) AS jam, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->groupBy('jam','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            
                            }else if($request->analis == 'tanggal'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, DAY(tanggal) AS tgl, DAY(tanggal) as awak , count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'tahun'){
                            $data = Transaksi::selectRaw("sum(jumlah) AS data, kolektor, YEAR(tanggal) as awak, YEAR(tanggal) AS tgl, count(id)  as transaksi, count(distinct(id_donatur)) as don, '$request->analis' as analis, '$request->persen' as persen,
                                                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' $pee) as yu,
                                                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuu,
                                                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND transaksi.jumlah > 0 $pee) as yuuu
                                                ")
                                    ->groupBy('tgl','yu','analis','yuu','yuuu','persen')
                                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");     
                            
                            }else if($request->analis == 'donatur'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, donatur, kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, id_donatur, '$request->analis' as analis,'$request->persen' as persen,
                                            (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') $pee) as yu,
                                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                            (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                            ")
                                        ->groupBy('id_donatur','donatur','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$year AND id_donatur IS NOT NULL AND $bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'program'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, prog.program, prog.program as awak, kolektor, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis, transaksi.id_program,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee) as yuuu
                                                    ")
                                        ->join('prog','prog.id_program','=','transaksi.id_program')
                                        ->groupBy('program','yu','analis','yuu','yuuu','persen','transaksi.id_program')
                                        ->whereRaw("$year AND transaksi.id_program IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 $pee");
                            }else if($request->analis == 'status'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, status, status as awak, kolektor, count(id)  as transaksi, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee) as yuuu
                                                    ")
                                        ->groupBy('status','yu','analis', 'yuuu', 'yuu')
                                        ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')  $pee");
                            }else if($request->analis == 'petugas'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, id_koleks, kolektor, kolektor as awak, count(id)  as transaksi, count(distinct(transaksi.id_donatur)) as don, '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $tgls AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $year AND transaksi.id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0) as yuuu
                                                    ")
                                        ->groupBy('id_koleks', 'kolektor','yu','analis','yuu','yuuu')
                                        ->whereRaw("$year AND id_koleks IS NOT NULL AND $approve AND $bay AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0");
                            }else if($request->analis == 'bayar'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, pembayaran, pembayaran as awak, count(id)  as transaksi, kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND $year  $pee) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND $year  $pee) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND via_input = 'transaksi' AND $year  $pee) as yuuu
                                                    ")
                                        ->groupBy('pembayaran','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$approve AND via_input = 'transaksi' AND $bay AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND $year");
                            }else if($request->analis == 'user'){
                                $data = Transaksi::selectRaw("sum(jumlah) AS data, users.name, users.name as awak, transaksi.user_insert, count(transaksi.id)  as transaksi, transaksi.kolektor, count(distinct(id_donatur)) as don , '$request->analis' as analis,'$request->persen' as persen,
                                                    (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yu,
                                                    (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yuu,
                                                    (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $approve AND via_input = 'transaksi'  AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') AND transaksi.jumlah > 0 AND $year) as yuuu
                                                    ")
                                        ->join('users','users.id','=','transaksi.user_insert')
                                        ->groupBy('users.name','yu','analis','yuu','yuuu','persen')
                                        ->whereRaw("$bay AND $approve AND via_input = 'transaksi' AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id') transaksi.jumlah > 0 AND $year");
                            }
                        }
                    }
                }
                
                $kotah = $request->kotal;
                
                $data->where(function($query) use ($request, $kotah) {
                        if(isset($request->kotal)){
                            $query->whereIn('transaksi.id_kantor', $kotah);
                        }
                });
                
                $pp = $data->get();
           
            $pdf = PDF::loadView('pdfanalis', ['data' => $pp, 'a' => $a, 'periode' => $periode, 'analis' => $analis])->setPaper('a4', 'potrait');
            return $pdf->stream('analisis-transaksi-pdf.pdf');
            
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .PDF pada halaman Analisis Transaksi');
        }else if($request->tombol == 'csv'){
            $response = Excel::download(new AnalisisExport($request, $tgltext1, $blntext1, $thntext1), $a.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .CSV pada halaman Analisis Transaksi');
        }else if($request->tombol == 'xlss'){
            $response = Excel::download(new AnalisisDetailExport($request, $tgltext1, $blntext1, $thntext1), $b.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .xlsx pada halaman Analisis Transaksi');
        }else if($request->tombol == 'csvv'){
            $response = Excel::download(new AnalisisDetailExport($request, $tgltext1, $blntext1, $thntext1), $b.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data .CSV pada halaman Analisis Transaksi');
        }
        return $response;
    }

    public function transaksi_funnel(Request $request)
    {   
        // return($request);
        $d = date('Y-m-d');
        $m = date('m');
        $y = date('Y');
        
        if($request->daterange != '') {
        	$tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$d' AND DATE(transaksi.tanggal) <= '$d'" ;
        
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $bul1 = '01-'.$b1;
        $bula1 = date('Y-m', strtotime($bul1));
        $real = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') = '$bula1'";
        
        $thn = $request->tahun == '' ? $y : $request->tahun;
        $year = "YEAR(transaksi.tanggal) = '$thn'";
        
        $req = $request->plhtgl;
        $sumberr = $request->sumber == '' ? "transaksi.id_sumdan IS NOT NULL" : "transaksi.id_sumdan = '$request->sumber'";
        
        $approve = "transaksi.approval = '1'";
        
        $judul = $request->judul;
        
        $dari_r = $request->tahun == '' ? date('Y-01-01') : date($request->tahun.'-01-01');
        $sampai_r = $request->tahun == '' ? date('Y-m-d') : date($request->tahun.'-12-31');
        $jumbul = $request->tahun == '' ? date('m') : 12;
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        
        if($req == 0){
            $jiday = $tgls;
            $condon = 0;
        }else if($req == 1){
            $jiday = $real;
            $condon = 1;
        }else if($req == 2){
            $jiday = $year;
            
            $datran = Transaksi::select('id_donatur', DB::raw("COUNT(DISTINCT(MONTH(tanggal))) as total, DATE(donatur.created_at) as tanggals"))
                                        ->join('donatur','donatur.id','=','transaksi.id_donatur')
                                        ->whereDate('tanggal', '>=', $dari_r)->whereDate('tanggal', '<=', $sampai_r)
                                        ->where('via_input', 'transaksi')
                                        ->whereRaw("$sumberr AND $approve AND transaksi.jumlah > 0")
                                        ->where(function($q) use ($request, $k, $kntr){
                                            if(Auth::user()->kolekting == 'kacab'){
                                                if($k == null){
                                                    $q->whereRaw("id_kantor = '$kntr'");
                                                }else{
                                                    $q->whereRaw("(id_kantor = '$kntr' OR id_kantor = '$k->id')");
                                                }
                                            }
                                        })
                                        ->groupBy('id_donatur')
                                        ->having('total', $jumbul)
                                        ->get();
            // $unch = [];                        
            // foreach($datran as $yy){
            //     if(date('Y', strtotime($yy->tanggals)) == date('Y')){
            //         $unch[] = [
            //             'id_donatur' => $yy->id_donatur,
            //             'tanggals' => $yy->tanggals,
            //             'total' => $yy->total
            //         ];
            //     }
            // }
        
            $condon = count($datran); 
        }
        
        
        
        if(Auth::user()->kolekting == 'admin'){
            $qq = "id_kantor IS NOT NULL";
        }else{
            if($k == null){
                $qq = "id_kantor = '$kntr'";
            }else{
                $qq = "(id_kantor = '$kntr' OR id_kantor = '$k->id')";
            }
        }
        
        
        if($request->tab == 'tab1'){
            
            $data = Transaksi::selectRaw("'$judul' as judul, count(distinct(transaksi.id_donatur)) as spesial,
                            (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $approve AND via_input = 'transaksi' AND $qq AND transaksi.jumlah > 0 AND $jiday) as donatur,
                            (SELECT count(distinct(transaksi.id_donatur)) FROM transaksi WHERE $approve AND via_input = 'transaksi' AND $qq AND status = 'Donasi' AND $jiday) as donasi,
                            (SELECT count(distinct(transaksi.id_donatur)) FROM transaksi WHERE $approve AND via_input = 'transaksi' AND $qq AND (status = 'Ditarik' OR status = 'Off') AND transaksi.jumlah > 0 AND $jiday) as donoff")
                            ->whereRaw("$sumberr AND $approve AND via_input = 'transaksi' AND $qq AND transaksi.jumlah > 0 AND $jiday");
            
            $kotah = $request->kotal;
            
            $data->where(function($query) use ($request, $kotah) {
                if(isset($request->kotal)){
                    $query->whereIn('transaksi.id_kantor', $kotah);
                }
            });
            
            $we = $data->get();
            
            // return($condon);
            
            $d = [];
            foreach($we as $da){
                if($req == 2){
                    $d =  [
                        ['Donatur', $da->donatur],
                        ['Berdonasi', $da->donasi],
                        ['<a class="btn btn-sm btn-info eco">Berdonasi Off</a>', $da->donoff],
                        ['<a class="btn btn-sm btn-info efo" id="'.$request->sumber.'_'.$da->judul.'">Berdonasi '.$da->judul.'</a>', $da->spesial],
                        ['<a class="btn btn-sm btn-info ember" id="'.$request->sumber.'_'.$da->judul.'">'.$da->judul.' Rutin Bulanan</a>', $condon]
                    ];
                }else{
                   $d =  [
                        ['Donatur', $da->donatur],
                        ['Berdonasi', $da->donasi],
                        ['<a class="btn btn-sm btn-info eco">Berdonasi Off</a>', $da->donoff],
                        ['<a class="btn btn-sm btn-info efo" id="'.$request->sumber.'_'.$da->judul.'">Berdonasi '.$da->judul.'</a>', $da->spesial]
                    ];
                }
            }
            
            return $d;
        }
        
        $sumber = DB::table('sumber_dana')->where('active', 'y')->get();
        return view('report-management.transaksi_tunnel', compact('sumber'));
    }
    
    public function detail_funnel(Request $request)
    {
        $approve = "approval = '1'";
        $dari_r = $request->tahun == '' ? date('Y-01-01') : date($request->tahun.'-01-01');
        $sampai_r = $request->tahun == '' ? date('Y-m-d') : date($request->tahun.'-12-31');
        $jumbul = $request->tahun == '' ? date('m') : 12;
        $sumber= "id_sumdan = '$request->id'";
        $kotah = $request->kotal;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        $req = $request->plhtgl;
        
        if($request->tab == 'berdonasi'){
            $d = date('Y-m-d');
            $m = date('m');
            $y = date('Y');
            
            if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
        
            $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$d' AND DATE(transaksi.tanggal) <= '$d'" ;
        
            $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
            $bul1 = '01-'.$b1;
            $bula1 = date('Y-m', strtotime($bul1));
            $real = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') = '$bula1'";
            
            $thn = $request->tahun == '' ? $y : $request->tahun;
            $year = "YEAR(transaksi.tanggal) = '$thn'";
            
            // return($request);
            
            if($req == 0){
                $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                        ->whereRaw("$tgls AND $approve AND via_input = 'transaksi' AND $sumber AND transaksi.jumlah > 0")
                        ->join('prog','prog.id_program','=','transaksi.id_program');
            }else if($req == 1){
                $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->whereRaw("$approve AND via_input = 'transaksi' AND $sumber AND transaksi.jumlah > 0 AND $real");
            }else if($req == 2){
                $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND $sumber AND transaksi.jumlah > 0");
            }
            
            $data->where(function ($query) use ($k, $kntr) {
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
            
            return $data->get();
        }
        
        if(Auth::user()->kolekting == 'admin'){
            $id = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi', DB::raw('COUNT(DISTINCT(MONTH(tanggal))) as total'))
                    ->join('prog','prog.id_program','=','transaksi.id_program')
                    ->whereDate('tanggal', '>=', $dari_r)->whereDate('tanggal', '<=', $sampai_r)
                    ->where('via_input', 'transaksi')
                    ->whereRaw("$sumber AND $approve AND jumlah > 0")
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kotal)){
                            $query->whereIn('transaksi.id_kantor', $kotah);
                        }
                    })
                    ->groupBy('id_donatur')
                    ->having('total', $jumbul)
                    ->orderBy('tanggal','asc')
                    ->get();
        }else{
            if($k == null){
                $id = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi', DB::raw('COUNT(DISTINCT(MONTH(tanggal))) as total'))
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->whereDate('tanggal', '>=', $dari_r)->whereDate('tanggal', '<=', $sampai_r)
                        ->where('via_input', 'transaksi')
                        ->whereRaw("$sumber AND $approve AND id_kantor = '$kntr' AND jumlah > 0")
                        ->where(function($query) use ($request, $kotah) {
                            if(isset($request->kotal)){
                                $query->whereIn('transaksi.id_kantor', $kotah);
                            }
                        })
                        ->groupBy('id_donatur')
                        ->having('total', $jumbul)
                        ->orderBy('tanggal','asc')
                        ->get(); 
            }else{
                $id = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi', DB::raw('COUNT(DISTINCT(MONTH(tanggal))) as total'))
                        ->join('prog','prog.id_program','=','transaksi.id_program')
                        ->whereDate('tanggal', '>=', $dari_r)->whereDate('tanggal', '<=', $sampai_r)
                        ->where('via_input', 'transaksi')
                        ->whereRaw("$sumber AND $approve AND (id_kantor = '$kntr' OR id_kantor = '$k->id') AND jumlah > 0")
                        ->where(function($query) use ($request, $kotah) {
                            if(isset($request->kotal)){
                                $query->whereIn('transaksi.id_kantor', $kotah);
                            }
                        })
                        ->groupBy('id_donatur')
                        ->having('total', $jumbul)
                        ->orderBy('tanggal','asc')
                        ->get();
            }
        }
        
        return $id;
    }
    
    public function detail_funnel_off(Request $request){
        $d = date('Y-m-d');
        $m = date('m');
        $y = date('Y');
        
        if($request->daterange != '') {
        	$tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        
        $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$d' AND DATE(transaksi.tanggal) <= '$d'" ;
        
        $b1 = $request->bulan == '' ? date('m-Y') : $request->bulan;
        $bul1 = '01-'.$b1;
        $bula1 = date('Y-m', strtotime($bul1));
        $real = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') = '$bula1'";
        
        $thn = $request->tahun == '' ? $y : $request->tahun;
        $year = "YEAR(transaksi.tanggal) = '$thn'";
        
        $req = $request->plhtgl;
        $sumber= $request->sumber == '' ? "id_sumdan IS NOT NULL" : "id_sumdan = '$request->sumber'";
        
        $approve = "approval = '1'";
        
        $judul = $request->judul;
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        
        
        if($req == 0){
            $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                    ->whereRaw("$tgls AND $approve AND via_input = 'transaksi' AND (status = 'Ditarik' OR status = 'Off') AND transaksi.jumlah > 0")
                    ->join('prog','prog.id_program','=','transaksi.id_program');
        }else if($req == 1){
            $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                    ->join('prog','prog.id_program','=','transaksi.id_program')
                    ->whereRaw("$approve AND via_input = 'transaksi' AND (status = 'Ditarik' OR status = 'Off') AND transaksi.jumlah > 0 AND $real");
        }else if($req == 2){
            $data = Transaksi::select('id_donatur', 'kolektor', 'tanggal', 'donatur', 'jumlah', 'prog.program', 'id_transaksi')
                    ->join('prog','prog.id_program','=','transaksi.id_program')
                    ->whereRaw("$year AND $approve AND via_input = 'transaksi' AND (status = 'Ditarik' OR status = 'Off') AND transaksi.jumlah > 0");
        }
        
        $data->where(function ($query) use ($k, $kntr) {
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
        
        return $data->get();
        
    }
    
    public function lokdon(Request $request){
        return view('report-management.lok_don');
    }
    
    public function donatur_det(Request $request){

        $kot = Auth::user()->id_kantor;
        $cari = Kantor::where('kantor_induk', $kot)->first();
        $analis = $request->analis;
        $pilihan = $request->yangdipilih;
        $kategori = $request->kategori;
        $tahun = $request->tahun != '' ? $request->tahun : Date('Y');
        
        
      
    $nambulan = [
        1 => 'Januari',
        2 =>'Februari',
        3 =>'Maret',
        4 =>'April', 
        5 =>'Mei',
        6 =>'Juni',
        7 =>'Juli',
        8 =>'Agustus',
        9 =>'September',
        10 =>'Oktober',
        11 =>'November' ,
        12 =>'Desember' 
        ];
        
    $bulan = array_search($kategori, $nambulan);
    
     if(Auth::user()->kolekting == ('admin')){
        if($analis == 'cara_bayar'){
          $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.jalur,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,tambahan.unit")
        ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.pembayaran = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' ")->get();
        }else if($analis == 'kantor' || $analis == ''){
          $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("tambahan.unit = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'jenis'){
          $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.jenis_donatur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun'AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'jalur'){
          $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.jalur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'warn'){
            $cari = Kantor::where('unit', $request->kategori)->first()->id;
            
            // $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $kot = Auth::user()->id_kantor;
                    
            $mindon = $request->mindon;
            $jumbul = $request->jumbul;
            $program = $request->prog;
                    
            $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$program')";
                    
            
            $kan = "transaksi.id_kantor = '$cari'";
                    
            $now = date('Y-m-d');
            $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
            $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
            $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, donatur.id_kantor, donatur.pembayaran, donatur.nama, donatur.alamat, donatur.kota, donatur.jenis_donatur, donatur.no_hp, donatur.jalur,
                        SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia AND $kan, jumlah, 0)) as ju")
                            
                        ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                            ->whereIn('donatur.id', function($q){
                            $q->select('id_don')->from('prosp')->where('ket','closing');
                        })
                        
                        ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                        
                        ->where(function($q) use ($cari) {
                                $q->whereRaw("donatur.id_kantor =  '$cari'");
                        })
                        ->groupBy('id_donatur','bulan')
                        ->get();
                    
            $targetAmount = $mindon;
            $jumbul = $jumbul;
                     
            $result = [];
            $count = 0;
            $sepong = [];
            $coy = [];
            $result2 = [];
            $tt = [];
                    
            $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                    
            foreach ($groupedData as $donatur => $donaturData) {
                    
                $kon = count(array_column($donaturData, 'bulan'));
                $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                                return $item['ju'] <  $targetAmount;
                        }));
                        
                        // $result[] = [
                        //     "bulan" => array_column($donaturData, 'bulan'),
                        //     "id_donatur" => $donaturData[0]['id_donatur'],
                        //     "jumlah" => array_column($donaturData, 'ju'),
                        //     "count_bulan" => $kon,
                        //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                            
                        // ];
                        
                if($kon == $jumbul){
                    if($hasil == $jumbul){
                                    
                        $result2[] = [
                            "nama" => $donaturData[0]['nama'],
                            "id" => $donaturData[0]['id_donatur'],
                            'donasi_kurang_dari_'.$targetAmount =>  $hasil,
                            'unit' => $donaturData[0]['kota'],
                            'alamat' => $donaturData[0]['alamat'],
                            'jalur' => $donaturData[0]['jalur'],
                            'pembayaran' => $donaturData[0]['pembayaran'],
                            'jenis_donatur' => $donaturData[0]['jenis_donatur'],
                            'no_hp' => $donaturData[0]['no_hp'],
                            "jumlah" => array_column($donaturData, 'ju'),
                            "bulan" => array_column($donaturData, 'bulan')
                            
                            
                        ];
                                    
                                    //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1, 'user_warning' => Auth::user()->id, 'jumbul' => $jumbul, 'mindon' => $mindon, 'user_update' => Auth::user()->id ]);
                    }
                }
            }
            
            $data = $result2;
                    
            // $ehe = collect($result2)->groupBy('kantor')->toArray();
                
            // foreach ($ehe as $do => $don) {
            //     $sek = count(array_column($don, 'donasi_kurang_dari_'.$targetAmount));
            //     $tt[] = [
            //         'kantor' => Kantor::find($do)->unit,
            //         'donasi' => $sek,
            //     ];
            // }
        }
        
    }else if(Auth::user()->kolekting == ('kacab')){
        if($analis == 'cara_bayar'){
            $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.pembayaran = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' AND id_kantor = '$kot'")->get();
        }else if($analis == 'kantor' || $analis == ''){
            $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("tambahan.unit = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' AND id_kantor = '$kot'")->get();
        } else if($analis == 'jenis'){
            $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.jenis_donatur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun'AND MONTH(donatur.created_at) = '$bulan'AND id_kantor = '$kot'")->get();
        } else if($analis == 'jalur'){
            $data = Donatur::selectRaw("donatur.id,donatur.pembayaran,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.jalur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'AND id_kantor = '$kot' ")->get();
        } else if($analis == 'warn'){
            $cari = Kantor::where('unit', $request->kategori)->first()->id;
            
            // $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $kot = Auth::user()->id_kantor;
                    
            $mindon = $request->mindon;
            $jumbul = $request->jumbul;
            $program = $request->prog;
                    
            $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$program')";
                    
            
            $kan = "transaksi.id_kantor = '$cari'";
                    
            $now = date('Y-m-d');
            $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
            $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
            $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, donatur.id_kantor, donatur.pembayaran, donatur.nama, donatur.alamat, donatur.kota, donatur.jenis_donatur, donatur.no_hp, donatur.jalur,
                        SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia AND $kan, jumlah, 0)) as ju")
                            
                        ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                            ->whereIn('donatur.id', function($q){
                            $q->select('id_don')->from('prosp')->where('ket','closing');
                        })
                        
                        ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                        
                        ->where(function($q) use ($cari) {
                                $q->whereRaw("donatur.id_kantor =  '$cari'");
                        })
                        ->groupBy('id_donatur','bulan')
                        ->get();
                    
            $targetAmount = $mindon;
            $jumbul = $jumbul;
                     
            $result = [];
            $count = 0;
            $sepong = [];
            $coy = [];
            $result2 = [];
            $tt = [];
                    
            $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                    
            foreach ($groupedData as $donatur => $donaturData) {
                    
                $kon = count(array_column($donaturData, 'bulan'));
                $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                                return $item['ju'] <  $targetAmount;
                        }));
                        
                        // $result[] = [
                        //     "bulan" => array_column($donaturData, 'bulan'),
                        //     "id_donatur" => $donaturData[0]['id_donatur'],
                        //     "jumlah" => array_column($donaturData, 'ju'),
                        //     "count_bulan" => $kon,
                        //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                            
                        // ];
                        
                if($kon == $jumbul){
                    if($hasil == $jumbul){
                                    
                        $result2[] = [
                            "nama" => $donaturData[0]['nama'],
                            "id" => $donaturData[0]['id_donatur'],
                            'donasi_kurang_dari_'.$targetAmount =>  $hasil,
                            'unit' => $donaturData[0]['kota'],
                            'alamat' => $donaturData[0]['alamat'],
                            'jalur' => $donaturData[0]['jalur'],
                            'pembayaran' => $donaturData[0]['pembayaran'],
                            'jenis_donatur' => $donaturData[0]['jenis_donatur'],
                            'no_hp' => $donaturData[0]['no_hp'],
                            "jumlah" => array_column($donaturData, 'ju'),
                            "bulan" => array_column($donaturData, 'bulan')
                            
                            
                        ];
                                    
                                    //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1, 'user_warning' => Auth::user()->id, 'jumbul' => $jumbul, 'mindon' => $mindon, 'user_update' => Auth::user()->id ]);
                    }
                }
            }
            
            $data = $result2;
                    
            // $ehe = collect($result2)->groupBy('kantor')->toArray();
                
            // foreach ($ehe as $do => $don) {
            //     $sek = count(array_column($don, 'donasi_kurang_dari_'.$targetAmount));
            //     $tt[] = [
            //         'kantor' => Kantor::find($do)->unit,
            //         'donasi' => $sek,
            //     ];
            // }
        }
    }
    
    
    return DataTables::of($data)
    ->make(true);

    }
    
    
    
    public function detail_donatur(Request $request){
        $tahun = $request->tahun != '' ? $request->tahun : Date('Y');
        $yangdipilih =  $request->yangdipilih ;
        $kategori =  $request->kategori ;
        
        if($request->analis == 'warn'){
            if($request->tombol == 'xls'){
                $r = Excel::download(new DetailAnalisisDonaturExport($request), 'Detail Donatur '.$yangdipilih.' Unit '.$kategori.'.xls');
                ob_end_clean();
                return $r;
            }else{
                $r = Excel::download(new DetailAnalisisDonaturExport($request), 'Detail Donatur '.$yangdipilih.' Unit '.$kategori.'.csv');
                ob_end_clean();
                return $r;
            }
        }else{
            if($request->tombol == 'xls'){
                $r = Excel::download(new DetailAnalisisDonaturExport($request), 'Detail Donatur '.$yangdipilih.' Bulan '.$kategori.' Tahun '.$tahun.'.xls');
                ob_end_clean();
                return $r;
            }else{
                $r = Excel::download(new DetailAnalisisDonaturExport($request), 'Detail Donatur '.$yangdipilih.' Bulan '.$kategori.' Tahun '.$tahun.'.csv');
                ob_end_clean();
                return $r;
            }
        }
    }

}
