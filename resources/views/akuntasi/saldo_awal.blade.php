@extends('template')
@section('konten')

<div class="content-body">
    <div class="container-fluid">
        
        <!--Modal-->
        <div class="modal fade" id="modaleditsaldo">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Saldo Akhir</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                    </div>
                    <form class="form-horizontal" id="sample_form_ok" method="post">
                        <div class="modal-body">
                            <h6 id="namcoa"></h6>
                            <input type="hidden" id="idna" name="idna">
                            <input type="hidden" id="coax" name="coax">
                            <input type="hidden" id="blnform" name="blnform">
                            <div class="basic-form">
                                <input id="sa" name="sa" class="form-control" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End Modal-->
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Saldo Awal</h4>
                        <div class="pull-right">
                        <!--<button type="button" id="add" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default">Tambah</button>-->
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{url('saldo_awal_export')}}" method="get">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>Bulan&Tahun :</label><span class="badge badge-danger badge-sm csss" style="float: right; cursor: pointer">Reset Bulan</span>
                                    <input type="text" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="{{date('m-Y') }}">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>COA</label>
                                    <select class="form-control cek5" name="coa" id="coa">
                                        <option value="">All</option>
                                        <option value="y">Parent</option>
                                        <option value="n">Child</option>
                                    </select>
                                    <!--<input type="text" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">-->
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Level</label>
                                    <select class="form-control cek3" name="lvl" id="lvl">
                                        <option value="">All</option>
                                        @foreach($lev as $l)
                                        <option value="{{$l->level}}">{{$l->level}}</option>
                                        @endforeach
                                    </select>
                                    <!--<input type="text" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">-->
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Cari</label>
                                    <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Program...">
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-2 d-grid">
                                    <div class="btn-group">
                                         <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" >
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
                        </form>
                        <!--<div class="table-responsive">-->
                        <!--    <table id="user_table" class="table table-bordered">-->
                        <!--    <table data-height="550" border="1px solid #000"  data-show-export="true" id="user_table" class="table  table-striped"   data-show-refresh="true"  data-regex-search="true" data-query-params="queryParams"  data-show-search-clear-button="true">-->
                        <!--        <thead>-->
                        <!--            <tr>-->
                                        <!--<th>#</th>-->
                        <!--                <th>Kode Akun</th>-->
                        <!--                <th>Nama Akun</th>-->
                        <!--                <th>Tanggal</th>-->
                        <!--                <th>Saldo Akhir</th>-->
                        <!--                <th>Kantor</th>-->
                        <!--                <th>Kantor</th>-->
                        <!--                <th>Kantor</th>-->
                        <!--                <th>Kantor</th>-->
                        <!--                <th>Kantor</th>-->
                        <!--            </tr>-->
                        <!--        </thead>-->
                        <!--        <tbody>-->
                                    
                        <!--        </tbody>-->
                        <!--    </table>-->
                        <!--</div>-->
                        
                        
                            <div class="table-responsive">    
                                    <!--data-height="350" -->
                                <table
                                    data-show-export="true" 
                                    id="user_table" 
                                    class="table table-bordered" 
                                    data-show-refresh="true"  
                                    data-search="false" 
                                    data-visible-search="false" 
                                    data-query-params="queryParams"  
                                    data-regex-search="true" 
                                    data-show-search-clear-button="false"
                                    data-resizable="true"
                                    >
                                   
                                </table>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection