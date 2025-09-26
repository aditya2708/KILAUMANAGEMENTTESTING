@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Gaji Pokok</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <div class="modal fade" id="exampleModal" >
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="sample_form">
                        <div class="modal-body">

                            @csrf
                            <div class="form">
                                <label>Jumlah Tahun Kenaikan Berkala</label>
                                <input type="text" id="jumlah" name="jumlah" class="form-control" onkeyup="Angka(this);">
                            </div>
                            <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" onclick="konfir()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalPersen">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            
                        </button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="form_persen">
                        <div class="modal-body">

                            @csrf
                            <div class="form">
                                <label id="label_persen"></label>
                                <!-- <div class="input-group">
                                            <input type="text" class="form-control">
											<span class="input-group-text">$</span>
											<span class="input-group-text">0.00</span>
                                        </div> -->
                                <div class="input-group">
                                    <input type="text" id="persen" name="persen" class="form-control" onkeyup="Angka(this);">
                                    <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                </div>

                                <input type="hidden" name="action" id="action" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" onclick="konfir_persen()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalEdit">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 align="center">Nominal Gaji Pokok 0 Tahun</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="form_edit">
                        <div class="modal-body">

                            @csrf

                            <div class="form-group row">

                                <div class="col-sm-4">
                                    <p class="col-sm-2">IA</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IA" id="IA" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIA</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIA" id="IIA" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIIA</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIIA" id="IIIA" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IB</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IB" id="IB" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIB</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIB" id="IIB" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIIB</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIIB" id="IIIB" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IC</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IC" id="IC" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIC</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIC" id="IIC" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIIC</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIIC" id="IIIC" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <p class="col-sm-2">ID</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="ID" id="ID" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IID</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IID" id="IID" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIID</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIID" id="IIID" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                </div>
                                <div class="col-sm-4">
                                    <p class="col-sm-2">IIE</p>
                                    <div class="col-sm-10">
                                        <input type="text" name="IIE" id="IIE" class="form-control col-sm-12" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" onclick="konfir()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Gaji Pokok</h4>
                        <div class="d-flex gap-2 flex-wrap">
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                            <a href="" class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah Gaji Pokok</a>
                            <a href="" class="btn btn-success btn-xxs edit" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit Gaji Pokok</a>
                            <a href="" class="btn btn-info btn-xxs naik" data-bs-toggle="modal" data-bs-target="#ModalPersen">Kenaikan Nominal</a>
                            <a href="" class="btn btn-warning btn-xxs turun" data-bs-toggle="modal" data-bs-target="#ModalPersen">Penurunan Nominal</a>
                        
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <!--<div class="basic-form">-->
                                <!--        <div class="col-lg-8">-->
                                <!--            <a href="" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah Gaji Pokok</a>-->
                                <!--            <a href="" class="btn btn-success btn-sm edit" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit Gaji Pokok</a>-->
                                <!--            <a href="" class="btn btn-info btn-sm naik" data-bs-toggle="modal" data-bs-target="#ModalPersen">Kenaikan Nominal</a>-->
                                <!--            <a href="" class="btn btn-warning btn-sm turun" data-bs-toggle="modal" data-bs-target="#ModalPersen">Penurunan Nominal</a>-->
                                <!--        </div>-->
                                <!--</div>-->
                                <br>
                                <div class="table-responsive">
                                <input type="hidden" id="acc_up" /><input type="hidden" id="konfirm" />
                                    <table id="user_table" class="table table-responsive-sm" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Masa&nbsp;Kerja</th>
                                                <th>IA</th>
                                                <th>IB</th>
                                                <th>IC</th>
                                                <th>ID</th>
                                                <th>IIA</th>
                                                <th>IIB</th>
                                                <th>IIC</th>
                                                <th>IID</th>
                                                <th>IIE</th>
                                                <th>IIIA</th>
                                                <th>IIIB</th>
                                                <th>IIIC</th>
                                                <th>IIID</th>
                                                <!--<th>IVA</th>-->
                                                <!--<th>IVB</th>-->
                                                <!--<th>IVC</th>-->
                                                <!--<th>IVD</th>-->
                                                <!--<th>IVE</th>-->
                                                <!--<th>Aksi</th>-->
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