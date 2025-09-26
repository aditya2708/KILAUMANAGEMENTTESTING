<?php
namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Kantor;
use App\Models\Bank;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class PenerimaanExport implements FromView
{
    // $dari, $sampai, $darib, $sampaib, $via, $unit, $stts, $viewnya, $periode
    
    public function __construct($request, $j_period)
    {
        $this->periodeHead = $j_period;
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {   
        $j_period = $this->periodeHead;
        $request = $this->request;
        
        if(Auth::user()->level === 'admin'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
        }
        
        $bank = Bank::where('id_kantor',Auth::user()->id_kantor)->get();
        

        $approve = $request->approve;
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        if ($request->bulan != '') {
            $tgl = explode('-', $request->bulan);
            $t = date($tgl[1]);
            $b = date($tgl[0]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        $selectPriode = $request->periode;
        $search = $request->cari ?? null;
        
        if($selectPriode == 'p'){
            $prd = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
        }else if($selectPriode == 'b'){
            $prd = "MONTH(transaksi.tanggal) = '$b' AND YEAR(transaksi.tanggal) = '$t'";
        }
        
        $via = $request->via != '' ? $request->via : '';
        // $via = $request->via ;
        $unit = $request->kantt != '' ? "transaksi.id_kantor = $request->kantt" : "transaksi.id_kantor IS NOT NULL ";
        
        $stts = $request->status != '' ? "transaksi.approval = $request->status" : "transaksi.approval IS NOT NULL";
        $viewnya = $request->view != '' ? $request->view : '';
        
        $columnsToSearch = ['transaksi.jumlah', 'transaksi.akun', 'transaksi.ket_penerimaan', 'transaksi.qty', 'transaksi.jumlah', 'us_ap.name', 'us_in.name', 'transaksi.donatur', 'transaksi.program', 'tambahan.unit', 'transaksi.coa_debet', 'transaksi.coa_kredit'];
        
        $like = '(';
        foreach ($columnsToSearch as $column) {
            $like .= "$column LIKE ? OR ";
        }
        $like = rtrim($like, 'OR '); // Menghapus 'OR' yang berlebihan di akhir.
        $pilihan = $request->pembayaran;
        $pembayaran = function ($query) use ($pilihan) {
                if($pilihan != '' && !empty($pilihan)){
                    if (in_array('mutasi', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'mutasi');
                    }
                    if (in_array('noncash', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'noncash');
                    }
                    if (in_array('bank', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'transfer')
                              ->orWhere('transaksi.pembayaran', 'bank');
                    }
                    if (in_array('cash', $pilihan)) {
                        $query->orWhere('transaksi.pembayaran', 'dijemput')
                              ->orWhere('transaksi.pembayaran', 'teller')
                              ->orWhere('transaksi.pembayaran', 'cash');
                    }
                }
            };
            
        
        $query = '%' . $search . '%';

            if($request->via == ''){
                if($viewnya == 'dp'){ 
                     $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                     ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                     ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                     ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                     ->selectRaw("transaksi.*,
                    (transaksi.dp/100 * transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("$prd AND $unit AND $stts AND transaksi.jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query))
                     ->get();
                }else{
                    $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                    ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                    ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->selectRaw("transaksi.*,
                    (transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("$prd AND $unit AND $stts  AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query))->get();
                }
            }else{
                if($viewnya == 'dp'){
                   $dp = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                        ->leftjoin('coa','coa.coa','=','prog.coa1')
                        ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                         ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                         ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                        ->selectRaw("transaksi.id,transaksi.tanggal,  coa.nama_coa as akun   ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                        transaksi.user_insert,transaksi.user_approve,transaksi.donatur , (transaksi.dp/100 * transaksi.jumlah) as tot ,transaksi.qty,transaksi.ket_penerimaan,transaksi.program,prog.coa1 as coa_debet,prog.coa2 as coa_kredit,coa.parent,'n' as wow,coa.level,transaksi.id_program  ")
                      ->orderBy('transaksi.id')
                      ->where($pembayaran)
                      ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));

                    $joss = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                     ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                     ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->leftjoin('coa','coa.coa','=','prog.coa2')
                    ->selectRaw("transaksi.id,transaksi.tanggal, coa.nama_coa as akun ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                    transaksi.user_insert,transaksi.user_approve,transaksi.donatur ,(transaksi.jumlah - transaksi.dp/100 * transaksi.jumlah  ) as tot,transaksi.qty ,transaksi.ket_penerimaan,transaksi.program,prog.coa1 as coa_debet,prog.coa2 as coa_kredit,coa.parent, 'n' as wow,coa.level,transaksi.id_program ")
                    ->orderBy('transaksi.id')
                    ->where($pembayaran)
                  ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts  AND jumlah > 0");
                   
                        $normal = Transaksi::leftjoin('prog','prog.id_program','=','transaksi.id_program')
                         ->leftjoin('coa','coa.coa','=','prog.coa1')
                         ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                         ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                         ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                        ->selectRaw("transaksi.id,transaksi.tanggal,  transaksi.subprogram as akun ,transaksi.tanggal,transaksi.id_kantor, transaksi.approval,transaksi.via_input,
                        transaksi.user_insert,transaksi.user_approve,transaksi.donatur , (transaksi.jumlah) as tot ,transaksi.qty,transaksi.ket_penerimaan,transaksi.program,coa_debet,coa_kredit,coa.parent,'y' as wow,coa.level,transaksi.id_program ")
                        ->unionAll($dp)
                        ->unionAll($joss)
                       ->orderBy('transaksi.id')
                      ->where($pembayaran)
                      ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query));
               
                 $data = $normal->get();
                }else{
                   $data = Transaksi::
                    leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                    ->leftjoin('users as us_ap', 'us_ap.id', '=', 'transaksi.user_approve')
                    ->leftjoin('users as us_in', 'us_in.id', '=', 'transaksi.user_insert')
                    ->leftjoin('tambahan','tambahan.id','=','transaksi.id_kantor')
                    ->selectRaw("transaksi.*,
                    (transaksi.jumlah) as tot")
                    ->where($pembayaran)
                    ->whereRaw("transaksi.via_input = '$via' AND $prd AND $unit AND $stts  AND jumlah > 0 AND $like)", array_fill(0, count($columnsToSearch), $query))->get();
                }
            }
            
        return view('ekspor.penerimaanekspor',[
            'data' => $data,
            'judul' => 'Penerimaan '.$j_period,
            'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
        
    }
}