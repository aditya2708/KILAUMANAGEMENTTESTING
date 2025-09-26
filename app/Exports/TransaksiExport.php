<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Models\Donatur;
use App\Models\Bank;
use App\Models\Kantor;
use App\Models\Prog;
use App\Models\Program;
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
class TransaksiExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View{
        $array = $this->request;
        $request = (object) $array;
        $bang = Bank::all();
        
        // if (Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat') {
            
                if ($request->daterange != '') {
                    $tgl = explode(' s.d. ', $request->daterange);
                    $dari = date('Y-m-d', strtotime($tgl[0]));
                    $sampai = date('Y-m-d', strtotime($tgl[1]));
                }
                
                $idk = Auth::user()->id_kantor;
                $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                
                $now = date('Y-m-d');
                $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
                    
                $program = Program::orderBy('program')->get();
                // dd($statak);
                $max = (int)$request->max;
                $min = (int)$request->min;
                
                $bln = $request->blns != '' ? $request->blns : date('m-Y');
                $bln1 = $bln;
                $bln2 = $request->blnnnn != '' ? $request->blnnnn : $bln1;
                $thn = $request->thnn != '' ? $request->thnn : date('Y');
                $carmin = $request->min != '' ? "transaksi.jumlah >= $min" : "transaksi.jumlah IS NOT NULL";
                $carmax = $request->max != '' ? "transaksi.jumlah <= $max" : "transaksi.jumlah IS NOT NULL";
                
                $dateString1 = $bln1;
                $dateString2 = $bln2;
                $dateTime1 = strtotime($dateString1);
                $dateTime2 = strtotime($dateString2);
                $bulanTahun1 = date('m-Y', $dateTime1);
                $bulanTahun2 = date('m-Y', $dateTime2);
                    
                $arr = $request->statuus ?? null;
                $kol = $request->kol ?? null;
                $kot = $request->kota ?? null;
                $stat = $request->statak ?? null;
                $bayar = $request->bayar ?? null;
                $bank = $request->bank ?? null;
                $aha = $request->program;
                
                if(isset($aha)){
                    $program = Prog::where('id_program', $aha)->first()->coa_individu;
                }
                
                    
                if ($request->plhtgl == 0) {
                    $plugin = $tgls;
                }else if($request->plhtgl == 1){
                    $plugin = "DATE_FORMAT(transaksi.tanggal, '%m-%Y') >= '$bln' AND DATE_FORMAT(transaksi.tanggal,'%m-%Y') <= '$bln2'";
                }else{
                    $plugin = "YEAR(tanggal) = '$thn' ";
                }
                
                $transaksi = Transaksi::select('transaksi.*', 'donatur.jalur')->leftjoin('donatur', 'donatur.id', '=', 'transaksi.id_donatur')->whereRaw(" via_input = 'transaksi' AND $plugin AND $carmin AND $carmax")
                    
