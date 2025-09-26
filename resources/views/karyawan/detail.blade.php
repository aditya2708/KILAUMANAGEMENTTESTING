@extends('template')
@section('konten')

<!-- Modal -->
<div class="modal fade" id="rekpangkat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rekap Kenaikan Pangkat {{$karyawan->nama}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="user_table0" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>Masa Kerja</th>
                                        <th>Golongan</th>
                                        <th>Tanggal MK</th>
                                        <th>Tanggal SK</th>
                                        <th>File SK</th>
                                    </tr>
                                </thead>
    
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rekjabatan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rekap Kenaikan Jabatan {{$karyawan->nama}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="user_table1" style="width:100%" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>Jabatan</th>
                                        <th>Tanggal Perubahan</th>
                                        <th>File</th>
                                        <th hidden></th>
                                    </tr>
                                </thead>
    
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="rekkeluarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rekap Data Keluarga {{$karyawan->nama}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="user_table2" style="width:100%" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>Status Nikah</th>
                                        <th>No KK</th>
                                        <th>Jumlah Pasangan</th>
                                        <th>Jumlah Anak</th>
                                        <th>Scane KK</th>
                                        <th>tanggal</th>
                                    </tr>
                                </thead>
    
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="mutasikaryawan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rekap Mutasi {{$karyawan->nama}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="user_table3" style="width:100%" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>Kantor Asal</th>
                                        <th>Kantor Baru</th>
                                        <th>Durasi</th>
                                        <th>Tanggal Mutasi</th>
                                        <th>File SK</th>
                                    </tr>
                                </thead>
    
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="perubahans">
    <div class="modal-dialog modal-lg" role="document" >
        <form method="post" id="upload_form" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Perubahan Karyawan</h5>
                </div>
                <div class="modal-body">
                {{ csrf_field() }}
                    <input type="hidden" name="karyawanhid" id="karyawanhid" value="{{$karyawan->id_karyawan}}">
                    <input type="hidden" name="action" value="" id="action">
                    <div id="div" style="display: none">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="kawin"></div>
                                <div id="lok" style="display: none">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="checkbox mb-1">
                                            <label>
                                                <input type="checkbox" name="tj_pas" id="tj_pas"> Karyawan ini memiliki Pasangan di Perusahaan
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div id="pass" style="display:none">
                                            <div class="checkbox mb-1">
                                                <label>
                                                    <input type="checkbox" name="warning_pasangan" id="dc_kar"> Data Pasangan belum ada didatabase Karyawan
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="">No KK</label>
                                            <input type="text" id="nokk" name="no_kk" onkeyup="btn()" class="form-control" aria-describedby="" placeholder="Nomor Kartu Keluarga" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group" id="scan_kk">
                                            <label for="">Scan KK</label>
                                                <!--<br />-->
                                            <img id="output3" style="height:80px; margin-bottom:10px;" src="">
                                            <input type="file" name="scan_kk" class="form-control" onchange="btn()" aria-describedby="" value="" id="limit1mb3" accept="image/*" onchange="loadFile3(event)">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3" id="nampas0" style="display:block">
                                        <div class="form-group">
                                            <label for="">Nama Suami / Istri</label>
                                            <input type="text" name="nm_pasangan" id="nm_pasangan1" class="form-control " aria-describedby="" placeholder="Nama Suami / Istri">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" id="nampas1" style="display:none">
                                        <div class="form-group">
                                            <label>Pasangan</label>
                                            <select id="id_pasangan" class="form-control  select-pass" style="width: 100%;" name="id_pasangan">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group" id="tgl_lahir">
                                            <label for="">Tanggal Lahir</label>
                                            <input type="date" name="tgl_lahir" id="tgl_lahir1" class="form-control " aria-describedby="">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group" id="tgl_nikah">
                                            <label for="">Tanggal Nikah</label>
                                            <input type="date" name="tgl_nikah" id="tgl_nikah1" class="form-control " aria-describedby="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3" mb-3>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-sm btn-primary" style="width:100%" id="tam_sum">Tambah Suami / Istri</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="tab_pasangan" style="display: none">
                                        <div class="col-md-12 mb-3">
                                            <table class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Nama Suami / Istri</th>
                                                        <th width="25%">Tanggal Lahir</th>
                                                        <th width="25%">Tanggal Nikah</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                            <div class="form-group" id="nm_anak1">
                                            <label for="">Nama Anak </label>
                                            <input type="text" name="nm_anak" class="form-control " id="nama_anak1" aria-describedby="" placeholder="Nama Anak">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group" id="nm_anak1">
                                            <label for="">Tanggal Lahir</label>
                                            <input type="date" name="tgl_lahir_anak" class="form-control " id="tgl_lahir_anak1" aria-describedby="">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group" id="nm_anak1">
                                            <label for="">Status Anak</label>
                                            <select class="form-control " style="width: 100%;" name="status_anak" id="status_anak1">
                                                <option value="">- Pilih Status -</option>
                                                <option value="Menikah">Menikah</option>
                                                <option value="Belum Menikah">Belum Menikah</option>
                                                <option value="Meninggal">Meninggal</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-sm btn-primary" style="width:100%" id="tam_anak">Tambah Anak</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="tab_anak" style="display: none">
                                        <div class="col-md-12">
                                            <table class="table table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Nama Anak</th>
                                                        <th width="25%">Tanggal Lahir</th>
                                                        <th width="25%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_anak">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div id="isi"></div>
    
                    <div id="okk"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="updatekar" disabled type="submit" class="btn btn-primary">Simpan</button>
                </div> 
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mutasikar">
    <div class="modal-dialog" role="document">
        <form method="post" id="mutasi_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mutasi Karyawan</h5>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="action" value="" id="action">
                    <div class="row">
                        <input type="hidden" name="id_karyawan" id="mutasi_karyawan" value="{{$karyawan->id_karyawan}}">
                        <div id="muta" style="display: none">
                            <div class="col-md-12 mb-3">
                                <label>Unit kerja Sekarang</label>
                                <input type="text" id="kantor_asal" class="form-control" value="" readOnly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Jabatan Sekarang</label>
                                <input type="text" id="jab_asal" class="form-control" value="" readOnly>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Pilih Unit kerja</label>
                            <select class="js-example-basic-single" style="width: 100%;" name="kantor_baru" id="kantor_baru">
                                <option selected="selected" value="">- Unit Kerja -</option>
                                @foreach($unit as $u)
                                <option value="{{$u->id}}">{{$u->unit}}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Pilih Jabatan Baru *(Opsional)</label>
                            <select class="js-example-basic-single" style="width: 100%;" name="jab_new" id="jab_new">
                                <option selected="selected" value="">-Pilih Jabatan Kerja -</option>
                                @foreach($jabat as $tan)
                                <option value="{{$tan->id}}">{{$tan->jabatan}}</option>
                                @endforeach
                                </select>
                        </div>
                        <div class="col-md-12 mb-3" id="_spv_new">
                            </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Upload SK</label>
                            <input type="file" name="file_sk" id="file_sk_mutasi" class="form-control" aria-describedby="">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Tanggal Mutasi</label>
                            <input type="date" name="tgl_mutasi" id="tgl_mutasi" class="form-control" aria-describedby="" placeholder="Tanggal Mutasi">
                        </div>
                                <!--</div>-->

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- End Modal -->

<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        
                                        <div class="media pt-3 pb-3">
											<img src="{{asset('upload/'.$karyawan->gambar_identitas)}}" alt="image" class="me-3 rounded" width="75">
											<div class="media-body">
												<h3 class="m-b-5"><a href="javascript:void(0)" class="text-black"  id="namax">{{$karyawan->nama}}</a></h3>
												<p class="mb-0">{{$jab->jabatan}}</p>
											</div>
										</div>
                                        
                                        <div id="niki">
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd')
                            <div class="col-lg-12">
                                <div class="card">
                                    <!--<div class="card-header">-->
                                        <!--<h3 class="card-title">Rekap Perubahan Karyawan</h3>-->
                                    <!--</div>-->
                                    <div class="card-body">
                                        <h4 class="text-primary d-inline ">Riwayat Perubahan Karyawan</h4>
                                        <ul class="list-group list-group-unbordered mt-3">
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <b>Kenaikan Pangkat</b>
                                                    </div>
                                                    <div class="col-sm-2">
                                                       <a href="#" data-bs-toggle="modal" data-bs-target="#rekpangkat" class="btn btn-primary btn-xxs pull-right" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="fa fa-eye"></i></a> 
                                                    </div>
                                                    <div class="col-sm-2">
                                                       <a href="#" data-bs-toggle="modal" data-bs-target="#perubahans" class="btn btn-info btn-xxs pull-right prb" perubahan="pangkat" id="{{$karyawan->id_karyawan}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pen"></i></a>
                                                    </div>
                                                </div> 
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <b>Kenaikan Jabatan</b>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#rekjabatan" class="btn btn-primary btn-xxs pull-right" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="fa fa-eye"></i></a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#perubahans" class="btn btn-info btn-xxs pull-right prb" perubahan="jabatan" id="{{$karyawan->id_karyawan}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pen"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <b>Perubahan Keluarga</b>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#rekkeluarga" class="btn btn-primary btn-xxs pull-right" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="fa fa-eye"></i></a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#perubahans" class="btn btn-info btn-xxs pull-right prb" perubahan="keluarga" id="{{$karyawan->id_karyawan}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pen"></i></a>
                                                    </div>
                                                </div>
                                                
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <b>Mutasi Karyawan</b>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#mutasikaryawan" class="btn btn-primary btn-xxs pull-right" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="fa fa-eye"></i></a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#mutasikar" class="btn btn-info btn-xxs pull-right mutasi mutasi_karyawan" id="{{$karyawan->id_karyawan}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pen"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="text-primary d-inline mb-3 ml-3">Data Diri</h4> <a href="javascript:void(0)" id="edito" class="btn btn-xxs btn-primary" style="float: right; display: block;">Edit</a>
                                                <div class="table-responsive">
                                                    <form class="form-horizontal" method="post" id="simple_form">
                                                        <div id="datadiri">
                                                            
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="text-primary d-inline">Pendidikan</h4> <a href="javascript:void(0)" id="editpen" class="btn btn-xxs btn-primary" style="float: right; display: block;">Edit</a>
                                                <div class="table-responsive">
                                                    <form class="form-horizontal" method="post" id="pendidikan_form">
                                                    <div id="pendiks"></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="text-primary d-inline">Data Jabatan</h4>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <tr>
                                                            <td style="width:35%">Jabatan</td>
                                                            <td> : </td>
                                                            <td>{{$jab->jabatan}}</td>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td style="width:35%">Tanggal diterima Kerja</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->tgl_kerja}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Unit Kerja</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->unit_kerja}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lokasi Kerja</td>
                                                            <td> : </td>
                                                            <td>{{$daerah}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Status Kerja</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->status_kerja}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Masa Kerja</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->masa_kerja}} Tahun</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Golongan</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->golongan}}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="kk" class="col-lg-12" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h4 class="text-primary d-inline">Data Keluarga</h4>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <?php
                                                        $no = 1;
                                                        $anak = unserialize($karyawan->nm_anak);
                                                        $tgl = unserialize($karyawan->tgl_lahir_anak);
                                                        $status_anak = unserialize($karyawan->status_anak);

                                                        $pasangan = unserialize($karyawan->nm_pasangan);
                                                        $tgl_lahir_pas = unserialize($karyawan->tgl_lahir);
                                                        $tgl_nikah = unserialize($karyawan->tgl_nikah);

                                                        $today = new DateTime("today");
                                                        ?>
                                                        <tr>
                                                            <td style="width:20%">No KK</td>
                                                            <td> : </td>
                                                            <td>{{$karyawan->no_kk}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width:20%">Scane KK</td>
                                                            <td> : </td>
                                                            <td><a href="{{asset('upload/'.$karyawan->scan_kk)}}" target="_blank"><span class="badge badge-success">Lihat</span></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Pasangan</td>
                                                            <td> : </td>
                                                            <td>
                                                                @if($pasangan)
                                                                <table style="width: 100%;">

                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th style="width:30%">Nama Pasangan</th>
                                                                        <th style="width:30%">Tanggal Lahir</th>
                                                                        <th style="width:30%">Tanggal Nikah</th>
                                                                    </tr>
                                                                    @foreach($pasangan as $key => $value)
                                                                    <tr>
                                                                        <td>{{$no++}}.</td>
                                                                        <td><?php echo ucwords($value); ?></td>
                                                                        <td>{{$tgl_lahir_pas[$key]}}</td>
                                                                        <td>{{$tgl_nikah[$key]}}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </table>
                                                                @else
                                                                Tidak ada Suami / Istri
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Anak </td>
                                                            <td> : </td>
                                                            <td>
                                                                @if($anak)
                                                                <table style="width: 100%;">
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th style="width:30%">Nama Anak</th>
                                                                        <th style="width:30%">Umur</th>
                                                                        <th style="width:30%">Status</th>
                                                                    </tr>
                                                                    @foreach($anak as $key => $value)
                                                                    <?php $tt = new DateTime($tgl[$key]) ?>
                                                                    <tr>
                                                                        <td>{{$no++}}.</td>
                                                                        <td><?php echo ucwords($value); ?></td>
                                                                        <td>{{$today->diff($tt)->y}} Tahun</td>
                                                                        <td>
                                                                            @if($status_anak[$key] == 'Menikah')
                                                                            <span class="badge badge-success">{{$status_anak[$key]}}</span>
                                                                            @elseif($status_anak[$key] == 'Belum Menikah')
                                                                            <span class="badge badge-warning">{{$status_anak[$key]}}</span>
                                                                            @else
                                                                            <span class="badge badge-danger">{{$status_anak[$key]}}</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </table>
                                                                @else
                                                                Tidak ada Anak
                                                                @endif
                                                            </td>
                                                        </tr>
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
            </div>
        </div>
    </div>
</div>

@endsection