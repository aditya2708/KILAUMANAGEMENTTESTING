<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <!-- Bootstrap CSS -->
    <style>
        p{
            font-size: 15px;
        },
        
        footer {
                position: fixed; 
                bottom: -25px; 
                left: 0px; 
                right: 0px;
                height: 50px; 
                margin-left: 80px;

                /** Extra personal styles **/
                /*background-color: #03a9f4;*/
                /*color: black;*/
                /*font-size: 12px;*/
                /*text-align: center;*/
                line-height: 30px;
            }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>PayRoll</title>
  </head>
  <body>
        <footer>
                
            <p style="color:#1f5daa; font-size: 14px; margin-bottom: -10px">Kilau Lembaga Kemanusiaan</p>
            <p style="font-size: 13px">Jl.Sudirman No. 62 Telp. 0234-7121601 <a href="https://berbgibahagia.org">https://berbgibahagia.org</a> email: info@berbagibahagia.org
            </p>
        </footer>
    <div class="container">

       <img src="https://kilauindonesia.org/kilau/upload/Kilau%20Biru.png" height="130px" width="160px" style="margin-left:35%; padding-bottom: 20px"/>
       
        <table style="font-size: 15px">
            <tr>
                <td style="width: 100px">No</td>
                <td style="width: 20px">:</td>
                <td>{{date('m',strtotime($blnxs->created_at))}}/GAJI/KILAU/{{($blnxs->created_at)->isoFormat('Y')}}</td>
            </tr>
            <tr>
                <td >Lampiran</td>
                <td>:</td>
                <td>1 Lembar</td>
            </tr>
            <tr>
                <td >Perihal</td>
                <td>:</td>
                <td>Pembayaran Gaji Karyawan Kilau</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Bulan {{($blnxs->created_at)->isoFormat('MMMM')}} Tahun {{($blnxs->created_at)->isoFormat('Y')}}</td>
            </tr>
        </table>
        
            <br>    
            <p><b>Kepada Ykh.<br> Kepala Bank Syariah Indonesia <br> KCP Indramyau Sudirman <br>
             Di</br> Indramayu </b></p>
            <br>
            <p><i><b>Assalamu'alaikum Warahmatullahi Wabarakatuh <br> Ba'da Tahmid dan Sholawat</b></i></p>
            <br>
            <p style="text-align: justify">Sehubungan dengan jadwal pembayaran gaji karyawan kilau, maka kami mengajukan pendebetan rekening <b>910.000.910.7 atas nama Yayasan Kilau Indonesia</b> untuk kemudian dipindahkan kepada rekening-rekening karyawan kilau sebagaimana terlampir, pada tanggal {{(\Carbon\Carbon::now())->isoFormat('D MMMM Y')}} </p>
            <br>
            <p>Demikian Surat ini kami buat untukdilaksanakan. Atas kerjasamanya kami ucapkan <i>Jazakumullah Khairan Katsiran</i>.</p>
            <br>
            <p><i><b>Wassalamu'alaikum Warahmatullahi Wabarakatuh</b></i></p>
            <br>
            <p>Indramayu, {{(\Carbon\Carbon::now())->isoFormat('D MMMM Y')}}</p>
            
            <br>
            <br>
            <br>
            <p><b>{{ $malas->direktur }}</b><br>Direktur</p>
            <br>
            <br>
        
        <p class="text-center" style="font-size: 15px; margin-bottom: 0px">Lampiran : 02/GAJI/KILAU/{{($blnxs->created_at)->isoFormat('Y')}}</p>
        <h6 class="text-center" style="margin-bottom: 0px">DAFTAR GAJI KARYAWAN KILAU</h6>
        <h6 class="text-center" >Bulan {{($blnxs->created_at)->isoFormat('MMMM Y')}}</h6>
        <div class="row">
            
            
            <table class="table mt-5 table-bordered" style="font-size: 13px">
                <thead>
                  <tr>
                    <th scope="col">NO</th>
                    <th scope="col">NAMA</th>
                    <th scope="col">JABATAN</th>
                    <th scope="col">NO REK</th>
                    <th scope="col">CREDIT</th>
                  </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($data as $data)
                    @php $jabat = \App\Models\Jabatan::where('id',$data->id_jabatan)->first(); @endphp
                    <tr>
                        <td>{{ ++$no }}</td>
                        <td>{{ $data->nama }}</td>
                        <td>{{ $jabat->jabatan}}</td>
                        <td>{{ $data->no_rek }}</td>
                        <td>{{'Rp.'. number_format($data->thp, 0, ',', '.') }}</td>
                      </tr>
                    @endforeach 
                      <tr>
                          <td></td>
                          <td colspan="3" ><b>Total</b></td>
                          <td>{{'Rp.'. number_format($tot->total, 0, ',', '.') }}</td>
                      </tr>
                </tbody>
              </table>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>