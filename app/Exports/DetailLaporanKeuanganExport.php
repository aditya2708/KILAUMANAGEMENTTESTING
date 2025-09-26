<?php
namespace App\Exports;

use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class DetailLaporanKeuanganExport implements FromView
{
    
    public function __construct($data, $request)
    {
        $this->data = $data;
        $this->request = $request;
        return $this;
    }

    public function view(): View { 
        $data = $this->data;
        $request = $this->request;
        $pembeda = $request->pembeda;
        $thism = $request->bln2;
        $title = $request->title;
        $nama = $request->nama_coa;
        $tahunnya = $request->pembedathn;
        $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
        $mon =  $request->bln == '' ? date('m') : $request->bln; 
        $currentYear = $p;
        $range = 1; // rentang tahun yang ingin ditampilkan
        $oldestYear = $currentYear - $range;
        $tet =  date('m') ; 
        $dummy = [$tet,$tet];
        $monbanyak =  $request->bln2 == '' ? $dummy : $request->bln2 ;
        $latestData = end($monbanyak);
        $multinya = $request->mulbul ; 
        return view('ekspor.detaillaporankeuanganexport',[
            'data' => $data,
            'bulan' => $multinya == '0' ? $mon : $latestData,
            'tahun' => $tahunnya == 'sekarang' ? $p : $oldestYear ,
            'nama' =>$nama,
            'title' => $title,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name,
            'pembeda' => ucfirst($pembeda)
        ]);
    }
}