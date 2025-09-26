<!DOCTYPE html>
<html>
<head>
    <title>Detail Rencana</title>
    <style>
        table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        display: table-header-group; /* Ensures the thead is treated as a table header */
    }

    tbody {
        display: table-row-group; /* Ensures tbody is treated as a table body */
    }

    tr {
        page-break-inside: avoid; /* Prevents row breaks inside the page */
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    /* Optional: Set table layout fixed to manage column sizes effectively */
    table {
        table-layout: fixed;
    }
    </style>
</head>
<body>
    <h1>Detail Rencana {{ $user }} Bulan {{ date('m-Y', strtotime($bulan)) }}</h1>

    <table autosize="1">
        <thead>
            <tr>
                <th>Bagian</th>
                <th>Jenis</th>
                <th>Rencana</th>
                <th>Satuan</th>
                <th>Metode</th>
                <th>Target</th>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
        </thead>
        
    <tbody>
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
                                        echo '<td rowspan="' . $rowspanCount . '"><h5>' . $bagian . '</h5></td>';
                                        $bagianDisplayed = true;
                                    }
                
                                    // Column Jenis
                                    $badgeClass = ($type == 'proses') ? 'bg-primary' : 'bg-success';
                                    echo '<td><span class="badge ' . $badgeClass . '">' . $type . '</span></td>';
                
                                    // Column Rencana
                                    echo '<td>' . $key . '</td>';
                
                                    // Column Satuan
                                    echo '<td>' . $details['satuan'] . '</td>';
                
                                    // Column Metode
                                    echo '<td>' . $details['metode'] . '</td>';
                
                                    // Column Target
                                    echo '<td>' . $details['target'] . '</td>';
                
                                    // Column Start Date
                                    echo '<td>' . $details['start_date'] . '</td>';
                
                                    // Column End Date
                                    echo '<td>' . $details['end_date'] . '</td>';
                
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
                                    echo '<td rowspan="' . $rowspanCount . '"><h5>' . $bagian . '</h5></td>';
                                    $bagianDisplayed = true;
                                }
            
                                // Column Jenis
                                $badgeClass = ($type == 'proses') ? 'bg-primary' : 'bg-success';
                                echo '<td><span class="badge ' . $badgeClass . '">' . $type . '</span></td>';
            
                                // Column Rencana
                                echo '<td>' . $key . '</td>';
            
                                // Displaying details based on type
                                if ($type == 'hasil') {
                                    echo '<td>' . $details['satuan'] . '</td>';
                                    echo '<td>' . $details['metode'] . '</td>';
                                    echo '<td>' . $details['target'] . '</td>';
                                    echo '<td>' . $details['bulan'] . '</td>';
                                    echo '<td>' . $details['selesai'] . '</td>';
                                } elseif ($type == 'proses') {
                                    echo '<td>' . $details[0]['satuan'] . '</td>';
                                    echo '<td>' . $details[0]['metode'] . '</td>';
                                    echo '<td>' . $details[0]['target'] . '</td>';
                                    echo '<td>' . $details[0]['bulan'] . '</td>';
                                    echo '<td>' . $details[0]['selesai'] . '</td>';
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
    </tbody>
    </table>
</body>
</html>