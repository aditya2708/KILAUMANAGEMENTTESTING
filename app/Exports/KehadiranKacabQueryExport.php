<?php
namespace App\Exports;

use App\Gaji;
use App\Kantor;
use App\Jabatan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KehadiranKacabQueryExport implements FromView
{
    
    
    public function __construct(string $status, string $bulan, string $tahun)
    {
        $this->status = $status;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        return $this;
    }

    public function view(): View
    {
        $status = $this->status != 'semua-status-kerja' ? "status_kerja = '$this->status'" : "status_kerja != ''";
        return view('eksportkehadirankacab', [
            'data' => Gaji::query()->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                    ->select('gaji.*','jabatan.jabatan','tambahan.unit')
                    ->whereRaw("$status AND MONTH(gaji.created_at) = $this->bulan AND YEAR(gaji.created_at) = $this->tahun ")->get()
        ]);
        // }
    }
}