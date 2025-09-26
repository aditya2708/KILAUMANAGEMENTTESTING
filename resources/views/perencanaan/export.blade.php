
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
    <tr><th colspan="8" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="8" style="text-align: center; text-transform: uppercase;"><h1 ><b>Detail Rencana {{$namas}}</b></h1></th></tr>
    <tr><th colspan="8" style="text-align: center; text-transform: uppercase;">Periode {{date('m-Y', strtotime($bulans))}}</th></tr>
    <tr></tr>
</table>

<table border="1">
    
    <thead>
        <tr>
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
   @if($ahha == 'karyawan')
            <?php
                echo '<tbody>';

                foreach ($data as $bagian => $activities) {
                    $bagianDisplayed = false;
                    $rowspanCount = 0;
                
                    // Calculate rowspan count
                    foreach ($activities as $activity) {
                        foreach (['proses', 'hasil'] as $type) {
                            if (isset($activity[$type])) {
                                $rowspanCount += count($activity[$type]);
                            }
                        }
                    }
                
                    // Generate table rows
                    foreach ($activities as $activity) {
                        foreach (['proses', 'hasil'] as $type) {
                            if (isset($activity[$type])) {
                                foreach ($activity[$type] as $key => $details) {
                                    echo '<tr>';
                
                                    // Column Bagian
                                    if (!$bagianDisplayed) {
                                        echo '<td rowspan="' . $rowspanCount . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $bagian . '</h5></td>';
                                        $bagianDisplayed = true;
                                    }
                
                                    // Column Jenis
                                    $badgeClass = ($type == 'proses') ? '#AD88C6' : '#06D001';
                                    echo '<td style="border: 1px solid black; background-color: '.$badgeClass.'">' . $type . '</td>';
                
                                    // Column Rencana
                                    echo '<td style="border: 1px solid black;">' . $key . '</td>';
                
                                    // Column Satuan
                                    echo '<td style="border: 1px solid black;">' . $details['satuan'] . '</td>';
                
                                    // Column Metode
                                    echo '<td style="border: 1px solid black;">' . $details['metode'] . '</td>';
                
                                    // Column Target
                                    echo '<td style="border: 1px solid black;">' . $details['target'] . '</td>';
                
                                    // Column Start Date
                                    echo '<td style="border: 1px solid black;">' . $details['start_date'] . '</td>';
                
                                    // Column End Date
                                    echo '<td style="border: 1px solid black;">' . $details['end_date'] . '</td>';
                
                                    echo '</tr>';
                                }
                            }
                        }
                    }
                }
                
                // End of table body
                echo '</tbody>';
            ?>
            @else
            <?php
            echo '<tbody>';

            foreach ($data as $bagian => $activities) {
                $bagianDisplayed = false;
                $rowspanCount = 0;
            
                // Calculate rowspan count
                foreach ($activities as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            if (is_array($activity[$type]) && isset($activity[$type][0])) {
                                $rowspanCount += count($activity[$type]); // If array, add its length
                            } else {
                                $rowspanCount += count($activity[$type]); // If object, count its keys
                            }
                        }
                    }
                }
            
                // Debugging: Display rowspan count (equivalent to console.log in JavaScript)
                echo '<!-- Rowspan Count: ' . $rowspanCount . ' -->';
            
                // Generate table rows
                foreach ($activities as $activity) {
                    foreach (['proses', 'hasil'] as $type) {
                        if (isset($activity[$type])) {
                            foreach ($activity[$type] as $key => $details) {
                                echo '<tr>';
            
                                // Column Bagian
                                if (!$bagianDisplayed) {
                                    echo '<td rowspan="' . $rowspanCount . '" style="border: 1px solid black; text-align:center; vertical-align: middle;"><h5>' . $bagian . '</h5></td>';
                                    $bagianDisplayed = true;
                                }
            
                                // Column Jenis
                                $badgeClass = ($type == 'proses') ? '#AD88C6' : '#06D001';
                                echo '<td style="border: 1px solid black; background-color: '.$badgeClass.'">' . $type . '</td>';
            
                                // Column Rencana
                                echo '<td style="border: 1px solid black;">' . $key . '</td>';
            
                                // Displaying details based on type
                                if ($type == 'hasil') {
                                    echo '<td style="border: 1px solid black;">' . $details['satuan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details['metode'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details['target'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details['bulan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details['selesai'] . '</td>';
                                } elseif ($type == 'proses') {
                                    echo '<td style="border: 1px solid black;">' . $details[0]['satuan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details[0]['metode'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details[0]['target'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details[0]['bulan'] . '</td>';
                                    echo '<td style="border: 1px solid black;">' . $details[0]['selesai'] . '</td>';
                                }
            
                                echo '</tr>';
                            }
                        }
                    }
                }
            }
            
            // End of table body
            echo '</tbody>';
            ?>
            @endif
</table>