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
use App\Models\TestModel;


class LaporanBulananExport implements  FromView
{
public function __construct($tahun,$jenis,$via,$title)
    {
        $this->tahun = $tahun;
        $this->jenis = $jenis;
        $this->via = $via;
        $this->title = $title;
        
        return $this;
    }

    public function view(): View
    {   
    
        $tahun = $this->tahun;
        $jenis = $this->jenis;
        $via = $this->via;
        $title = $this->title;
       
       
        $currentYear = $tahun;
        $range = 1; // rentang tahun yang ingin ditampilkan
        $oldestYear = $currentYear - $range;      
        $d = [];
        for ($year = $currentYear; $year >= $oldestYear; $year--) {
            $d[] = $year;
        }
        
            $blnz= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $blnz[] = date("Y-m-t", $date) ;
            }
            $blnzz= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December");
                $blnzz[] = date("m", $date) ;
            }
        
        $filun= [];
                for ($i = 0; $i < 13; $i++) {
                $date = strtotime("-" . $i . " month December" .$currentYear);
                $filun[] = date("Y-m",$date) ;
            }
        
        $vianya = $via == 0 ? 'Realtime' : 'Closing' ;

        $dummy =  date('m') ;
        $blnkurang = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$dummy.'-'.$currentYear)));
        $blnkurangthnlalu = date("Y-m-t", strtotime("-1 month", strtotime('01-'.$dummy.'-'.$oldestYear)));

                $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
                    
       

        
    
        
        //rumusnya
        $tet = RumlapKeuangan::select('rumlap_keuangan.*')->where('id_jenlap',$jenis)->where('aktif','y ')->orderBy('urutan', 'ASC')->get();
      
        foreach($tet as $s){
             $a[] = [
                        'rumusnya' =>  preg_split("/[\+\-\s]/" ,$s->rumus),
                    ];
        }    

        for($i = 0; $i < count($a); $i++){
          $rms[] = $a[$i]['rumusnya'];
        }
        
         for($i = 0; $i < count($rms); $i++){
          $getrumus[] = $rms[$i];
        }
        
          
         //tandanya
         
$tandnya = RumlapKeuangan::selectRaw("nama,rumus,urutan")->where('id_jenlap', $jenis)->where('aktif', 'y')->orderBy('urutan', 'ASC')->get();

$tt = [];

