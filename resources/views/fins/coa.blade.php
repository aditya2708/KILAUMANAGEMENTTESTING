@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Chart of Account</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <h4 class="modal-title" id="title"></h4>
                    </div>
                    <form class="form-horizontal" id="sample_form" method="post">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">Nama Akun :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-sm" name="nama_coa" id="nama_coa">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">COA Parent :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control kikik" id="selectAccountDeal" style="width: 100%;" name="id_parent" id="id_parent">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3" id="distran" hidden>
                                    <label for="" class="col-sm-4"></label>
                                    <label for="" class="text-danger col-sm-8">COA parent tidak dapat diubah karena sudah memiliki transaksi</label>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4">Grup</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2" multiple="multiple" data-placeholder="Select a State" name="group[]" id="multiple" style="width: 100%;">
                                            @foreach($group as $val)
                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-md-4">Level :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="level" id="level">
                                            <option value="">- Pilih Level -</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="" class="col-md-4">Parent :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="parent" id="parent">
                                            <option value="">- Pilih -</option>
                                            <option value="y">Parent</option>
                                            <option value="n">Child</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-md-4">Aktif :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm" name="aktif" id="aktif">
                                            <option value="">- Pilih -</option>
                                            <option value="y" selected>Ya</option>
                                            <option value="n">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="coa_parent" id="coa_parent" />
                                <input type="hidden" name="kondat" id="kondat" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="action" id="action" value="add" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-md btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                        <form method="GET" action="{{ url('coa-exp') }}">
                    <div class="card-header">
                        <h4 class="card-title">Chart of Account</h4>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" value="xls" name="exp">.XLS</button></li>
                                    <li><button class="dropdown-item" type="submit" value="csv" name="exp">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                            <button type="button" id="add" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default">Tambah</button>
                        </div>
                            <!--</form>-->
                    </div>

                    <div class="card-body">
                        <!--<div class="table-responsive">-->
                        <!--<form>-->
                        <div class="col-md-12 row" style="margin-bottom:20px">
                            <div class="col-md-3">
                                <label>Parent</label>
                                <select class="select2 form-control" name="parent" id="f_parent">
                                    <option value="">- All -</option>
                                    <option value="y">Parent</option>
                                    <option value="n">Child</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Level</label>
                                <select class="select2 form-control" name="level" id="coaa">
                                    <option value="">- All -</option>
                                    @foreach($coa as $c)
                                        <option value="{{ $c->level }}">{{ $c->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Status</label>
                                <select class="select2 form-control" name="aktif" id="f_aktif">
                                    <option value="">- All -</option>
                                    <option value="y">Aktif</option>
                                    <option value="n">Non-Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                 <label>Grup</label>
                                <select class="select2 form-control" name="grup[]" multiple='[]' id="grupCoa" placeholder="Group">
                                    @foreach($group as $g)
                                    <option value="{{$g->id}}">{{$g->id}} - {{$g->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 row">    
                            <div class="col-md-3">
                                 <label>Cari</label>
                                 <input type="text" class="form-control col-3" id="myInput" onkeyup="myFunction()" placeholder="Cari Akun..." style="border-radius:20px; height: 2.5rem; width:100%;">
                            </div>
                            <!--<div class="col-md-3">-->
                            <!--    <label>Export</label>-->
                            <!--    <div class="col-md-12 row">-->
                            <!--        <div class="col-md-8">    -->
                            <!--        <select class="select2 form-control" name="exp" id="exp">-->
                            <!--            <option value="">- Pilih Format -</option>-->
                            <!--            <option value="Excel">.XLS</option>-->
                            <!--            <option value="CSV">.CSV</option>-->
                            <!--        </select>-->
                            <!--        </div>-->
                            <!--        <div class="col-md-4"> -->
                            <!--        <input type="hidden" class="btn btn-success btn-sm ms-3" id="dwnexp" value="">-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                            
                        </form>
                            <table data-height="550" border="1px solid #000"  data-show-export="true" id="user_table" class="table  table-striped"   data-show-refresh="true"  data-regex-search="true" data-query-params="queryParams"  data-show-search-clear-button="true">
                               
                            </table>
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection