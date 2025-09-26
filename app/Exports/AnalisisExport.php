<?php

namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Kantor;
use Carbon\Carbon;
use DB;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AnalisisExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $tgltext1, $blntext1, $thntext1)
    {
        
        $this->tgltext1 = $tgltext1 ;
        $this->blntext1 = $blntext1 ;
        $this->thntext1 = $thntext1 ;
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $tgltext1 = $this->tgltext1;
        $blntext1 = $this->blntext1;
        $thntext1 = $this->thntext1;
        
        $request = $this->request;
        $kantor = Kantor::all();
        $now = date('Y-m-d');
        $month = date('m');
        $y = date('Y');
        $analisnya = 'Analis';
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
            
            // return($bul1);
            
            $bula1 = date('Y-m', strtotime($bul1));
            $bula2 = date('Y-m', strtotime($bul2));
            
            // return($bula2);
            
            // $bulan1 = Carbon::createFromFormat('m-Y', $b1)->format('m');
            // $tahun1 = Carbon::createFromFormat('m-Y', $b1)->format('Y');
            
            // $bulan2 = Carbon::createFromFormat('m-Y', $b2)->format('m');
            // $tahun2 = Carbon::createFromFormat('m-Y', $b2)->format('Y');
            
            
            if($request->bay == ''){
                $bay = "transaksi.pembayaran IS NOT NULL";
            }else if($request->bay == 'cash'){
                $bay = "transaksi.pembayaran != 'noncash'";
            }else if($request->bay == 'noncash'){
                $bay = "transaksi.pembayaran = 'noncash'";
            }
            
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
                $prdHeader = $tgltext1;
                $waktu = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'" ;
            }else if($prd == 1){
                $prdHeader = $blntext1;
                $waktu = "DATE_FORMAT(transaksi.tanggal, '%Y-%m') BETWEEN '$bula1' AND '$bula2'";
            }else if($prd == 2){
                $prdHeader = $thntext1;
                $waktu = "YEAR(transaksi.tanggal) >= '$thn1' AND YEAR(transaksi.tanggal) <= '$thn2' ";
            }
                 
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kntr = Auth::user()->id_kantor;
            if(Auth::user()->kolekting == 'admin'){
                $kondisiQuery = null;
            }else{
                if($k == null){
                    $kondisiQuery = "AND id_kantor = '$kntr'";
                }else{
                    $kondisiQuery = "AND (transaksi.id_kantor = '$kntr' OR transaksi.id_kantor = '$k->id')";
                }
            }
            
            $sel_bank = "transaksi.id_bank, sum(jumlah) AS data, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, bank.no_rek, CONCAT(bank.nama_bank, ' (', bank.no_rek,')') as nama, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi') as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi') as yuuu
                ";
            $sel_kantor = "transaksi.id_kantor, sum(jumlah) AS data,  SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, unit as nama, '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                (SELECT COUNT(DISTINCT(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu";
            $sel_jam = "sum(jumlah) AS data, CONCAT('Jam ', HOUR(created_at)) as nama, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                ";
            $sel_tanggal = "sum(jumlah) AS data, CONCAT('Tanggal ', DAY(tanggal)) as nama, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                ";
                
            $bulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $sel_bulan = "sum(jumlah) AS data, MONTH(tanggal) as nama, kolektor, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                ";
            $sel_tahun = "sum(jumlah) AS data, YEAR(tanggal) as nama, kolektor,SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu
                ";
            $sel_donatur = "sum(jumlah) AS data, donatur as nama, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , id_donatur, '$request->analis' as analis, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu";
            $sel_program = "sum(jumlah) AS data, prog.program as nama, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor,  COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,transaksi.id_program, '$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' ) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 ) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 ) as yuuu";  
            $sel_status = "sum(jumlah) AS data, status as nama, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1, '$request->analis' as analis,'$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yuuu";
            $sel_petugas = "sum(jumlah) AS data, id_koleks, kolektor as nama, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND status IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu"; 
            $sel_bayar = "sum(jumlah) AS data, pembayaran as nama, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi') as yuuu";
                
            $sel_user = "sum(jumlah) AS data, users.name as nama, transaksi.user_insert, SUM(CASE WHEN jumlah > 0 THEN 1 ELSE 0 END) AS transaksi,  SUM(CASE WHEN jumlah = 0 THEN 1 ELSE 0 END) AS nontransaksi, transaksi.kolektor, COUNT(DISTINCT CASE WHEN transaksi.jumlah > 0 THEN transaksi.id_donatur END) AS don,COUNT(DISTINCT CASE WHEN transaksi.jumlah = 0 THEN transaksi.id_donatur END) AS don1 , '$request->analis' as analis,'$request->persen' as persen $kondisiQuery,
                (SELECT sum(jumlah) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yu,
                (SELECT count(distinct(id_donatur)) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuu,
                (SELECT count(transaksi.id) FROM transaksi WHERE $bay AND $waktu AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0) as yuuu";
                
            $whereRawBank = "$waktu AND $bay AND transaksi.id_bank IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawKantor = "$waktu AND $bay AND transaksi.id_kantor IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawJTBT = "$waktu AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawDonatur = "$waktu AND AND $bay AND id_donatur IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawProgram = "$waktu AND $bay AND transaksi.id_program IS NOT NULL AND $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawStatus = "$waktu AND $bay AND  $approve AND via_input = 'transaksi' $kondisiQuery";  
            $whereRawPetugas = "$waktu AND $bay AND id_koleks IS NOT NULL AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $whereRawBayar = "$waktu AND $bay AND $approve AND via_input = 'transaksi'";  
            $whereRawUser = "$waktu AND $bay AND $approve AND via_input = 'transaksi' AND transaksi.jumlah > 0 $kondisiQuery";  
            $now = date('Y-m-d');
                if(Auth::user()->kolekting == 'admin'){
                    if($request->analis == 'bank'){
                        $data = Transaksi::selectRaw($sel_bank)
                            ->join('bank','bank.id_bank','=','transaksi.id_bank')
                            ->groupBy('transaksi.id_bank','yu','analis','nama','bank.no_rek','yuu','yuuu','persen')
                            ->whereRaw($whereRawBank);
                    }else if($request->analis == 'kantor'){
                        $data = Transaksi::selectRaw($sel_kantor) 
                                ->join('tambahan','tambahan.id','=','transaksi.id_kantor') 
                                ->groupBy('transaksi.id_kantor','yu','analis','yuu','yuuu','persen') 
                                ->whereRaw($whereRawKantor);
                    }else if($request->analis == 'jam'){
                        $data = Transaksi::selectRaw($sel_jam)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'tanggal'){
                        $data = Transaksi::selectRaw($sel_tanggal)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'bulan'){
                        $data = Transaksi::selectRaw($sel_bulan)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'tahun'){
                        $data = Transaksi::selectRaw($sel_tahun)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawJTBT);
                    }else if($request->analis == 'donatur'){
                        $data = Transaksi::selectRaw($sel_donatur)
                                ->groupBy('id_donatur','nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawDonatur);
                    }else if($request->analis == 'program'){
                        $data = Transaksi::selectRaw($sel_program)
                                ->join('prog','prog.id_program','=','transaksi.id_program')
                                ->groupBy('nama','yu','analis','transaksi.id_program','yuu','yuuu','persen')
                                ->whereRaw($whereRawProgram);
                    }else if($request->analis == 'status'){
                        $data = Transaksi::selectRaw($sel_status)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawStatus);
                    }else if($request->analis == 'petugas'){
                        $data = Transaksi::selectRaw($sel_petugas)
                                ->groupBy('id_koleks', 'nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawPetugas);
                    }else if($request->analis == 'bayar'){
                        $data = Transaksi::selectRaw($sel_bayar)
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
                                ->whereRaw($whereRawBayar);
                    }else if($request->analis == 'user'){
                        $data = Transaksi::selectRaw($sel_user)
                                ->join('users','users.id','=','transaksi.user_insert')
                                ->groupBy('nama','yu','analis','yuu','yuuu','persen')
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
            return view('ekspor.analisexport',[
                'data' => $data->get(),
                'analis' => $request->analis,
                'prd' => $prdHeader,
                'toggle' => $request->toggleData,
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
            ]);
            
    }
}