foreach ($tandnya as $s) {
    $tt[] = [
        'tanda' => preg_split("/[\d. ]+/", $s->rumus),
    ];
}

        for($i = 0; $i < count($tt); $i++){
          $ttbesar[] = $tt[$i]['tanda'];
        }
      

        //get nama saja
         $teto = DB::table('rumlap_keuangan')
          ->selectRaw("nama,rumus")
            ->whereRaw("id_jenlap = $jenis AND aktif = 'y'")
            ->orderBy('urutan', 'ASC')
            ->get();
         
    
                
                
             if($via == '0'){
                //  $union = DB::table('transaksi')
                //         ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                //          ->whereRaw("DATE_FORMAT(tanggal,'%m') >= 01 AND DATE_FORMAT(tanggal,'%m') <= 12 AND approval = 1 AND via_input != 'mutasi' AND DATE_FORMAT(tanggal,'%Y') <= '$currentYear' ")
                //         ->unionAll(DB::table('pengeluaran')
                //                 ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                //                  ->whereRaw("DATE_FORMAT(tgl,'%m') >= 01 AND DATE_FORMAT(tgl,'%m') <= 12 AND acc = 1 AND DATE_FORMAT(tgl,'%Y') <= '$currentYear'"))
                //         ->unionAll(DB::table('jurnal')
                //                 ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                //               ->whereRaw("DATE_FORMAT(tanggal,'%m') >= 01 AND DATE_FORMAT(tanggal,'%m') <= 12  AND acc = 1 AND DATE_FORMAT(tanggal,'%Y') <= '$currentYear'"))
                //         ->unionAll(DB::table('transaksi')
                //                 ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                //                 ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                //                 ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%m') >= 01 AND DATE_FORMAT(transaksi.tanggal,'%m') <= 12 AND transaksi.approval = 1 AND DATE_FORMAT(transaksi.tanggal,'%Y') <= '$currentYear'")
                //                 );
                                
                                
                    //  $unionthnlalu = DB::table('transaksi')
                    //     ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
                    //      ->whereRaw("DATE_FORMAT(tanggal,'%m') >= 01 AND DATE_FORMAT(tanggal,'%m') <= 12 AND approval = 1 AND via_input != 'mutasi' AND DATE_FORMAT(tanggal,'%Y') <= '$oldestYear'")
                    //     ->unionAll(DB::table('pengeluaran')
                    //             ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
                    //              ->whereRaw("DATE_FORMAT(tgl,'%m') >= 01 AND DATE_FORMAT(tgl,'%m') <= 12 AND acc = 1 AND DATE_FORMAT(tgl,'%Y') <= '$oldestYear'"))
                    //     ->unionAll(DB::table('jurnal')
                    //             ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
                    //           ->whereRaw("DATE_FORMAT(tanggal,'%m') >= 01 AND DATE_FORMAT(tanggal,'%m') <= 12  AND acc = 1 AND DATE_FORMAT(tanggal,'%Y') <= '$oldestYear'"))
                    //     ->unionAll(DB::table('transaksi')
                    //             ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
                    //             ->leftjoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
                    //             ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%m') >= 01 AND DATE_FORMAT(transaksi.tanggal,'%m') <= 12 AND transaksi.approval = 1 AND DATE_FORMAT(transaksi.tanggal,'%Y') <= '$oldestYear'")
                    //             );            
              
  
  
  
  $cteTransaksi1 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[11]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi'AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    
    ;

$ctePengeluaran1 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[11]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal1 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[11]' AND acc = 1 ")
    ;


$cteProg1 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[11]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union1 = $cteTransaksi1->unionAll($ctePengeluaran1)->unionAll($cteJurnal1)->unionAll($cteProg1);





$cteTransaksi2 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[10]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash' ")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran2 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[10]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal2 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[10]' AND acc = 1 ")
    ;

$cteProg2 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[10]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union2 = $cteTransaksi2->unionAll($ctePengeluaran2)->unionAll($cteJurnal2)->unionAll($cteProg2);






$cteTransaksi3 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[9]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran3 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[9]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal3 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[9]'   AND acc = 1 ")
    ;

$cteProg3 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[9]'AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union3 = $cteTransaksi3->unionAll($ctePengeluaran3)->unionAll($cteJurnal3)->unionAll($cteProg3);






$cteTransaksi4 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[8]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran4 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[8]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal4 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[8]'  AND acc = 1 ")
    ;

$cteProg4 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[8]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union4 = $cteTransaksi4->unionAll($ctePengeluaran4)->unionAll($cteJurnal4)->unionAll($cteProg4);




$cteTransaksi5 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[7]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran5 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') >= '$filun[7]' AND DATE_FORMAT(tgl,'%Y-%m') <= '$filun[7]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal5 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[7]'  AND acc = 1 ")
    ;

$cteProg5 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[7]'AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union5 = $cteTransaksi5->unionAll($ctePengeluaran5)->unionAll($cteJurnal5)->unionAll($cteProg5);




$cteTransaksi6 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[6]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran6 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[6]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal6 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[6]' AND acc = 1 ")
    ;

$cteProg6 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[6]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union6 = $cteTransaksi6->unionAll($ctePengeluaran6)->unionAll($cteJurnal6)->unionAll($cteProg6);




$cteTransaksi7 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[5]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran7 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[5]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal7 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[5]' AND acc = 1 ")
    ;

$cteProg7 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[5]'AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union7 = $cteTransaksi7->unionAll($ctePengeluaran7)->unionAll($cteJurnal7)->unionAll($cteProg7);





$cteTransaksi8 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[4]'AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi'")
    // ->whereIn('coa_debet', $saldo)
    ;

$ctePengeluaran8 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[4]' AND acc = 1 ")
    // ->whereIn('coa_debet', $s1)
    // ->whereIn('coa_kredit', $s1)
    ;

$cteJurnal8 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[4]' AND acc = 1 ")
    ;

$cteProg8 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[4]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union8 = $cteTransaksi8->unionAll($ctePengeluaran8)->unionAll($cteJurnal8)->unionAll($cteProg8);




$cteTransaksi9 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[3]'AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    ;

$ctePengeluaran9 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[3]'  AND acc = 1 ")
  
    ;

$cteJurnal9 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[3]' AND acc = 1 ")
    ;

$cteProg9 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') >= '$filun[3]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union9 = $cteTransaksi9->unionAll($ctePengeluaran9)->unionAll($cteJurnal9)->unionAll($cteProg9);




$cteTransaksi10 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[2]'AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    ;

$ctePengeluaran10 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[2]'AND acc = 1 ")
    ;

$cteJurnal10 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[2]' AND acc = 1 ")
    ;

$cteProg10 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[2]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union10 = $cteTransaksi10->unionAll($ctePengeluaran10)->unionAll($cteJurnal10)->unionAll($cteProg10);





$cteTransaksi11 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[1]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    
    ;

$ctePengeluaran11 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[1]' AND acc = 1 ")
    ;

$cteJurnal11 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[1]' AND acc = 1 ")
    ;

$cteProg11 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[1]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union11 = $cteTransaksi11->unionAll($ctePengeluaran11)->unionAll($cteJurnal11)->unionAll($cteProg11);


$cteTransaksi12 = DB::table('transaksi')
    ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[0]' AND approval = 1 AND jumlah > 0 AND via_input != 'mutasi' AND  pembayaran != 'noncash'")
    ;

$ctePengeluaran12 = DB::table('pengeluaran')
    ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc, pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tgl,'%Y-%m') = '$filun[0]' AND acc = 1 ")
  
    ;

$cteJurnal12 = DB::table('jurnal')
    ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, SUM(nominal_debit), nominal_kredit, 0 as via_input, tanggal, acc, 0 AS pembayaran, 0 AS dp")
    ->whereRaw("DATE_FORMAT(tanggal,'%Y-%m') = '$filun[0]' AND acc = 1 ")
    ;

