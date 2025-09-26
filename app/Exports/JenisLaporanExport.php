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
use App\Models\Kantor;
use App\Models\Bank;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as BladeView;;
use Maatwebsite\Excel\Concerns\FromView;
use DataTables;
use Illuminate\Support\Collection;
use DB;

class JenisLaporanExport implements  FromView
{
public function __construct($jenis)
    {
      
        $this->jenis = $jenis;
        return $this;
    }

    public function view(): View
    {   
      $jenis = $this->jenis;

      $laporan = RumlapKeuangan::select('rumlap_keuangan.*')->whereRaw("rumlap_keuangan.id_jenlap = '$jenis' ")->orderBy('rumlap_keuangan.urutan', 'ASC')->get();
                    return view('ekspor.jenislaporanexport',[
                        'data' => $laporan,
                        'judul' => DB::table('jenlap_keuangan')->where('jenlap_keuangan.id', $jenis)->first()->deskripsi,
                        'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
                    ]);

 
        }
    }
