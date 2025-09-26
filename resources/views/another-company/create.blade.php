@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Entry Karyawan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $kyn = \DB::select("SELECT * from tambahan ");
        $jab = \DB::select("SELECT * from jabatan ");
        ?>
        

        <div class="row">
            
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label for="">ID Karyawan</label>
                                                    <input type="text" name="id_kar" class="form-control input-sm" id="id_kar" aria-describedby="" placeholder="ID Karyawan">
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
                    
                        
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label for="">Nama</label>
                                                    <input type="text" name="nama" class="form-control input-sm" id="nama" aria-describedby="" placeholder="Nama Karyawan">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">NIK</label>
                                                    <input type="text" name="nik" class="form-control input-sm" id="nik" aria-describedby="" placeholder="NIK">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Tanggal Lahir</label>
                                                    <input type="date" name="ttl" class="form-control input-sm" id="ttl" placeholder="Tanggal Lahir">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label>Jenis Kelamin</label>
                                                    <select required id="jk" class="form-control default-select wide" name="jk">
                                                        <option selected="selected" value="">Pilih Jenis Kelamin</option>
                                                        <option value="Pria">Pria</option>
                                                        <option value="Wanita">Wanita</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label for="">E-mail</label>
                                                    <input type="email" name="email" class="form-control" id="email" aria-describedby="" placeholder="Email Aktif">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label>Status Pernikahan</label>
                                                    <select required id="status_nikah" class="form-control  wide" name="status_nikah">
                                                        <option selected="selected" value="">Pilih Status Pernikahan</option>
                                                        <option value="Menikah">Menikah</option>
                                                        <option value="Belum Menikah">Belum Menikah</option>
                                                        <option value="Bercerai">Bercerai</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label for="">Nomor HP</label>
                                                    <input type="text" name="nomerhp" class="form-control input-sm" id="nomerhp" aria-describedby="" placeholder="Nomor Hp Aktif">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Hobi</label>
                                                    <input type="text" name="hobi" class="form-control input-sm" id="hobi" aria-describedby="" placeholder="Hobi Anda Apa">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Foto Identitas

                                                    </label>
                                                    <img id="output" style="height:80px; margin-bottom:10px; display:none" src="">
                                                    
                                                    <button type="button" id="lihatgmb" class="btn btn-warning btn-xxs mb-2" style="float:right; display:none">Lihat <i class="fa fa-eye"></i></button>
                                                    
                                                    <div class="input-group mb-3">
                                                        <div class="form-file">
                                                            <input type="file" name="gambar_identitas" class="form-file-input form-control  gambar_identitas" aria-describedby="" value="" id="limit1mb1" accept="image/*" onchange="encodeImageFileAsURL_0(this)">
                                                        </div>
                                                        <span class="input-group-text">Upload</span>
                                                    </div>

                                                    <input type="hidden" id="nama_file_0" value="">
                                                    <input type="hidden" id="base64_0" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Alamat</label>
                                                    <textarea id="alamat" class="form-control " name="alamat" rows="4" cols="50" placeholder="Alamat Sesuai KTP" style="height: 200px;"></textarea>
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

            <div class="col-xl-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3" id="pendidikan_t" style="display: block">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label>Pendidikan Terakhir</label>
                                                <select required id="pendidikan" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="pendidikan">
                                                    <option selected="selected" value="">Pilih Pendidikan Terakhir</option>
                                                    <option value="S3">S3</option>
                                                    <option value="S2">S2</option>
                                                    <option value="S1">S1</option>
                                                    <option value="D4">D4</option>
                                                    <option value="D3">D3</option>
                                                    <option value="SMA">SMA</option>
                                                    <option value="SMP">SMP</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="">Nama Sekolah / Perguruan Tinggi</label>
                                                <input type="text" name="nm_sekolah" id="nm_sekolah" class="form-control input-sm" aria-describedby="" placeholder="Nama Sekolah / Perguruan Tinggi">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="">Jurusan</label>
                                                <input type="text" name="jurusan" id="jurusan" class="form-control input-sm" aria-describedby="" placeholder="Nama Jurusan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" id="tahun_l" style="display: block">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <input type="hidden" name="password" id="password" value="123456789" class="form-control" aria-describedby="" placeholder="Nama Sekolah / Perguruan Tinggi">
                                                <label for="">Tahun Lulus</label>
                                                <input type="text" name="th_lulus" id="th_lulus" class="form-control input-sm" aria-describedby="" placeholder="Tahun Lulus">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" id="gelar" style="display: block">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="">Gelar (Opsional)</label>
                                                <input type="text" name="gelar" id="gelar" class="form-control input-sm" aria-describedby="" placeholder="Gelar Pendidikan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" id="scan_i" style="display: block">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="">Scan Ijazah</label>
                                                <img id="output2" style="height:80px; margin-bottom:10px; display:none" src="">
                                                <button type="button" id="lihatgmb3" class="btn btn-warning btn-xxs mb-2" style="float:right; display:none">Lihat <i class="fa fa-eye"></i></button>
                                                <div class="input-group mb-3">
                                                    <div class="form-file">
                                                        <!-- <input type="file" name="scan_kk" class="form-file-input form-control input-sm scan_kk" aria-describedby="" id="limit1mb3" accept="image/*" onchange="encodeImageFileAsURL_2(this)" value=""> -->
                                                        <input type="file" name="ijazah" class="form-control input-sm ijazah" aria-describedby="ijazah" id="limit1mb2" accept="image/*" onchange="encodeImageFileAsURL_1(this)" value="">
                                                    </div>
                                                    <span class="input-group-text">Upload</span>
                                                </div>

                                                <input type="hidden" id="nama_file_1" value="">
                                                <input type="hidden" id="base64_1" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="myDIV" style="display:none">
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="tj_pas" id="tj_pas"> Karyawan ini memiliki Pasangan di Perusahaan
                                                </label>
                                            </div>
                                        </div>
                                        <div id="pass" style="display:none">
                                            <div class="col-md-12 mb-3">
                                                <div class="checkbox">
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
                                                <div class="col-md-12">
                                                    <label for="">No KK</label>
                                                    <input type="text" name="no_kk" class="form-control input-sm" id="no_kk" aria-describedby="" placeholder="Nomor Kartu Keluarga">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group" id="scan_kk">
                                                <div class="col-md-12">
                                                    <label for="">Scan KK</label>
                                                    <img id="output3" style="height:80px; margin-bottom:10px; display: none" src="">
                                                    
                                                    <button type="button" id="lihatgmb2" class="btn btn-warning btn-xxs mb-2" style="float:right; display:none">Lihat <i class="fa fa-eye"></i></button>

                                                    <div class="input-group mb-3">
                                                        <div class="form-file">
                                                            <input type="file" name="scan_kk" class="form-file-input form-control input-sm scan_kk" aria-describedby="" id="limit1mb3" accept="image/*" onchange="encodeImageFileAsURL_2(this)" value="">
                                                        </div>
                                                        <span class="input-group-text">Upload</span>
                                                    </div>

                                                    <input type="hidden" id="nama_file_2" value="">
                                                    <input type="hidden" id="base64_2" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3" id="nampas0" style="display:block">
                                            <div class="form-group" id="nm_pasangan">
                                                <div class="col-md-12">
                                                    <label for="">Nama Suami / Istri</label>
                                                    <input type="text" name="nm_pasangan" id="nm_pasangan1" class="form-control input-sm " aria-describedby="" placeholder="Nama Suami / Istri">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3" id="nampas1" style="display:none">
                                            <div class="form-group">
                                                <div class="col-md-12">

                                                    <label>Pasangan</label>
                                                    <select id="id_pasangan" class="form-control input-sm select-pass" name="id_pasangan">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group" id="tgl_lahir">
                                                <div class="col-md-12">
                                                    <label for="">Tanggal Lahir</label>
                                                    <input type="date" name="tgl_lahir" id="tgl_lahir1" class="form-control input-sm" aria-describedby="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group" id="tgl_nikah">
                                                <div class="col-md-12">
                                                    <label for="">Tanggal Nikah</label>
                                                    <input type="date" name="tgl_nikah" id="tgl_nikah1" class="form-control input-sm" aria-describedby="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" id="tam_sum">Tambah Suami / Istri</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="tab_pasangan" style="display: none">
                                        <div class="col-md-12 mb-3">
                                            <table id="user_table_1" class="table table-bordered ">
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
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group" id="nm_anak1">
                                                <div class="col-md-12">
                                                    <label for="">Nama Anak </label>
                                                    <input type="text" name="nm_anak" class="form-control input-sm" id="nama_anak1" aria-describedby="" placeholder="Nama Anak">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group" id="nm_anak1">
                                                <div class="col-md-12">
                                                    <label for="">Tanggal Lahir</label>
                                                    <input type="date" name="tgl_lahir_anak" class="form-control input-sm" id="tgl_lahir_anak1" aria-describedby="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group" id="nm_anak1">
                                                <div class="col-md-12">
                                                    <label for="">Status Anak</label>
                                                    <select class="form-control input-sm js-example-basic-single" style="width: 100%;" name="status_anak" id="status_anak1">
                                                        <option value="">- Pilih Status -</option>
                                                        <option value="Menikah">Menikah</option>
                                                        <option value="Belum Menikah">Belum Menikah</option>
                                                        <option value="Meninggal">Meninggal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" id="tam_anak">Tambah Anak</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="tab_anak" style="display: none">
                                        <div class="col-md-12 mb-3">
                                            <table id="user_table_1" class="table table-bordered ">
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
            </div>

            <div class="col-xl-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success btn-sm " id="simpan" style="width:100%">Tambah Karyawan</button>
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

<div id="myModal" class="modal">
    <span class="close tutup">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
</div>
<div id="myModal1" class="modal">
    <span class="close tutup2">&times;</span>
    <img class="modal-content" id="img02">
    <div id="caption1"></div>
</div>
<div id="myModal2" class="modal">
    <span class="close tutup3">&times;</span>
    <img class="modal-content" id="img03">
    <div id="caption2"></div>
</div>
@endsection