                    ->where(function($q) use ($request, $kan, $idk, $kot){
                        if(Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan cabang' ){
                            if($kan == null){
                                $q->whereRaw("transaksi.id_kantor = '$idk'");
                            }else{
                                if(isset($request->kota)){
                                    $q->whereIn('transaksi.id_kantor', $kot);
                                }else{
                                    $q->whereRaw("(transaksi.id_kantor = '$idk' OR transaksi.id_kantor = '$kan->unit')");
                                }
                            }
                        }else if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat' ){
                            if(isset($kot)){
                                $q->whereIn('transaksi.id_kantor', $kot);
                            }
                        }
                    })
                    
                    ->where(function($query) use ($request, $arr) {
                        if(isset($request->statuus)){
                            $query->whereIn('transaksi.status', $arr);
                        }
                    })
                            
                    ->where(function($query) use ($request, $kol) {
                        if(isset($request->kol)){
                            $query->whereIn('transaksi.kolektor', $kol);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kot) {
                        if(isset($request->kota)){
                            $query->whereIn('transaksi.id_kantor', $kot);
                        }
                    })
                            
                    ->where(function($query) use ($request, $bank) {
                        if(isset($request->bank)){
                            $query->where('transaksi.id_bank', $bank);
                        }
                    })
                    
                    ->where(function($query) use ($request, $program) {
                        if(isset($request->program)){
                            $query->where('transaksi.coa_kredit', $program);
                        }
                    })
                            
                    ->where(function($query) use ($request, $bayar) {
                        if(isset($request->bayar)){
                            $query->whereIn('transaksi.pembayaran', $bayar);
                        }
                    })
                            
                    ->where(function($query) use ($request, $stat) {
                        if(isset($request->statak)){
                            $query->whereIn('transaksi.approval', $stat);
                        }
                    });
                    
                    // if ($request->plhtgl == 0) {
                    //     $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND $tgls ")
                    //     ->where(function($query) use ($request, $arr) {
                    //         if(isset($request->statuus)){
                    //             $query->whereIn('status', $arr);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $kol) {
                    //         if(isset($request->kol)){
                    //             $query->whereIn('kolektor', $kol);
                    //         }
                    //     })
                    //     ->where(function($query) use ($request, $kot) {
                    //         if(isset($request->kota)){
                    //             $query->whereIn('id_kantor', $kot);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bank) {
                    //             if(isset($request->bank)){
                    //                 $query->where('id_bank', $bank);
                    //             }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bayar) {
                    //             if(isset($request->bayar)){
                    //                 $query->whereIn('pembayaran', $bayar);
                    //             }
                    //         })
                        
                    //     ->where(function($query) use ($request, $stat) {
                    //         if(isset($request->statak)){
                    //             $query->whereIn('approval', $stat);
                    //         }
                    //     });
                    // } elseif ($request->plhtgl == 1) {
                    //     // if(isset($request->statuus)) {
                    //     //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2'")->whereIn('status', $arr);
                    //     // }else{
                    //     //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND DATE(tanggal) >= '$bln1' AND DATE(tanggal) <= '$bln2'");
                    //     // }
                        
                    //     $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND DATE_FORMAT(tanggal, '%Y-%m') >= '$bln1' AND DATE_FORMAT(tanggal, '%Y-%m') <= '$bln2' ")
                    //     ->where(function($query) use ($request, $arr) {
                    //         if(isset($request->statuus)){
                    //             $query->whereIn('status', $arr);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bayar) {
                    //             if(isset($request->bayar)){
                    //                 $query->whereIn('pembayaran', $bayar);
                    //             }
                    //         })
                        
                    //     ->where(function($query) use ($request, $kol) {
                    //         if(isset($request->kol)){
                    //             $query->whereIn('kolektor', $kol);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bank) {
                    //             if(isset($request->bank)){
                    //                 $query->where('id_bank', $bank);
                    //             }
                    //         })
                            
                    //     ->where(function($query) use ($request, $kot) {
                    //         if(isset($request->kota)){
                    //             $query->whereIn('id_kantor', $kot);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $stat) {
                    //         if(isset($request->statak)){
                    //             $query->whereIn('approval', $stat);
                    //         }
                    //     });
                    // } else {
                    // //   return back()->with('gagal', 'Pesan berhasil disimpan.');
                    //     // if(isset($request->statuus)) {
                    //     //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'")->whereIn('status', $arr);
                    //     // }else{
                    //     //     $transaksi = Transaksi::whereRaw("$carkota AND $carkol AND $carmin AND $carmax AND $statak AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn'");
                    //     // }
                        
                    //     $transaksi = Transaksi::whereRaw("$carmin AND $carmax AND via_input = 'transaksi' AND YEAR(tanggal) = '$thn' ")
                    //     ->where(function($query) use ($request, $arr) {
                    //         if(isset($request->statuus)){
                    //             $query->whereIn('status', $arr);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bayar) {
                    //             if(isset($request->bayar)){
                    //                 $query->whereIn('pembayaran', $bayar);
                    //             }
                    //         })
                        
                    //     ->where(function($query) use ($request, $kol) {
                    //         if(isset($request->kol)){
                    //             $query->whereIn('kolektor', $kol);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $bank) {
                    //             if(isset($request->bank)){
                    //                 $query->where('id_bank', $bank);
                    //             }
                    //         })
                            
                    //     ->where(function($query) use ($request, $kot) {
                    //         if(isset($request->kota)){
                    //             $query->whereIn('id_kantor', $kot);
                    //         }
                    //     })
                        
                    //     ->where(function($query) use ($request, $stat) {
                    //         if(isset($request->statak)){
                    //             $query->whereIn('approval', $stat);
                    //         }
                    //     });
                    //     ;
                    // }
    
                // }
                
            $data = $transaksi->get();
            return view('ekspor.transaksiexport', [
                'data' => $data,
                'priode' => $request->plhtgl == 0 ? 'Data Transaksi Priode ' . ($dari ?? date('d-m-Y')) . ' s/d ' . ($sampai ?? date('d-m-Y')) : ($request->plhtgl == 1 ? 'Data Transaksi Bulan ' . $bln . ' s/d ' . $bln2 : 'Data Transaksi Tahun ' . $request->thnn),
                'company' => DB::table('company')->where('id_com', Auth::user()->id_com)->first(),
            ]);

        }
}
