<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    
    td {
      vertical-align: middle;
      text-align: center;    
    }
    
    td[rowspan] {
      vertical-align: middle;
    }
    
    td[rowspan] h5 {
        margin: 0;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
</style>

<table>
    <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 ><b>Detail Rencana Seluruh Karyawan Unit {{$kantornya}}</b></h1></th></tr>
    <tr><th colspan="10" style="text-align: center; text-transform: uppercase;">Bulan {{date('m-Y', strtotime($bulans))}}</th></tr>
    <tr></tr>
</table>

<table border="1">

<thead>
        <tr>
            <td style="border: 1px solid black;">Unit</td>
            <td style="border: 1px solid black;">Karyawan</td>
            <td style="border: 1px solid black;">Bagian</td>
            <td style="border: 1px solid black;">Jenis</td>
            <td style="border: 1px solid black;">Rencana</td>
            <td style="border: 1px solid black;">Satuan</td>
            <td style="border: 1px solid black;">Metode</td>
            <td style="border: 1px solid black;">Target</td>
            <td style="border: 1px solid black;">Mulai</td>
            <td style="border: 1px solid black;">Selesai</td>    
        </tr>
    </thead>
<?php
    $detaa = [];
    echo '<tbody>';
    
    foreach ($data as $index => $data1) {
        
        $rowspanCountKota = 0; // Menghitung rowspan untuk kota
        foreach ($data1 as $index2 => $data2) {
            foreach ($data2 as $index3 => $data3) {
                foreach ($data3 as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            $rowspanCountKota += count($activity[$type]); // Jumlahkan semua tugas di setiap bagian untuk kota
                        }
                    }
                }
            }
        }
        
        $kotaDisplayed = false;
        
        foreach ($data1 as $index2 => $data2) {
            
            $rowspanCountKrywn = 0; // Menghitung rowspan untuk bagian

            foreach ($data2 as $index3 => $data3) {
                foreach ($data3 as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            $rowspanCountKrywn += count($activity[$type]); // Jumlahkan semua tugas di setiap bagian untuk kota
                        }
                    }
                }
            }
        
            $krywnDisplayed = false;
            
            foreach ($data2 as $index3 => $data3) {
                
                $rowspanCountBagian = 0; // Menghitung rowspan untuk bagian

                foreach ($data3 as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            $rowspanCountBagian += count($activity[$type]); // Jumlahkan tugas di setiap bagian
                        }
                    }
                }
        
                $bagianDisplayed = false;
                
                foreach ($data3 as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            foreach ($activity[$type] as $key => $details) {
                                
                                echo '<tr>';
                                
                                if ($rowspanCountKota > 0 && !$kotaDisplayed) {
                                    echo '<td rowspan="' . $rowspanCountKota . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $index . '</h5></td>';
                                    $kotaDisplayed = true; // Tampilkan sekali untuk kota
                                }
                                
                                if ($rowspanCountKrywn > 0 && !$krywnDisplayed) {
                                    echo '<td rowspan="' . $rowspanCountKrywn . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $index2 . '</h5></td>';
                                    $krywnDisplayed = true; // Tampilkan sekali untuk bagian
                                }
                                
                                if ($rowspanCountBagian > 0 && !$bagianDisplayed) {
                                    echo '<td rowspan="' . $rowspanCountBagian . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $index3 . '</h5></td>';
                                    $bagianDisplayed = true; // Tampilkan sekali untuk bagian
                                }
                                
                                $badgeClass = ($type == 'proses') ? '#AD88C6' : '#06D001';
                                echo '<td style="border: 1px solid black; background-color: '.$badgeClass.'">' . $type . '</td>';
                                echo '<td style="border: 1px solid black;">' . $key . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['satuan'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['metode'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['target'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['start_date'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['end_date'] . '</td>';
                                
                                echo '</tr>'; 
                            }
                        }
                    }
                }
            }
        }
        
    }
    
    echo '</tbody>';
    // dd();
?>
</table>