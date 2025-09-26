@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
            <!--Modal-->
            <div class="modal fade" id="modal-default2">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Jurnal Penyesuaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h3 style="margin-top: -10px">Informasi Jurnal Penyesuaian</h3>
                                        <hr style="margin: 0px; margin-bottom: 10px">
    
                                        <div class="basic-form">
                                            <div class="mb-3 row">
                                                <label class="col-sm-3 col-form-label">User Input :</label>
                                                <div class="col-sm-6">
                                                    <text id="user_input" name="user_input">{{ Auth::user()->name }}</text>
                                                </div>
                                            </div>
    
                                            <div class="mb-3 row">
                                                <label class="col-sm-3 col-form-label">Kantor :</label>
                                                <div class="col-sm-3">
                                                    <select required class="form-control " name="kantor_m" id="kantor_m">
                                                        <option value="">- Pilih -</option>
                                                        @foreach($kantor as $val)
                                                        <option value="{{$val->id}}" {{ $val->id == Auth::User()->id_kantor ? "selected" : "" }} data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
    
                                            <div class="mb-3 row">
                                                <label class="col-sm-3">Tanggal :</label>
                                                <div class="col-sm-6">
                                                    <input type="date" class="form-control" id="tgl_now_m" name="tgl_now_m" style="max-width: 45%">
                                                    <span style=" color: red; font-size : 11px;">*tidak diisi akan menggunakan tanggal sekarang</span>
                                                </div>
                                            </div>
    
                                        </div>
                                    </div>
                                </div>
    
    
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h3>Detail Jurnal Penyesuaian</h3>
                                        <hr style="margin: 0px; margin-bottom: 10px">
                                        <div class="basic-form">
                                            <div class="row">
                                                <div class="col-lg-3 mb-3">
                                                    <label for="">Jenis : </label>
                                                    <select class="form-control" name="jenis" id="jenis">
                                                        <option value="debit">Debit</option>
                                                        <option value="kredit">Kredit</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label for="">Jenis Transaksi : </label>
                                                    <select class="form-control jurnals" name="jenis_t" id="jenis_t">
                                                        <option value="">- Pilih -</option>
                                                    </select>
                                                </div>
    
                                                <div class="col-md-3 mb-3">
                                                    <label for="">Keterangan :</label>
                                                    <input type="text" name="ket" id="ket"  value="" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="">Nominal :</label>
                                                    <input type="text" name="nominal" id="nominal" class="form-control" onkeyup="rupiah(this);" autoxomplete="off">
                                                </div>
    
                                                <div class="col-md-1 mb-3">
                                                    <label>&nbsp;</label>
                                                    <a id="add_jurnal" class="btn btn-primary okkk"><i class="fa fa-plus"></i></a>
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
                                                    <th>COA</th>
                                                    <th>Nama Akun</th>
                                                    <th>Debit</th>
                                                    <th>Kredit</th>
                                                    <th>Keterangan</th>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary ">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--ENd Modal-->
            <form method="GET" action="{{ url('export-jurnal') }}">
            <div class="card">
                <div class="card-body">
                    <div class="basic-form">
                        <div class="row">
                            <div class="mb-3 col-md-4" id="rangetgl">
                                <label class="form-label">Priode</label>
                                 <select class="form-control cek12" name="prd" id="prd">
                                    <option value="0">Priode</option>
                                    <option value="1">Bulan</option>
                                    <option value="2">Tahun</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 col-md-4" id="rangetgl">
                                <label class="form-label">Range Tanggal</label>
                                <div id="hide_tgl" style="display: block">
                                    <input type="text" class="form-control" autocomplete="off" id="daterange" name="daterange" placeholder="{{date('Y-m-d').' s/d '.date('Y-m-d')}}" />
                                </div>
    
                                <div id="hide_thn" style="display: none">
                                    <input type="month" class="form-control cek12" id="month" name="month" style="width: 100%" onchange="">
                                </div>
                                <div id="hides" style="display: none">
                                    <input type="" class="form-control cek12 ttttt" id="years" name="years" autocomplete="off" value="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                                </div>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Jenis Transaksi</label>
                                <select class="form-control buook cek7" name="buku" id="buku">
                                </select>
                            </div>
                        </div>
                          <div class="row">
                                
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Group By</label>
                                    <select class="groupby" name="groupby" id="groupby">
                                        <option value="">- Pilih -</option>
                                        <option value="0">Transkasi Tanggal</option>
                                        <option value="1">Transkasi Perbulan</option>
                                        <option value="2">Transkasi Pertahun</option>
                                    </select>
                                </div>
                               <div class="mb-3 col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-control cek7" name="unit" id="unit">
                                    <option value="">- Pilih -</option>
                                    @foreach($kantor as $ka)
                                    <option value="{{$ka->id}}">{{$ka->unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Jenis</label>
                                    <select class="form-control cek111 jen" name="jen" id="jen">
                                        <option value="">- Pilih -</option>
                                        <option value="0">Debet</option>
                                        <option value="1">Kredit</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Via Jurnal</label>
                                    <select class="form-control via_jurnal" name="via_jurnal" id="via_jurnal">
                                        <option value="">- Pilih -</option>
                                        <option value="0">Otomatis</option>
                                        <option value="1">Oprasional</option>
                                        <option value="2">Mutasi</option>
                                        <option value="3">Penyesuaian</option>
                                    </select>
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
                            <div class="pull-right">
                                <a href="javascript:void(0)" class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default2"><i class="fa fa-plus"></i> Tambah</a>
                                 <div class="btn-group">
                                     <button type="button" class="btn btn-success btn-xxs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-download"></i> Ekspor
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="user_table" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th >Tanggal</th>
                                            <th >COA</th>
                                            <th >Jenis Transaksi</th>
                                            <th >Debet</th>
                                            <th >Kredit</th>
                                            <th id="kolket">Keterangan</th>
                                            <th >Via Jurnal</th>
                                            <th >ID Transaksi</th>
                                            <th hidden>urut</th>
                                            <th hidden>ids</th>
                                            <th hidden>urut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align:center">Total:</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th hidden></th>
                                            <th hidden></th>
                                            <th hidden></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
        </form>
     </div>
</div>
@endsection