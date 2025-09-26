<?php
namespace App\Exports;

use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Models\Donatur;
use App\Models\Kantor;
class DetailAnalisisDonaturExport implements FromView
{
    
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View { 
        $request = $this->request;
            
            $nambulan = [
        1 => 'Januari',
        2 =>'Febuari',
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
      
        
        $kot = Auth::user()->id_kantor;
        $cari = Kantor::where('kantor_induk', $kot)->first();
        $analis = $request->analis;
        $pilihan = $request->yangdipilih;
        $kategori = $request->kategori;
        $tahun = $request->tahun != '' ? $request->tahun : Date('Y');
        
        $bulan = array_search($kategori, $nambulan);
    if(Auth::user()->kolekting == ('admin')){
        if($analis == 'cara_bayar'){
          $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
        ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.pembayaran = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' ")->get();
        }else if($analis == 'kantor' || $analis == ''){
          $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("tambahan.unit = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'jenis'){
          $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.jenis_donatur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun'AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'jalur'){
          $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
         ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
         ->whereRaw("donatur.jalur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'")->get();
        } else if($analis == 'warn'){
            $cari = Kantor::where('unit', $request->kategori)->first()->id;
                    
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
                    }
                }
            }
            
            $data = $result2;
        }
        
    }else if(Auth::user()->kolekting == ('kacab')){
        if($analis == 'cara_bayar'){
            $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.pembayaran = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' AND id_kantor = '$kot'")->get();
        }else if($analis == 'kantor' || $analis == ''){
            $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("tambahan.unit = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan' AND id_kantor = '$kot'")->get();
        } else if($analis == 'jenis'){
            $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.jenis_donatur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun'AND MONTH(donatur.created_at) = '$bulan'AND id_kantor = '$kot'")->get();
        } else if($analis == 'jalur'){
            $data = Donatur::selectRaw("donatur.pembayaran,donatur.id,donatur.nama,donatur.alamat,donatur.kota,donatur.jenis_donatur,donatur.no_hp,donatur.jalur,tambahan.unit")
                ->leftJoin('tambahan','tambahan.id','=','donatur.id_kantor')
                ->whereRaw("donatur.jalur = '$pilihan' AND YEAR(donatur.created_at) = '$tahun' AND MONTH(donatur.created_at) = '$bulan'AND id_kantor = '$kot' ")->get();
        } else if($analis == 'warn'){
            $cari = Kantor::where('unit', $request->kategori)->first()->id;
                    
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
                    }
                }
            }
            
            $data = $result2;
        }
    } 


        return view('ekspor.detailanalisisdonaturexport',[
            'data' => $data,
            'bulan' => $kategori,
            'tahun' => $tahun ,
            'pilihan' => $pilihan,
            'analis' => $request->analis,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name,
        ]);
    }
}