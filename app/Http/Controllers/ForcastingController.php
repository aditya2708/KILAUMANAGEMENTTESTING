<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use Auth;
use Carbon\Carbon;
use DataTables;

class ForcastingController extends Controller
{
    public function forcasting(&$datas, $alpha, $jumdat){
        // dd($id);
        // $data = [
        //     '580000', '620000', '660000', '700000', '700000', '720000', '620000', '600000'
        // ];
        // $data = Transaksi::select(\DB::raw("MONTH(created_at) AS date, SUM(jumlah) as jumlah"))->where('created_at', '>', Carbon::now()->subMonth(3)->toDateTimeString())->whereMonth('created_at', '!=', Carbon::now()->month)->groupBy('date')->get();
        // $data = Transaksi::select(\DB::raw("DATE_FORMAT(created_at, '%Y-%m') AS date, SUM(jumlah) as jumlah"))
        //         ->whereRaw("MONTH(created_at) != MONTH(now()) AND YEAR(created_at) != MONTH(now())")
        //         // ->Where( \DB::raw('YEAR(created_at)'), '!=', Carbon::now()->year )
        //         ->whereMonth('created_at', '!=', 4)
        //         ->groupBy('date')->get();
        // dd($data);
        $data = [
            '57332200', '61464500', '66912600', '76663400', '81279100', '72765700', '78153700', '90137750', '91007400', '82542200', '100556200', '91587200', '81382100', '96577000', '103142100', '107269300'
        ];
        // $data = [];
        // dd($data);
        
        // metode forecasting dengan trend moment
        
        // $x = 0;
        // $i = 0;
        // $y = 0;
        // $totxy = 0;
        // $xy = 0;
        // $x2 = 0;
        // foreach($data as $key => $val){
        //     $i += 1;
        //     $y += $val;
        //     // $y += $val->jumlah;
        //     $x += $key;
        //     // $totxy = $key * $val->jumlah;
        //     $totxy = $key * $val;
        //     $xy += $totxy;
        //     $x2 += pow($key, 2);
        // }
        // // dd($x);
        // $tot1 = $y * $x;
        // $a = $i * $x;
        // $b = $x * $x;
        
        // $tot2 = $xy * $i;
        // $a1 = $x * $i;
        // $b1 = $x2 * $i;
        // // $e = $
        
        // $tot3 = $tot1 - $tot2;
        // $tot4 = ($a + $b) - ($a1 + $b1);
        // $nilai_b = $tot3 / $tot4;
        
        
        // $tot5 = $y - ($x *round($nilai_b,2));
        // $nilai_a = $tot5 / $i;
        
        // // $tot = (round($nilai_b,2) * 9);
        // $datas = [];
        // for($c = 0; $c < 12; $c++){
        //     $datas[] = [
        //         'hasil' => round($nilai_a) + (round($nilai_b,2) * $c),
        //         's' => $c
        //     ];
        // }
        
        // tutup trend moment
        
        // Forecasting dengan triple exponetial smoothing
        
        // foreach($data as $key => $val){
            
        // }
        
        $jumlah_data = count($data);
        $datas = [];
        
        $tahun = array();
		$kasus = array();
		$jml = array();
		$s1 = array();
		$s2 = array();
		$s3 = array();
		$at = array();
		$bt = array();
		$ct = array();
		$fct = array();
		$err = array();
		$abs_err = array();
		$pe = array();
// 		$a = 0.041625687;
        $a1 = $alpha;
        $jumlah = $jumdat == 0 || $jumdat == '' ? $jumlah_data : $jumdat + $jumlah_data;
        
        
        
		$m = 1;
		$sum = 0;
		$sum_pe = 0;
        
        // dd($data[0]['jumlah']);
        
        // a1
        for ($i = 0; $i < $jumlah; $i++) {
            if ($i == 0) {
                // $no = $i;
                $datas['a1'][] = [
                    'yt' => $jml[$i] = (float)$data[$i],
                    's1t' => $s1[$i] = (float)$data[$i],
                    's2t' => $s2[$i] = (float)$data[$i],
                    's3t' => $s3[$i] = (float)$data[$i],
                    'at' => $at[$i] = (float)$data[$i],
                    'bt' => $bt[$i] = 0,
                    'ct' => $ct[$i] = 0,
                    'forcasting' => $fct[$i] = (float)$data[$i],
                    'error' => $err[$i] = 0,
                    'abs' => $abs_err[$i] = 0,
                    'PE' => $pe[$i] = 0
                ];
            }else{
                if($i < $jumlah_data){
                    $datas['a1'][] = [
                        'yt' => $jml[$i] = (float)$data[$i],
                        's1t' => $s1[$i] = ($a1 * (float)$data[$i]) + ((1 - $a1) * ($s1[$i - 1])),
                        's2t' => $s2[$i] = ($a1 * $s1[$i]) + ((1 - $a1) * ($s2[$i - 1])),
                        's3t' => $s3[$i] = ($a1 * $s2[$i]) + ((1 - $a1) * ($s3[$i - 1])),
                        'at' => $at[$i] = (3 * $s1[$i]) - (3 * $s2[$i]) + $s3[$i],
                        'bt' => $bt[$i] = ($a1 / (2 * (pow(1 - $a1, 2)))) * (((6 - (5 * $a1)) * $s1[$i]) - ((10 - (8 * $a1)) * $s2[$i]) + ((4 - (3 * $a1)) * $s3[$i])),
                        'ct' => $ct[$i] = ((pow($a1, 2)) / pow((1 - $a1), 2)) * ($s1[$i] - (2 * $s2[$i]) + $s3[$i]),
                        'forcasting' => $fct[$i] = $at[$i - 1] + ($bt[$i - 1] * $m) + (0.5 * ($ct[$i - 1] * pow($m, 2))),
                        'error' => $err[$i] = $jml[$i] - $fct[$i],
                        'abs' => $abs_err[$i] = abs($err[$i]),
                        'PE' => $pe[$i] = $abs_err[$i] / $jml[$i] * 100,
                    ];
                }else{
                    $datas['a1'][] = [
                        'yt' => $jml[$i] = (float)$datas['a1'][count($datas['a1'])-1]['forcasting'],
                        's1t' => $s1[$i] = ($a1 * (float)$datas['a1'][count($datas['a1'])-1]['forcasting']) + ((1 - $a1) * ($s1[$i - 1])),
                        's2t' => $s2[$i] = ($a1 * $s1[$i]) + ((1 - $a1) * ($s2[$i - 1])),
                        's3t' => $s3[$i] = ($a1 * $s2[$i]) + ((1 - $a1) * ($s3[$i - 1])),
                        'at' => $at[$i] = (3 * $s1[$i]) - (3 * $s2[$i]) + $s3[$i],
                        'bt' => $bt[$i] = ($a1 / (2 * (pow(1 - $a1, 2)))) * (((6 - (5 * $a1)) * $s1[$i]) - ((10 - (8 * $a1)) * $s2[$i]) + ((4 - (3 * $a1)) * $s3[$i])),
                        'ct' => $ct[$i] = ((pow($a1, 2)) / pow((1 - $a1), 2)) * ($s1[$i] - (2 * $s2[$i]) + $s3[$i]),
                        'forcasting' => $fct[$i] = $at[$i - 1] + ($bt[$i - 1] * $m) + (0.5 * ($ct[$i - 1] * pow($m, 2))),
                        'error' => $err[$i] = $jml[$i] - $fct[$i],
                        'abs' => $abs_err[$i] = abs($err[$i]),
                        'PE' => $pe[$i] = $abs_err[$i] / $jml[$i] * 100,
                    ];
                }
            }
        }
        
        // dd($pe);
        
        // $a = [$datas['a1'][$jumlah_data - 1]['forcasting'], $datas['a2'][$jumlah_data - 1]['forcasting'], $datas['a3'][$jumlah_data - 1]['forcasting'],  $datas['a4'][$jumlah_data - 1]['forcasting'], $datas['a5'][$jumlah_data - 1]['forcasting'], $datas['a6'][$jumlah_data - 1]['forcasting'], $datas['a7'][$jumlah_data - 1]['forcasting'], $datas['a8'][$jumlah_data - 1]['forcasting'], $datas['a9'][$jumlah_data - 1]['forcasting']];
        // $mid = sort($a);
        // $datas['data'] = [
        //     'max' => 'Rp. '.number_format(round(max($a)), 0, ',', '.'),
        //     'mid' => 'Rp. '.number_format(round($a[4]), 0, ',', '.'),
        //     'min' => 'Rp. '.number_format(round(min($a)), 0, ',', '.'),
        // ];
    }
    
