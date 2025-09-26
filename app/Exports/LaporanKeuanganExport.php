<?php
namespace App\Exports;

use Auth;
use App\Models\JenlapKeuangan;
use App\Models\RumlapKeuangan;
use App\Models\Pengeluaran;
use App\Models\Penutupan;
use App\Models\SaldoAw;
use App\Models\Transaksi;
use App\Models\Jurnal;
use App\Models\Tunjangan;
use App\Models\COA;
use App\Models\Kantor;
use App\Models\Bank;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as BladeView;;
use Maatwebsite\Excel\Concerns\FromView;
use DataTables;
use Illuminate\Support\Collection;
use DB;
use DateTime;

class LaporanKeuanganExport implements  FromView
{
    
public function __construct($data, $title, $tahun)
    {
        $this->title = $title;
        $this->data = $data;
        $this->tahun = $tahun;
        return $this;
    }

    public function view(): View
    {   
        $title = $this->title;
        $tahun = $this->tahun; //'2024'
        $data = $this->data;
        
        $tahunIni = date('Y', strtotime($tahun));
        
        $tahunLaluObj = new DateTime($tahun); // Menggunakan DateTime dengan tahun sekarang
        $tahunLaluObj->modify('-1 year');
        $tahunLalu = $tahunLaluObj->format('Y');

        return view('ekspor.laporankeuanganexport',[
            'data' => $data,
            'title' => $title,
            'tahunini' => $tahunIni,
            'tahunlalu' => $tahunLalu,
            'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
                     
    }
}
