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

class DetailRencanaExport implements FromView, WithStyles
{
    use Exportable;
     
    public function __construct($request, $namas, $bulans, $ahha)
    {
        $this->request = $request ;
        $this->namas = $namas ;
        $this->bulans = $bulans ;
        $this->ahha = $ahha ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $namas = $this->namas;
        $bulans = $this->bulans;
        $ahha = $this->ahha;
        
        return view('perencanaan.export',[
            'data' => $request,
            'bulans' => $bulans,
            'namas' => $namas,
            'ahha' => $ahha,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);
            
    }
    public function styles(Worksheet $sheet)
    {
        // Apply vertical and horizontal alignment for the Bagian column
        $sheet->getStyle('A1:A1000')->getAlignment()->setVertical('center')->setHorizontal('center');

        // Additional styles can be applied here
    }
}