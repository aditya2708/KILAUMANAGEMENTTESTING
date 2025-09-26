
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
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>Detail Rencana Seluruh Unit</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;">Bulan {{date('m-Y', strtotime($bulans))}}</th></tr>
    <tr></tr>
</table>

<table border="1">

<thead>
        <tr>
            <td style="border: 1px solid black;">Unit</td>
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
    echo '<tbody>';
    
    $detaa = [];
    
    foreach ($data as $index => $act) {
        
        $rowspanCountKota = 0; // Menghitung rowspan untuk kota
        foreach ($act as $index2 => $bagian) {
            foreach ($bagian as $activity) {
                foreach (['proses', 'hasil'] as $type) {
                    if (isset($activity[$type])) {
                        $rowspanCountKota += count($activity[$type]); // Jumlahkan semua tugas di setiap bagian untuk kota
                    }
                }
            }
        }
        $kotaDisplayed = false;
        
        foreach ($act as $index2 => $bagian) {
            
            $rowspanCountBagian = 0; // Menghitung rowspan untuk bagian

            foreach ($bagian as $activity) {
                foreach (['proses', 'hasil'] as $type) {
                    if (isset($activity[$type])) {
                        $rowspanCountBagian += count($activity[$type]); // Jumlahkan tugas di setiap bagian
                    }
                }
            }
    
            $bagianDisplayed = false;
            
            foreach ($bagian as $activity) {
                foreach (['proses', 'hasil'] as $type) {
                    if (isset($activity[$type])) {
                        foreach ($activity[$type] as $key => $details) {
                            // $detaa[] = $details;
                          echo '<tr>';
                            // Kolom Kota
                            // echo '<td style="border: 1px solid black;">' . $index . '</td>';
                            if (!$kotaDisplayed) {
                                echo '<td rowspan="' . $rowspanCountKota . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $index . '</h5></td>';
                                $kotaDisplayed = true; // Tampilkan sekali untuk kota
                            }
                            
                            // Kolom Bagian
                            if (!$bagianDisplayed) {
                                echo '<td rowspan="' . $rowspanCountBagian . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $index2 . '</h5></td>';
                                $bagianDisplayed = true; // Tampilkan sekali untuk bagian
                            }
                            
                            $badgeClass = ($type == 'proses') ? '#AD88C6' : '#06D001';
                            echo '<td style="border: 1px solid black; background-color: '.$badgeClass.'">' . $type . '</td>';
                            if ($type == 'hasil') {
                                echo '<td style="border: 1px solid black;">' . $key . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['satuan'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['metode'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['target'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['bulan'] . '</td>';
                                echo '<td style="border: 1px solid black;">' . $details['selesai'] . '</td>';
                            } elseif ($type == 'proses') {
                                foreach($details as $keys => $hehe){
                                    echo '<td style="border: 1px solid black;">' . $keys . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $hehe['satuan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $hehe['metode'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $hehe['target'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $hehe['bulan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $hehe['selesai'] . '</td>';
                                }
                            }
                            echo '</tr>'; 
                        }
                    }
                }
            }
        }
        
    }
    
    echo '</tbody>';
?>
</table>