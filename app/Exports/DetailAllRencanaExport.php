<?php

namespace App\Exports;

use Auth;
use App\Models\RencanaThn;
use App\Models\RencanaBln;
use App\Models\Rencana;
use App\Models\Kantor;
use Carbon\Carbon;
use DB;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;

class DetailAllRencanaExport implements FromView, WithStyles
{
    use Exportable;
     
    public function __construct($request, $bulans)
    {
        $this->request = $request ;
        $this->bulans = $bulans ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $bulans = $this->bulans;
        
        return view('perencanaan.exportall',[
            'data' => $request,
            'bulans' => $bulans,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);
            
    }
    public function styles(Worksheet $sheet)
    {
        // Apply vertical and horizontal alignment for the Bagian column
        $sheet->getStyle('A1:A1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B1:B1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C1:C1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E1:E1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F1:F1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('G6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('H6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I6')->getAlignment()->setHorizontal('center');
        // $sheet->getStyle('D1:D1000')->getAlignment()->setWrapText(true);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical('center')->setWrapText(true);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(14);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
        $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(31);
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(10); // Column A with minimum width of 15
        // $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(20); // Column B with minimum width of 20
        // $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(25);
    
    }
}