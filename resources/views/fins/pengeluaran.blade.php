@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Pengeluaran</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modal-default2">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Entry Mutasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Informasi Mutasi</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">

                                    <div class="basic-form">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">User Input</label>
                                            <div class="col-sm-6">
                                                <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Kantor</label>
                                            <div class="col-sm-6">
                                                <select required class="form-control " name="kantor_m" id="kantor_m">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($kantor as $val)
                                                    <option value="{{$val->id}}"  data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Tanggal</label>
                                            <div class="col-sm-6">
                                                <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                <input type="date" class="form-control" value="" id="tgl_now_m" name="tgl_now_m">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Detail Mutasi</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <label for="">Pengirim : <span class="saldo_pengirim">Saldo Rp. 0</span> </label>
                                                <input type="hidden" id="saldopengirim" name="saldopengirim">
                                                <select class="js-example-basic-single-pengirim" name="pengirim_m" id="pengirim_m">
                                                    <option value="">- Pilih -</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4 mb-3">
                                                <label for="">Penerima : <span class="saldo_penerima">Saldo Rp. 0</span> </label>
                                                <input type="hidden" id="saldopenerima" name="saldopenerima">
                                                <select class="js-example-basic-single-penerima" name="penerima_m" id="penerima_m">
                                                    <option value="">- Pilih -</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="">Nominal :</label>
                                                <input type="text" min="0.0" name="nominal_m" id="nominal_m" onkeyup="rupiah(this);" value="" class="form-control form-control-sm" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="">Keterangan :</label>
                                                <input type="text" name="ket_m" id="ket_m" class="form-control form-control-sm">
                                            </div>
                                            
                                            <div class="col-md-5 mb-3" >
                                                <label>Foto Bukti :</label>
                                                <!--<div class="form-file">-->
                                                <!--    <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="foto_mut" id="foto_mut">-->
                                                <!--</div>-->
                                                
                                                <div class="input-group mb-3">
                                                    <div class="form-file">
                                                        <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL1(this)" name="foto_mut" id="foto_mut">
                                                    </div>
        											<span class="input-group-text">Upload</span>
                                                </div>

                                                
                                                <input type="hidden" id="nama_file_mut" value="">
                                                <input type="hidden" id="base64_mut" value="">
                                            </div>

                                            <div class="col-md-1 mb-3">
                                                <label>&nbsp;</label>
                                                <a id="add_mutasi" class="btn btn-primary btn-sm okkk"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <table id="user_table_1" class="table table-bordered ">
                                        <thead>
                                            <tr>
                                                <th>Pengirim</th>
                                                <th>Penerima</th>
                                                <th>Nominal</th>
                                                <th>Keterangan</th>
                                                <th>Kantor</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablex">

                                        </tbody>
                                        <tfoot id="footx">

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary btn-sm blokkkk" id="smpnn" disabled>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" id="modal-default1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Entry Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <h3 style="margin-top: -10px">Informasi Pengeluaran</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="col-lg-6">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Via Bayar :</label>
                                            <div class="col-sm-6">
                                                <select class="form-control cekin" name="via_bayar" id="via_bayar">
                                                    <option value="">Pilih Via bayar</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="bank">Bank</option>
                                                    <option value="noncash">Non Cash</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div hidden id="bank_hide">
                                            <div class="mb-3 row">
                                                <label class="col-sm-3 col-form-label">Bank :</label>
                                                <div class="col-sm-9">
                                                    <select class="select30" name="bank" id="bank" style="width: 100%">
                                                        <option value="">- Pilih -</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div hidden id="noncash_hide">
                                            <div class="mb-3 row">
                                                <label class="col-sm-3">Non Cash :</label>
                                                <div class="col-sm-9">
                                                    <select class="js-example-basic-singlex" name="non_cash" id="non_cash" style="width: 100%">
                                                        <option value="">Pilih</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Saldo <span class="judul"></span> :</label>
                                            <div class="col-sm-9">
                                                <span class="saldo_pengeluaran">Rp. 0</span>
                                            </div>
                                            <input type="hidden" id="s_keluar" name="s_keluar">
                                        </div>
                                        
                                        <input type="hidden" id="saldo_now" name="saldo_now" value="{{ $saldo }}">

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Saldo Dana :</label>
                                            <div class="col-sm-9">
                                                <select class="js-example-basic-singley saldd" name="saldo_dana" id="saldo_dana" style="width:100%">
                                                    <option value="">- Pilih -</option>

                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <input type="hidden" id="saldo_dananya" name="saldo_dananya" >
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Saldo : </label>
                                            <div class="col-sm-9">
                                                <span class="saldo_dananya_saldo">Rp. 0</span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">User Input:</label>
                                            <div class="col-sm-9">
                                                <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Kantor :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="kantor" id="kantor">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($kantor as $val)
                                                    <option value="{{$val->id}}" {{ $val->id == Auth::User()->id_kantor ? "selected" : "" }} data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Tanggal :</label>
                                            <div class="col-sm-9">
                                                <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                <input type="date" class="form-control" value="" id="tgl_now" name="tgl_now">
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Department:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($jabat as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row" >
                                            <label class="col-sm-3">Foto Bukti :</label>
                                            <div class="col-sm-9">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">
                                                </div>
                                                <div id="bmbs" style="display: none">
                                                    <small style="margin-left: 10px; font-size: 10px">*gunakan watermark dari <a href="https://play.google.com/store/apps/details?id=net.sourceforge.opencamera&hl=en&pli=1" target="_blank" style="color: blue"><i>Open Camera</i></a></small>
                                                </div>
                                            </div>
                                            <input type="hidden" id="nama_file" value="">
                                            <input type="hidden" id="base64" value="">
                                        </div>
                                        
                                        <div id="bukti_kegiatan" style="display: none">
                                            <div class="mb-3 row" >
                                                <label class="col-sm-3">Foto Kegiatan :</label>
                                                <div class="col-sm-9">
                                                    <div class="form-file">
                                                        <input type="file" class="form-file-input form-control mb-2" onchange="encodeImageFileAsURL2(this)" name="foto2" id="foto2">
                                                    </div>
                                                    <small style="margin-left: 10px; font-size: 10px">*gunakan watermark dari <a href="https://play.google.com/store/apps/details?id=net.sourceforge.opencamera&hl=en&pli=1" target="_blank" style="color: blue"><i>Open Camera</i></a></small>
                                                </div>
                                                <input type="hidden" id="nama_file2" value="">
                                                <input type="hidden" id="base642" value="">
                                            </div>
                                        </div>
                                        
                                        <div id="berita_acara" style="display: none">
                                            <div class="mb-3 row" >
                                                <label class="col-sm-3">Foto Berita Acara :</label>
                                                <div class="col-sm-9">
                                                    <div class="form-file">
                                                        <input type="file" class="form-file-input form-control mb-2" onchange="encodeImageFileAsURL3(this)" name="foto3" id="foto3">
                                                    </div>
                                                    <!--<small style="margin-left: 10px; font-size: 10px">*gunakan watermark dari <a href="https://play.google.com/store/apps/details?id=net.sourceforge.opencamera&hl=en&pli=1" target="_blank" style="color: blue"><i>Open Camera</i></a></small>-->
                                                </div>
                                                <input type="hidden" id="nama_file3" value="">
                                                <input type="hidden" id="base643" value="">
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Detail Pengeluaran</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                
                                                <div class="col-md-5">
                                                    <label for="">Jenis Transaksi :</label>
                                                    <select class="js-example-basic-single wrap carianggaran" name="jenis_t" id="jenis_t">
                                                        <option value="" selected>Pilih Jenis Transaksi</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="">Nominal :</label>
                                                    <input type="text" min="0.0" name="nominal" id="nominal" onkeyup="rupiah(this);" value="" class="form-control form-control-sm" autocomplete="off">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="">Keterangan :</label>
                                                    <input type="text" name="ket" id="ket" class="form-control">
                                                </div>

                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <a id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for=""> Anggaran :</label>
                                                    <select class="js-example-basic-singleyu wrap" name="anggaran" id="anggaran">
                                                        <option value="">Pilih Anggaran</option>
                                                    </select>
                                                </div>
                                                <!--<div class="col-md-4">-->
                                                <!--    <br>-->
                                                <!--    <label for="">Pengajuan :</label>-->
                                                    <!--<input type="text" -->
                                                    <!--name="pengajuannya" id="pengajuannya"  class="form-control form-control-sm"  >-->
                                                <!--<text id="pengajuannya" name="pengajuannya"></text>-->
                                                <!--</div>-->

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <br>
                            
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <table id="user_table_1" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>COA</th>
                                                <th>Jenis Transaksi</th>
                                                <th>Qty</th>
                                                <th>Nominal</th>
                                                <th>Total</th>
                                                <th>Keterangan</th>
                                                <th>Kantor</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table">
    
                                        </tbody>
                                        <tfoot id="foot">
    
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            <input type="hidden" name="total_akhir" id="total_akhir">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary btn-sm blokkk" id="smpn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @if(Auth::user()->name == 'SUDIROH')
        @endif
        
        <div class="modal fade" id="modals" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="boday">
                                
                            </div>
                            <div id="rorrr"></div>
                        </div>
                        
                            
                        
                        <div class="modal-footer">
                            <div id="footay">
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-reject" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Alasan Pengeluaran di Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="reject_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div id="rej"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
                            <button type="submit" class="btn btn-primary blokkk" id="smpnz">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <!--<form method="GET" action="{{url('pengeluaran/export')}}" >-->
                        <input type="hidden" id="advsrc" value="tutup">
                        <div class="card-header">
                            <h4 class="card-title"></h4>
                            <div class="pull-right">
                                
                                @if(Auth::user()->name == 'SUDIROH')
                                <button type="button" id="tambahin" class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default123">Entri Pengeluaranxx</button>
                                @endif
                                
                                <button type="button" id="tambah" class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Entri Pengeluaran</button>
                                <button type="button" id="mutasi" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default2" >Mutasi</button>
                                <div class="btn-group me-2">
                                 <button type="button" class="btn btn-success btn-xxs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Laporan" style="width:100%;" fdprocessedid="n4kin5">
                                    <i class="fa fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu" style="margin: 0px;">
                                    <li><button class="dropdown-item exp" type="submit" id="xls" value="xls" name="tombol" >.XLS</button></li>
                                    <li><button class="dropdown-item exp" type="submit" id="csv" value="csv" name="tombol" >.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label>Pilih Via</label>
                                        <select class="form-control default-select wide cek" name="via" id="via">
                                            <option value="">All</option>
                                            <option value="pengeluaran">Pengeluaran</option>
                                            <option value="penyaluran">Penyaluran</option>
                                            <option value="mutasi">Mutasi</option>
                                            <!--<option value="2">Tahun</option>-->
                                        </select>
                                    </div>
                                    
                                    <div class="col-lg-3 mb-3">
                                        <label >Unit :</label>
                                            <select class="form-control cekl" name="kntr" id="kntr">
                                                <option value="">- Pilih -</option>
                                                @foreach($kantor as $val)
                                                <option value="{{$val->id}}">{{$val->unit}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    
                                    
    
    
                                    <div class="col-lg-3 mb-3">
                                        
                                        <select class="mb-2 ctt" id="tt" name="tt" panelheight="auto" style="width:100px; border:0;">
                                            <option value="p" selected="selected">Periode</option>
                                            <option value="b">Bulan</option>
                                        </select>
                                        
                                        <div id="tgl_hide" style="display: block">
                                            <input type="text" name="daterange" class="form-control mt--1" id="daterange" autocomplete="off" placeholder="DD/MM/YYYY - DD/MM/YYYY">
                                        </div>
                                        
                                        <div id="bulan_hide" style="display: none">
                                            <input type="text" class="form-control month cekb mt--1 " id="bulan" name="bulan" autocomplete="off" placeholder="MM-YYYY">
                                        </div>
                                        
                                        <!--<label>Dari :</label>-->
                                    </div>
    
                                    <!--<div class="col-lg-3 mb-3" id="tglke">-->
                                    <!--    <label>Ke :</label>-->
                                    <!--    <input type="date" class="form-control cek1" id="sampai" name="sampai">-->
                                    <!--</div>-->
                                    
                                    <div class="col-lg-3 mb-3">
                                        <label>Status</label>
                                        <select class="form-control default-select wide ceks" name="stts" id="stts">
                                            <option value="">All</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Pending</option>
                                            <option value="0">Rejected</option>
                                            <!--<option value="2">Tahun</option>-->
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label>Pembayaran</label>
                                        <select class="form-control ceks" name="pembayaran[]" multiple="multiple" id="pembayaran">
                                            <option value="">All</option>
                                            <option value="cash">Cash</option>
                                            <option value="noncash">Non Cash</option>
                                            <option value="bank">Bank</option>
                                            <option value="mutasi">Mutasi</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 mb-3" style="display: none" id="one" >
                                    <label></label>
                                    <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                                    @if(Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level == 'admin')
                                        <button type="button" class="btn btn-success btn-sm"  id="acc_semua"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>
                                    @endif
                                    </div>
                                    @if(Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level == 'admin' || Auth::user()->level == 'kacab')
                                    <label>*double click data untuk melakukan aksi(approve/reject).</label>
                                    @endif
                                </div>
                            </div>
                            
                           
                            <div class="table-responsive">
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th class="cari">Jenis Transaksi</th>
                                                <th class="cari">Keterangan</th>
                                                <th class="cari">Qty</th>
                                                <th class="cari">Nominal</th>
                                                <th class="cari">User Input</th>
                                                <th class="cari">User Approve</th>
                                                <th class="cari">Referensi</th>
                                                <th class="cari">Program</th>
                                                <th class="cari">Kantor</th>
                                                <th hidden>created_at</th>
                                                <th>COA Debet</th>
                                                <th>COA Kredit</th>
                                                <th>Acc</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>Σ Total :</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th hidden></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>

    </div>
</div>
@endsection