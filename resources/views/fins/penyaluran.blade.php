@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Penyaluran</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modal-default1">
            <div class="modal-dialog" style="max-width: 80%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        
                        <h4 class="modal-title" id="exampleModalLabel"><span class="headerModal"></span>Penyaluran</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h3 style="margin-top: -10px">Informasi PM</h3>
                                            <hr style="margin: 0px; margin-bottom: 10px">

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Nama PM :</label>
                                                        <div class="col-md-7">
                                                            <select required class="pm" style="width: 100%;" name="nama_pm" id="nama_pm" type="text">
                                                                <option value="">- Pilih PM -</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="idpmTrigger" name="idpmTrigger">
                                            <input type="hidden" id="jenis_pm" name="jenis_pm">
                                            <input type="hidden" id="idpm" name="idpm">

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">HP :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control " name="hppm" id="hppm" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Email :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control " name="emailpm" id="emailpm" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Alamat :</label>
                                                        <div class="col-md-7">
                                                            <textarea class="form-control" name="alamat_pm" id="alamat_pm"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Koordinat :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control" name="koordinat_pm" id="koordinat_pm" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Asnaf :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="asnaf" id="asnaf" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">PJ :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control" name="pj" id="pj" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-5">Kantor :</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control " name="kantor_pm" id="kantor_pm" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h3 style="margin-top: -10px">Informasi Penyaluran</h3>
                                            <hr style="margin: 0px; margin-bottom: 10px">

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">Permohonan Via :</label>
                                                        <div class="col-md-8">
                                                            <select required class="form-control input-sm" style="width: 100%;" name="via_per" id="via_per">
                                                                <option value="">- Pilih -</option>
                                                                <option value="datang">Datang Langsung</option>
                                                                <option value="email">Email</option>
                                                                <option value="pos">Pos</option>
                                                                <option value="fax">Fax</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">User Insert :</label>
                                                        <div class="col-md-8">
                                                            <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">Pencairan Via :</label>
                                                        <div class="col-md-8">
                                                            <select required class="form-control cekin input-sm" style="width: 100%;" name="via_cair" id="via_cair">
                                                                <option value="">- Pilih -</option>
                                                                <option value="cash">Cash</option>
                                                                <option value="bank">Bank</option>
                                                                <option value="noncash">Non Cash</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">

                                                    <div class="row mb-3" hidden id="bank_hide">
                                                        <label class="col-sm-4">Bank :</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control input-sm select30" style="width: 100%;" name="bank" id="bank">
                                                                <option value="">- Pilih -</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3" hidden id="noncash_hide">
                                                        <label class="col-sm-4">Non Cash :</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control select311" style="width: 100%;" name="non_cash" id="non_cash">
                                                                <option value="">- Pilih -</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3" hidden id="saldoVia">
                                                        <label class="col-sm-4">Saldo <span id="viaKet"></span>:</label>
                                                        <div class="col-md-8">
                                                            <span class="saldo_pengeluaran">Rp. 0</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">Kantor :</label>
                                                        <div class="col-md-8">
                                                            <select required class="form-control input-sm" style="width: 100%;" name="kantor" id="kantor">
                                                                <option value="">- Pilih -</option>
                                                                @foreach($kantor as $val)
                                                                <option value="{{$val->id}}" data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">Tanggal Permohonan :</label>
                                                        <div class="col-md-8">
                                                            <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                            <input type="date" class="form-control" id="tgl_per" name="tgl_per">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">

                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <label class="col-sm-4">Tanggal Salur : </label>
                                                        <div class="col-md-8">
                                                            <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                            <input type="date" class="form-control" id="tgl_now" name="tgl_now">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 ">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h3 style="margin-top: -10px">Detail Penyaluran</h3>
                                            <hr style="margin: 0px; margin-bottom: 10px">
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="">Jenis Transaksi :</label>
                                                            <select class="js-example-basic-single" style="width: 100%;" name="jenis_t" id="jenis_t">
                                                                <option value="">Pilih Jenis Transaksi</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label for="">Qty :</label>
                                                            <input type="text" min="0.0" name="qty" id="qty" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label for="">Nominal :</label>
                                                            <input type="text" min="0.0" name="nominal" id="nominal" onkeyup="rupiah(this);" class="form-control form-control-sm">
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <label for="">total :</label>
                                                            <input type="text" min="0.0" name="total" id="total" onkeyup="rupiah(this);" class="form-control form-control-sm">
                                                        </div>

                                                        <div class="col-md-5 mb-3">
                                                            <label for="">Keterangan :</label>
                                                            <input type="text" name="ket" id="ket" class="form-control form-control-sm">
                                                        </div>

                                                        <div class="col-md-3 mb-3" id="kondisiEdit">
                                                            <label>&nbsp;</label>
                                                            <a id="add" class="btn btn-primary mt-3"><i class="fa fa-plus"></i></a>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table id="user_table_1" class="table table-bordered ">
                                                    <thead>
                                                        <tr>
                                                            <!--<th>COA</th>-->
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
                                    </div>
                                </div>
                            </div>
                    <input type="hidden" id="editTrigger" >

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="smpn" disabled>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
<!--dashboard-->
         <div class="row">
             
            <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                                        <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Quantity</p>
                                                <h4 class="mb-0" id="qtyData">0</h4>
                                                    <!-- <span class="badge badge-primary">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                            <!-- <i class="ti-user"></i> -->
                                           <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Penerima Manfaat</p>
                                                <h4 class="mb-0" id="qtyPM">0</h4>
                                                    <!-- <span class="badge badge-info">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Nominal</p>
                                                <h4 class="mb-0" id="totalNominal">0</h4>
                                                    <!-- <span class="badge badge-success">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right d-flex">
                            <div class="btn-group me-2">
                                 <button type="button" class=" btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Laporan" style="width:100%;" fdprocessedid="n4kin5">
                                    <i class="fa fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item exp" type="submit" value="xls" name="tombol">.XLS</button></li>
                                    <li><button class="dropdown-item exp" type="submit" value="csv" name="tombol">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                            <!--<a href="#" class="btn btn-dark rounded-lg " id="taki" style=" margin-right: 20px;">Adv Search</a>-->
                            <!--<button type="button" id="tambah" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default1" style="float:left; margin-left:5px"><i class="fa fa-plus"></i>&nbsp Tambah</button>-->
                            <button type="button" id="tambah" class="btn btn-primary btn-sm tambahPen" data-bs-toggle="modal" data-bs-target="#modal-default1"><span class="btn-icon-start text-primary"><i class="fa fa-plus color-primary"></i></span>Entry Penyaluran</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-3 mb-3">
                                <label>Priode</label>
                                <select class="form-control load s2" id="slct1">
                                    <option value="0">Tanggal</option>
                                    <option value="1">Tahun</option>
                                </select>
                            </div>
                            <div class="col-3 mb-3">
                                <label>Mohon</label>
                                <select class="form-control s2 cek" id="mohon">
                                    <option value="0">Mohon</option>
                                    <option value="1">Salur</option>
                                    <option value="2">Input</option>
                                </select>
                            </div>
                            <div class="col-3 mb-3" id="tgl">
                                <label>Tanggal</label>
                                <input class="form-control cek daterange" autocomplete="off" type="text" id="daterange" name="daterange" placeholder="{{date('d-m-Y')}} s/d {{ date('d-m-Y') }}">
                            </div>
                            <div class="col-3 mb-3" hidden id="blnthn">
                                <label>Bulan&Tahun</label>
                                <input class="form-control cek" type="text"  autocomplete="off"  id="bln" name="bln">
                            </div>
                            <div class="col-3 mb-3">
                                <label>Status</label>
                                <select class="form-control cek s2" id="slct2">
                                    <option value="">--All--</option>
                                    <option value="1">Approve</option>
                                    <option value="0">Reject</option>
                                    <option value="2">Pending</option>
                                </select>
                            </div>
                            <div class="col-3 mb-3">
                                <label>Kolom</label>
                                <select id="kolom" class="checkBoxSelect form-control select2-hidden-accessible" style="width: 100%" multiple="" name="kolom[]" tabindex="-1" aria-hidden="true">
                                </select>
                            </div>
                            <div class="mb-3 col-md-3 d-grid">
                                <label class="form-label">Advance</label>
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle"  data-bs-toggle="collapse" href="#multiCollapseExample2" role="button" aria-expanded="false" aria-controls="multiCollapseExample2">
                                Advance
                                </button>
                            </div>
                                <div class="bg-collaps rounded mt-4 mb-4 pb-4" style="width: 100%;">
                                <div class="collapse multi-collapse " id="multiCollapseExample2" >
                                    <div class="row">
                                        <div class=" mb-3 col-md-6">
                                            <label class="form-label mt-3">Nominal</label>
                                            <div class="d-flex gap-2">
                                                <input type="number" class="form-control dari_nominal" name="dari_nominal" id="dari_nominal" placeholder="Dari nominal"/> 
                                                <input type="number" class="form-control sampai_nominal" name="sampai_nominal" id="sampai_nominal" placeholder="Sampai nominal" /> 
                                            </div>
                                        </div>
                                        <!--<div class=" mt-3 col-md-3">-->
                                        <!--    <label class="form-label ">Tgl Salur</label>-->
                                        <!--    <input class="form-control cek daterange" autocomplete="off" type="text" id="tglSalur" name="tglSalur" placeholder="{{date('d-m-Y')}} s/d {{ date('d-m-Y') }}">-->
                                        <!--</div>-->
                                        <div class="col-md-3  mt-3">
                                            <label class="form-label ">Backdate</label>
                                            <select class="sel2 form-control backdate s2 cek" name="backdate" id="backdate">
                                                <option value="">- Pilih -</option>
                                                <option value="0">Ya</option>
                                                <option value="1">No</option>
                                            </select>
                                        </div>
                                        
                                        <div class="  col-md-3">
                                            <label class="form-label ">User Insert</label>
                                            <select class="sel2 form-control pjdanuserin user_insert s2 cek" name="user_insert" id="user_insert">
                                            </select>
                                        </div>
                                        
                                        <div class="  col-md-3">
                                            <label class="form-label ">Kantor</label>
                                            <select class="sel2 form-control advKantor s2 cek" name="advKantor[]" id="advKantor" multiple="multiple">
                                                <option value="">- Pilih -</option>
                                                 @foreach($kantor as $val)
                                                <option value="{{$val->id}}" data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="  col-md-3">
                                            <label class="form-label ">Cek Data</label>
                                            <select class="sel2 form-control cekData s2 cek" name="cekData" id="cekData">
                                                    <option value="">- Pilih -</option>
                                                    <option value="0">Double Transaksi</option>
                                                    <option value="1">Tanpa Transaksi</option>
                                            </select>
                                        </div>
                                        
                                        <div class=" col-md-3">
                                            <label class="form-label ">Asnaf</label>
                                            <select class="sel2 form-control asnaf s2 cek" name="asnaf[]" id="asnaf" multiple="multiple">
                                                    <option value="">- Pilih -</option>
                                                     @foreach($asnaf as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->id}}">{{$val->asnaf}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class=" mt-3 col-md-3">
                                            <label class="form-label ">Jenis PM</label>
                                            <select class="sel2 form-control jenisPM s2 cek" name="jenisPM" id="jenisPM">
                                                    <option value="">- Pilih -</option>
                                                    <option value="personal">Perorangan</option>
                                                    <option value="entitas">Lembaga</option>
                                            </select>
                                        </div>
                                         
                                        <div class="mt-3 col-md-3">
                                            <label class="form-label">Jenis Transaksi</label>
                                            <select class="sel2 form-control jenis_transaksi s2 cek" multiple="multiple" name="jenis_transaksi[]" id="jenis_transaksi">
                                                <option value="">- Pilih -</option>
                                                <option value="cash">Cash</option>
                                                <option value="bank">Bank</option>
                                                <option value="noncash">Non Cash</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mt-3 col-md-3">
                                            <label class="form-label">Program</label>
                                            <select class="sel2 form-control program s2 cek" name="prog" id="program">
                                            </select>
                                        </div>
                                        
                                        <div class="mt-3 col-md-3">
                                            <label class="form-label ">Campaign</label>
                                            <select class="sel2 form-control campaign s2 cek" name="campaign" id="campaign">
                                                     <option value="">- Pilih -</option>
                                            </select>
                                        </div>
                            
                                        <div class="mt-3 col-md-3">
                                            <label class="form-label">PJ</label>
                                            <select class="sel2 form-control  pjdanuserin PJ cek s2" name="PJ" id="PJ">
                                            </select>
                                        </div>
                                    </div>
                                       
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-end">
                                <div class="col-sm-3 ">
                                   <input type="search" class="form-control search-table" id="search-table" name="search" autocomplete="off" style="width:200px; height:30px; margin-top:5px; position:absolute; z-index:2;" placeholder="Search here....." fdprocessedid="u6ty67">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>ID Salur</th>
                                        <th>ID PM</th>
                                        <th>Penerima Manfaat</th>
                                        <th>Program</th>
                                        <th>Nominal</th>
                                        <th>Tgl Mohon</th>
                                        <th>Tgl Salur</th>
                                        <th>Kantor Salur</th>
                                        <th>Acc</th>
                                        <th>Jenis PM</th>

                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Total :</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
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