@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Golongan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="sample_form">
                        <div class="modal-body">

                            @csrf
                            <div class="form">
                                <label>Kenaikan</label>
                                <input type="text" id="kenaikan" name="kenaikan" class="form-control">
                                <input type="hidden" id="acc_up" value="{{$gapok->acc_up}}">
                            </div>
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Golongan</h4>
                        <div>
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="user_table" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Golongan</th>
                                                <th>Kenaikan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>

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
</div>
@endsection