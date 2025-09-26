<?php
namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Kantor;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RutinExport implements FromView
{
    
    
    public function __construct( $request)
    {
        // $this->kota = $kota;
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {   
        $request = $this->request;
        
        
        $tahun = $request->bulan == '' ? Carbon::now()->format('Y') : $request->bulan;
            
        $prog = $request->prog == '' ? "id_program IS NOT NULL" : "id_program = '$request->prog'";
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kntr = Auth::user()->id_kantor;
        
        
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
                    })
                    ->get();
        
        return view('eksportr',[
            'data' => $transaksi,
        ]);
    }
}