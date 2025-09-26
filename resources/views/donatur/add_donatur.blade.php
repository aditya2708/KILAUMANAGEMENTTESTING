@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-sm">

        <div class="row">
            <div class="col-md-12" id="info-don" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Info Donatur</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_tabelek" class="display">
                                <thead>
                                    <tr>
                                        <th class="cari">Nama Donatur</th>
                                        <th width="15%">Program</th>
                                        <th class="cari">Nomor HP</th>
                                        <th width="20%">Alamat</th>
                                        <th class="cari">Status</th>
                                        <th width="15%">Kelola</th>
                                        <th width="17%">Warning</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_dup">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="form-horizontal" id="simple_form" method="post">
            <div class="row" style="margin-bottom: 1.875rem">
                <!-- <div class="card">
                    <div class="card-body"> -->
                <div class="col-lg-12">
                    <div class="mb-4 row d-flex justify-content-center">
                        <!--<label class="col-sm-2 col-form-label">Jenis Donatur</label>-->
                        <div class="col-sm-6">
                            <select id="jenis_donatur" name="jenis_donatur" class="default-select form-control wide">
                                <option value="">- Pilih Jenis Donatur -</option>
                                <option value="personal">Personal</option>
                                <option value="entitas">Entitas</option>
                            </select>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <input type="hidden" id="cek-mail-nohp" value="">
                <div class="col-lg-6 mb-3" id="pr" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Donatur</label>
                                        <input type="text" class="form-control" placeholder="Nama Donatur" name="nama" id="nama">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">NIK</label>
                                        <input type="number" min="0" class="form-control" placeholder="NIK" name="nik" id="nik" autocomplete="off">
                                        <small>*boleh dikosongkan</small>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select name="jk" id="jk" class="form-control wide">
                                            <option value="">- Pilih Jenis Kelamin -</option>
                                            <option value="laki-laki">Pria</option>
                                            <option value="perempuan">Wanita</option>
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Tahun Lahir</label>
                                        <select name="tahun_lahir" id="tahun_lahir" class="donatur-select form-control wide">
                                            <option value="">- Pilih Tahun Lahir -</option>
                                            @for($i = date('Y'); $i >= date('Y') - 100; $i -= 1)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="mb-3 col-md-4">
                                        <label>Email</label>
                                        <span class="badge badge-xs badge-warning " style="float: right; cursor: pointer" id="cek_email">Cek <i class="fa fa-eye"></i></span>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="info@gmail.com">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label>Nomor Hp </label>
                                        <span class="badge badge-xs badge-warning " style="float: right; cursor: pointer" id="cek_hp">Cek <i class="fa fa-eye"></i></span>
                                        <input type="text" class="form-control" name="no_hp" id="no_hp" placeholder="nomor HP">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label>Pekerjaan</label>
                                        <input type="text" class="form-control" name="pekerjaan" id="pekerjaan" placeholder="Pekerjaan">
                                    </div>
                                    
                                    
                                    <!--<div class="mb-3 col-md-4">-->
                                    <!--    <label>Provinsi</label>-->
                                    <!--    <select required class="donatur-select form-control" name="provinsi" id="provinsi">-->
                                    <!--        <option value="">- Pilih Provinsi -</option>-->

                                    <!--    </select>-->
                                    <!--</div>-->
                                    <!--<div class="mb-3 col-md-4">-->
                                    <!--    <label>Kota</label>-->
                                    <!--    <select required class="donatur-select form-control" name="kota" id="kota">-->
                                    <!--        <option value="">- Pilih Kota -</option>-->

                                    <!--    </select>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3" id="et" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-8">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" placeholder="Nama Perusahaan" name="perusahaan" id="perusahaan">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">No Telepon</label>
                                        <span class="badge badge-xs badge-warning " style="float: right; cursor: pointer" id="cek_tlp">Cek <i class="fa fa-eye"></i></span>
                                        <input type="text" name="nohap" id="nohap" class="form-control" placeholder="no telepon perusahaan">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Email</label>
                                        <span class="badge badge-xs badge-warning " style="float: right; cursor: pointer" id="cek_email_pt">Cek <i class="fa fa-eye"></i></span>
                                        <input type="email" name="email1" id="email1" class="form-control" placeholder="contoh@gmail.com">
                                    </div>
                                    <!--<div class="mb-3 col-md-4">-->
                                    <!--    <label>Provinsi</label>-->
                                    <!--    <select required class="donatur-select form-control" name="provinsi" id="provinsii">-->
                                    <!--        <option value="">- Pilih Provinsi -</option>-->
                                    <!--    </select>-->
                                    <!--</div>-->
                                    <!--<div class="mb-3 col-md-4">-->
                                    <!--    <label>Kota</label>-->
                                    <!--    <select required class="donatur-select form-control" name="kota" id="kotaa">-->
                                    <!--        <option value="">- Pilih Kota -</option>-->
                                    <!--    </select>-->
                                    <!--</div>-->
                                    
                                    <div class="mb-3 col-md-4">
                                        <label>Orang yang ditemui</label>
                                        <input type="text" class="form-control" name="orng_dihubungi" id="orng_dihubungi" placeholder="perwakilan">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label>Nomor Hp</label>
                                        <input type="text" class="form-control" name="no_hp2" id="no_hp2" placeholder="nomor hp perwakilan">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label>Jabatan</label>
                                        <input type="text" class="form-control" name="jabatan" id="jabatan" placeholder="jabatan perwakilan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-3" id="pr1" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Sumber Dana</label>
                                        <select name="sumdan" id="sumdan" class="donatur-select form-control wide cb">
                                            <option value="">Tidak ada</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Program</label>
                                        <select name="program" id="program" class="donatur-select form-control wide">
                                            <option value="">Tidak ada</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-8">
                                        <label class="form-label">Petugas SO</label>
                                        <select name="id_peg" id="id_peg" class="donatur-select select2boy form-control wide donatur-select">
                                            <option value="" disabled selected>Tidak ada</option>
                                            @foreach ($petugas as $j)
                                            <option value="{{$j->id}}" data-value="{{$j->name}}">{{$j->name}} ({{$j->jabatan}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4" style="margin-top:25px">
                                        <button type="button" id="tam_prog" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <div class="table-responsive">
                                            <table id="user_table_1" class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th>SO</th>
                                                        <th>Nama Program</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="table">

                                                </tbody>
                                                <tfoot id="foot">

                                                </tfoot>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                
                                <div class="mb-3 col-md-3">
                                    <label>Provinsi</label>
                                    <!--<input type="text" name="prov" id="prov" class="form-control" placeholder="Nama Provinsi">-->
                                    <select required class="donatur-select form-control" name="provinsi" id="provinsi">-->
                                        <option value="">- Pilih Provinsi -</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-3">
                                    <label>Kabupaten/Kota</label>
                                    <select required class="donatur-select form-control" name="kota" id="kota">
                                        <option value="">- Pilih Kabupaten/Kota -</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-3">
                                    <label>Kecamatan</label>
                                    <input type="text" name="kec" id="kec" class="form-control" placeholder="Nama Kecamatan">
                                </div>
                                
                                <div class="mb-3 col-md-3" style="display: none">
                                    <label>Desa/Kelurahan</label>
                                    <input type="text" name="des" id="des" class="form-control" placeholder="Nama Desa/Kelurahan">
                                </div>
                                
                                <div class="mb-3 col-md-3" style="display: none">
                                    <label>RT/RW</label>
                                    <input type="text" name="rtrw" id="rtrw" class="form-control" placeholder="000/000">
                                    <small>*boleh dikosongkan</small>
                                </div>
                                
                                <div class="mb-3 col-md-3" style="display: none">
                                    <label>Lainnya</label>
                                    <input type="text" name="lainnya" id="lainnya" class="form-control" placeholder="Nama Blok/Jln">
                                    <small>*boleh dikosongkan</small>
                                </div>
                                
                                <div class="mb-3 col-md-12">
                                    <label>Alamat Berdasarkan Marker</label>
                                    <!--<textarea id="alamat1" class="form-control" name="alamat1" rows="6" placeholder="isi alamat donatur"></textarea>-->
                                    <input type="text" autocomplete="off" name="alamat" id="alamat" class="form-control" placeholder="alamat donatur">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 mb-3" id="pb" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="label-form">Pembayaran :</label>
                                        <select required class="donatur-select form-control" name="pembayaran" id="pembayaran">
                                            <option value="">- Pilih Pembayaran -</option>
                                            <option value="transfer">Tansfer</option>
                                            <option value="dijemput">Dijemput</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="label-form">Foto Donatur :</label>
                                        <div class="form-file">
                                            <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">
                                        </div>
                                        <input type="hidden" id="nama_file" value="">
                                        <input type="hidden" id="base64" value="">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="label-form">Petugas :</label>
                                        <select required class="donatur-select form-control select2boy" name="petugas" id="petugas">

                                            <option value="">Pilih Petugas</option>
                                            @foreach ($petugas as $j)
                                            <option value="{{$j->id}}" data-value="{{$j->name}}">{{$j->name}} ({{$j->jabatan}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="label-form">Jalur</label>
                                        <select required class="donatur-select form-control" name="jalur" id="jalur">

                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="label-form">Kantor :</label>
                                        <select required class="form-control" name="id_kantor" id="id_kantor">
                                            <option value="">Pilih Kantor</option>
                                            @foreach ($datdon as $j)
                                            <option value="{{$j->id}}" data-value="{{$j->unit}}">{{$j->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <button type="button" id="simpan" class="btn btn-success btn-sm" style="float: right;width: 100%; margin-top:25px">Simpan</button>
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
@endsection