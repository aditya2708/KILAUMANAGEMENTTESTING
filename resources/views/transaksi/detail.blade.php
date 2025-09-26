@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                        <div class="card mt-3">
                            <div class="card-header"> Detail Transaksi {{$response->id_transaksi}} <span class="float-end">
                                    <!--<strong>Status:</strong> Pending-->
                                    <strong>{{ $response->tanggal }}</strong> </span>
                                    </div>
                            <div class="card-body">
                                <div class="row mb-5">
                                    <div class="mt-4 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <h6>From:</h6>
                                        <div> <strong>{{$response->donatur}}</strong> </div>
                                        <div>{{$response->alamat}}</div>
                                    </div>
                                    <div class="mt-4 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <h6>To:</h6>
                                        <div> <strong>{{ $response->kolektor }}</strong> </div>
                                        <div>{{ $response->jabatan }}</div>
                                        <!--<div>43-190 Mikolow, Poland</div>-->
                                        <!--<div>Email: marek@daniel.com</div>-->
                                        <!--<div>Phone: +48 123 456 789</div>-->
                                    </div>
           <!--                         <div class="mt-4 col-xl-6 col-lg-6 col-md-12 col-sm-12 d-flex justify-content-lg-end justify-content-md-center justify-content-xs-start">-->
           <!--                             <div class="row align-items-center">-->
											<!--<div class="col-sm-9"> -->
											<!--	<div class="brand-logo mb-3">-->
											<!--	    @php $iya = 'https://www.kilauindonesia.org/datakilau/gambarUpload/'.$response->bukti; @endphp-->
											<!--		<img class="logo-abbr me-2" width="50" src=" {{ $iya }}" alt="">-->
											<!--		<img class="logo-compact" width="110" src="page-error-404.html" alt="">-->
											<!--	</div>-->
           <!--                                     <span>Please send exact amount: <strong class="d-block">0.15050000 BTC</strong>-->
           <!--                                         <strong>1DonateWffyhwAjskoEwXt83pHZxhLTr8H</strong></span><br>-->
           <!--                                     <small class="text-muted">Current exchange rate 1BTC = $6590 USD</small>-->
           <!--                                 </div>-->
           <!--                                 <div class="col-sm-3 mt-3"> <img src="images/qr.png" alt="" class="img-fluid width110"> </div>-->
           <!--                             </div>-->
           <!--                         </div>-->
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="center">#</th>
                                                <th class="center">ID Transaksi</th>
                                                <th>Program</th>
                                                <th>Keterangan</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                                <th class="right">Nominal</th>
                                                <th class="right">Foto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; $jumlah = 0; @endphp
                                            @foreach($tabel as $t)
                                            @php $jumlah += $t->jumlah; @endphp
                                            @php $iya = 'https://www.kilauindonesia.org/datakilau/gambarUpload/'.$t->bukti; @endphp
                                            
                                            <tr>
                                                <td class="center">{{$no++}}</td>
                                                <td class="center">{{$t->id_transaksi}}</td>
                                                <td class="left strong">{{$t->subprogram}}</td>
                                                <td class="left">{{$t->keterangan == null ? '-' : $t->keterangan }}</td>
                                                <td class="left">
                                                    @if($t->status == 'Donasi')
                                                    <?php echo $a = '<label class="badge badge-sm badge-success">'.$t->status.'</label>'; ?>
                                                    @else
                                                    <?php echo $a = '<label class="badge badge-sm badge-primary">'.$t->status.'</label>'; ?>
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($t->pembayaran) }}</td>
                                                <td class="right">{{ 'Rp '. number_format($t->jumlah, 0, ',', '.') }}</td>
                                                <td class="right"><a href="{{$iya}}" class="btn btn-xs btn-info" target="_blank">Lihat Foto</a></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td><strong>Total</strong></td>
                                                <td></td>
                                                <td><strong>{{'Rp '. number_format($jumlah, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!--<div class="row">-->
                                <!--    <div class="col-lg-4 col-sm-5"> </div>-->
                                <!--    <div class="col-lg-4 col-sm-5 ms-auto">-->
                                <!--        <table class="table table-clear">-->
                                <!--            <tbody>-->
                                <!--                <tr>-->
                                <!--                    <td class="left"><strong>Total</strong></td>-->
                                <!--                    <td class="center"><strong>{{$jumlah}}</strong></td>-->
                                <!--                </tr>-->
                                <!--            </tbody>-->
                                <!--        </table>-->
                                <!--    </div>-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                </div>
    </div>        
</div>
@endsection