$cteProg12 = DB::table('transaksi')
    ->leftJoin('prog', 'prog.id_program', '=', 'transaksi.id_program')
      ->selectRaw("prog.coa2, prog.coa1, IF(transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi', (transaksi.jumlah * transaksi.dp / 100), 0) as jumlah, 0 as nominal,
      0 as nominal_debit, 0 as nominal_kredit, 
      transaksi.via_input, transaksi.tanggal, transaksi.approval AS acc, transaksi.pembayaran, 1 AS dp")
    ->whereRaw("DATE_FORMAT(transaksi.tanggal,'%Y-%m') = '$filun[0]' AND transaksi.approval = 1
     AND transaksi.pembayaran != 'noncash' AND transaksi.via_input = 'transaksi' AND jumlah > 0");


// // Gabungkan query union
$union12 = $cteTransaksi12->unionAll($ctePengeluaran12)->unionAll($cteJurnal12)->unionAll($cteProg12);
  
  
  
  
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
          

//         $saldo1 = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($currentYear) {
//                             $join->on('sub.coa_debet' ,'=', 't.coa')
//                                  ->whereRaw("DATE_FORMAT(sub.tanggal,'%Y') = '$currentYear' ")
//                                 ->where('acc', 1);
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
                        

//                 $saldo2 = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$filun) {
//                             $join->on('t2.coa' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(t2.bulan,'%Y-%m') >= '$filun[12]' AND DATE_FORMAT(t2.bulan,'%Y-%m') <= '$filun[0]'")
                                
//                                     ;
//                         })
//                         ->groupBy('root')
//                         ->get(); 
           
//                 $saldox = DB::table('b as t')
//                         ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
//                         ->withRecursiveExpression('b', $query)
//                         ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($currentYear) {
//                             $join->on('sub.coa_kredit' ,'=', 't.coa')
//                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%Y') = '$currentYear' ")
//                                 ->where('acc', 1)
                               
//                                 ;
//                         })
                            
//                         ->groupBy('root')
//                         ->get(); 
            
//             $tahundipilih = [];
//             $a = 0;
//             foreach($saldo1 as $i => $val){
//                 if($saldo2[$i]->coa == $val->coa ){
//                     $a = $saldo2[$i]->saldo_awal ;
//                 }else{
//                     $a = 0;
//                 }
                
//                 if($saldox[$i]->coa == $val->coa){
//                     $deb = $saldox[$i]->debit + $val->debit;
//                     $kre = $saldox[$i]->kredit + $val->kredit;
//                 }
//                         $tahundipilih[] = [
//                           'root' => $val->root,
//                             'coa' => $val->coa,
//                             'nama_coa' => $val->nama_coa,
//                             'neraca_s' => ($a + $deb) - $kre + $val->debit_s - $val->kredit_s,
//                         ];
                    
//           }
         
      
// foreach ($getrumus as $keys => $coas) {
//     foreach ($coas as $index => $coa) {
//         foreach ($tahundipilih as $item) {
//             if ($item['coa'] === $coa) {
//                 $arraynya[$keys][$index] = [
//                     // 'nama' => $item->nama,
//                     'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
//                     'coa' => $item['coa'],
//                 ];
//             }
//         }
//     }
// }
//  //ini tahun sekarang
//     $totals = [];
//       for ($m = 0; $m < count($teto); $m++) {
//             if (empty($arraynya)) {
//                 $totals[$m] = 0;
//             } else {
//                 for ($i = 0; $i < count($arraynya); $i++) {
//                     $total = 0; 
//                     foreach ($arraynya[$i] as $index => $coa) {
//                         $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
//                     }
//                     $totals[$i] = $total; 
//                 }
//             }
//       }    
//     $results = [];
//     for ($i = 0; $i < count($totals); $i++) {
//         $results[] = $totals[$i];
//     }


 $tahundipilih = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("t.bulan = '$blnkurang'")
              ->get();
        
    foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($tahundipilih as $item) {
                if ($item->coa === $coa) {
                    $arraynya[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
     //ini tahun sekarang
        $totals = [];
          for ($m = 0; $m < count($teto); $m++) {
                if (empty($arraynya)) {
                    $totals[$m] = 0;
                } else {
                    for ($i = 0; $i < count($arraynya); $i++) {
                        $total = 0; 
                        foreach ($arraynya[$i] as $index => $coa) {
                            $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                        }
                        $totals[$i] = $total; 
                    }
                }
          }    
        $results = [];
        for ($i = 0; $i < count($totals); $i++) {
            if($dummy == '01'){
                $results = 0;
                    }else{
                $results[] = $totals[$i];
            }
        }


                     $jan = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[11]' ")
                                ->where('acc', 1)
                               
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $jan2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz,$oldestYear) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $oldestYear)
                                 ->whereRaw("t2.bulan= '$blnz[12]'");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $janx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union1->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[11]' ")
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
            $januari = [];
            $b = 0;
            foreach($jan as $i => $val){
                if($jan2[$i]->coa == $val->coa){
                    $b = $jan2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                
                if($janx[$i]->coa == $val->coa){
                    $deb = $janx[$i]->debit + $val->debit;
                    $kre = $janx[$i]->kredit + $val->kredit;
                }
                        $januari[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            // 'neraca_s' => ($b + $val->debit) - $val->kredit + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }


$janu = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($januari as $item) {
                if (empty($item)) {
                    $janu[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $janu[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $jans = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($janu)) {
                $jans[$m] = 0;
            } else {
                for ($i = 0; $i < count($janu); $i++) {
                    $total = 0; 
                    foreach ($janu[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $jans[$i] = $total; 
                }
            }
      } 

    $janresults = [];
    for ($i = 0; $i < count($jans); $i++) {
        $janresults[] = $jans[$i];
    }

            $feb = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnz,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                 ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $feb2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz,$blnzz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                    ->whereRaw("t2.bulan= '$blnz[11]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
           
                  $febx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union2->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[10]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
            $febuari = [];
            $b = 0;
            foreach($feb as $i => $val){
                if($feb2[$i]->coa == $val->coa ){
                    $b = $feb2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($febx[$i]->coa == $val->coa){
                    $deb = $febx[$i]->debit + $val->debit;
                    $kre = $febx[$i]->kredit + $val->kredit;
                }
                
                        $febuari[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }

        $febu = [];
        foreach ($getrumus as $keys => $coas) {
                foreach ($coas as $index => $coa) {
                    foreach ($febuari as $item) {
                        if (empty($item)) {
                            $febu[$item] = 0;
                        } else {
                        if ($item['coa'] === $coa) {
                            $febu[$keys][$index] = [
                                // 'nama' => $item->nama,
                                'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                                'coa' => $item['coa'],
                            ];
                        }
                    }
                }
            }
        }
        
        
            $febs = [];
              for ($m = 0; $m < count($teto); $m++) {
                    if (empty($febu)) {
                        $febs[$m] = 0;
                    } else {
                        for ($i = 0; $i < count($febu); $i++) {
                            $total = 0; 
                            foreach ($febu[$i] as $index => $coa) {
                                $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                            }
                            $febs[$i] = $total; 
                        }
                    }
              } 
        
            $febresults = [];
            for ($i = 0; $i < count($febs); $i++) {
                $febresults[] = $febs[$i];
            }


             $mar = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                              ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $mar2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                  ->whereRaw("t2.bulan= '$blnz[10]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                $marx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union3->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[9]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
            $maret = [];
            $b = 0;
            foreach($mar as $i => $val){
                if($mar2[$i]->coa == $val->coa ){
                    $b = $mar2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($marx[$i]->coa == $val->coa){
                    $deb = $marx[$i]->debit + $val->debit;
                    $kre = $marx[$i]->kredit + $val->kredit;
                }
                        $maret[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
          
        $mare = [];
        foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($maret as $item) {
                if (empty($item)) {
                    $mare[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $mare[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $mars = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($mare)) {
                $mars[$m] = 0;
            } else {
                for ($i = 0; $i < count($mare); $i++) {
                    $total = 0; 
                    foreach ($mare[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $mars[$i] = $total; 
                }
            }
      } 

    $marresults = [];
    for ($i = 0; $i < count($mars); $i++) {
        $marresults[] = $mars[$i];
    }
          
          
          $apr = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                              ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $apr2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                 ->whereRaw("t2.bulan= '$blnz[9]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                  $aprx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union4->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[8]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
            $april = [];
            $b = 0;
            foreach($apr as $i => $val){
                if($apr2[$i]->coa == $val->coa ){
                    $b += $apr2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                 if($aprx[$i]->coa == $val->coa){
                    $deb = $aprx[$i]->debit + $val->debit;
                    $kre = $aprx[$i]->kredit + $val->kredit;
                }
                        $april[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
          
          
          $aprl = [];
        foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($april as $item) {
                if (empty($item)) {
                    $aprl[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $aprl[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $aprs = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($aprl)) {
                $aprs[$m] = 0;
            } else {
                for ($i = 0; $i < count($aprl); $i++) {
                    $total = 0; 
                    foreach ($aprl[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $aprs[$i] = $total; 
                }
            }
      } 

    $aprresults = [];
    for ($i = 0; $i < count($aprs); $i++) {
        $aprresults[] = $aprs[$i];
    }
          
          

                $mei = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $mei2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                            ->whereYear('t2.bulan', $currentYear)
                            ->whereRaw("t2.bulan= '$blnz[8]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                  $meix = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union5->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[7]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
            $meii = [];
            $b = 0;
            foreach($mei as $i => $val){
                if($mei2[$i]->coa == $val->coa){
                    $b = $mei2[$i]->saldo_awal;
                }else{
                    $b = 0;
                }
                if($meix[$i]->coa == $val->coa){
                    $deb = $meix[$i]->debit + $val->debit;
                    $kre = $meix[$i]->kredit + $val->kredit;
                }
                        $meizz[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,


                        ];
                    
          }

        $meii = [];
        foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($meizz as $item) {
                if (empty($item)) {
                    $meii[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $meii[$keys][$index] = [
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $meis = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($meii)) {
                $meis[$m] = 0;
            } else {
                for ($i = 0; $i < count($meii); $i++) {
                    $total = 0; 
                    foreach ($meii[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $meis[$i] = $total; 
                }
            }
      } 

    $meiresults = [];
    for ($i = 0; $i < count($meis); $i++) {
        $meiresults[] = $meis[$i];
    }


                $jun = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                  ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $jun2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                              ->whereYear('t2.bulan', $currentYear)
                              ->whereRaw("t2.bulan= '$blnz[7]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $junx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union6->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[6]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                
            $juni = [];
            $b = 0;
            foreach($jun as $i => $val){
                if($jun2[$i]->coa == $val->coa){
                    $b += $jun2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                if($junx[$i]->coa == $val->coa){
                    $deb = $junx[$i]->debit + $val->debit;
                    $kre = $junx[$i]->kredit + $val->kredit;
                }
                
                        $juni[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }

        $junii = [];
        foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($juni as $item) {
                if (empty($item)) {
                    $junii[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $junii[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $juns = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($junii)) {
                $juns[$m] = 0;
            } else {
                for ($i = 0; $i < count($junii); $i++) {
                    $total = 0; 
                    foreach ($junii[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $juns[$i] = $total; 
                }
            }
      } 

    $junresults = [];
    for ($i = 0; $i < count($juns); $i++) {
        $junresults[] = $juns[$i];
    }

                $jul = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                              ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $jul2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                              ->whereRaw("t2.bulan= '$blnz[6]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
                $julx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union7->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[5]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
           
            $juli = [];
            $b = 0;
            foreach($jul as $i => $val){
                if($jul2[$i]->coa == $val->coa ){
                    $b += $jul2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                 if($julx[$i]->coa == $val->coa){
                    $deb = $julx[$i]->debit + $val->debit;
                    $kre = $julx[$i]->kredit + $val->kredit;
                }
                        $juli[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }


            $julii = [];
            foreach ($getrumus as $keys => $coas) {
                    foreach ($coas as $index => $coa) {
                        foreach ($juli as $item) {
                            if (empty($item)) {
                                $julii[$item] = 0;
                            } else {
                            if ($item['coa'] === $coa) {
                                $julii[$keys][$index] = [
                                    'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                                    'coa' => $item['coa'],
                                ];
                            }
                        }
                    }
                }
            }
            
            
                $juls = [];
                  for ($m = 0; $m < count($teto); $m++) {
                        if (empty($julii)) {
                            $juls[$m] = 0;
                        } else {
                            for ($i = 0; $i < count($julii); $i++) {
                                $total = 0; 
                                foreach ($julii[$i] as $index => $coa) {
                                    $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                                }
                                $juls[$i] = $total; 
                            }
                        }
                  } 
            
                $julresults = [];
                for ($i = 0; $i < count($juls); $i++) {
                    $julresults[] = $juls[$i];
                }

 $ags = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                              ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $ags2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                    ->whereRaw("t2.bulan= '$blnz[5]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                  $agsx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union8->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[4]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get();         
           
            $agustus = [];
            $b = 0;
            foreach($ags as $i => $val){
                if($ags2[$i]->coa == $val->coa ){
                    $b += $ags2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                 if($agsx[$i]->coa == $val->coa){
                    $deb = $agsx[$i]->debit + $val->debit;
                    $kre = $agsx[$i]->kredit + $val->kredit;
                }
                        $agustus[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }

            $agst = [];
            foreach ($getrumus as $keys => $coas) {
                    foreach ($coas as $index => $coa) {
                        foreach ($agustus as $item) {
                            if (empty($item)) {
                                $agst[$item] = 0;
                            } else {
                            if ($item['coa'] === $coa) {
                                $agst[$keys][$index] = [
                                    'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                                    'coa' => $item['coa'],
                                ];
                            }
                        }
                    }
                }
            }
            
            
                $ags = [];
                  for ($m = 0; $m < count($teto); $m++) {
                        if (empty($agst)) {
                            $ags[$m] = 0;
                        } else {
                            for ($i = 0; $i < count($agst); $i++) {
                                $total = 0; 
                                foreach ($agst[$i] as $index => $coa) {
                                    $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                                }
                                $ags[$i] = $total; 
                            }
                        }
                  } 
            
                $agstresults = [];
                for ($i = 0; $i < count($ags); $i++) {
                    $agstresults[] = $ags[$i];
                }


             $sep = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                              ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $sep2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                  ->whereRaw("t2.bulan= '$blnz[4]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $sepx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union9->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[3]' ")
                                ->where('acc', 1)
                                ;
                        })
                            
                        ->groupBy('root')
                        ->get();   
                        
            $september = [];
            $b = 0;
            foreach($sep as $i => $val){
                if($sep2[$i]->coa == $val->coa ){
                    $b += $sep2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                 if($sepx[$i]->coa == $val->coa){
                    $deb = $sepx[$i]->debit + $val->debit;
                    $kre = $sepx[$i]->kredit + $val->kredit;
                }
                        $september[] = [
                            'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }


$sept = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($september as $item) {
                if (empty($item)) {
                    $sept[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $sept[$keys][$index] = [
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $sep = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($sept)) {
                $sep[$m] = 0;
            } else {
                for ($i = 0; $i < count($sept); $i++) {
                    $total = 0; 
                    foreach ($sept[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $sep[$i] = $total; 
                }
            }
      } 

    $septresults = [];
    for ($i = 0; $i < count($sep); $i++) {
        $septresults[] = $sep[$i];
    }
    
    $okt = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $okt2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                             ->whereRaw("t2.bulan= '$blnz[3]' ");
                        })
                            
                        ->groupBy('root')
                        ->get(); 
           
           
                $oktx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union10->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[2]' ")
                                ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get();   
                        
            $oktober = [];
            $b = 0;
            foreach($okt as $i => $val){
                if($okt2[$i]->coa == $val->coa ){
                    $b += $okt2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                 if($oktx[$i]->coa == $val->coa){
                    $deb = $oktx[$i]->debit + $val->debit;
                    $kre = $oktx[$i]->kredit + $val->kredit;
                }
                        $oktober[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
          
         $okto = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($oktober as $item) {
                if (empty($item)) {
                    $okto[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $okto[$keys][$index] = [
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $okt = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($okto)) {
                $okt[$m] = 0;
            } else {
                for ($i = 0; $i < count($okto); $i++) {
                    $total = 0; 
                    foreach ($okto[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $okt[$i] = $total; 
                }
            }
      } 

    $oktresults = [];
    for ($i = 0; $i < count($okt); $i++) {
        $oktresults[] = $okt[$i];
    } 
    
    
     $nov = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                  ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
                                // ->whereMonth('sub.tanggal', $blnz[1])
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $nov2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                             ->whereRaw("t2.bulan= '$blnz[2]' ");
                                //  ->whereMonth('t2.bulan', $blnz[1])
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                 $novx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union11->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[1]' ")
                                ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
                        
            $november = [];
            $b = 0;
            foreach($nov as $i => $val){
                if($nov2[$i]->coa == $val->coa ){
                    $b += $nov2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                if($novx[$i]->coa == $val->coa){
                    $deb = $novx[$i]->debit + $val->debit;
                    $kre = $novx[$i]->kredit + $val->kredit;
                }
                        $november[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                        ];
                    
          }
          
          $nove = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($november as $item) {
                if (empty($item)) {
                    $nove[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $nove[$keys][$index] = [
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $nov = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($nove)) {
                $nov[$m] = 0;
            } else {
                for ($i = 0; $i < count($nove); $i++) {
                    $total = 0; 
                    foreach ($nove[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $nov[$i] = $total; 
                }
            }
      } 

    $novresults = [];
    for ($i = 0; $i < count($nov); $i++) {
        $novresults[] = $nov[$i];
    }
          
                          $des = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                  ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
                                // ->whereMonth('sub.tanggal', $blnz[1])
                                ->where('acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $des2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0) as saldo_awal, t.parent")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin('saldo_awal as t2',function($join) use ($currentYear,$blnz) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                 ->whereYear('t2.bulan', $currentYear)
                                  ->whereRaw("t2.bulan= '$blnz[1]' ");
                                //  ->whereMonth('t2.bulan', $blnz[1])
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                  $desx = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.nominal) as debit, SUM(sub.jumlah) as kredit, 0 as kredit_s, 0 as debit_s, 0 as closed, 0 as closing, 0 as neraca_saldo")
                        ->withRecursiveExpression('b', $query)
                        ->leftjoin(DB::raw("({$union12->toSql()}) as sub"),function($join) use ($currentYear,$blnzz) {
                            $join->on('sub.coa_kredit' ,'=', 't.coa')
                                ->whereYear('sub.tanggal', $currentYear)
                                ->whereRaw("DATE_FORMAT(sub.tanggal,'%m') = '$blnzz[0]' ")
                                ->where('acc', 1)
                                ;
                        })
                        ->groupBy('root')
                        ->get(); 
            $desember = [];
            $b = 0;
            foreach($des as $i => $val){
                if($des2[$i]->coa == $val->coa ){
                    $b += $des2[$i]->saldo_awal;
                }else{
                    $b += 0;
                }
                if($desx[$i]->coa == $val->coa){
                    $deb = $desx[$i]->debit + $val->debit;
                    $kre = $desx[$i]->kredit + $val->kredit;
                }
                        $desember[] = [
                          'root' => $val->root,
                            'coa' => $val->coa,
                            'nama_coa' => $val->nama_coa,
                            'neraca_s' => ($b + $deb) - $kre + $val->debit_s - $val->kredit_s,
                            

                        ];
                    
          }
          
          $dese = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($desember as $item) {
                if (empty($item)) {
                    $dese[$item] = 0;
                } else {
                if ($item['coa'] === $coa) {
                    $dese[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
                        'coa' => $item['coa'],
                    ];
                }
            }
        }
    }
}


    $desem = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($dese)) {
                $desem[$m] = 0;
            } else {
                for ($i = 0; $i < count($dese); $i++) {
                    $total = 0; 
                    foreach ($dese[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
                    }
                    $desem[$i] = $total; 
                }
            }
      } 

    $desresults = [];
    for ($i = 0; $i < count($desem); $i++) {
        $desresults[] = $desem[$i];
    }
          

          }else if ($via == '1'){
                $tahundipilih = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]'")
              ->get();
              
                $tahunsebelumnya = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[1]'")
              ->get();
              
                $januari = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 01")
              ->get();
              
                $febuari = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 02")
              ->get();
              
              
                $maret = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 03")
              ->get();
              
                $april = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 04")
              ->get();
              
                $mei = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 05")
              ->get();
              
                $juni = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 06")
              ->get();
              
               $juli = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 07")
              ->get();
            
               $agustus = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 08")
              ->get();
              
              $september = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 09")
              ->get();
              
               $oktober = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 10")
              ->get();
              
              $november = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 11")
              ->get();
              
              $desember = DB::table('saldo_awal as t')
              ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
              ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = 12")
              ->get();
// // // ini tahun yang sebelumnya
// $arraythnlalu = [];
// foreach ($getrumus as $keys => $coas) {
//     foreach ($coas as $index => $coa) {
//         foreach ($tahunsebelumnya as $item) {
//              if (empty($tahunsebelumnya)) {
//                 $arraythnlalu[$item] = 0;
//             } else {
//             if ($item->coa === $coa) {
//                 $arraythnlalu[$keys][$index] = [
//                     // 'nama' => $item->nama,
//                       'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
//                     'coa' => $item->coa,
//                 ];
//             }
//             }
//         }
//     }
// }

//         $totalsthnlalu = [];
//           for ($m = 0; $m < count($teto); $m++) {
//                 if (empty($arraythnlalu)) {
//                     $totalsthnlalu[$m] = 0;
//                 } else {
//                      for ($i = 0; $i < count($arraythnlalu); $i++) {
//                         $total = 0; 
//                         foreach ($arraythnlalu[$i] as $index => $coa) {
//                             $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
//                         }
//                         $totalsthnlalu[$i] = $total; 
//                      }
//                 }
//             }
            
            
//         $resultsthnlalu = [];
//         for ($i = 0; $i < count($totalsthnlalu); $i++) {
//             $resultsthnlalu[] = $totalsthnlalu[$i];
//         }

            
 //ini tahun sekarang

foreach ($getrumus as $keys => $coas) {
    foreach ($coas as $index => $coa) {
        foreach ($tahundipilih as $item) {
            if ($item->coa === $coa) {
                $arraynya[$keys][$index] = [
                    // 'nama' => $item->nama,
                    'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                    'coa' => $item->coa,
                ];
            }
        }
    }
}
 //ini tahun sekarang
    $totals = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($arraynya)) {
                $totals[$m] = 0;
            } else {
                for ($i = 0; $i < count($arraynya); $i++) {
                    $total = 0; 
                    foreach ($arraynya[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $totals[$i] = $total; 
                }
            }
      }    
    $results = [];
    for ($i = 0; $i < count($totals); $i++) {
        $results[] = $totals[$i];
    }



 //ini januari
$janu = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($januari as $item) {
                if (empty($item)) {
                    $janu[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $janu[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $jans = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($janu)) {
                $jans[$m] = 0;
            } else {
                for ($i = 0; $i < count($janu); $i++) {
                    $total = 0; 
                    foreach ($janu[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $jans[$i] = $total; 
                }
            }
      } 

    $janresults = [];
    for ($i = 0; $i < count($jans); $i++) {
        $janresults[] = $jans[$i];
    }



 //ini febuari
$febu = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($febuari as $item) {
                if (empty($item)) {
                    $febu[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $febu[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $febs = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($febu)) {
                $febs[$m] = 0;
            } else {
                for ($i = 0; $i < count($febu); $i++) {
                    $total = 0; 
                    foreach ($febu[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $febs[$i] = $total; 
                }
            }
      } 

    $febresults = [];
    for ($i = 0; $i < count($febs); $i++) {
        $febresults[] = $febs[$i];
    }


 //ini maret
$mare = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($maret as $item) {
                if (empty($item)) {
                    $mare[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $mare[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $mars = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($mare)) {
                $mars[$m] = 0;
            } else {
                for ($i = 0; $i < count($mare); $i++) {
                    $total = 0; 
                    foreach ($mare[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $mars[$i] = $total; 
                }
            }
      } 

    $marresults = [];
    for ($i = 0; $i < count($mars); $i++) {
        $marresults[] = $mars[$i];
    }

$aprl = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($april as $item) {
                if (empty($item)) {
                    $aprl[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $aprl[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $aprs = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($aprl)) {
                $aprs[$m] = 0;
            } else {
                for ($i = 0; $i < count($aprl); $i++) {
                    $total = 0; 
                    foreach ($aprl[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $aprs[$i] = $total; 
                }
            }
      } 

    $aprresults = [];
    for ($i = 0; $i < count($aprs); $i++) {
        $aprresults[] = $aprs[$i];
    }


$meii = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($mei as $item) {
                if (empty($item)) {
                    $meii[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $meii[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $meis = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($meii)) {
                $meis[$m] = 0;
            } else {
                for ($i = 0; $i < count($meii); $i++) {
                    $total = 0; 
                    foreach ($meii[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $meis[$i] = $total; 
                }
            }
      } 

    $meiresults = [];
    for ($i = 0; $i < count($meis); $i++) {
        $meiresults[] = $meis[$i];
    }

$junii = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($juni as $item) {
                if (empty($item)) {
                    $junii[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $junii[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $juns = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($junii)) {
                $juns[$m] = 0;
            } else {
                for ($i = 0; $i < count($junii); $i++) {
                    $total = 0; 
                    foreach ($junii[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $juns[$i] = $total; 
                }
            }
      } 

    $junresults = [];
    for ($i = 0; $i < count($juns); $i++) {
        $junresults[] = $juns[$i];
    }

$julii = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($juli as $item) {
                if (empty($item)) {
                    $julii[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $julii[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $juls = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($julii)) {
                $juls[$m] = 0;
            } else {
                for ($i = 0; $i < count($julii); $i++) {
                    $total = 0; 
                    foreach ($julii[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $juls[$i] = $total; 
                }
            }
      } 

    $julresults = [];
    for ($i = 0; $i < count($juls); $i++) {
        $julresults[] = $juls[$i];
    }
        
$agst = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($agustus as $item) {
                if (empty($item)) {
                    $agst[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $agst[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $ags = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($agst)) {
                $ags[$m] = 0;
            } else {
                for ($i = 0; $i < count($agst); $i++) {
                    $total = 0; 
                    foreach ($agst[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $ags[$i] = $total; 
                }
            }
      } 

    $agstresults = [];
    for ($i = 0; $i < count($ags); $i++) {
        $agstresults[] = $ags[$i];
    }


$sept = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($september as $item) {
                if (empty($item)) {
                    $sept[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $sept[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $sep = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($sept)) {
                $sep[$m] = 0;
            } else {
                for ($i = 0; $i < count($sept); $i++) {
                    $total = 0; 
                    foreach ($sept[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $sep[$i] = $total; 
                }
            }
      } 

    $septresults = [];
    for ($i = 0; $i < count($sep); $i++) {
        $septresults[] = $sep[$i];
    }

$okto = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($oktober as $item) {
                if (empty($item)) {
                    $okto[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $okto[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $okt = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($okto)) {
                $okt[$m] = 0;
            } else {
                for ($i = 0; $i < count($okto); $i++) {
                    $total = 0; 
                    foreach ($okto[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $okt[$i] = $total; 
                }
            }
      } 

    $oktresults = [];
    for ($i = 0; $i < count($okt); $i++) {
        $oktresults[] = $okt[$i];
    }


$nove = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($november as $item) {
                if (empty($item)) {
                    $nove[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $nove[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $nov = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($nove)) {
                $nov[$m] = 0;
            } else {
                for ($i = 0; $i < count($nove); $i++) {
                    $total = 0; 
                    foreach ($nove[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $nov[$i] = $total; 
                }
            }
      } 

    $novresults = [];
    for ($i = 0; $i < count($nov); $i++) {
        $novresults[] = $nov[$i];
    }

$dese = [];
foreach ($getrumus as $keys => $coas) {
        foreach ($coas as $index => $coa) {
            foreach ($desember as $item) {
                if (empty($item)) {
                    $dese[$item] = 0;
                } else {
                if ($item->coa === $coa) {
                    $dese[$keys][$index] = [
                        // 'nama' => $item->nama,
                        'saldo_awal_int' => $item->saldo_awal_int == '' ? 0 :$item->saldo_awal_int ,
                        'coa' => $item->coa,
                    ];
                }
            }
        }
    }
}


    $desem = [];
      for ($m = 0; $m < count($teto); $m++) {
            if (empty($dese)) {
                $desem[$m] = 0;
            } else {
                for ($i = 0; $i < count($dese); $i++) {
                    $total = 0; 
                    foreach ($dese[$i] as $index => $coa) {
                        $total += ($ttbesar[$i] === '-') ? -$coa['saldo_awal_int'] : $coa['saldo_awal_int'];
                    }
                    $desem[$i] = $total; 
                }
            }
      } 

    $desresults = [];
    for ($i = 0; $i < count($desem); $i++) {
        $desresults[] = $desem[$i];
    }
          }    
                




                
            // $tetos = SaldoAw::select('saldo_awal.bulan,.saldo_awal.coa,CONVERT(INT, saldo_awal.saldo_awal) as saldo_awal') ->whereRaw("YEAR(bulan) = '$d[0]' AND DATE_FORMAT(bulan,'%m') = $mon ")->get();

//               $tetos = DB::table('saldo_awal as t')
//               ->selectRaw("CAST(t.saldo_awal AS SIGNED) as saldo_awal_int, t.coa")
//               ->whereRaw("YEAR(t.bulan) = '$d[0]' AND DATE_FORMAT(t.bulan, '%m') = $mon")
//               ->get();
              
            


//  if($via == '0'){
    
//       //ini tahun sekarang
//     foreach ($getrumus as $keys => $coas) {
//     foreach ($coas as $index => $coa) {
//         foreach ($inArray as $item) {
//             if ($item['coa'] === $coa) {
//                 $arraynya[$keys][$index] = [
//                     // 'nama' => $item->nama,
//                     'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
//                     'coa' => $item['coa'],
//                 ];
//             }
//         }
//     }
// }

//     $totals = [];
//       for ($m = 0; $m < count($teto); $m++) {
//             if (empty($arraynya)) {
//                 $totals[$m] = 0;
//             } else {
//                 for ($i = 0; $i < count($arraynya); $i++) {
//                     $total = 0; 
//                     foreach ($arraynya[$i] as $index => $coa) {
//                         $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
//                     }
//                     $totals[$i] = $total; 
//                 }
//             }
//       }    
//     $results = [];
//     for ($i = 0; $i < count($totals); $i++) {
//         $results[] = $totals[$i];
//     }
    
//               //ini tahun sebelumnya
//             foreach ($getrumus as $keys => $coas) {
//             foreach ($coas as $index => $coa) {
//                 foreach ($tahunlalu as $item) {
//                     if ($item['coa'] === $coa) {
//                         $arraythnlalu[$keys][$index] = [
//                             // 'nama' => $item->nama,
//                                 'neraca_s' => $item['neraca_s'] == '' ? 0 :$item['neraca_s'] ,
//                             'coa' => $item['coa'],
//                         ];
//                     }
//                 }
//             }
//         }
    
    
//         $totalsthnlalu = [];
//           for ($m = 0; $m < count($teto); $m++) {
//                 if (empty($arraythnlalu)) {
//                     $totalsthnlalu[$m] = 0;
//                 } else {
//                      for ($i = 0; $i < count($arraythnlalu); $i++) {
//                         $total = 0; 
//                         foreach ($arraythnlalu[$i] as $index => $coa) {
//                              $total += ($ttbesar[$i] === '-') ? -$coa['neraca_s'] : $coa['neraca_s'];
//                         }
//                         $totalsthnlalu[$i] = $total; 
//                     }
//                 }
//             }
//         $resultsthnlalu = [];
//         for ($i = 0; $i < count($totalsthnlalu); $i++) {
//             $resultsthnlalu[] = $totalsthnlalu[$i];
//         }    
// }


                    return view('ekspor.laporanbulananexport',[
                        'nama' => $teto,
                        'tahundipilih' => $results ,
                        // 'tahunsebelumnya' => $resultsthnlalu ,
                        'januari' => $janresults ,
                        'febuari' => $febresults ,
                        'maret' => $marresults ,
                        'april' => $aprresults ,
                        'mei' => $meiresults ,
                        'juni' => $junresults ,
                        'juli' => $julresults ,
                        'agustus' => $agstresults ,
                        'september' => $septresults ,
                        'oktober' => $oktresults ,
                        'november' => $novresults ,
                        'desember' => $desresults ,
                        'judul' => 'Laporan Bulanan' .$title. '  Tahun ' . $tahun . ' ' . $vianya,
                        'tahunini' => $tahun,
                        'tahunlalu' => $tahun -1  ,
                        'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
                    ]);

 
        }
    }
