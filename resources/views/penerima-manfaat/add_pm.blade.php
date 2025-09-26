@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-sm">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">CORE</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerima Manfaat</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Entry Penerima Manfaat</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <div class="row">
            <div class="col-lg-12">
                <form class="form-horizontal" id="simple_form" method="post">
                    <div class="row" style="margin-bottom: 1.875rem">
                        <input type="hidden" id="cek-mail-nohp" value="">
                        <div class="col-lg-12">
                            <div class="basic-form">
                                <div class="mb-4 row d-flex justify-content-center">
                                    <!-- <label class="col-sm-5 col-form-label">Jenis Penerima Manfaat</label> -->
                                    <div class="col-sm-6">
                                        <select required class="form-control default-select wide" name="jenis_pm" id="jenis_pm">
                                            <option value="">Pilih Jenis PM</option>
                                            <option value="personal">Personal</option>
                                            <option value="entitas">Entitas</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-6" id="pr" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Nama PM :</label>
                                                    <input type="text" name="nama" id="nama" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">NIK :</label>
                                                    <input type="number" name="nik" id="nik" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Jenis Kelamin :</label>
                                                    <select required class="form-control default-select wide" name="jk" id="jk">
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="laki-laki">Pria</option>
                                                        <option value="perempuan">Wanita</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Tanggal Lahir :</label>
                                                    <!--<input type="text" name="tahun_lahir" id="tahun_lahir" class="form-control input-sm" value="">-->
                                                    <input type="date" class="form-control" style="width: 100%;" name="tgl_lahir" id="tgl_lahir">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>E-mail :</label>
                                                    <button type="button" id="cek_email" class="badge badge-warning badge-xs" style="float:right;">Cek <i class="fa fa-eye"></i></button>
                                                    <input type="email" name="email" id="email" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Nomor Hp : </label>
                                                    <button type="button" id="cek_hp" class="badge badge-warning badge-xs" style="float:right; ">Cek <i class="fa fa-eye"></i></button>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <b>+62</b>
                                                        </div>
                                                        <input type="number" name="no_hp" id="no_hp" class="form-control input-sm" oninput="cekhpp(this)" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Provinsi :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="provinsi" id="provinsi">
                                                        <option value="">- Pilih Provinsi -</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Kota :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="kota" id="kota">
                                                        <option value="">- Pilih Kota -</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!--<div class="row">-->
                                    <!--    <div class="col-md-4 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">latitude :</label>-->
                                    <!--                <input type="text" name="latitude" id="latitude" class="form-control input-sm" value="">-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->

                                    <!--    <div class="col-md-4 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">longitude :</label>-->
                                    <!--                <input type="text" name="longitude" id="longitude" class="form-control input-sm" value="">-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="row">-->
                                    <!--    <div class="col-md-12 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">Alamat :</label>-->
                                    <!--                <textarea id="alamat" class="form-control input-sm" name="alamat" rows="4" cols="50"></textarea>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6" id="et" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">

                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Lembaga / Kegiatan :</label>
                                                    <input type="text" name="lembaga" id="lembaga" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Nomor Telp :</label>
                                                    <button type="button" id="cek_tlp" class="badge badge-warning badge-xs" style="float:right; ">Cek <i class="fa fa-eye"></i></button>
                                                    <input type="text" name="nohap" id="nohap" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">E-mail :</label>
                                                    <button type="button" id="cek_email_pt" class="badge badge-warning badge-xs" style="float:right; ">Cek <i class="fa fa-eye"></i></button>
                                                    <input type="email" name="email1" id="email1" class="form-control input-sm" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Provinsi :</label>
                                                    <select required class="js-example-basic-single" name="provinsi" id="provinsii">
                                                        <option value="">Pilih Provinsi</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Kota :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="kota" id="kotaa">
                                                        <option value="">Pilih Kota</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--<div class="row">-->
                                    <!--    <div class="col-md-4 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">latitude :</label>-->
                                    <!--                <input type="text" name="latitude1" id="latitude1" class="form-control input-sm" value="">-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->

                                    <!--    <div class="col-md-4 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">longitude :</label>-->
                                    <!--                <input type="text" name="longitude1" id="longitude1" class="form-control input-sm" value="">-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="row">-->
                                    <!--    <div class="col-md-12 mb-3">-->
                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <label for="">Alamat :</label>-->
                                    <!--                <textarea id="alamat1" class="form-control input-sm" name="alamat1" rows="4" cols="50"></textarea>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6" id="pb" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                          <div class="col-md-6 mb-3">
                                                <label></label> 
                                                <label class="form-label">PJ</label> 
                                                   <select class="js-example-basic-single1" style="width: 100%;" name="petugas"  id="petugas">
                                                        <option value="">Pilih Petugas</option>
                                                    </select>
                                        </div>
                                        
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Asnaf :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="asnaf" id="asnaf">
                                                        <option value="">Pilih Asnaf</option>
                                                        @foreach($asnaf as $a)
                                                        <option value="{{$a->id}}">{{$a->asnaf}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--<div class="col-md-6 mb-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <div class="col-md-12">-->
                                        <!--            <label for="">Penanggung Jawab :</label>-->
                                        <!--            <select required class="js-example-basic-single" style="width: 100%;" name="petugas" id="petugas">-->
                                        <!--                <option value="">Pilih Petugas</option>-->
                                        <!--                @foreach ($petugas as $j)-->
                                        <!--                <option value="{{$j->id}}" data-value="{{$j->name}}">{{$j->name}} ({{$j->jabatan}})</option>-->
                                        <!--                @endforeach-->
                                        <!--            </select>-->
                                        <!--        </div>-->
                                        <!--    </div>-->
                                        <!--</div>-->

                                         

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Foto PM :</label>
                                                    <div class="input-group mb-3">
                                                        <div class="form-file">
                                                            <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">
                                                        </div>
                                                        <span class="input-group-text">Upload</span>
                                                    </div>

                                                    <input type="hidden" id="nama_file" value="">
                                                    <input type="hidden" id="base64" value="">
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Kantor :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="id_kantor" id="id_kantor">
                                                        <option value="">Pilih Kantor</option>
                                                        @foreach ($datdon as $j)
                                                        <option value="{{$j->id}}" data-value="{{$j->unit}}">{{$j->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <!--<div class="row">-->
                                       
                                    <!--    <div class="col-md-6">-->

                                    <!--        <div class="form-group">-->
                                    <!--            <div class="col-md-12">-->
                                    <!--                <button type="button" id="simpan" class="btn btn-success btn-sm" style="float: right;width: 100%; margin-top:25px">Simpan</button>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12 mb-3" id="mapa" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row" >
                                        
                                        <div class="col-md-6 mb-3">
                                            <label>Cari Lokasi</label>
                                            <input type="text" id="lok" class="form-control" placeholder="Enter a location">
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Latitude</label>
                                            <input type="text" name="latitude" id="latitude" class="form-control" placeholder="0">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label>Longitude</label>
                                            <input type="text" name="longitude" id="longitude" class="form-control" placeholder="0">
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <div id="map" style="width:100%;height:200px;"></div>
                                        </div>
                                        
                                        <div class="mb-3 col-md-10">
                                            <label>Alamat</label>
                                            <!--<textarea id="alamat1" class="form-control" name="alamat1" rows="6" placeholder="isi alamat donatur"></textarea>-->
                                            <input type="text" name="alamat" id="alamat" class="form-control" placeholder="alamat donatur">
                                        </div>
                                        
                                        <div class="col-md-2 mb-3">
                                            <label>&nbsp;</label><br>
                                            <button type="button" id="simpan" class="btn btn-success btn-sm" style="width: 100%">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection