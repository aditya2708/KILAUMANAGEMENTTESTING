<?php
namespace App\Exports;

use Auth;
use App\Models\Gaji;
use App\Models\Kantor;
use App\Models\Jabatan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GajiQueryExport implements FromView
{
    
    
    public function __construct(string $unit, string $status, string $bulan, string $tahun, string $header, string $perusahaan)
    {
        $this->unit = $unit;
        $this->status = $status;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->header = $header;
        $this->perusahaan = $perusahaan;
        return $this;
    }

    public function view(): View
    {   
        // if($this->unit == ''){
        //     return view('eksportgaji', [
        //         'data' => Gaji::query()->select('gaji.*','jabatan.jabatan','tambahan.unit')->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')->whereMonth('gaji.created_at',$this->bulan)->whereYear('gaji.created_at',$this->tahun)->get()
        //     ]);
        // }else{
       
        // $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
        // $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
        $kantor = $this->unit != 'semua-unit-kerja' ? "id_kantor = $this->unit" : "id_kantor != ''";
        $status = $this->status != 'semua-status-kerja' ? "status_kerja = '$this->status'" : "status_kerja != ''";
        
        
        if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            return view('eksportgaji', [
                'data' => Gaji::query()->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                        ->select('gaji.*','jabatan.jabatan','tambahan.unit')
                        ->whereRaw("$kantor AND $status AND MONTH(gaji.created_at) = $this->bulan AND YEAR(gaji.created_at) = $this->tahun ")->get(),
                        'priode' => $this->header,
                        'company' => $this->perusahaan,
            ]);
        }else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
            $id = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', $id)->first();
            
            if($this->unit == 'semua-unit-kerja'){
                // dd('gada');
                if($k == null){
                    return view('eksportgaji', [
                        'data' => Gaji::query()->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                                ->select('gaji.*','jabatan.jabatan','tambahan.unit')
                                ->whereRaw("id_kantor = '$id' AND $status AND MONTH(gaji.created_at) = $this->bulan AND YEAR(gaji.created_at) = $this->tahun ")->get(),
                                'priode' => $this->header,
                        'company' => $this->perusahaan,
                    ]);    
                }else{
                    return view('eksportgaji', [
                        'data' => Gaji::query()->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                                ->select('gaji.*','jabatan.jabatan','tambahan.unit')
                                ->whereRaw("(id_kantor = '$id' OR id_kantor = '$k->id') AND $status AND MONTH(gaji.created_at) = $this->bulan AND YEAR(gaji.created_at) = $this->tahun ")->get(),
                                'priode' => $this->header,
                        'company' => $this->perusahaan,
                    ]);
                }
            }else{
                // dd($this->unit);
                return view('eksportgaji', [
                    'data' => Gaji::query()->join('jabatan','jabatan.id','=','gaji.id_jabatan')->join('tambahan','tambahan.id','=','gaji.id_kantor')
                            ->select('gaji.*','jabatan.jabatan','tambahan.unit')
                    ->whereRaw("id_kantor = '$this->unit' AND $status AND MONTH(gaji.created_at) = $this->bulan AND YEAR(gaji.created_at) = $this->tahun ")->get(),
                    'priode' => $this->header,
                        'company' => $this->perusahaan,
                
                ]);
            }
        }
        // }
    }
}