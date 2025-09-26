@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Karyawan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" action="{{url('karyawan/import')}}" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                        </div>
                        <div class="modal-body">

                            {{ csrf_field() }}

                            <label>Pilih file excel</label>
                            <div class="form-group">
                                <input type="file" name="file" required="required">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="modal fade" id="editkenaikan">
            <div class="modal-dialog modal-lg" role="document" >
                <form method="post" id="upload_form" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Perubahan Karyawan</h5>
                        </div>
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <!-- <div class="basic-form"> -->
                            <div class="basic-form">
                                <div class="row">
                                    <input type="hidden" name="action" value="" id="action">
                                    <div class="col-lg-5 mb-3">
                                        <label>Nama Karyawan</label>
                                        <select required class="form-control js-example-basic-single" name="nm_karyawan" id="karyawan" style="width: 100%">
                                            <option selected value="">- Pilih Karyawan -</option>
                                            @foreach ($karyawan as $kar)
                                            <option value="{{$kar->id_karyawan}}">{{$kar->nama}} ( {{$kar->jabatan}} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-5 mb-3">
                                        <label>Jenis Perubahan</label>
                                        <select required class="form-control default-select wide" name="perubahan" id="perubahan">
                                            <option value="">Jenis Perubahan</option>
                                            <option value="pangkat">Kenaikan Pangkat</option>
                                            <option value="jabatan">Kenaikan Jabatan</option>
                                            <option value="keluarga">Perubahan Keluarga</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 mb-3 ">
                                        <button type="button" id="pilih" style="margin-top:25px" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </div>
                            <!-- </div> -->
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
                                                        <button type="button" class="btn btn-sm btn-primary" style="width:100%; display: block" id="tam_sum">Tambah Suami / Istri</button>
                                                        
                                                        <div class="row">
                                                        <div class="col-lg-6"></div>
                                                        <div class="col-lg-6">
                                                            <div class="pull-right">
                                                                <button type="button" class="btn btn-sm btn-success simpan_pas" style=" display: none; float: right" id="sim_p">Simpan</button>
                                                                <button type="button" class="btn btn-sm btn-danger batal_pas" style=" display: none; float: right; margin-right: 10px" id="bat_p">Batal</button>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                                    <th >Aksi</th>
                                                
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
                                                    <!--<div class="form-group">-->
                                                    <button type="button" class="btn btn-sm btn-primary" style="width:100%; display: block" id="tam_anak">Tambah Anak</button>
                                                    
                                                    <div class="row">
                                                        <div class="col-lg-6"></div>
                                                        <div class="col-lg-6">
                                                            <div class="pull-right">
                                                                <button type="button" class="btn btn-sm btn-success simpan_ank" style=" display: none; float: right" id="sim_a">Simpan</button>
                                                                <button type="button" class="btn btn-sm btn-danger batal_ank" style=" display: none; float: right; margin-right: 10px" id="bat_a">Batal</button>
                                                            </div>
                                                        </div>
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
                                                                    <th>Aksi</th>

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

        <div class="modal fade" id="bpjskaryawan">
            <div class="modal-dialog" role="document">
                <form method="post" id="bpjs_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Keikutsertaan BPJS Karyawan</h5>
                        </div>
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <input type="hidden" name="action" value="" id="action">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <label>Nama Karyawan</label>
                                        <select required class="form-control  suuu karyawan" style="width: 100%;" name="nm_karyawan" id="karya">
                                            <option selected value="">- Pilih Karyawan -</option>
                                            @foreach ($karyawan as $kar)
                                            <option value="{{$kar->id_karyawan}}">{{$kar->nama}} ( {{$kar->jabatan}} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!--<div class="col-md-2">-->
                                    <!--    <a id="pilih" style="margin-top:25px" onclick="btn_dis()" class="btn btn-primary">Submit</a>-->
                                    <!--</div>-->
                                </div>
                                <div id="ok" hidden>

                                </div>
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
                                
                                <div class="col-md-12 mb-3">
                                    <label>Nama Karyawan</label>
                                    <select class="form-control" style="width: 100%;" name="id_karyawan" id="mutasi_karyawan">
                                         @if(count($karyawan) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($karyawan as $c)
                                                <option value="{{ $c->id_karyawan }}">{{$c->nama}} ( {{$c->jabatan}} )</option>
                                            @endforeach
                                        @endif
                                      
                                      
                                        <!--<option selected value="">- Pilih Karyawan -</option>-->
                                        <!--@foreach ($karyawan as $kar)-->
                                        <!--<option value="{{$kar->id_karyawan}}">{{$kar->nama}} ( {{$kar->jabatan}} )</option>-->
                                        <!--@endforeach-->
                                    </select>
                                </div>
                                
                                <div id="muta" style="display: none">
                                    <div class="col-md-12 mb-3">
                                        <label>Unit kerja Sekarang</label>
                                        <input type="text" id="kantor_asal" class="form-control" value="" readOnly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label>Lokasi kerja Sekarang</label>
                                        <input type="text" id="lokasi_asal_val" class="lokasi_asal_val form-control" value="" readOnly>
                                        <input type="hidden" id="lokasi_asal" name="lokasi_asal" class="lokasi_asal form-control" value="" readOnly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label>Jabatan Sekarang</label>
                                        <input type="text" id="jab_asal" class="form-control" value="" readOnly>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Pilih Unit kerja</label>

                                    <select class="form-control" style="width: 100%;" name="kantor_baru" id="kantor_baru">
                                           @if(count($units) > 0)
                                            <option selected="selected" value="">Pilih Unit</option>
                                            @foreach($units as $u)
                                            <option value="{{$u->id}}">{{$u->unit}}</option>
                                            @endforeach
                                            @else
                                            <!--<option value="">Tidak Ada</option>-->
                                            @endif
                                        
                                        <!--<option selected="selected" value="">- Unit Kerja -</option>-->
                                        <!--@foreach($units as $u)-->
                                        <!--<option value="{{$u->id}}">{{$u->unit}}</option>-->
                                        <!--@endforeach-->

                                    </select>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label>Pilih Lokasi kerja</label>

                                    <select class="form-control" style="width: 100%;" name="lokasi_baru" id="lokasi_baru">
                                       @if(count($daerah) > 0)
                                            <option selected="selected" value="">Pilih Lokasi</option>
                                            @foreach($daerah as $val)
                                            <option value="{{$val->id_daerah}}">{{$val->kota}}</option>
                                            @endforeach
                                        @else
                                            <option value="">Tidak Ada</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Pilih Jabatan Baru *(Opsional)</label>
                                    <select class="form-control" style="width: 100%;" name="jab_new" id="jab_new">
                                        <option selected="selected" value="">-Pilih Jabatan Kerja -</option>
                                        @foreach($jabatbaru as $tan)
                                        <option value="{{$tan->id}}">{{$tan->jabatan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3" id="_spv_new">
                                </div>
                                <div class="col-md-12">
                                    <div class="bg-collaps rounded">
                                        <div class="collapse multi-collapse " id="multiCollapseExample4" >
                                           <div class="row">
                                               <div  id="proses" hidden>
                                                   <div  class="d-flex justify-content-center align-items-center m-5">
                                                       <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                                                          <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                   </div>
                                               </div>
                                               <div  id="berhasil" hidden class="">
                                                    <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                        <div class="bg-success d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                            <i class="fa fa-check color-success text-white" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <br/>
                                                        <span class="">Berhasil!, Silahkan Approve.</span>
                                                    </div>
    
                                               </div>
                                               <div  id="gagal" hidden class="">
                                                    <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                        <div class="bg-danger d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                            <i class="fa fa-times color-success text-white" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <br/>
                                                        <span class="">Gagal!, Silahkan Cob Lagi.</span>
                                                    </div>
    
                                               </div>
                                               <div id="pilihSurat" hidden>
                                                    <div class="d-flex justify-content-start row mx-auto" id="elementPilihSurat">
                                                        
                                                    </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                                @if(Auth::user()->pengaturan == 'admin')
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label">Upload SK</label>
                                                <a href="javascript:void(0)" class="text-success generateSK" id="generateSK">Generate SK <i class="fa fa-download"></i></a>
                                        </div>
                                        <input type="hidden" name="file_sk_mutasi" id="skmutasi" class="form-control" aria-describedby="">
                                        <input type="file" name="file_sk" id="file_sk_mutasi" class="form-control" aria-describedby="">
                                    </div>
                                @endif
                                
                                
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

        <!-- row -->
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header justify-content-end gap-2 flex-wrap">    
                                
                                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                                            @endif
                                            @if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd')
                                            <div class="btn-group">
                                                 <button type="button" class="btn btn-success btn-xxs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Rekap Kehadiran" style="width:100%;" fdprocessedid="fgjpkn">
                                                    <i class="fa fa-download"></i> Export
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol" id="xls">.XLS</button></li>
                                                    <li><button class="dropdown-item" type="submit" value="csv" name="tombol" id="csv">.CSV</button></li>
                                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                                </ul>
                                            </div>
                                            <a class="btn btn-primary btn-xxs itunghela" data-bs-toggle="tooltip" data-bs-placement="top" title="Entri Karyawan Baru">Entri Karyawan </a>
                                            @if(Auth::user()->id_com == 1)
                                            <button type="button" id="edt" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#editkenaikan" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Perubahan Karyawan">Perubahan Karyawan</button>
                                            <button type="button" id="bpjs" class="btn btn-info btn-xxs" data-bs-toggle="modal" data-bs-target="#bpjskaryawan" data-bs-toggle="tooltip" data-bs-placement="top" title="Keikutsertaan BPJS Karyawan">BPJS Karyawan</button>
                                            @endif
                                            <button type="button" id="mutasi" class="btn btn-warning btn-xxs" data-bs-toggle="modal" data-bs-target="#mutasikar" data-bs-toggle="tooltip" data-bs-placement="top" title="Mutasi Karyawan">Mutasi Karyawan</button>
                                            @else
                                            <button type="button" id="mutasi" class="btn btn-warning btn-xxs" data-bs-toggle="modal" data-bs-target="#mutasikar" data-bs-toggle="tooltip" data-bs-placement="top" title="Mutasi Karyawan">Mutasi Karyawan</button>
                                            <button type="button" id="edt" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#editkenaikan" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Perubahan Karyawan">Pengajuan Perubahan Karyawan</button>
                                            <a class="btn btn-primary btn-xxs itunghela" data-bs-toggle="tooltip" data-bs-placement="top" title="Entri Karyawan Baru">Entri Karyawan </a>
                                            @endif
                                                
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form mb-3">
                                            <div class="row">
                                                @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                                    <div class="col-md-3 mb-3">
                                                        <select required class="form-control cek1 zzzzz" name="unit" id="unit">
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-3 mb-3">
                                                           <select required class="form-control cek2 zzzzz" name="jabata" id="jabata">
                                                           </select>
                                                    </div>
                                                @else
                                                
                                                <div class="col-md-3 mb-3">
                                                    <select required class="form-control cek1 zzzzz" name="unit" id="unit">
                                                        @if(count($units) > 0)
                                                        <option selected="selected" value="">Pilih Unit</option>
                                                        @foreach($units as $u)
                                                        <option value="{{$u->id}}">{{$u->unit}}</option>
                                                        @endforeach
                                                        @else
                                                        <!--<option value="">Tidak Ada</option>-->
                                                        @endif
                                                        </select>
                                                </div>
                                                
                                                 <div class="col-md-3 mb-3">
                                                    <select required class="form-control cek2 zzzzz" name="jabata" id="jabata">
                                                        @if(count($jabatbaru) > 0)
                                                        <option selected="selected" value="">Pilih Jabatan</option>
                                                        @foreach($jabatbaru as $tan)
                                                        <option value="{{$tan->id}}">{{$tan->jabatan}}</option>
                                                        @endforeach
                                                        @else
                                                        <!--<option value="">Tidak ada</option>-->
                                                        @endif
                                                    </select>
                                                </div>
                                                @endif
                                                
                                               

                                                <?php $jenis = App\Models\Karyawan::select('status_kerja')->whereRaw("status_kerja IS NOT NULL")->distinct('status_kerja')->get() ?>

                                                <div class="col-md-2 mb-3">
                                                    <select required class="form-control cek2 zzzzz" name="jenis_t" id="jenis_t">
                                                        <option selected="selected" value="">Pilih Status Kerja</option>
                                                        @foreach($jenis as $tan)
                                                        <option value="{{$tan->status_kerja}}">{{ $tan->status_kerja }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                

                                                <div class="col-md-2 mb-3">
                                                    <select required class="form-control cek3 zzzzz" name="status" id="status">
                                                        <option value="1" selected>Aktif </option>
                                                        <option value="0">Nonaktif</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-3 ">
                                                    <button type="button" class="btn btn-primary btn-sm w-100 dropdown-toggle collapsed" data-bs-toggle="collapse" href="#collapseAdvanceKaryawan" role="button" aria-expanded="false" aria-controls="collapseAdvanceKaryawan">
                                                    Advance
                                                    </button>
                                                </div>    
                                            </div>
                                            <div class="row  d-flex justify-content-center ">
                                                <div class="bg-collaps rounded mt-4">
                                                    <div class="multi-collapse row mt-2 collapse" id="collapseAdvanceKaryawan" style="">
                                                        <div class="col-md-3 mb-3">
                                                            <label>Tanggal Aktif</label>
                                                            <input type="text" class="form-control cek1" autocomplete="off" id="tglAktif" name="tglAktif" range="daterange" placeholder="{{ date('Y-m-d') }} s/d {{ date('Y-m-d') }}">
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label>Tanggal Non Aktif</label>
                                                            <input type="text" class="form-control cek1" autocomplete="off" id="tglNonAktif" name="tglNonAktif" range="daterange" placeholder="{{ date('Y-m-d') }} s/d {{ date('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="user_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>ID</th>
                                                        <th>Nama Karyawan</th>
                                                        <th>Unit Kerja</th>
                                                        <th>Status Kerja</th>
                                                        <th>Jabatan</th>
                                                        <th>Masa Kerja</th>
                                                        <th>Golongan</th>
                                                        <th>NIK</th>
                                                        <th>Tanggal Lahir</th>
                                                        <th>Jenis Kelamin</th>
                                                        <th>Alamat</th>
                                                        <th>Nomor Hp</th>
                                                        <th>E-mail</th>
                                                        <th>Pendidikan</th>
                                                        <th>Jurusan</th>
                                                        <th>Status Nikah</th>
                                                        <th>Pasangan</th>
                                                        <th>Tanggal Lahir</th>
                                                        <th>Tanggal Nikah</th>
                                                        <th>Anak</th>
                                                        <th>Umur</th>
                                                        <th>Status Anak</th>
                                                        <th>Detail</th>
                                                        <th>Edit</th>
                                                        <th>Aktif</th>
                                                        @if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'hrd')
                                                        <th>Hapus</th>
                                                        @endif
                                                        <th></th>
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
    </div>
</div>
@endsection