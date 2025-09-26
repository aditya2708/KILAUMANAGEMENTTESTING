@extends('template')
@section('konten')

<div class="content-body">
    
    <!--Modal START-->
    <div class="modal fade"  id="addSkema"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Skema Gaji</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                                
                        <div class="form-group mb-3 col-sm-10 col-md-10">
                            <label>Skema</label>
                            <input class="form-control" name="inputSkema" id="inputSkema" >
                            <input type="hidden" id="aksi" >
                            <input type="hidden" id="index" >
                            <input type="hidden" id="id_hide" >
                        </div>
                                
                                
                        <div class="form-group mb-3 col-sm-2 col-md-2">
                            <button type="button" class="btn btn-sm btn-success mt-4" id="save_skema"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-success mt-4" id="saved_skema" style="display: none"><i class="fa fa-arrow-down"></i></button>
                        </div>
                                
                    </div>    
                    
                    
                    <hr class="mt-3 mb-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Skema</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sutu">
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <hr class="mt-3 mb-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-rounded btn-success btn-block" id="simpanSkema">Simpan</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade"  id="addKomponen"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Komponen Gaji</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                                
                        <div class="form-group mb-3 col-sm-5 col-md-5">
                            <label>Komponen</label>
                            <input class="form-control" name="inputKom" id="inputKom" >
                            <input type="hidden" id="aksi_com" >
                        </div>
                        
                        <div class="form-group mb-3 col-sm-5 col-md-5">
                            <label>Grup</label>
                            <select class="form-control" id="grup" name="grup">
                                <option value="utama">Gaji Utama</option>
                                <option value="potongan">Potongan</option>
                                <option value="bpjs">BPJS</option>
                                <option value="bonus">Bonus</option>
                            </select>
                        </div>
                                
                                
                        <div class="form-group mb-3 col-sm-2 col-md-2">
                            <button type="button" class="btn btn-sm btn-success mt-4" id="save_kom"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-success mt-4" id="saved_kom" style="display: none"><i class="fa fa-arrow-down"></i></button>
                        </div>
                                
                    </div>    
                    
                    
                    <hr class="mt-3 mb-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Komponen</th>
                                        <th>Grup</th>
                                    </tr>
                                </thead>
                                <tbody id="koms">
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <hr class="mt-3 mb-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-rounded btn-success btn-block" id="simpanKom">Simpan</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade"  id="setPres"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Set Presentase Tunjangan</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        
                        <div class="form-group mb-3 col-sm-5 col-md-5">
                            <label>Komponen</label>
                            <select class="form-control trol" id="kompon" name="kompon">
                            </select>
                        </div>
                        
                        <div class="form-group mb-3 col-sm-5 col-md-5">
                            <label>Presentase Berdasarkan</label>
                            <select class="form-control trol" id="press" name="press">
                            </select>
                        </div>
                                
                        <input type="hidden" id="aksinya">
                        <input type="hidden" id="indeksnya">
                                
                        <div class="form-group mb-3 col-sm-2 col-md-2">
                            <button type="button" class="btn btn-sm btn-success mt-4" id="save_pres"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-success mt-4" id="saved_pres" style="display: none"><i class="fa fa-arrow-down"></i></button>
                        </div>
                        
                    </div>    
                    
                    
                    <hr class="mt-3 mb-3">
                    
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Komponen</th>
                                        <th>Presentase Berdasarkan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="eoh">
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <hr class="mt-3 mb-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-rounded btn-success btn-block" id="simpann">Simpan</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    @foreach($kom as $k)
    <div class="modal fade"  id="{{ $k->modal }}"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel"><span id="tmbahan"></span> {{ $k->nama }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @if($k->modal == 'bpjs')
                        <form method="post" id="bpjs_form">
                            @csrf
                            <input type="hidden" name="modal" value="{{ $k->modal }}">
                            <div id="potbpjs"></div>
                            <hr class="mt-3">
                            <div id="tombil"></div>
                        </form>
                        @elseif($k->modal == 'ketenagakerjaan')
                        <form method="post" id="ketenaga_form">
                            @csrf
                            <input type="hidden" name="modal" value="{{ $k->modal }}">
                            <div id="ketenaga"></div>
                            <hr class="mt-3">
                            <div id="timbil"></div>
                        </form>
                        @elseif($k->modal == 'kesehatan')
                         <form method="post" id="keseh_form">
                            @csrf
                            <input type="hidden" name="modal" value="{{ $k->modal }}">
                            <div id="keseh"></div>
                            <hr class="mt-3">
                            <div id="biti"></div>
                        </form>
                        @elseif($k->modal == 'umk')
                            <div id="umkyu"></div>
                        @elseif($k->modal == 'tunjanganberas')
                            <div id="tunjanganber"></div>
                        @elseif($k->modal == 'tunjangananak')
                            <div id="tunjangannak"></div>
                        @elseif($k->modal == 'tunjanganpasangan')
                            <div id="tunpas"></div>
                        @elseif($k->modal == 'uangtransport')
                            <div id="tuntras"></div>
                        @elseif($k->modal == 'tidaklaporanataupresensipulang')
                            <div id="lapppat"></div>
                            <hr class="mt-3">
                            <div id="ui"></div>
                        @elseif($k->modal == 'tidaklaporandanpresensipulang')
                            <div id="lapppat2"></div>
                            <hr class="mt-3">
                            <div id="uo"></div>
                        @elseif($k->modal == 'keterlambatan')
                            <div id="newrule2"></div>
                        @elseif($k->modal == 'tunjangandaerah')
                            <div id="tundar"></div>
                        @elseif($k->modal == 'tunjanganfungsional')
                            <div id="cobb"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
    <!--Modal END-->
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <select class="form-control cuak" id="skemaf" name="skemaf">
                                
                            </select>
                        </h4>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success btn-rounded cemen" id="cemen" data-bs-target="#setPres" data-bs-toggle="modal" style="margin-right: 10px; display: none" type="button">Set Presentase Tunjangan</button>
                                <button class="btn btn-sm btn-primary btn-rounded okey" data-bs-target="#addSkema" data-bs-toggle="modal" style="margin-right: 10px" type="button">Tambah Skema</button>
                                @if(Auth::user()->id == 6)
                                <button class="btn btn-sm btn-info btn-rounded ohiya" data-bs-target="#addKomponen" data-bs-toggle="modal" style="margin-right: 10px" type="button">Tambah Komponen</button>
                                @endif
                                <!--<button class="btn btn-sm btn-primary btn-rounded" style="margin-right: 10px" id="satuan" type="button"></button>-->
                                <!--<button type="button" class="btn btn-info btn-sm btn-rounded dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">-->
                                <!--    Ekspor-->
                                <!--</button>-->
                                <!--<ul class="dropdown-menu">-->
                                <!--    <li><button class="dropdown-item apatuh" type="submit" id="xls" value="xls" name="tombol">.XLS</button></li>-->
                                <!--    <li><button class="dropdown-item apatuh" type="submit" id="csv" value="csv" name="tombol">.CSV</button></li>-->
                                <!--</ul>-->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Komponen</th>
                                        <th>Grup</th>
                                        <th>Skema</th>
                                        <th>AKtif</th>
                                        <th>Editable</th>
                                        <th></th>
                                        <th></th>
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
    
@endsection