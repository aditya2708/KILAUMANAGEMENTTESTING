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

class DetailAllKarRencanaExport implements FromView, WithStyles
{
    use Exportable;
     
    public function __construct($request, $bulans, $kantornya)
    {
        $this->request = $request ;
        $this->bulans = $bulans ;
        $this->kantornya = $kantornya ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $bulans = $this->bulans;
        $kantornya = $this->kantornya;
        
        return view('perencanaan.exportallkar',[
            'kantornya' => $kantornya,
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
        $sheet->getStyle('D1:D1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F1:F1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('G1:G1000')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('H6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J6')->getAlignment()->setHorizontal('center');
        // $sheet->getStyle('D1:D1000')->getAlignment()->setWrapText(true);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical('center')->setWrapText(true);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(14);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(14);
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(20);
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(31);
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
        $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(10);
        $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(12);
        $sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(12);
    
    }
}