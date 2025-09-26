@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <?php
        $auth = Auth::user()->id_com;
        $kyn = DB::select("SELECT * from tambahan WHERE id_com = '$auth' ");
        $jab = DB::select("SELECT * from jabatan WHERE id_com = '$auth' ");
        ?>
        
        <!--<div id="myModal" class="modal">-->
        <!--    <span class="btn-close tutup"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img01">-->
        <!--    <div id="caption"></div>-->
        <!--</div>-->
        
        <!--<div id="myModal1" class="modal">-->
        <!--    <span class="btn-close tutup2"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img02">-->

            <!-- Modal Caption (Image Text) -->
        <!--    <div id="caption1"></div>-->
        <!--</div>-->
        
        <!--<div id="myModal2" class="modal">-->
        <!--    <span class="btn-close tutup3"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img03">-->

            <!-- Modal Caption (Image Text) -->
        <!--    <div id="caption2"></div>-->
        <!--</div>-->

        <div class="row">
            <form class="form-horizontal" method="post" id="simple_form">
                @csrf
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
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
                                                                <input type="text" name="nama" class="form-control " id="nama" aria-describedby="" value="{{$karyawan->nama}}" placeholder="Nama Karyawan">
                                                                <input type="hidden" id="id_karyawan" name="id_karyawan" value="{{$karyawan->id_karyawan}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label for="">NIK</label>
                                                                <input type="text" name="nik" class="form-control " id="nik" value="{{$karyawan->nik}}" aria-describedby="" placeholder="NIK">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>Tanggal Lahir</label>
                                                                <input type="date" name="ttl" class="form-control " id="ttl" value="{{$karyawan->ttl}}" placeholder="Tanggal Lahir">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label>Jenis Kelamin</label>
                                                                <select required id="jk" class="form-control  js-example-basic-single" style="width: 100%;" name="jk">
                                                                    <option selected="selected" value="">- Pilih Jenis Kelamin -</option>
                                                                    <option value="Pria" {{$karyawan->jk == 'Pria' ? 'selected' : ''}}>Pria</option>
                                                                    <option value="Wanita" {{$karyawan->jk == 'Wanita' ? 'selected' : ''}}>Wanita</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label for="">E-mail</label>
                                                                <input type="email" name="email" class="form-control " id="email" aria-describedby="" value="{{$karyawan->email}}" placeholder="Email Aktif">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label for="">Foto Identitas</label>
                                                                <a href="{{ url('/upload/'.$karyawan->gambar_identitas) }}" class="btn btn-warning btn-xxs mb-2" target="_blank" style="float:right;">Lihat <i class="fa fa-eye"></i></a>
                                                                <!--<button type="button" id="lihatgmb" class="btn btn-warning btn-xxs mb-2" style="float:right;">Lihat <i class="fa fa-eye"></i></button>-->
                                                                <img id="output" style="height:80px; margin-bottom:10px; display:none" src="{{ url('/upload/'.$karyawan->gambar_identitas) }}">
                                                                
                                                                <div class="input-group">
                                                                    <div class="form-file">
                                                                        <input type="file" name="gambar_identitas" class="form-control form-file-input gambar_identitas" aria-describedby="" value="" id="limit1mb1" accept="image/*" onchange="encodeImageFileAsURL_0(this)">
                                                                    </div>
                        											<!--<span class="input-group-text">Upload</span>-->
                                                                </div>
                                                                
                                                                <!--<input type="file" name="gambar_identitas" class="form-control  gambar_identitas" aria-describedby="" value="" id="limit1mb1" accept="image/*" onchange="encodeImageFileAsURL_0(this)">-->

                                                                <input type="hidden" id="nama_file_0" value="">
                                                                <input type="hidden" id="base64_0" value="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label for="">Nomor HP</label>
                                                                <input type="text" name="nomerhp" class="form-control " id="nomerhp" value="{{$karyawan->nomerhp}}" aria-describedby="" placeholder="Nomor Hp Aktif">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label for="">Hobi</label>
                                                                <input type="text" name="hobi" class="form-control " id="hobi" aria-describedby="" value="{{$karyawan->hobi}}" placeholder="Hobi Anda Apa">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label for="">No Rek</label>
                                                                <input type="number" min="0" name="norek" class="form-control " id="norek" aria-describedby="" value="{{$karyawan->no_rek}}" placeholder="No Rekening">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="col-md-12 mb-3">
                                                                <label for="">Alamat</label>
                                                                <textarea id="alamat" class="form-control " name="alamat" rows="4" cols="50" placeholder="Alamat Sesuai KTP" style="height: 100px">{{$karyawan->alamat}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        
                                                        <div class="form-group">
                                                            <div class="col-md-12 mb-3">
                                                            <button type="button" class="btn btn-success btn-sm " id="simpan" style="width:100%; margin-bottom: -20px">Simpan Karyawan</button>
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

                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <label>Pendidikan Terakhir</label>
                                                            <select required id="pendidikan" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="pendidikan">
                                                                <option selected="selected" value="">- Pilih Pendidikan Terakhir -</option>
                                                                <option value="S3" {{$karyawan->pendidikan == 'S3' ? 'selected' : ''}}>S3</option>
                                                                <option value="S2" {{$karyawan->pendidikan == 'S2' ? 'selected' : ''}}>S2</option>
                                                                <option value="S1" {{$karyawan->pendidikan == 'S1' ? 'selected' : ''}}>S1</option>
                                                                <option value="D4" {{$karyawan->pendidikan == 'D4' ? 'selected' : ''}}>D4</option>
                                                                <option value="D3" {{$karyawan->pendidikan == 'D3' ? 'selected' : ''}}>D3</option>
                                                                <option value="SMA" {{$karyawan->pendidikan == 'SMA' ? 'selected' : ''}}>SMA</option>
                                                                <option value="SMP" {{$karyawan->pendidikan == 'SMP' ? 'selected' : ''}}>SMP</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="">Nama Sekolah / Perguruan Tinggi</label>
                                                            <input type="text" name="nm_sekolah" id="nm_sekolah" class="form-control input-sm" aria-describedby="" value="{{$karyawan->nm_sekolah}}" placeholder="Nama Sekolah / Perguruan Tinggi">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="">Jurusan</label>
                                                            <input type="text" name="jurusan" id="jurusan" class="form-control input-sm" aria-describedby="" value="{{$karyawan->jurusan}}" placeholder="Nama Jurusan">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <input type="hidden" name="password" id="password" value="123456789" class="form-control" aria-describedby="" placeholder="Nama Sekolah / Perguruan Tinggi">
                                                            <label for="">Tahun Lulus</label>
                                                            <input type="text" name="th_lulus" id="th_lulus" class="form-control input-sm" aria-describedby="" value="{{$karyawan->th_lulus}}" placeholder="Tahun Lulus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="">Gelar (Opsional)</label>
                                                            <input type="text" name="gelar" id="gelar" class="form-control input-sm" aria-describedby="" value="{{$karyawan->gelar}}" placeholder="Gelar Pendidikan">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="">Scan Ijazah</label>
                                                            <img id="output2" style="height:80px; margin-bottom:10px; display:none" src="{{url('/upload/'.$karyawan->ijazah)}}">
                                                            <!--<button type="button" id="lihatgmb3" class="btn btn-warning btn-xxs mb-2" style="float:right;">Lihat <i class="fa fa-eye"></i></button>-->
                                                            <a href="{{ url('/upload/'.$karyawan->ijazah) }}" class="btn btn-warning btn-xxs mb-2" target="_blank" style="float:right;">Lihat <i class="fa fa-eye"></i></a>
                                                            
                                                            <div class="input-group">
                                                                <div class="form-file">
                                                                    <input type="file" name="ijazah" class="form-control form-file-input input-sm ijazah" aria-describedby="ijazah" id="limit1mb2" accept="image/*" onchange="encodeImageFileAsURL_1(this)" value="">
                                                                </div>
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
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection