@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-sm">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Donatur</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Edit Donatur</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div id="myModal" class="modal">
            <span class="btn-close tutup">&times;</span>

            <!-- Modal Content (The Image) -->
            <img class="modal-content" id="img01">

            <!-- Modal Caption (Image Text) -->
            <div id="caption"></div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <form class="form-horizontal" id="simple_form" method="post">
                    <div class="row">
                        <input value="{{ Request::segment(3) }}" type="hidden" id="idboss">
                        <input type="hidden" name="jenis_donatur" id="jenis_donatur" value="{{$data->jenis_donatur}}">
                        <input type="hidden" name="id_donatur" id="id_donatur" value="{{$data->id}}">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="pr" style="display:none;">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="basic-form">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nama Donatur :</label>
                                                                    <input type="text" name="nama" id="nama" class="form-control input-sm" value="{{$data->nama}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">NIK</label>
                                                            <input type="number" min="0" class="form-control" placeholder="NIK" name="nik" id="nik" autocomplete="off" value="{{$data->nik}}">
                                                            <small>*boleh dikosongkan</small>
                                                        </div>

                                                        
                                                    </div>

                                                    <div class="row">
                                                        
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Jenis Kelamin :</label>
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="jk" id="jk">
                                                                        <option value="">- Pilih Jenis Kelamin -</option>
                                                                        <option value="laki-laki" <?= $data->jk == 'laki-laki' ? ' selected="selected"' : ''; ?>>Laki - Laki</option>
                                                                        <option value="perempuan" <?= $data->jk == 'perempuan' ? ' selected="selected"' : ''; ?>>Perempuan</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Tahun Lahir :</label>
                                                                    <!--<input type="text" name="tahun_lahir" id="tahun_lahir" class="form-control input-sm" value="">-->
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="tahun_lahir" id="tahun_lahir">
                                                                        <option value="">Pilih Tahun Lahir</option>
                                                                        @for($i = date('Y'); $i >= date('Y') - 100; $i -= 1) {
                                                                            {{ $cek = $data->tahun_lahir == $i ? 'selected="selected"' : ''; }}
                                                                            <option value="{{ $i }}" {{$cek}} >{{$i}}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">E-mail :</label>
                                                                    <input type="email" name="email" id="email" class="form-control input-sm" value="{{$data->email}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nomor Hp :</label>
                                                                    <input type="text" name="no_hp" id="no_hp" class="form-control input-sm" value="{{$data->no_hp}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Pekerjaan :</label>
                                                                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control input-sm" value="{{$data->pekerjaan}}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!--<div class="row">-->
                                                    <!--    <div class="col-md-4 mb-3">-->
                                                    <!--        <div class="form-group">-->
                                                    <!--            <div class="col-md-12">-->
                                                    <!--                <label for="">Alamat :</label>-->
                                                    <!--                <textarea id="alamat" class="form-control input-sm" name="alamat" rows="4" cols="50">{{$data->alamat}}</textarea>-->
                                                    <!--            </div>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                    <!--    <div class="col-md-4 mb-3">-->
                                                    <!--        <div class="form-group">-->
                                                    <!--            <div class="col-md-12">-->
                                                    <!--                <label for="">latitude :</label>-->
                                                    <!--                <input type="text" name="latitude" id="latitude" class="form-control input-sm" value="{{$data->latitude}}">-->
                                                    <!--            </div>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                    <!--    <div class="col-md-4 mb-3">-->
                                                    <!--        <div class="form-group">-->
                                                    <!--            <div class="col-md-12">-->
                                                    <!--                <label for="">longitude :</label>-->
                                                    <!--                <input type="text" name="longitude" id="longitude" class="form-control input-sm" value="{{$data->longitude}}">-->
                                                    <!--            </div>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                    <!--</div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="et" style="display:none;">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="basic-form">
                                                    <div class="row">
                                                        <div class="col-md-8">

                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Perusahaan :</label>
                                                                    <input type="text" name="perusahaan" id="perusahaan" class="form-control input-sm" value="{{$data->nama}}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">

                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nomor Telp :</label>
                                                                    <input type="text" name="nohap" id="nohap" class="form-control input-sm" value="{{$data->nohap}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">E-mail :</label>
                                                                    <input type="email" name="email" id="email1" class="form-control input-sm" value="{{$data->email}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--<div class="col-md-4 mb-3">-->
                                                        <!--    <div class="form-group">-->
                                                        <!--        <div class="col-md-12">-->
                                                        <!--            <label for="">Provinsi :</label>-->
                                                        <!--            <select required class="js-example-basic-single" style="width: 100%;" name="provinsi" id="provinsii">-->
                                                        <!--                <option value="">- Pilih Provinsi -</option>-->

                                                        <!--            </select>-->
                                                        <!--        </div>-->
                                                        <!--    </div>-->
                                                        <!--</div>-->
                                                        <!--<div class="col-md-4 mb-3">-->
                                                        <!--    <div class="form-group">-->
                                                        <!--        <div class="col-md-12">-->
                                                        <!--            <label for="">Kota :</label>-->
                                                        <!--            <select required class="js-example-basic-single" style="width: 100%;" name="kota" id="kotaa">-->
                                                        <!--                <option value="">- Pilih Kota -</option>-->

                                                        <!--            </select>-->
                                                        <!--        </div>-->
                                                        <!--    </div>-->
                                                        <!--</div>-->
                                                    </div>

                                                    <!--<div class="row">-->
                                                    <!--    <div class="col-md-12">-->
                                                    <!--        <div class="form-group">-->
                                                    <!--            <div class="col-md-12">-->
                                                    <!--                <label for="">Alamat :</label>-->
                                                    <!--                <textarea id="alamat1" class="form-control input-sm" name="alamat1" rows="4" cols="50">{{$data->alamat}}</textarea>-->
                                                    <!--            </div>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                    <!--</div>-->

                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <label>Orang yang ditemui :</label>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nama :</label>
                                                                    <input type="text" name="orng_dihubungi" id="orng_dihubungi" class="form-control input-sm" value="{{$data->orng_dihubungi}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nomor Hp :</label>
                                                                    <input type="text" name="no_hp" id="no_hp2" class="form-control input-sm" value="{{$data->no_hp}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Jabatan :</label>
                                                                    <input type="text" name="jabatan" id="jabatan" class="form-control input-sm" value="{{$data->jabatan}}">
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
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="pr1" style="display:none;">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="basic-form">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Sumber Dana :</label>
                                                                    <select required class="js-example-basic-single cb" style="width: 100%;" name="sumdan" id="sumdan">

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Nama Program :</label>
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="program" id="program">
                                                                        <option>- Pilih Program -</option>

                                                                    </select>

                                                                    <input type="hidden" id="index1" name="index1" value="00">
                                                                    <input type="hidden" id="action" name="action" value="add">

                                                                    <input type="hidden" id="uwu" name="action">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Petugas SO :</label>
                                                                    <select required class="asssa" style="width: 100%;" name="id_peg" id="id_peg">

                                                                        <option value="">- Pilih Petugas -</option>
                                                                        @foreach ($pet_so as $j)
                                                                        <option value="{{$j->id}}" data-value="{{$j->name}}">{{$j->name}} ({{$j->jabatan}})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class=" col-md-6 mb-3" style="margin-top:25px" id="div1">
                                                            <button type="button" id="tam_prog" class="btn btn-primary btn-sm" style="width: 100%">Tambah</button>
                                                        </div>
                                                        <div class=" col-md-3" style="margin-top:25px; display: none" id="div2">
                                                            <button type="button" id="bat_prog" class="btn btn-danger btn-sm" style="width: 100%">Batal</button>
                                                        </div>
                                                        <hr>
                                                        <div class="col-md-12" style="margin-top:20px">
                                                            <table id="user_table_1" class="table table-bordered ">
                                                                <thead>
                                                                    <tr>
                                                                        <th>SO</th>
                                                                        <th>Nama Program</th>
                                                                        <th>status</th>


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
                                        
                                        <!--<div class="col-md-2 mb-3">-->
                                            <!--<label>Reset</label>-->
                                        <!--    <button type="button" id="cuak" class="btn btn-sm btn-danger" style="margin-top: 28px">Reset</button>-->
                                        <!--</div>-->
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Latitude</label>
                                            <input type="text" name="latitude" id="latitude" class="form-control" placeholder="0" value="{{$data->latitude}}">
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Longitude</label>
                                            <input type="text" name="longitude" id="longitude" class="form-control" placeholder="0" value="{{$data->longitude}}">
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <div id="map" style="width:100%;height:200px;"></div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Provinsi :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="provinsi" id="provinsi">
                                                        <option value="">- Pilih Provinsi -</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Kota :</label>
                                                    <select required class="js-example-basic-single" style="width: 100%;" name="kota" id="kota">
                                                        <option value="">- Pilih Kota -</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Kecamatan</label>
                                            <input type="text" name="kec" id="kec" class="form-control" placeholder="Nama Kecamatan" value="{{$data->kecamatan}}">
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Desa/Kelurahan</label>
                                            <input type="text" name="des" id="des" class="form-control" placeholder="Nama Desa/Kelurahan" value="{{$data->desa}}">
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>RT/RW</label>
                                            <input type="text" name="rtrw" id="rtrw" class="form-control" placeholder="000/000" value="{{$data->rtrw}}">
                                            <small>*boleh dikosongkan</small>
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label>Lainnya</label>
                                            <input type="text" name="lainnya" id="lainnya" class="form-control" placeholder="Nama Blok/Jln" value="{{$data->alamat_detail}}">
                                            <small>*boleh dikosongkan</small>
                                        </div>
                                        
                                        <div class="mb-3 col-md-10">
                                            <label>Alamat Berdasarkan Marker</label>
                                            <!--<textarea id="alamat1" class="form-control" name="alamat1" rows="6" placeholder="isi alamat donatur"></textarea>-->
                                            <input type="text" name="alamat" id="alamat" class="form-control" placeholder="alamat donatur" value="{{$data->alamat}}">
                                        </div>
                                        
                                        <div class="mb-3 col-md-2">
                                            <label>&nbsp;</label><br>
                                            <button class="btn btn-sm btn-danger" type="button" id="reli" disabled>Reset Lokasi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-12">
                                    <div id="pb" style="display:none;">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="basic-form">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Pembayaran :</label>
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="pembayaran" id="pembayaran">
                                                                        <option value="">- Pilih Pembayaran -</option>
                                                                        <option value="transfer" <?= $data->pembayaran == 'transfer' ? ' selected="selected"' : ''; ?>>Tansfer</option>
                                                                        <option value="dijemput" <?= $data->pembayaran == 'dijemput' ? ' selected="selected"' : ''; ?>>Dijemput</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Foto Donatur :</label>
                                                                    <img id="output" style="height:80px; margin-bottom:10px; display:none" src="https://kilauindonesia.org/kilau/gambarDonatur/{{$data->gambar_donatur }}">
                                                                    
                                                                    <a href="https://kilauindonesia.org/kilau/gambarDonatur/{{$data->gambar_donatur }}" class="btn btn-warning btn-xxs mb-2" target="_blank" style="float:right;">Lihat <i class="fa fa-eye"></i></a>
                                                                    <!--<button type="button" id="lihatgmb" class="btn btn-warning btn-xxs mb-2" style="float:right; display:block">Lihat <i class="fa fa-eye"></i></button>-->
                                                                    
                                                                    <div class="input-group">
                                                                        <div class="form-file">
                                                                            <input type="file" class="form-control form-file-input" accept="image/*" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!--<input type="file" class="form-control input-sm" onchange="encodeImageFileAsURL(this)" name="foto" id="foto">-->

                                                                    <input type="hidden" id="nama_file" value="">
                                                                    <input type="hidden" id="base64" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">

                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Petugas :</label>
                                                                    <select required class="asssa" style="width: 100%;" name="petugas" id="petugas">

                                                                        <option value="">- Pilih Petugas -</option>
                                                                        @foreach ($pet_so as $j)
                                                                        <option value="{{$j->id}}" data-value="{{$j->name}}" <?= $data->id_koleks == $j->id ? ' selected="selected"' : ''; ?>>{{$j->name}} ({{$j->jabatan}})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Jalur :</label>
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="jalur" id="jalur">

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label for="">Kantor :</label>
                                                                    <select required class="js-example-basic-single" style="width: 100%;" name="id_kantor" id="id_kantor">
                                                                        <option value="">- Pilih Kantor -</option>
                                                                        @foreach ($datdon as $j)
                                                                        <option value="{{$j->id}}" data-value="{{$j->unit}}" <?= $data->id_kantor == $j->id ? ' selected="selected"' : ''; ?>>{{$j->unit}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <!--<img id="output" style="height:80px; margin-bottom:10px;" src="{{asset('gambarDonatur/'.$data->gambar_donatur)}}" >-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="col-md-6 mb-3">
                                                                    <button type="button" id="simpan" class="btn btn-success btn-sm" style="float: right;width: 100%; margin-top:8px">Simpan</button>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Riwayat Transaksi</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="user_tables">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>ID Transaksi</th>
                                                <th>Program</th>
                                                <th>Nominal</th>
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>Σ Total : </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
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