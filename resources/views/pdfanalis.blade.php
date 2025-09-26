<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>{{$a}}</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <label>{{$analis}}</label>
            <label style="float: right">{{$periode}}</label>
            <table class="table table-striped mt-5">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Analis</th>
                    <th scope="col">Nominal</th>
                    <th scope="col">Transaksi</th>
                    <th scope="col">Donatur</th>
                  </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                        $dd = 0;
                        $tt = 0;
                        $do = 0;
                    @endphp
                    @foreach ($data as $data)
                    <tr>
                        <th scope="row">{{ ++$no }}</th>
                        <td>{{ $data->awak }}</td>
                        <td>{{ 'Rp '. number_format($data->data, 0, ',', '.') }}</td>
                        <td>{{ $data->transaksi }}</td>
                        <td>{{ $data->don }}</td>
                    </tr>
                     
                    <?php $dd += $data->data ?>
                    <?php $tt += $data->transaksi ?>
                    <?php $do += $data->don ?>
                    @endforeach 
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{'Rp '. number_format($dd, 0, ',', '.')}}</td>
                        <td>{{$tt}}</td>
                        <td>{{$do}}</td>
                    </tr>
                </tfoot>
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