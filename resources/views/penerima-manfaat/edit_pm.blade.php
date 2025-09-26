@extends('template')

@section('konten')
    <div class="content-body">
        <div class="container-sm">
            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Edit Penerima Manfaat</a></li>
                </ol>
            </div>
                <div class="col-lg-12 mb-3" id="pe" style="display:none;"> 
                    <div class="row">
                        <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label>ID PM</label>
                                                        <input type="text" name="idnya" id="idnya" readonly class="form-control input-sm" value="{{ $data->id }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Jenis PM</label>
                                                <select required class="form-control input-sm jenis_pmb" name="edjenis" id="edjenis">
                                                    <option value="personal">Perorangan</option>
                                                    <!--<option selected value="personal" {{ $data->jenis_pm == 'personal' ? ' selected="selected"' : '' }}>Perorangan</option>-->

                                                    <option value="entitas" >Entitas</option>
                                                </select>
                                            </div>
                                        
                                                <div class="col-md-4 mb-3">
                                                    <div form-group>
                                                        <div class="col-md-12">
                                                            <label>NIK</label>
                                                            <input type="text" name="ednik" id="ednik" class="form-control input-sm" value="{{ $data->nik }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>Nama</label>
                                                            <input type="text" name="ednamas" id="ednamas" class="form-control input-sm" value="{{ $data->penerima_manfaat }}" >
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Jenis Kelamin</label>
                                                    <select required class="form-control input-sm" name="edjk" id="edjk">
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="laki-laki" {{ $data->jk == 'laki-laki' ? ' selected="selected"' : '' }}>Laki - Laki</option>
                                                        <option value="perempuan" {{ $data->jk == 'perempuan' ? ' selected="selected"' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Tanggal Lahir</label>
                                                    <input type="date" name="edttl" class="form-control" id="edttl" value="{{ $data->tgl_lahir }}" placeholder="Tanggal">
                                                </div>

                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Penangung Jawab</label>
                                                    <select id="edpj" class="form-control input-sm" name="edpj">
                                                        @foreach ($h1 as $item)
                                                            <option value="{{ $item['id'] }}" {{ $data->nama_pj == $item['id'] ? 'selected' : '' }}>
                                                                {{ $item['nama'] }} ({{ $item['jabatan'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Asnaf</label>
                                                    <select id="edasnaf" class="form-control input-sm" name="edasnaf">
                                                        @foreach ($asnaf as $item)
                                                            <option value="{{ $item->id }}" {{ $data->asnaf == $item->id ? 'selected' : '' }}>
                                                                {{ $item->asnaf }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>HP</label>
                                                            <input type="text" name="edhp" id="edhp" class="form-control input-sm" value="{{ $data->hp }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>Email</label>
                                                            <input type="text" name="edemail" id="edemail" class="form-control input-sm" value="{{ $data->email }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--<div class="col-md-4 mb-3">-->
                                                <!--    <div class="form-group">-->
                                                <!--        <div class="col-md-12">-->
                                                <!--            <label for="">Provinsi :</label>-->
                                                <!--            <select class="form-control input-sm prov" name="provinsi" id="provinsi">-->
                                                <!--                @foreach ($provinces as $item)-->
                                                <!--                    <option value="{{ $item->province_id }}" {{ $data->id_prov == $item->province_id ? 'selected' : '' }}>-->
                                                <!--                        {{ $item->name }}-->
                                                <!--                    </option>-->
                                                <!--                @endforeach-->
                                                <!--            </select>-->
                                                <!--        </div>-->
                                                <!--    </div>-->
                                                <!--</div>-->

                                                <!--<div class="col-md-4 mb-3">-->
                                                <!--    <div class="form-group">-->
                                                <!--        <div class="col-md-12">-->
                                                <!--            <label for="">Kota :</label>-->
                                                <!--            <select class="js-example-basic-single" name="kota" id="kota">-->
                                                <!--                <option value="">- Pilih Kota -</option>-->
                                                <!--            </select>-->
                                                <!--        </div>-->
                                                <!--    </div>-->
                                                <!--</div>-->

                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Kantor</label>
                                                    <select id="edkantor" class="form-control input-sm" name="edkantor">
                                                        @foreach ($kantor as $item)
                                                            <option value="{{ $item->id }}"{{ $data->kantor == $item->id ? 'selected' : '' }}>
                                                                {{ $item->unit }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                
                
                
                  <div class="col-lg-12 mb-3 " id="et" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-4 col-md-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>ID PM</label>
                                                    <input type="text" name="idnya" id="idnya" readonly class="form-control input-sm" value="{{ $data->id }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-3">
                                            <label class="form-label">Jenis PM</label>
                                            <select required class="form-control input-sm jenis_pmb" name="edjenislebaga" id="edjenislebaga">
                                                <option value="personal">Perorangan</option>
                                                <option selected value="entitas" >Entitas</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-4 col-md-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Lembaga / Kegiatan :</label>
                                                    <input type="text" name="ednamalembaga" id="ednamalembaga" class="form-control input-sm" value="{{ $data->penerima_manfaat }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4 col-md-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Nomor Telp :</label>
                                                    <!--<button type="button" id="cek_tlp" class="badge badge-warning badge-xs" style="float:right; ">Cek <i class="fa fa-eye"></i></button>-->
                                                    <input type="text" name="edhplembaga" id="edhplembaga" class="form-control input-sm" value="{{ $data->hp }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">E-mail :</label>
                                                    <!--<button type="button" id="cek_email_pt" class="badge badge-warning badge-xs" style="float:right; ">Cek <i class="fa fa-eye"></i></button>-->
                                                    <input type="email" name="emaillembaga" id="emaillembaga" class="form-control input-sm" value="{{ $data->email }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4 col-md-3">
                                                    <label class="form-label">Penangung Jawab</label>
                                                    <select id="edpjlembaga" class="form-control input-sm" name="edpjlembaga">
                                                        @foreach ($h1 as $item)
                                                            <option value="{{ $item['id'] }}" {{ $data->nama_pj == $item['id'] ? 'selected' : '' }}>
                                                                {{ $item['nama'] }} ({{ $item['jabatan'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-4 col-md-3">
                                                    <label class="form-label">Asnaf</label>
                                                    <select id="edasnaflembaga" class="form-control input-sm" name="edasnaflembaga">
                                                        @foreach ($asnaf as $item)
                                                            <option value="{{ $item->id }}" {{ $data->asnaf == $item->id ? 'selected' : '' }}>
                                                                {{ $item->asnaf }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                       <!--<div class="col-md-3 mb-4">-->
                                       <!--             <div class="form-group">-->
                                       <!--                 <div class="col-md-12">-->
                                       <!--                     <label for="">Provinsi :</label>-->
                                       <!--                     <select class="form-control input-sm prov" name="provinsi" id="provinsi">-->
                                       <!--                         @foreach ($provinces as $item)-->
                                       <!--                             <option value="{{ $item->province_id }}" {{ $data->id_prov == $item->province_id ? 'selected' : '' }}>-->
                                       <!--                                 {{ $item->name }}-->
                                       <!--                             </option>-->
                                       <!--                         @endforeach-->
                                       <!--                     </select>-->
                                       <!--                 </div>-->
                                       <!--             </div>-->
                                       <!--         </div>-->
                                       <!-- <div class="col-md-3 mb-4">-->
                                       <!--     <div class="form-group">-->
                                       <!--         <div class="col-md-12">-->
                                       <!--             <label for="">Kota :</label>-->
                                       <!--             <select required class="js-example-basic-single" style="width: 100%;" name="kota" id="kotaa">-->
                                       <!--                 <option value="">Pilih Kota</option>-->

                                       <!--             </select>-->
                                       <!--         </div>-->
                                       <!--     </div>-->
                                       <!-- </div>-->
                                        
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
                                                        <input type="text" name="latitude" id="latitude" class="form-control" placeholder="0" value="{{ $data->latitude }}">
                                                    </div>

                                                    <div class="mb-3 col-md-3">
                                                        <label>Longitude</label>
                                                        <input type="text" name="longitude" id="longitude" class="form-control" placeholder="0" value="{{ $data->longitude }}">
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <div id="map" style="width:100%;height:300px;"></div>
                                                    </div>

                                                    <div class="mb-3 col-md-10">
                                                        <label>Alamat</label>
                                                        <input type="text" readonly name="alamat" id="alamat" class="form-control" placeholder="alamat donatur" value="{{ $data->alamat }}">
                                                    </div>

                                                    <div class="mb-3 col-md-2">
                                                        <label>&nbsp;</label><br>
                                                        <button class="btn btn-sm btn-danger" type="button" id="reli" disabled>Reset Lokasi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                     
                <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                                        <table id="user_table_salur" class="table table-striped" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="cari">ID Salur</th>
                                                    <th class="cari">Via Bayar</th>
                                                    <th class="cari">Program</th>
                                                    <th class="cari">Nominal</th>
                                                    <th class="cari">Tanggal Salur</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-12 mb-3">
                                                <button type="submit" class="btn btn-success btn-sm editod" style="width:30%; margin-bottom: -20px">Simpan</button>
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
