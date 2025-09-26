<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Export BPJS</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <label>Bulan : {{$tgl}}</label>
            @if($unit == '')
                <label style="float: right">Unit : Semua</label>
            @else
                @php $get = \App\Models\Kantor::where('id',$unit)->first(); @endphp
                <label style="float: right">Unit : {{$get->unit}}</label>
            @endif
            <table class="table table-striped mt-5">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama</th>
                    <th scope="col">No. Rekening</th>
                    <th scope="col">BPJS ({{ $bpjs }})</th>
                  </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($data as $data)
                    <tr>
                        <th scope="row">{{ ++$no }}</th>
                        <td>{{ $data->nama }}</td>
                        <td>{{ $data->no_rek }}</td>
                        @if($bpjs == 'kesehatan')
                            <td>{{'Rp.'. number_format($data->kesehatan, 0, ',', '.') }}</td>
                        @else
                            <td>{{'Rp.'. number_format($data->ketenagakerjaan, 0, ',', '.') }}</td>
                        @endif
                      </tr>
                    @endforeach 
                      <tr>
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