@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">CRM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Jalur</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form">
                        <div class="modal-body">
                            @csrf

                            <div class="basic-form">
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">Jalur :</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="jalur" name="nama_jalur" class="form-control input-sm" placeholder="nama jalur..">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">Kantor :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control uws" id="id_kantor" name="id_kantor">
                                            <option value="">- Pilih Kantor -</option>
                                            @foreach($kantor as $value)
                                            <option value="{{ $value->id }}" data-value="{{$value->unit}}">{{$value->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">SPV :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control uws" id="id_spv" name="id_spv">
                                            <option value="">- Pilih SPV -</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="action" id="action" value="add" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="exampleModall">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Set Jalur SPV</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="spvForm">
                        <div class="modal-body">
                            @csrf

                            <div class="basic-form">
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">Kantor :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control uws" id="kntr" name="kntr">
                                            <option value="">- Pilih Kantor -</option>
                                            @foreach($kantor as $value)
                                            <option value="{{ $value->id }}" data-value="{{$value->unit}}">{{$value->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">SPV :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control uws" id="nm_spv" name="nm_spv">
                                            <option value="" selected disabled>- Pilih SPV -</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="col-sm-4">Jalur :</label>
                                    <div class="col-sm-8">
                                        <select class="form-control multi-select" name="jlr[]" multiple="multiple" id="multiple">
                                            <!--<option value="">- Pilih Jalur -</option>-->
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ENd Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Jalur</h4>
                        <div class="pull-right">
                            <a href="javascript:void(0)" class="btn btn-info btn-xxs" data-bs-toggle="modal" data-bs-target="#exampleModall">Set Jalur SPV</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-xxs awwbit" id="record">Tambah Jalur</a>
                            <!--data-bs-toggle="modal" data-bs-target="#exampleModal"-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jalur</th>
                                        <th>Kantor</th>
                                        <th>SPV</th>
                                        <th>Action</th>
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
@endsection