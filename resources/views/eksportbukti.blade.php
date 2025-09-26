<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Bukti Setor Zakat</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .logo img {
            max-width: 100px;
            height: auto;
        }

        .text {
            text-align: right;
            font-size: 9px;
            margin-right: 12px;
            margin-top: -23%;
        }

        .separator {
            border-top: 1px solid #000;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
        }
        .info-value4 {
            display: inline-block;
            font-size: 12px;
            margin-top: 15px;
        }
        .info-label {
            font-size: 9px;
        }
        .info-label2 {
            font-weight: bold;
            font-size: 12px;
        }
        .info-label3 {
            font-size: 12px;
        }
         .info-value3 {
            flex: 1;
            font-size: 12px;
            white-space: nowrap;
            /*overflow: hidden;*/
            text-overflow: ellipsis;
        }
        .info-label1 {
          font-size: 9px;
        }
        .info-value {
            flex: 1;
            font-size: 9px;
            white-space: nowrap;
            /*overflow: hidden;*/
            text-overflow: ellipsis;
        }
       
         .info-label4 {
            width: 500px;
            display: inline-block;
            font-size: 12px;
            text-align: left;
            margin-right: 10px;
        }
        .content {
            padding: 20px;
            font-size: 13px;
        }
        .table {
            font-size: 13px;
        }
        p {
            font-size: 18px;
        }
        @page {
                size: A4 portrait;
            }
        
            /* Gaya untuk setiap tanda tangan */
            .signature-container {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }
        
            /* Gaya untuk setiap tanda tangan */
            .signature {
                width: 45%; /* Atur lebar tanda tangan agar sesuai dengan kertas A4 */
                margin: 0; /* Atur margin ke 0 agar sesuai dengan kertas A4 */
                border-top: 1px solid #000; /* Garis atas tanda tangan */
            }
            .small-text {
                font-size: 12px;
              
            }
            /* Teks label di bawah tanda tangan */
            .signature-label {
                text-align: center;
            }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
           <?php 
                $path = 'upload/'.$ttdz->logo;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data1 = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data1);
            ?>
            <img src="{{ $base64 }}" alt="Logo">
        </div>
        <div class="text">
              <span class="info-label2">{{ $ttdz->name }}</span>
            <div class="info-row">
                <span class="info-label">Alamat Kantor Pusat / Telepon: </span>
                <span class="info-value">{{ $ttdz->alamat }} / {{ $ttdz->wa }}</span>
            </div>
             <div class="info-row">
                <span class="info-label">E-mail: </span>
                <span class="info-value">{{ $ttdz->email }}</span>
            </div>
              <div class="info-row">
                <span class="info-label">SK MENKUMHAM RI No</span>
                <span class="info-value">{{ $ttdz->sk }}</span>
            </div>
              <div class="info-row">
                <span class="info-label">NPWP:</span>
                <span class="info-value">{{ $ttdz->npwp }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">SMS CENTER:</span>
                <span class="info-value">{{ $ttdz->wa }}</span>
            </div>
        </div>
    </div>


    <div class="separator"></div>

    <div class="content">
        <div class="info-row">
            <span class="info-label3">Nama:</span>
            <span class="info-value3">{{ $don->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label3">Alamat:</span>
            <span class="info-value3">{{ $don->alamat }}</span>
        </div>
    </div>

    <table class="table mt-5 table-bordered">
        <thead>
            <tr>
                <th scope="col">NO</th>
                <th scope="col">Nama Program</th>
                <th scope="col">Tanggal Transaksi</th>
                <th scope="col">Petugas</th>
                <th scope="col">Total Transaksi(IDR)</th>
            </tr>
        </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($data as $data)
                    <tr>
                        <td>{{ ++$no }}</td>
                        <td>{{ $data->subprogram }}</td>
                        <td>{{ $data->tanggal}}</td>
                        <td>{{ $data->kolektor}}</td>
                        <td>{{'Rp.'.number_format($data->jumlah, 0, ',', '.')  }}</td>
                      </tr>
                    @endforeach 
                      <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          
                          <td colspan="1" ><b>Total</b></td>
                          <td>{{'Rp.'. number_format($tot->total, 0, ',', '.') }}</td>
                      </tr>
                </tbody>
        <!-- Tambahkan baris data transaksi di sini -->
    </table>
    <div class="info-row">
            <span class="info-label1">*Terimakasih atas kepercayaannya berdonasi, semoga menjadikan Amal Jariyah dan pembersih Harta Sdr/i sekeluarga:</span>
        </div>
        
        <br/>
     <div class="content">
        <div class="info-row">
            <div class="info-label4">
                
            </div>
            
            <div class="info-value4">
                 <div class="col-md-6">
                    <p class="small-text">Yang Menerima,</p>
                    <?php 
                    $path = 'kilauindonesia.org/kilau/gambarTtd/'.$ttd->ttd;
                        // Check if the file exists and is readable
                        if (file_exists($path) && is_readable($path)) {
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data1 = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data1);
                            echo '<img src="' . $base64 . '" alt="Tanda Tangan 1" class="left-aligned-image">';
                        } else {
                            echo 'Gambar tidak ada: ';
                        }
                    ?>
                    <br/>
                    <p class="small-text">{{$ttd->nama }}</p>
                </div>
            </div>
        </div>
        
    </div>
            
       <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
