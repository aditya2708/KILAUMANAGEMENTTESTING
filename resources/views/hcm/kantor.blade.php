@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Setting</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Data Kantor</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal" id="exampleModal" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Tambah Kantor</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <!--<span id="form_result"></span>-->
                    <form class="form-horizontal" method="post" id="sample_form">
                        <div class="modal-body">
                            @csrf

                            <div class="row">
                                 @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                     <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label  class="col-sm-4">Perusahaan</label>
                                            <div class="col-lg-12">
                                                <select class="form-control cek2" id="perus" name="perus">
                                                       
                                                @if(count($company) > 0)
                                                <option selected="selected" value="">Pilih Perusahaan</option>
                                                @foreach($company as $c)
                                                <option value="{{ $c->id_com }}">{{$c->name}}</option>
                                                @endforeach
                                                @else
                                                <!--<option selected="selected" value="">Pilih jabatan</option>-->
                                                <!--<option value="">Tidak Ada</option>-->
                                                @endif
                                                      
                                                    <!--@foreach($company as $c)-->
                                                    <!--    <option value="{{ $c->id_com }}">{{$c->name}}</option>-->
                                                    <!--@endforeach-->
                                                  </select>
                                                <!--<input type="text" id="perus" name="perus" class="form-control input-sm">-->
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label  class="col-sm-4">Kantor :</label>
                                        <div class="col-lg-12">
                                            <input type="text" id="unit" name="unit" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label  class="col-sm-4">Level :</label>
                                        <div class="col-lg-12">
                                            <select class="form-control" id="level" name="level">-->
                                                <option selected="selected" value="">- Pilih Level -</option>
                                                <option value="pusat" data-value="101.01.000.000">Pusat</option>
                                                <option value="cabang" data-value="101.01.002.000">Cabang</option>
                                                <option value="kcp" data-value="101.01.002.000">KCP</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <!--<div id="kan_cab" style="display:none;">-->
                                    <div class=" col-md-4" id="kan_cab" style="display:none;">
                                        <div class="form-group mb-3">
                                            <label class="col-sm-12">Kantor Induk :</label>
                                            <div class="col-lg-12">
                                                <select class="form-control" id="kantor_in" name="kantor_induk">
                                                        @if(count($kan) > 0)
                                                        <option selected="selected" value="">Pilih  Kantor Induk</option>
                                                        @foreach($kan as $kolek)
                                                        <option value="{{ $kolek->id }}">{{$kolek->unit}}</option>
                                                        @endforeach
                                                        @else
                                                        @endif
                                                    <!--<option selected="selected" value="">- Pilih Kantor Induk -</option>-->
                                                    <!--@foreach ($kan as $kolek)-->
                                                    <!--<option value="{{$kolek->id}}">{{$kolek->unit}}</option>-->
                                                    <!--@endforeach-->
                                                </select>
                                            </div>
                                        </div>
                                    <!--</div>-->
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label  class="col-sm-4">No HP :</label>
                                        <div class="col-lg-12">
                                            <input type="text" id="no_hp" name="no_hp" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                                        
                            <!--<div class="form-group mb-3">-->
                            <!--    <label  class="col-sm-4">Alamat :</label>-->
                            <!--    <div class="col-lg-12">-->
                            <!--        <textarea type="text" name="alamat" id="alamat" class="form-control" col="5" row="5"></textarea>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <div class="row" >
                                <!--<div class="col-md-6 mb-3">-->
                                <!--    <label>Cari Lokasi</label>-->
                                <!--    <input type="text" id="lok" nama="lok" class="form-control" placeholder="Cari Lokasi">-->
                                <!--</div>-->
                                
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
                                
                                <div class="mb-3 col-md-12">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" id="alamat" class="form-control" placeholder="alamat donatur">
                                </div>
                            </div>        
                                    
                                    
                            
                            <div class="row">    
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label  class="col-sm-4">Pilih jabatan :</label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="id_jabdir" id="piljab">
                                                
                                                @if(count($jab) > 0)
                                                <option selected="selected" value="">Pilih jabatan</option>
                                                @foreach($jab as $j)
                                                <option value="{{ $j->id }}">{{ $j->jabatan }}</option>
                                                @endforeach
                                                @else
                                                <!--<option selected="selected" value="">Pilih jabatan</option>-->
                                                <!--<option value="">Tidak Ada</option>-->
                                                @endif
                                              
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label  class="col-sm-4">Nama Pimpinan :</label>
                                        <div class="col-lg-12">
                                            <select class="form-control" readonly name="direktur" class="form-control" id="direktur">
                                                <option value="">-- Pilih --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                    
                                <div class="form-group mb-3" id="ceklis_coa" style="display:block">
                                    <label  class="col-sm-4"></label>
                                    <div class="col-lg-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="cek_coa" id="cek_coa"> Data Coa sudah ada ?
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-md-6" id="coa" style="display:none;">
                                    <div class="form-group mb-3">
                                        <label class="col-sm-4">COA :</label>
                                        <div class="col-lg-12">
                                            <select class="selectAccountDeal" name="id_coa" id="coa_cek">
                                                <!--<option></option>-->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="action" id="action" value="add" />
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Kantor</h4>
                        <div class="pull-right">
                             @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>                            
                            @endif
                            <a href="javascript:void(0)" class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#exampleModal" id="record">Tambah Kantor</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                           
                            
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>no</th>
                                                <th>Kantor</th>
                                                <th>No HP</th>
                                                <th>Alamat</th>
                                                <th>Kantor Induk</th>
                                                <!--<th>Tunjangan Daerah</th>-->
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

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