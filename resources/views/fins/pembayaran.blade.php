@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Bank</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <!--<div class="modal fade" id="modal-default" aria-labelledby="modal-default" aria-hidden="true">-->
        <!--    <div class="modal-dialog modal-dialog-centered">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                    <h4 class="modal-title" id="title"></h4>-->
        <!--            </div>-->
        <!--            <form id="sample_form" method="post" enctype="multipart/form-data">-->
        <!--                @csrf-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="basic-form">-->
        <!--                        <div class="mb-3 row">-->
        <!--                            <label for="" class="col-sm-4">Kantor :</label>-->
        <!--                            <div class="col-sm-8">-->
        <!--                                <select class="form-control default-select wide" name="id_kantor" id="id_kantor">-->
        <!--                                    <option selected="selected" value="">- Pilih Kantor -</option>-->
        <!--                                    
        <!--                                </select>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div class="mb-3 row">-->
        <!--                            <label for="" class="col-sm-4">Nama Bank :</label>-->
        <!--                            <div class="col-sm-8">-->
        <!--                                <input type="text" class="form-control" name="nama_bank" id="nama_bank" placeholder="Nama Bank" />-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div class="mb-3 row">-->
        <!--                            <label for="" class="col-sm-4">No Rekening :</label>-->
        <!--                            <div class="col-sm-8">-->
        <!--                                <input type="text" class="form-control" name="no_rek" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="no_rek" placeholder="Nomer Rekening" />-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div class="mb-3 row">-->
        <!--                            <label for="" class="col-sm-4">Jenis Tabungan :</label>-->
        <!--                            <div class="col-sm-8">-->
        <!--                                <select class="form-control default-select wide" name="jenis_rek" id="jenis_rek">-->
        <!--                                    <option selected="selected" value="">- Pilih Tabungan -</option>-->
        <!--                                    <option value="Rekening Tabungan" data-value="101.02.001.000">Rekening Tabungan</option>-->
        <!--                                    <option value="Rekening Giro" data-value="101.02.002.000">Rekening Giro</option>-->
        <!--                                    <option value="Deposito" data-value="101.02.003.000">Deposito</option>-->
        <!--                                </select>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div id="ceklis_coa" style="diplay:block">-->
        <!--                            <div class="mb-3 row">-->
        <!--                                <label for="" class="col-sm-4"></label>-->
        <!--                                <div class="col-sm-8">-->
        <!--                                    <div class="checkbox">-->
        <!--                                        <label>-->
        <!--                                            <input type="checkbox" name="cek_coa" id="cek_coa"> Data Coa sudah ada ?-->
        <!--                                        </label>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div id="coa" style="display:none">-->
        <!--                            <div class="mb-3 row">-->
        <!--                                <label for="" class="col-sm-4">COA :</label>-->
        <!--                                <div class="col-sm-8">-->
        <!--                                    <select class="form-control selectAccountDeal" name="id_coa" id="coa_cek">-->
        <!--                                        <option></option>-->
        <!--                                    </select>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <input type="hidden" name="hidden_id" id="hidden_id" />-->
        <!--                        <input type="hidden" name="action" id="action" value="add" />-->

        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="modal-footer">-->
        <!--                    <button type="submit" class="btn btn-primary">Save changes</button>-->
        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pembayaran</h4>
                        <div class="pull-right">
                            <button type="button" id="add" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#modal-default">Tambah</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pembayaran</th>
                                        <th>Kelola</th>
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