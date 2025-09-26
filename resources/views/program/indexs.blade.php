@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Program</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Program</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal --> 
        <div class="modal fade" id="modal-default" aria-hidden="true" aria-labelledby="modal-defaultLabel">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="closes" aria-label="Close"></button>
                    </div>
                    <form id="form-program" method="post">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Nama Program :</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="program" id="program" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Program Parent :</label>
                                        <div class="col-sm-7">

                                            <select class="js-example-basic-single1" style="width: 100%;" name="id_program_parent" id="id_program_parent">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Sumber Dana :</label>
                                        <div class="col-sm-7">
                                            <select required class="js-example-basic-single" style="width: 100%;" name="id_sumber_dana" id="id_sumber_dana">
                                                <option value="">- Pilih -</option>
                                                @foreach($sum_dana as $val)
                                                <option value="{{$val->id_sumber_dana}}">{{$val->sumber_dana}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Dana Pengelola (%) :</label>
                                        <div class="col-sm-7">
                                            <div class="input-group">
                                                <input type="text" name="dp" id="dp" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="4" onchange="changeHandler(this)" class="form-control input-sm">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Special :</label>
                                        <div class="col-sm-7">
                                            <select required class="form-control input-sm" style="width: 100%;" name="spc" id="spc">
                                                <option value="">- Pilih -</option>
                                                <option value="y">Ya</option>
                                                <option value="n">Tidak</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Level :</label>
                                        <div class="col-sm-7">
                                            <select required class="form-control" name="level" id="level">
                                                <option value="">Pilih Level</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Parent :</label>
                                        <div class="col-sm-7">
                                            <select required class="form-control input-sm" style="width: 100%;" name="parent" id="parent">
                                                <option value="">Pilih Parent</option>
                                                <option value="y">Ya</option>
                                                <option value="n">Tidak</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                    </div>
                                </div>
                            </div>

                            <div hidden id="coa_coa_hide">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-5">COA Individu :</label>
                                            <div class="col-sm-7">
                                                <select class="js-example-basic-single2" style="width: 100%;" name="coa_individu" id="coa_individu">
                                                    <option></option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-5">COA Entitas :</label>
                                            <div class="col-sm-7">
                                                <select class="js-example-basic-single2" style="width: 100%;" name="coa_entitas" id="coa_entitas">
                                                    <option></option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-5">Penerimaan DP :</label>
                                            <div class="col-sm-7">
                                                <select class="js-example-basic-single_pndp" style="width: 100%;" name="coa1" id="coa1">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-5">Pengeluaran DP :</label>
                                            <div class="col-sm-7">
                                                <select class="js-example-basic-single-pngdp" style="width: 100%;" name="coa2" id="coa2">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Aktif :</label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" style="width: 100%;" name="aktif" id="aktif">
                                                <option value="y" selected>Ya</option>
                                                <option value="n">Tidak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="hidden_idprog" id="hidden_idprog" />
                                    <input type="hidden" name="action_prog" id="action_prog" value="add" />
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Jenis Pembayaran :</label>
                                        <div class="col-sm-7">
                                            <select required class="form-control input-sm" style="width: 100%;" name="jp" id="jp">
                                                <option value="">Pilih Pembayaran</option>
                                                <option value="0">Cash</option>
                                                <option value="1">Non-Cash</option>
                                                <option value="2">Cash/Non-Cash</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3" hidden id="jp_hide">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Keterangan :</label>
                                        <div class="col-sm-7">
                                            <select class="form-control input-sm" style="width: 100%;" name="ket_ada" id="ket_ada">
                                                <option value="">- Pilih -</option>
                                                <option value="0">Ada</option>
                                                <option value="1">Tidak Ada</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Presentase non-cash :</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="text" name="pnc" id="pnc" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="4" onchange="changeHandler(this)" class="form-control input-sm">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-5">Kategori Campaign :</label>
                                        <div class="col-sm-4">
                                            <select class="form-control input-sm" style="width: 100%;" name="camps" id="camps">
                                                <option value="">Pilih Kategori Campaign</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <a href="javascript:void(0)" class="btn btn-xs btn-primary" id="juma" style="display: none;"  data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#modalCam">Lihat Campaign</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-default1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h4 class="modal-title" id="title"></h4>
                    </div>
                    <form class="form-horizontal" id="sample_form" method="post">
                        <div class="modal-body">
                            <div class="basic-form">

                                <div class="row mb-3">
                                    <label class="col-sm-5">Sumber Dana :</label>
                                    <div class="col-sm-7">

                                        <input type="text" name="sumber_dana" id="sumber_dana" class="form-control input-sm">
                                    </div>
                                </div>
                                <div class="row mb-3 ">
                                    <label class="col-sm-5">Aktif :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control default-control wide" name="active" id="active">
                                            <!--<option value="">- Pilih -</option>-->
                                            <option value="y">Ya</option>
                                            <option value="n">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="hidden" name="action" id="action" value="add" />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-bonus">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="nama_prog_prog"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-bonus" method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-5">
                                        <label>Omset Minimal/Poin</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input class="form-control form-control-sm" name="omsetmin" id="omsetmin" type="text" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">
                                        <input name="hide_id_prog_ya" id="hide_id_prog_ya" type="hidden">
                                    </div>
                                </div>

                                <div class="col-md-6">

                                </div>
                            </div>
                            <br>
                            <br>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Bulan</th>
                                        <th scope="col">Honor</th>
                                        <th scope="col">Bonus Poin</th>
                                        <th scope="col">Bonus Omset</th>
                                    </tr>
                                </thead>
                                <tbody id="bonus-table">

                                </tbody>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modalCam" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">List Camapign <span id="campon"></span></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#modal-default" data-bs-toggle="modal"  ></button>
                    </div>
                    <form class="form-horizontal" id="sample_form" method="post">
                        <div class="modal-body">
                            <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
                                <ul class="timeline">
                                    <div id="mudeng">
                                
                                    </div>
                                </ul>
					        </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="submit" class="btn btn-primary">Simpan</button>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="entryProgPenyaluran">
            <div class="modal-dialog modal-lg modal-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_penyaluran">Tambah Program Penyaluran</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-programin">
                        <div class="modal-body">
                            <div class="row form-group mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Nama Program :</label>
                                        <div class="col-md-7">
                                            <input type="text" name="nama_program" id="nama_program" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Program Parent :</label>
                                        <div class="col-md-7">

                                            <select class="js-example-basic-singlex" style="width: 100%;" name="id_program_pp" id="id_program_pp">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Sumber Dana :</label>
                                        <div class="col-md-7">
                                            <select required class="js-example-basic-single" style="width: 100%;" name="id_sumber_dana_penyaluran" id="id_sumber_dana_penyaluran">
                                                <option value="">- Pilih -</option>
                                                @foreach($sum_dana as $val)
                                                <option value="{{$val->id_sumber_dana}}">{{$val->sumber_dana}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Level :</label>
                                        <div class="col-md-7">
                                            <select required class="form-control input-sm" style="width: 100%;" name="level_penyaluran" id="level_penyaluran">
                                                <option value="">- Pilih -</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Parent :</label>
                                        <div class="col-md-7">
                                            <select required class="form-control input-sm" style="width: 100%;" name="parent_penyaluran" id="parent_penyaluran">
                                                <option value="">- Pilih -</option>
                                                <option value="y">Ya</option>
                                                <option value="n">Tidak</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div hidden id="coa_hide">
                                <div class="row form-group mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-5">COA Penyaluran :</label>
                                            <div class="col-md-7">
                                                <select class="js-example-basic-single-penyaluran" style="width: 100%;" name="coa_penyaluran" id="coa_penyaluran">
                                                    <option></option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-5">COA Penerimaan :</label>
                                            <div class="col-md-7">
                                                <select class="js-example-basic-single-penyaluran" style="width: 100%;" name="coa_penerimaan" id="coa_penerimaan">
                                                    <option></option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-sm-5">Aktif :</label>
                                        <div class="col-md-7">
                                            <select class="form-control input-sm" style="width: 100%;" name="aktif_penyaluran" id="aktif_penyaluran">
                                                <option value="y" selected>Ya</option>
                                                <option value="n">Tidak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="hidden_idprog_penyaluran" id="hidden_idprog_penyaluran" />
                                    <input type="hidden" name="action_prog_penyaluran" id="action_prog_penyaluran" value="add" />
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="tambahin">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- End Modal -->

        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card">
                    @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                    <div class="card-header">
                         <h4 class="title-header"></h4>
                        <div class="d-flex justify-content-end">
                            <div class="bd-highlight">
                                <button type="button" class="btn btn-sm btn-primary" id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan"><i class="fa fa-home"></i> Pilih Perusahaan</button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#home"> Sumber Dana</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#profile"> Program Penerimaan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#contact"> Program Penyaluran</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="home" role="tabpanel">
                                    <div class="pt-4">
                                        <div class="row">
                                            
                                            <div class="col-lg-12">
                                                <div class="pull-right ">
                                                    <button type="button" id="add" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default1" style="float: right; margin-bottom: 20px">Tambah</button>
                                                </div>
                                                <!--<div class="table-responsive">-->
                                                    <table id="user_table" class="table table-striped"  style="width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Sumber Dana</th>
                                                                <th>Kelola</th>
                                                            </tr>
                                                        </thead>

                                                    </table>
                                                <!--</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile">
                                    <div class="pt-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="basic-form mb-5">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>Parent :</label>
                                                            <select class="form-control cek11" name="parent" id="parent">
                                                                <option value="">Semua</option>
                                                                <option value="y">parent</option>
                                                                <option value="n">child</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Jenis :</label>
                                                            <select class="form-control cek21" name="jenis" id="jenis">
                                                                <option value="">Semua</option>
                                                                <option value="0">cash</option>
                                                                <option value="1">noncash</option>
                                                                <option value="2">cash&noncash</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Spesial :</label>
                                                            <select class="form-control cek31" name="spesial" id="spesial">
                                                                <option value="">Semua</option>
                                                                <option value="y">iya</option>
                                                                <option value="n">tidak</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Aktif :</label>
                                                            <select class="form-control cek41" name="aktif" id="aktif">
                                                                <option value="">Semua</option>
                                                                <option value="y">aktif</option>
                                                                <option value="n">nonaktif</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="btn-group">
                                                                 <button type="button" class="btn btn-success btn-sm dropdown-toggle" style="margin-top:40px;" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Rekap Kehadiran" style="width:100%;" fdprocessedid="5wa0zs">
                                                                    <i class="fa fa-download"></i> Export
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol" id="sip">.XLS</button></li>
                                                                    <li><button class="dropdown-item disabled" type="submit" value="csv" name="tombol">.CSV</button></li>
                                                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <!--<div class="table-responsive">-->
                                                    <!--<div>-->
                                                    <!--    <select class="form-control default-select wide cek1" name="jenis_pem" id="jenis_pem" style="float: left">-->
                                                    <!--        <option value="">- pilih -</option>-->
                                                    <!--        <option value="0">Cash</option>-->
                                                    <!--        <option value="1">Non-cash</option>-->
                                                    <!--        <option value="2">Cash / Non-cash</option>-->
                                                    <!--    </select>-->
                                                    <!--</div>-->
                                                    <a id="tambah" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default" style="float: right; margin-top : 10px; margin-left: 10px"><i class="fa fa-plus"></i> Tambah</a>
                                                    <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Program..." style="float: right; margin-top : 10px; margin-left: 10px; width: 20%; height: 2.5rem">
                                                    <table id="user_table1" data-toggle="user_table1" class="table table-striped">

                                                    </table>
                                                <!--</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="contact">
                                    <div class="pt-4">
                                        <div class="row mb-5">
                                            <div class="col-md-2">
                                                <label>Parent :</label>
                                                <select class="form-control parentPenyaluran" name="parentPenyaluran" id="parentPenyaluran">
                                                    <option value="">Semua</option>
                                                    <option value="y">parent</option>
                                                    <option value="n">child</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Jenis :</label>
                                                <select class="form-control jenisPenyaluran" name="jenisPenyaluran" id="jenisPenyaluran">
                                                    <option value="">Semua</option>
                                                    <option value="0">cash</option>
                                                    <option value="1">noncash</option>
                                                    <option value="2">cash&noncash</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Valid :</label>
                                                <select class="form-control validPenyaluran" name="validPenyaluran" id="validPenyaluran">
                                                    <option value="">Semua</option>
                                                    <option value="y">valid</option>
                                                    <option value="n">In Valid</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Aktif :</label>
                                                <select class="form-control aktifPenyaluran" name="aktifPenyaluran" id="aktifPenyaluran">
                                                    <option value="">Semua</option>
                                                    <option value="y">aktif</option>
                                                    <option value="n">nonaktif</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center gap-3 mt-3">
                                                <div>
                                                    <div class="btn-group">
                                                         <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="width:100%;" fdprocessedid="5wa0zs">
                                                            <i class="fa fa-download"></i> Export
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><button class="dropdown-item expProgPenyaluran" type="submit" value="xls" name="tombol">.XLS</button></li>
                                                            <li><button class="dropdown-item expProgPenyaluran" type="submit" value="csv" name="tombol">.CSV</button></li>
                                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" id="addProgPenyaluran" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#entryProgPenyaluran"><i class="fa fa-plus me-1"></i>Tambah</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                 <!--<div class="table-responsive"> -->
                                                    <table id="user_table2" class="table table-striped">

                                                    </table>
                                                 <!--</div> -->
                                            </div>
                                        </div>
                                    </div>
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