    public function test1(Request $request)
    {
        
        // dd($datas);
        
        if($request->ajax()){
            $datas = array();
            $this->forcasting($datas, $request->alpha, $request->jumlah);
            $data = $datas['a1'];
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('yt', function($data){
                    $yt = 'Rp. '.number_format($data['yt'], 0, ',', '.');
                    return $yt;
                })
                ->addColumn('s1t', function($data){
                    $s1t = number_format($data['s1t'],3);
                    return $s1t;
                })
                ->addColumn('s1t', function($data){
                    $s1t = number_format($data['s1t'],3);
                    return $s1t;
                })
                ->addColumn('s2t', function($data){
                    $s2t = number_format($data['s2t'],3);
                    return $s2t;
                })
                ->addColumn('s3t', function($data){
                    $s3t = number_format($data['s3t'],3);
                    return $s3t;
                })
                ->addColumn('at', function($data){
                    $at = number_format($data['at'],3);
                    return $at;
                })
                ->addColumn('bt', function($data){
                    $bt = number_format($data['bt'],3);
                    return $bt;
                })
                ->addColumn('ct', function($data){
                    $ct = number_format($data['ct'],3);
                    return $ct;
                })
                ->addColumn('forcasting', function($data){
                    $forcasting = 'Rp. '.number_format(round($data['forcasting']), 0, ',', '.');
                    return $forcasting;
                })
                ->addColumn('error', function($data){
                    $error = number_format($data['error'],3);
                    return $error;
                })
                ->addColumn('abs', function($data){
                    $abs = number_format($data['abs'],3);
                    return $abs;
                })
                ->addColumn('PE', function($data){
                    $pe = round($data['PE']).'%';
                    return $pe;
                })
                ->addColumn('pe', function($data){
                    $pe = round($data['PE']);
                    return $pe;
                })
                
                ->make(true);
        }
        
        return view('forcasting.index');
    }
    
    public function getdatas(Request $request){
        
        // dd($datas);
        
        if($request->ajax()){
            $datas = array();
            $this->forcasting($datas, $request->alpha, $request->jumlah);
            $data = $datas['a1'];
            
            // dd($data);
            $grap = [];
            // $n = 0;
            // dd(count($data));
            for($i = 0; $i < count($data); $i++){
                $no =  $i+2;
                $grap['bln'][] = [
                   $no,
                ];
            }
            
            foreach($data as $key => $val){
                $grap['isi'][] = [
                    round($val['forcasting']),    
                ];
            }
            
            
            
            return response()->json($grap);
        }
    }
    
    

}
