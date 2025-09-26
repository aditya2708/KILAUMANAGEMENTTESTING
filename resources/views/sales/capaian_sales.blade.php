@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Sales</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Capaian Sales</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modalwar">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Rincian Prospek</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form>
                        <div class="modal-body" style="height: 400px; overflow-y: auto;">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="nyon">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Input</th>
                                                <th>Nama Donatur</th>
                                                <th>Program</th>
                                                <th>Jenis</th>
                                                <th>Tanggal Closing(Regis)</th>
                                                <th>laporan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="div1">

                                        </tbody>
                                        <!--<div id="div1">-->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modallap" >
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Laporan Prospek Oleh Petugas</h4>
                        <!--<button type="button" class="btn-close owh" data-bs-dismiss="modal" aria-label="Close">-->
                        <!--</button>-->
                        <button class="btn btn-sm btn-primary" data-bs-target="#modalwar" data-bs-toggle="modal" data-bs-dismiss="modal">Kembali</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-success">
                                    <div class="box-body">
                                        <div id="lapo">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <?php
        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
        if ($k != null) {
            $datdon = App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
        }
        $kota = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Capaian Sales</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('sales-export') }}">
                            <div class="basic-form mb-4">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label>Pilih</label>
                                    <select id="plhtgl" class="form-control cek1" name="plhtgl">
                                        <option value="0">Periode</option>
                                        <option value="1">Bulan</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-3" id="blnbln" hidden>
                                    <label>Bulan&Tahun :</label>
                                    <input type="text" class="form-control  default-input datepic cek4 cek1" name="bln" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                </div>
                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Dari</label>
                                    <input type="date" class="form-control cek1" id="darii" name="darii">
                                </div>
                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Ke</label>
                                    <input type="date" class="form-control cek1" id="sampaii" name="sampaii">
                                </div>
                                
                                @if(Auth::user()->level == 'kacab')
                                @if($k != null)
                                <div class="col-md-3 mb-3" id="kotas" style="display:block;">
                                    <label>Unit</label>
                                    <select class="form-control cek1" id="unit" name="unit">
                                        <option value="">- Pilih Kota -</option>
                                        @foreach ($datdon as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @endif
                                
                                @if(Auth::user()->level == 'admin')
                                <div class="col-md-3 mb-3">
                                    <label>Unit</label>
                                    <select class="form-control cek1" id="unit" name="unit">
                                        <option value="">- Pilih Kota -</option>
                                        @foreach ($kota as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Jabatan</label>
                                    <select class="form-control cek1" name="jabat" id="jabat" >
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatan as $j)
                                        <option value="{{$j->id}}">{{$j->jabatan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label style="margin-top: 40px">&nbsp;</label>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ekspor
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <div class="row">
                            <div class="table-responsive">
                                <table id="user_table1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Petugas</th>
                                            <th>Jabatan</th>
                                            <th>Open</th>
                                            <th>Closing</th>
                                            <th>Cancel</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection