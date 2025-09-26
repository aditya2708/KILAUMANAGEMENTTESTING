<?php

namespace App\Exports;

use Auth;
use App\Models\COA;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class COAExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    // $tgls, $jabat, $kota, $stts, $opsi, $month, $year
    
    public function __construct($level, $parent , $aktif , $grup)
    {
        $this->level = $level;
        $this->parent = $parent;
        $this->aktif = $aktif;
        $this->grup = $grup;
        return $this;
    }
    
    public function collection()
    {
      
        $data = COA::all();
        
        foreach($data as $key => $val){
            $inArray[] = [
                "coa" => $val->coa,
                "nama_coa" => $val->nama_coa,
                "coa_parent" => $val->coa_parent,
                "level" => $val->level,
                "id_kantor" => $val->id_kantor,
                "id_jabatan" => $val->id_jabatan,
                "grup" => $val->grup,
                "parent" => $val->parent,
                "aktif" => $val->aktif,
            ];
        }
        
        $filRay = array_filter($inArray, function ($p) {
            $grup = explode(",",$p['grup']);
            $fillvl = $this->level == '' ? $p['level'] != 'haha' : $p['level'] == $this->level;
            $filcoa = $this->parent == '' ? $p['parent'] != 'haha' : $p['parent'] == $this->parent;
            $filakt = $this->aktif == '' ? $p['aktif'] != 'haha' : $p['aktif'] == $this->aktif;
            $filgrup = $this->grup == '' ? $p['grup'] != 'haha' : array_intersect($grup, $this->grup);
            return $fillvl && $filcoa && $filgrup && $filakt;
        });
        
        $inArray = collect($filRay);
        
        // $data = collect($inArray);
        // $arid = array_column($inArray, 'id');
        
        // foreach ($inArray as $key => $obj) {
        //     if (!in_array($obj['id_parent'], $arid)) {
        //         $inArray[$key]['id_parent'] = '';
        //     }
        // }
        
        // $data = COA::selectRaw('coa,nama_coa,coa_parent,level,id_jabatan,grup,parent,aktif')->get();
        // dd($inArray);
        return $inArray;
    }    
    
    public function headings(): array
    {
        return [
            'COA', 'Nama COA', 'COA Parent','Level', 'ID Kantor', 'ID Jabatan','Group','Parent', 'Aktif'
        ];
    }
 public function styles(Worksheet $sheet)
    {
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '074E67',
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getStyle('A1:I1')->applyFromArray($borderStyle);

        $sheet->getStyle('A2:I' . ($sheet->getHighestRow()))->applyFromArray($borderStyle);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
    }
}
