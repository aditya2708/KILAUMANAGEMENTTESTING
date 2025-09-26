<?php 

namespace App\Exports;

use App\Models\PenerimaManfaat;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PenerimaManfaatExport implements FromView
{
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function  view(): View
    {
        $request  = $this->request ;
            $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
            $tgl_akhir = $request->sampai != '' ? $request->sampai : date('Y-m-d');
            $jenis = $request->jenis != '' ? "jenis_pm = '$request->jenis'" : "jenis_pm IS NOT NULL";
            $jk = $request->jk != '' ? "penerima_manfaat.jk = '$request->jk'" : "penerima_manfaat.jk IS NOT NULL";
            $status = $request->status != '' ? "penerima_manfaat.status = '$request->status'" : "penerima_manfaat.status IS NOT NULL";
            $asnaf = $request->asnaf;
            $kantor = $request->kantor;
            $pj = $request->pj;
            $no_hp = $request->no_hp;
            $periode = 'Dari Tanggal '. $tgl_awal. 'Sampai '. $tgl_akhir ;
        $data = PenerimaManfaat::leftjoin('tambahan', 'tambahan.id', '=', 'penerima_manfaat.kantor')
        ->leftjoin('asnaf', 'asnaf.id', '=', 'penerima_manfaat.asnaf')->whereRaw("DATE_FORMAT(tgl_reg , '%Y-%m-%d') >= '$tgl_awal' AND DATE_FORMAT(tgl_reg , '%Y-%m-%d') <= '$tgl_akhir' AND $jenis  AND $status")
        ->selectRaw("penerima_manfaat.*,tambahan.unit,asnaf.asnaf")
                    ->where(function($query) use ($request, $asnaf) {
                        if(isset($request->asnaf)){
                            $query->whereIn('asnaf', $asnaf);
                        }
                    })
                    ->where(function($query) use ($request, $kantor) {
                        if(isset($request->kantor)){
                            $query->whereIn('kantor', $kantor);
                        }
                    })
                    ->where(function($query) use ($request, $pj) {
                        if(isset($request->pj)){
                            $query->whereIn('nama_pj', $pj);
                        }
                    })
                    // ->where('hp', 'like', '%$no_hp%')
                    ->where(function($query) use ($request) {
                        $no_hp = $request->input('no_hp'); // Mengambil nomor HP dari permintaan
                        if(isset($no_hp)){
                            $query->where('hp', 'LIKE', '%' . $no_hp . '%');
                        }
                    })
                    ->get();
  
        return view('ekspor.penerimamanfaatexport',[
            'data' => $data,
            'periode' => $periode,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);
        
    }
}
