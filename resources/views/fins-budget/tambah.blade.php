@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
          
        <div class="modal fade" id="modal-import" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="upload_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Pilih File</span>
                                <div class="form-file">
                                    <input type="file" name="file" id="file" class="form-file-input form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary " id="smpp">Simpan</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        
            <div class="modal fade" id="modalb">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Data Donatur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{url('pengajuananggaran/import')}}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            @csrf
                            
                            <div class="input-group mb-3">
                                <span class="input-group-text">Pilih File</span>
                                <div class="form-file">
                                    <input type="file" name="file" id="file" class="form-file-input form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!--<div class="modal fade" id="modal-default2">-->
        <!--    <div class="modal-dialog modal-lg" role="document">-->
        <!--        <div class="modal-content">-->
                    
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">Input Pengajuan</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="panel panel-default">-->
        <!--                        <div class="panel-body">-->
        <!--                            <h3 style="margin-top: -10px">Informasi</h3>-->
        <!--                            <hr style="margin: 0px; margin-bottom: 10px">-->

        <!--                            <div class="basic-form">-->
        <!--                                <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3 col-form-label">User Input</label>-->
        <!--                                    <div class="col-sm-6">-->
        <!--                                        <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>-->
        <!--                                    </div>-->
        <!--                                </div>-->

        <!--                                <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3 col-form-label">Kantor</label>-->
        <!--                                    <div class="col-sm-6">-->
        <!--                                        <select required class="form-control " name="kantor_m" id="kantor_m">-->
        <!--                                            <option value="">- Pilih -</option>-->
        <!--                                            @foreach($kantor as $val)-->
        <!--                                            <option value="{{$val->id}}"  data-value="{{$val->unit}}">{{$val->unit}}</option>-->
        <!--                                            @endforeach-->
        <!--                                        </select>-->
        <!--                                    </div>-->
        <!--                                </div>-->

        <!--                                <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3">Tanggal</label>-->
        <!--                                    <div class="col-sm-6">-->
        <!--                                        <text>{{ date('Y-m-d H:i:s') }}</text>-->
        <!--                                        <input type="date" class="form-control" value="" id="tgl_now_m" name="tgl_now_m">-->
        <!--                                    </div>-->
        <!--                                </div>-->

        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->


        <!--                    <div class="panel panel-default">-->
        <!--                        <div class="panel-body">-->
        <!--                            <h3 style="margin-top: -10px">Detail Mutasi</h3>-->
        <!--                            <hr style="margin: 0px; margin-bottom: 10px">-->
        <!--                            <div class="basic-form">-->
        <!--                                <div class="row">-->
        <!--                                    <div class="col-lg-4 mb-3">-->
        <!--                                        <label for="">Pengirim : <span class="saldo_pengirim">Saldo Rp. 0</span> </label>-->
        <!--                                        <input type="hidden" id="saldopengirim" name="saldopengirim">-->
        <!--                                        <select class="js-example-basic-single-pengirim" name="pengirim_m" id="pengirim_m">-->
        <!--                                            <option value="">- Pilih -</option>-->
        <!--                                        </select>-->
        <!--                                    </div>-->
        <!--                                    <div class="col-lg-4 mb-3">-->
        <!--                                        <label for="">Penerima : <span class="saldo_penerima">Saldo Rp. 0</span> </label>-->
        <!--                                        <input type="hidden" id="saldopenerima" name="saldopenerima">-->
        <!--                                        <select class="js-example-basic-single-penerima" name="penerima_m" id="penerima_m">-->
        <!--                                            <option value="">- Pilih -</option>-->
        <!--                                        </select>-->
        <!--                                    </div>-->

        <!--                                    <div class="col-md-4 mb-3">-->
        <!--                                        <label for="">Nominal :</label>-->
        <!--                                        <input type="text" min="0.0" name="nominal_m" id="nominal_m" onkeyup="rupiah(this);" value="" class="form-control form-control-sm">-->
        <!--                                    </div>-->
                                            
        <!--                                </div>-->
        <!--                                <div class="row">-->
        <!--                                    <div class="col-md-4 mb-3">-->
        <!--                                        <label for="">Keterangan :</label>-->
        <!--                                        <input type="text" name="ket_m" id="ket_m" class="form-control form-control-sm">-->
        <!--                                    </div>-->

        <!--                                    <div class="col-md-1 mb-3">-->
        <!--                                        <label>&nbsp;</label>-->
        <!--                                        <a id="add_mutasi" class="btn btn-primary okkk"><i class="fa fa-plus"></i></a>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->

        <!--                    <div class="panel panel-default">-->
        <!--                        <div class="panel-body">-->
        <!--                            <table id="user_table_1" class="table table-bordered ">-->
        <!--                                <thead>-->
        <!--                                    <tr>-->
        <!--                                        <th>Pengirim</th>-->
        <!--                                        <th>Penerima</th>-->
        <!--                                        <th>Nominal</th>-->
        <!--                                        <th>Keterangan</th>-->
        <!--                                        <th>Kantor</th>-->
        <!--                                        <th>Aksi</th>-->
        <!--                                    </tr>-->
        <!--                                </thead>-->
        <!--                                <tbody id="tablex">-->

        <!--                                </tbody>-->
        <!--                                <tfoot id="footx">-->

        <!--                                </tfoot>-->
        <!--                            </table>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="modal-footer">-->
        <!--                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
        <!--                    <button type="submit" class="btn btn-primary blokkkk" id="smpnn" disabled>Simpan</button>-->
        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Input Anggaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <h3 style="margin-top: -10px">Informasi</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="col-lg-6">
                                        
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Tanggal :</label>
                                            <div class="col-sm-9">
                                                <text>{{ date('Y-m-d H:i:s') }}</text>
                                                <input type="date" class="form-control" value="" id="tgl_now" name="tgl_now">
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
                                            <label class="col-sm-3">Jabatan :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($jabat as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" id="saldo_now" name="saldo_now" value="{{ $saldo }}">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Nama Akun :</label>
                                            <div class="col-sm-9">
                                                <select class="js-example-basic-singley saldd " name="saldo_dana" id="saldo_dana" style="width:100%">
                                                    <option value="">- Pilih -</option>
                                                </select>
                                            </div>
                                        </div>
                                       
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

                                      
                                        
                                        
                                      
                                    </div>

                                    <div class="col-lg-6">
                                        
                                          <div class="mb-3 row">
                                            <label class="col-sm-3">Nominal :</label>
                                            <div class="col-sm-9">
                                                 <input type="text" min="0.0" name="nominal" id="nominal" onkeyup="rupiah(this);" value="" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        
                                          <div class="mb-3 row">
                                            <label class="col-sm-3">Jenis :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    <option value="anggaran">Anggaran</option>
                                                    <option value="tambahan">Tambahan</option>
                                                    <option value="relokasi">Relokasi</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Realisasi :</label>
                                            <div class="col-sm-9">
                                                 <input type="text" name="realisasi" id="realisasi"  value="" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Saldo Anggaran :</label>
                                            <div class="col-sm-9">
                                                 <input type="text" name="realisasi" id="realisasi"  value="" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        
                                        
                                       
                                        
                                        <div class="mb-3 row" >
                                            <label class="col-sm-3">Foto Bukti :</label>
                                            <div class="col-sm-9">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">
                                                </div>
                                            </div>
                                            <input type="hidden" id="nama_file" value="">
                                            <input type="hidden" id="base64" value="">
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Detail Pengajuan</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div  class="col-md-4">
                                                    <label for="">Keterangan :</label>
                                                    <input type="text" name="ket" id="ket" class="form-control">
                                                </div>
                                                
                                       
                                                 <div  class="col-md-4">
                                                    <label for="">Referensi :</label>
                                                    <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($jabat as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                                
                                                <div class="col-md-2">
                                                    <label for="">Nominal :</label>
                                                    <input type="text" min="0.0" name="nominal" id="nominal" onkeyup="rupiah(this);" value="" class="form-control form-control-sm">
                                                </div>

                                                
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Jenis :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($jabat as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <a id="add" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="table-responsive">
                                <table id="user_table_1" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>COA</th>
                                            <th>ID Buku</th>
                                            <th>Nama Akun</th>
                                            <th>Qty</th>
                                            <th>Nominal</th>
                                            <th>total nominal</th>
                                            <th>Keterangan</th>
                                            <th>Kantor</th>

                                        </tr>
                                    </thead>
                                    <tbody id="table">

                                    </tbody>
                                    <tfoot id="foot">

                                    </tfoot>
                                </table>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary blokkk" id="smpn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
        
        
        <div class="modal fade" id="modals" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="boday">
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="footay">
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-default3" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-reject" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Alasan Pengajuan di Reject</h5>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary blokkk" id="smpnz">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         End Modal 



        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                     <form method="GET" action="{{url('pengajuan-anggaran/export')}}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                            <a href="{{url('downloadformat/export')}}" name="data1" value="data1" class= "btn btn-primary btn-xxs" style="float:right">Download Format</a>
                            <a class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right ;margin-left:3">Import</a>
                            <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Tambah</button>
                            <button type="submit" class="btn btn-primary btn-xxs" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>
                           
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
                                        <option value="2">Tahun</option>
                                    </select>
                                </div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label >Unit :</label>
                                        <select class="form-control cekk" name="kntr" id="kntr">
                                            <option value="">- Pilih -</option>
                                            @foreach($kantor as $val)
                                            <option value="{{$val->id}}">{{$val->unit}}</option>
                                            @endforeach
                                        </select>
                                </div>



                               

                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Dari :</label>
                                    <input type="date" class="form-control cekd" id="dari" name="dari">
                                </div>

                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Sampai :</label>
                                    <input type="date" class="form-control cekt" id="sampai" name="sampai">
                                </div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Status</label>
                                    <select class="form-control default-select wide ceks" name="stts" id="stts">
                                        <option value="">All</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Pending</option>
                                        <option value="0">Rejected</option>
                                        <option value="2">Tahun</option>
                                    </select>
                                </div>

                                
                                  <div class="pull-right" style="display: none" id="one">
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>

                            <a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>
                                </div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>

                            <button type="submit" class="btn btn-success btn-block" name="data1" value="data1" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>

                            <div class="pull-right"> 

                            <a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>
                            </div>
                              
                            </div>
                        </div>
                        <br>
                        
                        <div class="table-responsive">
                            <button id="aksis">buka</button>
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                                <table id="user_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>Nama Akun</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Anggaran</th>
                                             <th>Relokasi</th>
                                             <th>Tambahan</th>
                                             <th>Total</th>
                                             <th>Kantor</th>
                                             <th>Jabatan</th>
                                             <th>Referensi</th>
                                             <th>Program</th>
                                             <th>Keterangan</th>
                                             <th>User Input</th>
                                             <th>User Approver</th>
                                        </tr>
                                    </thead>
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