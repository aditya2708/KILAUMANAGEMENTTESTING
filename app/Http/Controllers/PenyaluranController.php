<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Transaksi;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\User;
use App\Models\Prog;
use App\Models\Bank;
use App\Models\SumberDana;
use App\Models\Penerimaan;
use App\Models\PenerimaManfaat;
use App\Models\Pengeluaran;
use App\Models\Penyaluran;
use App\Models\Asnaf;
use Auth;
use DB;
use Carbon\Carbon;
use DataTables;
use Excel;

use App\Exports\PenyaluranExport;


class PenyaluranController extends Controller
{
    
    
    
    
    public function index(Request $request, Donatur $donatur)
    {
        
        
        if (Auth::user()->level === 'admin') {
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        } else {
            $kantor = Kantor::where('id', Auth::user()->id_kantor)->get();
        }
        $asnaf = Asnaf::all();
        $bank = Bank::where('id_kantor', Auth::user()->id_kantor)->get();
        // dd($request);
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        if ($request->bln != '') {
            $tgl = explode('-', $request->bln);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        
        if ($request->tglSalur != '') {
            $tgl1 = explode(' - ', $request->tglSalur);
            $dari1 = date('Y-m-d', strtotime($tgl1[0]));
            $sampai1 = date('Y-m-d', strtotime($tgl1[1]));
        }else{
            $hari_ini1 = date('Y-m-d');
            $dari1 = date('Y-m-d', strtotime($hari_ini1));
            $sampai1 = date('Y-m-d', strtotime($hari_ini1));
        }
        if ($request->bln != '') {
            $tgl = explode('-', $request->bln);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        
        
         if($request->mohon == '0' || $request->mohon == null){
             $mohon = "pengeluaran.tgl_mohon";
        }
        else if($request->mohon == '1'){
             $mohon = "pengeluaran.tgl_salur";
        }
        else if($request->mohon == '2'){
             $mohon = "pengeluaran.tgl";
        }
        
        
        if($request->prd == 0){
            $prd = "DATE($mohon) >= '$dari' AND DATE($mohon) <= '$sampai'";
            $prdFile = $dari . '-' . $sampai;
            $tglSalur = "DATE(pengeluaran.tgl_salur) >= '$dari' AND DATE(pengeluaran.tgl_salur) <= '$sampai'";
        }else if($request->prd == 1){
            $tglSalur = "DATE(pengeluaran.tgl_salur >= '$dari' AND DATE(pengeluaran.tgl_salur) <= '$sampai'";
            $prdFile = $b . '-' . $t;
            $prd = "MONTH($mohon) = '$b' AND YEAR($mohon) = '$t'";
        }
        
        //EXPORT LINE
             if($request->tombol != null || !empty($request->tombol)){
                if($request->tombol == 'xls'){
                    $r = Excel::download(new PenyaluranExport($request, $prdFile), 'Penyaluran Priode '.$prdFile.'.xls');
                    ob_end_clean();
                    return $r;
                }else{
                    $r = Excel::download(new PenyaluranExport($request, $prdFile), 'Penyaluran Priode '.$prdFile.'.csv');
                    ob_end_clean();
                    return $r;
                }
             }
         //END EXPORT LINE


        $r_prog = $request->program;
         
        $nom_tran = $request->dari_nominal != ''  || !empty($request->dari_nominal) ? "pengeluaran.nominal >= '$request->dari_nominal' AND pengeluaran.nominal <= '$request->sampai_nominal'" : "pengeluaran.nominal > 0";
        
        $jenisTransaksi = function($q) use ($request) {
                            if($request->jenis_transaksi != ''){ $q->whereIn('pengeluaran.pembayaran', $request->jenis_transaksi); }
                        };
                        
        $jenisPM = function($q) use ($request) {
                            if( $request->jenisPM != ''){ $q->where('pmnf.jenis_pm', $request->jenisPM); }
                        };
                        
        $PJ = function($q) use ($request) {
                            if($request->PJ != ''){ $q->where('pmnf.nama_pj', $request->PJ); }
                        };
                        
        $status = function($q) use ($request) {
                            if($request->status != ''){ $q->where('pengeluaran.acc', $request->status); }
                        };
                        
        $filAsnaf = function($q) use ($request) {
                        if($request->asnaf != ''){
                            // $q->whereRaw('pmnf.asnaf IS NOT NULL');
                            $q->whereIn('pmnf.asnaf', $request->asnaf);
                        }
                        // else{
                        // }
                    };
                    
        $advKantor = function($q) use ($request) {
                        if($request->advKantor == '' || empty($request->advKantor)){
                            $q->whereRaw('pengeluaran.kantor IS NOT NULL');
                        }else{
                            $q->whereIn('pengeluaran.kantor', $request->advKantor);
                        }
                    };
        
        $backdate    = $request->backdate == '' || empty($request->backdate) ? "DATE(pengeluaran.tgl) IS NOT NULL AND DATE(pengeluaran.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(pengeluaran.tgl_mohon) != DATE(pengeluaran.created_at) " : "DATE(pengeluaran.tgl_mohon) = DATE(pengeluaran.created_at)");
        
      
        if ($request->ajax()) {
            if($request->tab == 'tab1'){
                 $dataFoot = Pengeluaran::selectRaw("count(pengeluaran.id) as qty, COUNT(DISTINCT pmnf.penerima_manfaat) as pm, sum(nominal) as sum")
                 ->leftjoin('penerima_manfaat AS pmnf', 'pmnf.id','=','pengeluaran.id_pm')
                 ->leftjoin('tambahan as kantor','kantor.id','=','pengeluaran.kantor')
                    ->whereRaw("$prd AND via_input = 'penyaluran' AND $nom_tran AND $backdate")
                    ->where(function($q) use ($request) {
                        if($request->user_insert != ''){ $q->where('pengeluaran.user_input', $request->user_insert); }
                    })
                    ->where(function($q) use ($r_prog) {
                        if($r_prog != ''){ $q->where('pengeluaran.program', $r_prog); }
                    }) 
                    ->where($jenisPM)
                    ->where($PJ)
                    ->where($status)
                    // ->where($mohon)
                    ->where($filAsnaf)
                    ->where($advKantor)
                    ->where($jenisTransaksi)
                    ->where(function($query) use ($request){
                            if ($request->cartext != '' && !empty($request->cartext)) {
                                $searchTerm = '%' . $request->cartext . '%';
                                $query->where('pengeluaran.id', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.id_pm', 'LIKE', $searchTerm)
                                      ->orWhere('pmnf.penerima_manfaat', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.jenis_transaksi', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.nominal', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_mohon', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_salur', 'LIKE', $searchTerm)
                                      ->orWhere('kantor.unit', 'LIKE', $searchTerm)
                                      ;
                            }
                        })
                    ->orderBy('pengeluaran.created_at', 'desc')
                    ->first();
                    return response()->json($dataFoot);
            }
            
            $data = Pengeluaran::selectRaw("pengeluaran.*, pmnf.penerima_manfaat as pm, pmnf.jenis_pm as jenis_pm")
            ->whereRaw("$prd AND via_input = 'penyaluran' AND $nom_tran AND $backdate")
                ->leftjoin('penerima_manfaat AS pmnf', 'pmnf.id','=','pengeluaran.id_pm')
                ->leftjoin('tambahan as kantor','kantor.id','=','pengeluaran.kantor')
                        ->where(function($q) use ($request) {
                            if($request->user_insert != ''){ $q->where('pengeluaran.user_input', $request->user_insert); }
                        })
                        ->where(function($q) use ($r_prog) {
                            if($r_prog != ''){ $q->where('pengeluaran.program', $r_prog); }
                        }) 
                        ->where($jenisPM)
                        ->where($PJ)
                        ->where($status)
                        // ->where($mohon)
                        ->where($filAsnaf)
                        ->where($advKantor)
                        ->where($jenisTransaksi)
                        ->where(function($query) use ($request){
                            if ($request->cartext != '' && !empty($request->cartext)) {
                                $searchTerm = '%' . $request->cartext . '%';
                                $query->where('pengeluaran.id', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.id_pm', 'LIKE', $searchTerm)
                                      ->orWhere('pmnf.penerima_manfaat', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.jenis_transaksi', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.nominal', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_mohon', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_salur', 'LIKE', $searchTerm)
                                      ->orWhere('kantor.unit', 'LIKE', $searchTerm)
                                      ;
                            }
                        })
                        // ->havingRaw($selhave)
                        ->orderBy('pengeluaran.created_at', 'desc');
                
                
                
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('donaturr', function ($data) {
                //     if ($data->via_input == 'penerimaan') {
                //         $ttr = '';
                //     } else {
                //         $ttr = $data->donatur;
                //     }
                //     return $ttr;
                // })

                ->addColumn('kantorr', function ($data) {
                    $trr = Kantor::select('unit')->where('id', $data->kantor)->first();
                    return $trr['unit'];
                })

                ->addColumn('user_i', function ($data) {
                    $ppp = User::select('name')->where('id', $data->user_input)->first();
                    if ($ppp != null) {
                        $ttr = $ppp->name;
                    } else {
                        $ttr = '';
                    }
                    return $ttr;
                })

                ->addColumn('user_a', function ($data) {
                    $ppp = User::select('name')->where('id', $data->user_approve)->first();
                    if ($ppp != null) {
                        $ttr = $ppp->name;
                    } else {
                        $ttr = '';
                    }
                    return $ttr;
                })

                ->addColumn('tanggal_salur', function ($data) {

                    $ttr = date('Y-m-d', strtotime($data->tgl_salur));
                    return $ttr;
                })

                ->addColumn('tanggal_mohon', function ($data) {

                    $ttr = date('Y-m-d', strtotime($data->tgl_mohon));
                    return $ttr;
                })

                ->addColumn('apr', function ($data) {

                    if ($data->acc == 1) {
                        $button = '<label class="btn btn-success btn-xxs"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved">Acc</label>';
                    } elseif ($data->acc == 0) {
                        $button = '<label class="btn btn-danger btn-xxs" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected">Reject</i></label>';
                    } else {
                        $button = '<label class="btn btn-warning btn-xxs" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending">Pending</label>';
                    }
                    return $button;
                })

                ->addColumn('jml', function ($data) {
                    $jml = number_format($data->nominal, 0, ',', '.');
                    return $jml;
                })

                ->rawColumns(['kantorr', 'user_a', 'tanggal_mohon', 'tanggal_salur', 'user_i', 'donaturr', 'apr', 'jml'])
                ->make(true);
        }
        return view('fins.penyaluran', compact('kantor', 'bank','asnaf'));
    }

    public function button(Request $request){
        if (!empty($request->button) && $request->button == 'hapus') {
            $data = Pengeluaran::where('id', $request->id)->first();
            if ($data) {
                $data->delete();
                return response()->json(['message' => 'Data berhasil dihapus']);
            } else {
                return response()->json(['message' => 'Data tidak ditemukan']);
            }
        } else if (!empty($request->button) && $request->button == 'acc') {
            // Fungsi untuk mengubah status 'acc' menjadi 1
            $data = Pengeluaran::where('id', $request->id)->first();
            if ($data) {
                $data->update(['acc' => 1]);
                return response()->json(['message' => 'Data berhasil diupdate menjadi accepted']);
            } else {
                return response()->json(['message' => 'Data tidak ditemukan']);
            }
        } else if (!empty($request->button) && $request->button == 'reject') {
            // Fungsi untuk mengubah status 'acc' menjadi 0
            $data = Pengeluaran::where('id', $request->id)->first();
            if ($data) {
                $data->update(['acc' => 0]);
                return response()->json(['message' => 'Data berhasil diupdate menjadi rejected']);
            } else {
                return response()->json(['message' => 'Data tidak ditemukan']);
            }
        }
    }

    public function get_program_penyaluran()
    {
        $prog = Prog::all();
        foreach ($prog as $val) {
            $sd = SumberDana::where('id_sumber_dana',$val->id_sumber_dana);
            if (count($sd->get()) > 0) {
                $pp = $sd->first()->sumber_dana;
            } else {
                $pp = '-';
            }
            $h1[] = [
                "text" => $val->parent . "-" . $val->level . "-" . $val->program,
                "program" => $val->program,
                "id" => $val->id_program,
                "parent" => $val->parent,
                "sumberdana" => $pp,
            ];
        }
        return response()->json($h1);
    }

    public function post_penyaluran(Request $request)
    {
        $bank = [];
        $noncash = [];
        $coa = [];
        $id_kantor  = [];
        $jenis_trans = [];
        $nominal = [];
        $qty = [];
        $tgl_salur = [];
        $tgl_mohon = [];
        $via_mohon = [];
        $keterangan = [];
        $pembayaran = [];
        $debet = [];
        $pm = [];
        $program = [];
        
        foreach ($request->arr as $val) {

            if ($val['pembayaran'] == 'bank') {
                $p = Bank::where('id_bank', $val['bank'])->first();
                $debet[] = $p['id_coa'];
            } else if ($val['pembayaran'] == 'noncash') {
                $debet[] = $val['non_cash'];
            } else {
                $p = Kantor::where('id', $val['id_kantor'])->first();
                $debet[] = $p['id_coa'];
            }

            if ($val['jenis_pm'] == 'personal') {
                $prog = Prog::where('id_program', $val['coa'])->first()->coa_individu;
            } else {
                $prog = Prog::where('id_program', $val['coa'])->first()->coa_entitas;
            }

// dd($val['jenis_pm']);


            $bank[] = $val['bank'];
            $noncash[] = $val['non_cash'];
            $coa[] = $prog;
            $id_kantor[] = $val['id_kantor'];
            $jenis_trans[] = $val['jenis_trans'];
            $nominal[] = $val['total'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['total']);
            $qty[] = $val['qty'];
            $pm[] = $val['idpm'];
            $via_mohon[] = $val['via_per'];
            $tgl_salur[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
            $tgl_mohon[] = $val['tgl_per'] == '' ? date('Y-m-d') : $val['tgl_per'];
            $keterangan[] = $val['keterangan'];
            $pembayaran[] = $val['pembayaran'];
            $program[] = $val['programPush'];
        }

        for ($i = 0; $i < count($request->arr); $i++) {
            $data = new Pengeluaran;
            $data->coa_kredit = $debet[$i];
            $data->coa_debet = $coa[$i];
            $data->jenis_transaksi = $jenis_trans[$i];
            $data->program = $program[$i];
            $data->qty = $qty[$i];
            $data->nominal = $nominal[$i];
            $data->pembayaran = $pembayaran[$i];
            $data->bank = $bank[$i];
            $data->keterangan = $keterangan[$i];
            $data->kantor = $id_kantor[$i];
            $data->tgl = $tgl_salur[$i];
            $data->tgl_salur = $tgl_salur[$i];
            $data->non_cash = $noncash[$i];
            $data->via_mohon = $via_mohon[$i];
            $data->tgl_mohon = $tgl_mohon[$i];
            $data->id_pm = $pm[$i];
            // $data->department = $depart[$i];
            // $data->saldo_dana = $saldo_dana[$i];
            $data->user_input = Auth::user()->name;
            $data->acc = 2;
            $data->user_input = Auth::user()->id;
            $data->via_input = 'penyaluran';
            $data->save();
        }

        // for ($i = 0; $i < count($request->arr); $i++) {
        //     $data = new Penyaluran;
        //     $data->coa_kredit = $debet[$i];
        //     $data->coa_debet = $coa[$i];
        //     $data->jenis_transaksi = $jenis_trans[$i];
        //     $data->qty = $qty[$i];
        //     $data->nominal = $nominal[$i];
        //     $data->pembayaran = $pembayaran[$i];
        //     $data->bank = $bank[$i];
        //     $data->keterangan = $keterangan[$i];
        //     $data->kantor = $id_kantor[$i];
        //     $data->tgl_salur = $tgl_salur[$i];
        //     $data->tgl_mohon = $tgl_mohon[$i];
        //     $data->nama_pm = $pm[$i];
        //     $data->non_cash = $noncash[$i];
        //     // $data->department = $depart[$i];
        //     // $data->saldo_dana = $saldo_dana[$i];
        //     $data->user_input = Auth::user()->name;
        //     $data->acc = 1;
        //     $data->user_input = Auth::user()->id;
        //     $data->via_input = 'penyaluran';

        //     // dd($data);
        //     $data->save();
        // }

        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function edit_penyaluran($id){
        $data = Pengeluaran::findOrFail($id);
        return response()->json($data);
    }
    
    public function editPostPenyaluran($id, Request $request){
        $bank = [];
        $noncash = [];
        $coa = [];
        $id_kantor  = [];
        $jenis_trans = [];
        $nominal = [];
        $qty = [];
        $tgl_salur = [];
        $tgl_mohon = [];
        $via_mohon = [];
        $keterangan = [];
        $pembayaran = [];
        $debet = [];
        $pm = [];
        $program = [];
        
        foreach ($request->arr as $val) {

            if ($val['pembayaran'] == 'bank') {
                $p = Bank::where('id_bank', $val['bank'])->first();
                $debet[] = $p['id_coa'];
            } else if ($val['pembayaran'] == 'noncash') {
                $debet[] = $val['non_cash'];
            } else {
                $p = Kantor::where('id', $val['id_kantor'])->first();
                $debet[] = $p['id_coa'];
            }

            if ($val['jenis_pm'] == 'personal') {
                $prog = Prog::where('id_program', $val['coa'])->first()->coa_individu;
            } else {
                $prog = Prog::where('id_program', $val['coa'])->first()->coa_entitas;
            }



            $bank[] = $val['bank'];
            $noncash[] = $val['non_cash'];
            $coa[] = $prog;
            $id_kantor[] = $val['id_kantor'];
            $jenis_trans[] = $val['jenis_trans'];
            $nominal[] = $val['total'] == '' ? 0 : preg_replace("/[^0-9]/", "", $val['total']);
            $qty[] = $val['qty'];
            $pm[] = $val['idpm'];
            $via_mohon[] = $val['via_per'];
            $tgl_salur[] = $val['tgl'] == '' ? date('Y-m-d') : $val['tgl'];
            $tgl_mohon[] = $val['tgl_per'] == '' ? date('Y-m-d') : $val['tgl_per'];
            $keterangan[] = $val['keterangan'];
            $pembayaran[] = $val['pembayaran'];
            $program[] = $val['programPush'];
        }

        for ($i = 0; $i < count($request->arr); $i++) {
            $data = Pengeluaran::where('id', $id)->first();
            $data->coa_kredit = $debet[$i];
            $data->coa_debet = $coa[$i];
            $data->jenis_transaksi = $jenis_trans[$i];
            $data->program = $program[$i];
            $data->qty = $qty[$i];
            $data->nominal = $nominal[$i];
            $data->pembayaran = $pembayaran[$i];
            $data->bank = $bank[$i];
            $data->keterangan = $keterangan[$i];
            $data->kantor = $id_kantor[$i];
            $data->tgl = $tgl_salur[$i];
            $data->tgl_salur = $tgl_salur[$i];
            $data->non_cash = $noncash[$i];
            $data->via_mohon = $via_mohon[$i];
            $data->tgl_mohon = $tgl_mohon[$i];
            $data->id_pm = $pm[$i];
            // $data->department = $depart[$i];
            // $data->saldo_dana = $saldo_dana[$i];
            // $data->user_input = Auth::user()->name;
            // $data->acc = 2;
            // $data->user_input = Auth::user()->id;
            // $data->via_input = 'penyaluran';
            // dd($data->nominal);
            // dd($data);
            $data->update();
            return response()->json(['success' => 'Data is successfully added']);
        }
        
    }
    
}
