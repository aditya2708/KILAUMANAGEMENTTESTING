@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Donatur</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Riwayat Donasi</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $data = \DB::select("SELECT distinct status from donatur");
        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if ($k != null) {

            $datdon =  App\Models\Transaksi::where('kota', Auth::user()->kota)->orWhere('kota', $k->unit)->select('kota')->distinct()->get();
        }
        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Donasi</h4>
                    </div>

                    <div>
                        <table class="table" style="margin-left: 25px">
                            @php $don = App\Models\Donatur::where('id',Request::segment(2))->first(); @endphp
                            <tr>
                                <th>Nama</th>
                                <td>:</td>
                                <td>{{$don->nama}}</td>
                            </tr>
                            <tr>
                                <th>No Hp</th>
                                <td>:</td>
                                <td>0{{$don->no_hp}}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>:</td>
                                <td>{{$don->alamat}}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>:</td>
                                <td>Rp.{{number_format($jmlh, 0, ',', '.')}}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kolektor</th>
                                        <th>Sub Program</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection