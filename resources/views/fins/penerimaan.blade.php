@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $datdon = App\Models\Kantor::all();
        $datstat = DB::select("SELECT distinct status from donatur");
        ?>

        <!-- Modal -->
        <div class="modal fade" id="modal-default1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Entry Penerimaan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Informasi Penerimaan</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">

                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3 row">
                                                    <label class="col-sm-3 col-form-label">Via Bayar</label>
                                                    <div class="col-sm-9">
                                                        <select required class="form-control default-select wide" name="via_bayar" id="via_bayar">
                                                            <option value="">Pilih Via Bayar</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="bank">Bank</option>
                                                            <option value="noncash">Non Cash</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div hidden id="bank_hide">
                                                    <div class="row mb-3">
                                                        <label class="col-sm-3">Bank</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control default-select wide" name="bank" id="bank">
                                                                <option value="">Pilih Bank</option>
                                                                @foreach($bank as $val)
                                                                <option value="{{$val->id_bank}}" data-value="{{$val->nama_bank}}">{{$val->nama_bank}} {{$val->no_rek}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div hidden id="noncash_hide">
                                                    <div class="row mb-3">
                                                        <label class="col-sm-3">Non Cash</label>
                                                        <div class="col-sm-9">
                                                            <select class="js-example-basic-singlex" style="width: 100%;" name="non_cash" id="non_cash">
                                                                <option value="">Pilih COA</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="row mb-3">
                                                    <label class="col-sm-3">User Input</label>
                                                    <div class="col-md-9">
                                                        <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-sm-3">Kantor</label>
                                                    <div class="col-sm-9">
                                                        <select required class="form-control input-sm" style="width: 100%;" name="kantor" id="kantor">
                                                            <option value="">Pilih Kantor</option>
                                                            @foreach($kantor as $val)
                                                            <option value="{{$val->id}}" data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-sm-3">Tanggal</label>
                                                    <div class="col-sm-9">
                                                        <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                        <input type="date" class="form-control" value="" id="tgl_now" name="tgl_now">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default mb-4">
                                <div class="panel-body">
                                    <h3 style="margin-top: -10px">Detail Penerimaan</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="">Jenis Transaksi :</label>
                                                    <select class="js-example-basic-single" style="width: 100%;" name="jenis_t" id="jenis_t">
                                                        <option value="">- Pilih -</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="">Nominal :</label>
                                                    <input type="text" min="0.0" name="nominal" id="nominal" onkeyup="rupiah(this);" value="" class="form-control form-control-sm">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="">Keterangan :</label>
                                                    <input type="text" name="ket" id="ket" class="form-control form-control-sm">
                                                </div>

                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <a id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table id="user_table_1" class="table table-bordered ">
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
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary blokkk btn-sm" id="smpn" disabled>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="modals" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Penerimaan</h5>
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
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"></h4>
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-xxs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ekspor
                                    </button>
                                    <ul class="dropdown-menu">
                                        
                                        <li><button class="dropdown-item exp" type="submit" value="xls" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item exp" type="submit" value="csv" name="tombol">.CSV</button></li>
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                                
                                <a href="javascript:void(0)" class="btn btn-success btn-xxs filtt"  >Adv Search</a>
                                <button type="button" class="btn btn-rounded btn-primary btn-xxs" id="tambah" data-bs-toggle="modal" data-bs-target="#modal-default1">Entri Penerimaan</button>
                                <!--<button class="btn btn-primary btn-xs" id="tambah" data-bs-toggle="modal" data-bs-target="#modal-default1">Entry Penerimaan</button>-->
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="advsrc" value="tutup">
                            <div class="basic-form mb-4">
                                <div class="row">
                                    
                                    <div class="col-lg-3 mb-3">
                                        
                                        <select class="mb-2 ctt cek" id="tt" name="tt" panelheight="auto" style="width:100px; border:0;">
                                            <option value="p" selected="selected">Periode</option>
                                            <option value="b">Bulan</option>
                                        </select>
                                        
                                        <div id="tgl_hide" style="display: block">
                                            <input type="text" name="daterange" class="form-control mt--1 cek daterange" id="daterange" autocomplete="off" placeholder="DD/MM/YYYY - DD/MM/YYYY">
                                        </div>
                                        
                                        <div id="bulan_hide" style="display: none">
                                            <input type="text" class="form-control month cekb mt--1 bulan cek" id="bulan" name="bulan" autocomplete="off" placeholder="MM-YYYY">
                                        </div>
                                        
                                        <!--<label>Dari :</label>-->
                                    </div>
                                    
                                    <div class="col-lg-3 mb-3">
                                        <label>Pilih Via</label>
                                        <select class="form-control default-select wide cek doo" name="via" id="via">
                                            <option value="">All</option>
                                            <option value="transaksi">Transaksi</option>
                                            <option value="penerimaan">Penerimaan</option>
                                            <option value="mutasi">Mutasi</option>
                                            <!--<option value="2">Tahun</option>-->
                                        </select>
                                    </div>
    
                                    <div class="col-lg-3 mb-3">
                                        <label>Pilih View</label>
                                        <select class="form-control cekview siuw" name="view" id="view">
                                            <option value="">Normal</option>
                                            <option value="dp">DP</option>
                                            <!--<option value="2">Tahun</option>-->
                                        </select>
                                    </div>
    
                                  <div class="col-lg-3 mb-3">
                                        <label>Pilih Status</label>
                                        <select class="form-control cekstat" name="stts" id="stts">
                                            <option value="">Semua</option>
                                            <option value="0">Rejected</option>
                                            <option value="1">Approved</option>
                                            <option selected value="2">Pending</option>
                                        </select>
                                    </div>
                                  <div class="col-lg-3 mb-3">
                                        <label>Pembayaran</label>
                                        <select class="form-control cek" name="pembayaran[]" multiple="multiple" id="pembayaran">
                                            <option value="">Semua</option>
                                            <option value="cash">Cash</option>
                                            <option value="bank">Bank</option>
                                            <option value="noncash">Non Cash</option>
                                            <option value="mutasi">Mutasi</option>
                                        </select>
                                    </div>
                                    
                                  
                                    
                                    <!--<div class="col-lg-3 mb-3" id="tgldari">-->
                                    <!--    <label>Dari :</label>-->
                                    <!--    <input type="date" class="form-control cek" id="dari" name="dari">-->
                                    <!--</div>-->
    
                                    <!--<div class="col-lg-3 mb-3" id="tglke">-->
                                    <!--    <label>Ke :</label>-->
                                    <!--    <input type="date" class="form-control cek1" id="sampai" name="sampai">-->
                                    <!--</div>-->
                                    
                                    <div class="col-lg-3 mb-3">
                                        <label>Unit</label>
                                        <select class="form-control default-select wide cek2" name="kantt" id="kantt">
                                            <option value="">All</option>
                                            @foreach($kantor as $k)
                                                <option @if(Auth::user()->id_kantor == $k->id) selected @endif value="{{$k->id}}">{{$k->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-lg-3 mb-3" id="hideApp">
                                        <label>Approve All</label>
                                        <div>
                                            <button type="button" class="btn btn-rounded btn-success btn-sm" id="approveALl" >Approve All <i class="fa fa-check"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     <div class="card-body">
                         <!--<input class="form-control" id="search">-->
                         
                            <div class="table-responsive">
                                <!--<input type="hidden" id="advsrc" value="tutup">-->
                                <table id="user_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="cari">Tanggal</th>
                                            <th class="cari">Akun</th>
                                            <th class="cari">Keterangan</th>
                                            <th class="cari">Qty</th>
                                            <!--<th hidden></th>-->
                                            <th class="cari">Nominal</th>
                                            <th class="cari">User Input</th>
                                            <th class="cari">User Approve</th>
                                            <th class="cari">Referensi</th>
                                            <th class="cari">Program</th>
                                            <th class="cari">Kantor</th>
                                            <th hidden>created_at</th>
                                            <th class="cari">COA Debet</th>
                                            <th class="cari">COA Kredit</th>
                                            <th>Acc</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Σ Total :</th>
                                            <th></th>
                                            <th id="totalFooter"></th>
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
                </div>
            </div>
        </div>

    </div>
</div>
@endsection