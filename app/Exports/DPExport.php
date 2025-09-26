<?php
namespace App\Exports;

use Auth;
use App\Models\Prog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class DPExport implements FromView
{
    
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View { 
        
            $request = $this->request;
            
            $sumber = $request->sdana != '' ? "sumber_dana.id_sumber_dana = $request->sdana" : "sumber_dana.id_sumber_dana != ''"; 
            $kntr = $request->kntr != '' ? "transaksi.id_kantor = $request->kntr" : "transaksi.id_kantor != ''";  
            // $jenis = $request->jenis != '' ? "transaksi.pembayaran = $request->jenis" : "transaksi.pembayaran != ''";  
             $jenis = $request->jenis;
             $dpdari = $request->dpdari != '' ? $request->dpdari : 0;
              $dpsampai = $request->dpsampai != '' ? $request->dpsampai : 100;
            // $kntr = $request->kntr != '' ? "transaksi.id_kantor = ''" : "transaksi.id_kantor = '$request->kntr'";
            // $sumber = $request->sdana == '' ? "sumber_dana.id_sumber_dana = '$request->sdana'" : "sumber_dana.id_sumber_dana = ''";
            
            $tgl_awal = $request->dari != '' ? $request->dari : date('Y-m-d');
            $tgl_akhir = $request->sampai != '' ? $request->sampai : $tgl_awal;
            
            if ($request->month != '') {
                $tgl = explode('/', $request->month);
                $b = $tgl[0];
                $t = $tgl[1];
            }else{
                $b = date('m');
                $t = date('Y');
            }
            
            if($request->periode == 'hari'){
                if($jenis == 'cash'){
                                $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,transaksi.qty,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran != 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else if($jenis == 'noncash'){
                    $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran = 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else{
                      $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp,transaksi.jumlah,transaksi.subprogram,
                                  SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                  SUM(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                  COUNT(IF(DATE(transaksi.tanggal) >= '$tgl_awal' AND DATE(transaksi.tanggal) <= '$tgl_akhir' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls
                                 ")
                                ->where('prog.parent', 'n')
                                ->whereRaw("$kntr AND $sumber AND prog.parent = 'n' AND transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }
            }else if($request->periode == 'bulan'){
                  if($jenis == 'cash'){
                    $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,transaksi.qty,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah, 
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran != 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else if($jenis == 'noncash'){
                    $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp ,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls  ")
                                 ->whereRaw("transaksi.pembayaran = 'noncash' AND $kntr AND $sumber AND prog.parent = 'n'  AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }else{
                      $datas = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                ->leftjoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'prog.id_sumber_dana')
                                ->selectRaw("sumber_dana.id_sumber_dana,sumber_dana.sumber_dana,prog.id_program, prog.program,transaksi.dp,transaksi.jumlah,transaksi.subprogram,
                                  SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1' , transaksi.jumlah, 0)) AS jumlah,
                                  SUM(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1' , transaksi.dp/100 * transaksi.jumlah, 0)) AS tot,
                                  COUNT(IF(MONTH(transaksi.tanggal) >= '$b' AND YEAR(transaksi.tanggal) <= '$t' AND transaksi.approval = '1'  , transaksi.jumlah, NULL) > 0 ) AS jmls
                                 ")
                                ->where('prog.parent', 'n')
                                ->whereRaw("$kntr AND $sumber AND prog.parent = 'n' AND transaksi.dp >= '$dpdari' AND transaksi.dp <= '$dpsampai' AND transaksi.approval = '1'")
                                ->groupBy('prog.id_program', 'prog.program')
                                ->orderBy('transaksi.id', 'DESC')
                                ->get();
                }
            }    
          
        return view('ekspor.dpexport',[
            'data' => $datas,
            'priode' => 'Dana Pengelola Priode '.$tgl_awal. ' s/d '. $tgl_akhir,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
}