@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <!-- modal -->
            <div class="modal fade" id="exampleModal">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Target Bulan Ini</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" action="{{url('target')}}">
                            @csrf
                            <div class="modal-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Masukan Target Bulan Ini</label>
                                            <input type="text" name="target" class="form-control" aria-describedby="name" placeholder="Contoh: 100.000.000">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Kota</label>
                                            <select required id="bank" class="form-control default-select wide" name="kota">
                                                <option selected value="">- Target Capaian Untuk Cabang -</option>
                                                <option value="bandung">Bandung</option>
                                                <option value="sumedang">Sumedang</option>
                                                <option value="indramayu">Indramayu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modali">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Rincian potongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Kantor</label>
                                        <select id="kota" class="form-control default-select wide" onchange="kota()">
                                            <option value="" selected>Pilih Kantor</option>
                                            @foreach ($datacabang as $cabang)
                                            <option value="{{$cabang->id}}">{{$cabang->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select id="bulan" class="form-control default-select wide" onchange="kota()">
                                            <option value="" selected>Pilih Bulan</option>
                                            @foreach ($rincian as $rin)
                                            <option value="{{$rin->bulan}}">{{$rin->namebulan}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Belum dikunjungi</th>
                                            <th>Tutup 1x</th>
                                            <th>Potongan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="a">
                                    </tbody>
                                    <tfoot id="total">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalbelum">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Rincian yang Belum di Assignment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Kantor</label>
                                        <select id="kota2" class="form-control default-select wide" onchange="kota2()">
                                            <option value="" selected>Pilih Kantor</option>
                                            @foreach ($datacabang as $cabang)
                                            <option value="{{$cabang->id}}">{{$cabang->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select id="bulan2" class="form-control default-select wide" onchange="kota2()">
                                            <option value="" selected>Pilih Bulan</option>
                                            @foreach ($rincian as $rin)
                                            <option value="{{$rin->bulan}}">{{$rin->namebulan}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="table-responsive">
                                <table class="table table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Belum di Assignment</th>
                                            <th>Tutup 1x</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bel">
                                    </tbody>
                                    <tfoot id="tot">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
            
            <!--Modal 2-->
            <div class="modal fade" id="modaldonasi" >
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <!--<div class="row">-->
                            <div class="table-responsive">
                                <table class="table table-striped" id="onecan">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Donatur</th>
                                            <th>Total Donasi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="div">
                                        
                                    </tbody>  
                                    <tfoot id="divc">
                                        
                                    </tfoot>
                                </table>
                                
                                
                            <!--    <div class="col-lg-8">-->
                            <!--        <label for="" class="label-control">Nama Donatur</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-4">-->
                            <!--        <label for="" class="label-control">Total Donasi</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div id="div">-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="modalDonasiProgram" >
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <!--<div class="row">-->
                            <div class="table-responsive">
                                <table class="table table-striped" id="onecan">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Donatur</th>
                                            <th>Total Donasi</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                                
                            <!--    <div class="col-lg-8">-->
                            <!--        <label for="" class="label-control">Nama Donatur</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-4">-->
                            <!--        <label for="" class="label-control">Total Donasi</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div id="div">-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="modalwar">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document" >
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <label for="" class="label-control">Tanggal&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Donasi&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Tidak Donasi&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Tutup&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Tutup 2x&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Ditarik&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Kotak Hilang&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Transaksi Diatas Minimal&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Total Kunjungan&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Capaian Target&nbsp;&nbsp;</label>
                                </div>
                                <div class="col-lg-1">
                                    <label for="" class="label-control">Capaian Kunjungan&nbsp;&nbsp;</label>
                                </div>
                                <div id="div1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modaldon" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Rincian Donasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#modalwar" data-bs-toggle="modal">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <label for="" class="label-control"><b>Nama Donatur</b></label>
                                </div>
                                <div class="col-lg-4">
                                    <label for="" class="label-control"><b>Total Donasi</b></label>
                                </div>
                            </div>
                            <div id="boy">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!--End Modal-->
        <!--<form>-->
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Filter</h4>
                                        <div class="pull-right">
                                            <button id="vs" type="button" class="btn btn-xxs btn-primary light" data-value="no" >Show Kolom Versus</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <div class="row">
                                                <?php $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first(); ?>
                                                @if(Auth::user()->level == 'admin')
                                                <div class="col-md-4 mb-3">
                                                    <label>Berdasarkan</label>
                                                    <select required id="cu" class="form-control default-select wide" name="field">
                                                        <option value="nama">Tim Kolektor</option>
                                                        <option value="program">Program</option>
                                                        <option value="kota">Kantor</option>
                                                        <!--<option value="bayar">Berdasarkan Via Bayar</option>-->
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-4 mb-3">
                                                    <label>Unit</label>
                                                    <select class="form-control sss" id="val_kot" name="kotas">
                                                        <option value="">Pilih</option>
                                                        @foreach($kotas as $kot)
                                                        <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-4 mb-3" id="bayars" style="display:block;">
                                                    <label>Pembayaran</label>
                                                    <select class="form-control multi" id="bayar" name="bayar[]" multiple="multiple">
                                                        <!--<option value="">Pilih</option>-->
                                                        @foreach($pem as $p)
                                                        <option value="{{$p->pembayaran}}">{{$p->pembayaran}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-1 " id="kotas" style="display:block;">
                                                    </div>
                                                @elseif(Auth::user()->level == 'kacab')
                                                    @if($k == null)
                                                    <div class="col-md-4 mb-3">
                                                        <label>Berdasarkan</label>
                                                        <select required id="cu" class="form-control default-select wide" name="field">
                                                            <option value="nama">Tim Kolektor</option>
                                                            <option value="program">Program</option>
                                                            <option value="kota">Kantor</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3" id="bayars" style="display:block;">
                                                        <label>Pembayaran</label>
                                                        <select class="form-control multi" id="bayar" name="bayar[]" multiple="multiple">
                                                            <!--<option value="">Pilih</option>-->
                                                            @foreach($pem as $p)
                                                            <option value="{{$p->pembayaran}}">{{$p->pembayaran}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3" id="kotas" style="display:block;">
                                                    </div>
                                                    @else
                                                        <div class="col-md-4 mb-3">
                                                            <label>Berdasarkan</label>
                                                            <select required id="cu" class="form-control default-select wide" name="field">
                                                                <option value="nama">Tim Kolektor</option>
                                                                <option value="program">Program</option>
                                                                <option value="kota">Kantor</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="col-md-4 mb-3" id="kotas" style="display:block;">
                                                            <label>Unit</label>
                                                            <select class="form-control default-select wide sss" id="val_kot" name="kotas">
                                                                <option value="">Pilih</option>
                                                                @foreach($kotas as $kot)
                                                                <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3" id="bayars" style="display:block;">
                                                            <label>Pembayaran</label>
                                                            <select class="form-control multi" id="bayar" name="bayar[]" multiple="multiple">
                                                                <!--<option value="">Pilih</option>-->
                                                                @foreach($pem as $p)
                                                                <option value="{{$p->pembayaran}}">{{$p->pembayaran}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                <input type="hidden" id="val">
                                                <input type="hidden" id="val1">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Status Approval</label>
                                                    <select id="approve" class="form-control default-select wide" name="approve">
                                                        <option value="">- Pilih Status Approval -</option>
                                                        <option value="2">Pending</option>
                                                        <option value="1" selected>Approved</option>
                                                        <option value="0">Rejected</option>
                                                        <option value="">All</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Pilih</label>
                                                    <select id="plhtgl" class="form-control default-select wide" name="plhtgl">
                                                        <option value="0">Periode</option>
                                                        <option value="1">Bulan</option>
                                                        <option value="2">Tahun</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3" id="blnbln" hidden>
                                                    <label>Bulan</label>
                                                    <input type="text" class="form-control dato cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                </div>
                                                <div class="col-md-12 mb-3" id="thnthn" hidden>
                                                    <label>Tahun</label>
                                                    <input type="text" class="form-control year" name="thnn" id="thnn" autocomplete="off" placeholder="contoh {{date('Y') }}">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tgldari">
                                                    <label>Dari</label>
                                                    <input type="date" class="form-control" id="darii" name="dari">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tglke">
                                                    <label>Sampai</label>
                                                    <input type="date" class="form-control" id="sampaii" name="sampai">
                                                </div>
                                            </div>
                                            <div id="versus_bln" style="display:none">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3" id="blnbln1" hidden>
                                                        <label>Bulan</label>
                                                        <input type="text" class="form-control dato cek4" name="blns" id="blns1" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3" id="thnthn1" hidden>
                                                        <label>Tahun</label>
                                                        <input type="text" class="form-control year" name="thnn2" id="thnn2" autocomplete="off" placeholder="contoh {{date('Y') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="tgldari1">
                                                        <label>Dari</label>
                                                        <input type="date" class="form-control" id="dari2" name="dari2">
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="tglke1">
                                                        <label>Sampai</label>
                                                        <input type="date" class="form-control" id="sampai2" name="sampai2">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary btn-sm col-md-12" type="button" id="filterr">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->level == 'admin')
                    <?php $thun = date('Y'); ?>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Grafik</h4>
                                        <div class="pull-right">
                                            <div class="row">

                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <select id="thn" style="display: none;" name="thn" >
                                                                @foreach($tahunn as $val)
                                                                <option value="{{$val->date}}" {{($val->date == $thun ? 'selected' : '' )}}>{{$val->date}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <select id="blnkan" style="display: block;"  name="blnkan">
                                                                <option value="0">Periode</option>
                                                                <option value="1">Tahun</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <select id="kanprog"  name="kanprog">
                                                                <option value="0">Kantor</option>
                                                                <option value="1">Program</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tabkanprog" id="tabkanprog" value="kantor">
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Pilih</label>
                                                <select id="plhcom" class="form-control" name="plhcom">
                                                    <option value="0">Single</option>
                                                    <option value="1">Compare</option>

                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <div id="kann" style="display: block">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label>Kantor 1</label>
                                                            <input type="hidden" id="grafkot" name="grafkot" value="">
                                                            <select id="graf" class="form-control" name="filgraf">
                                                                <option value="">Semua Kantor</option>
                                                                @foreach($kotas as $kot)
                                                                <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6" id="compare" style="display:none">
                                                            <label>Kantor 2</label>
                                                            <input type="hidden" id="grafkot1" name="grafkot1" value="">
                                                            <select id="graf1" class="form-control" name="filgraf">
                                                                <option value="">Semua Kantor</option>
                                                                @foreach($kotas as $kot)
                                                                <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="progg" style="display: none">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label>Program 1</label>
                                                            <input type="hidden" id="grafprog" name="grafprog" value="">
                                                            <select id="graf2" class="form-control" name="filgraf">
                                                                <option value="">Semua Program</option>
                                                                @foreach($progs as $progss)
                                                                <option value="{{$progss->program}}">{{$progss->program}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6" id="compare" style="display:none">
                                                            <label>Program 2</label>
                                                            <input type="hidden" id="grafprog1" name="grafprog1" value="">
                                                            <select id="graf3" class="form-control" name="filgraf">
                                                                <option value="">Semua Program</option>
                                                                @foreach($progs as $progss)
                                                                <option value="{{$progss->program}}">{{$progss->program}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="container1" style="height: 400px; min-width: 350px; display: block"></div>
                                                <div id="container2" style="height: 400px; min-width: 350px; display: none"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header justify-content-start align-items-center gap-2">
                        <h4 class="card-title">Report Transaksi</h4>
                        <div class="pull-right">
                            <button class="btn btn-sm btn-primary" id="exportt" type="button" style="display: none" target="_blank">Ekspor</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="toggle" style="display: none">
                                <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="height : 20px; width : 40px;">
                                        <label class="mt-1 ms-1" for="flexSwitchCheckChecked">Show/Hide Program dengan Omset 1 Rp. 0</label>
                                </div>
                            </div>
                            <!--<h4 class="d-flex align-items-end">Report Transaksi</h4>-->
                            <div id="gett" style="display:blok">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       <!--capaian kolekting-->
       <!--</form>-->
       
    </div>
</div>
@endsection