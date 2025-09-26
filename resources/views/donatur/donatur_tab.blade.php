@extends('template_nyoba')
@section('konten')
<!--<div class="content-body">-->
    <div class="container-fluid" style="padding-top: 1.7rem">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Donatur</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">List Donatur</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <div class="modal fade" id="modalb">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Data Donatur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{url('donatur/import')}}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            @csrf
                            <!-- <div class="form-group">
                                <label>PILIH FILE</label>
                                <input type="file" name="file" class="form" required>
                            </div> -->
                            <div class="input-group mb-3">
                                <span class="input-group-text">Pilih File</span>
                                <div class="form-file">
                                    <input type="file" class="form-file-input form-control" required>
                                </div>
                            </div>
                            <!-- <button type="submit" class="btn btn-success" style="margin-right: 120%">Save</button> -->
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Donatur</h4>
                        <div class="pull-right">

                            <!--<button class="btn btn-primary light buka" type="button" style="float:right; margin-right:15px">-->
                            <!--    <span id="tomboy">Buka Filter  <i class="fa fa-plus"></i></span>-->
                            <!--</button>-->

                            <a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>
                            <a href="{{url('donatur/export')}}" class="btn btn-primary light" style="float:right; margin-right:15px">Export</a>
                            <a class="btn btn-primary light" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right; margin-right:15px">Import</a>
                        </div>
                    </div>
                    
                    <?php
                    if(Auth::user()->level == 'spv'){
                        $k = null;
                    }else{
                        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                        
                    }
                    
                    
                    if(Auth::user()->level == 'admin'){
                        $datdon = App\Models\Kantor::all();
                    }else{
                        if($k != null){
                          $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
                        }
                    }
                    
                    
                    ?>
                    
                    <div class="card-body">
                        <div class="mb-4" id="tomboyin" value="t">
                            <form method="GET">
                                <div class="row">
                                    @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                    <div class="col-md-2">
                                        <!-- <label class="form-label">Unit</label> -->
                                        <select required id="kota" class="cek multi" style="width:100%" name="kota[]" multiple="multiple">
                                            <!--<option value="">Semua Unit</option>-->
                                            @foreach ($datdon as $item)
                                            <option value="{{$item->id}}">{{$item->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    
                                    @if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang')
                                        @if($k != null)
                                            <div class="col-md-2">
                                                <!-- <label class="form-label">Unit</label> -->
                                                <select required id="kota" class="cek multi" style="width:100%" name="kota[]" multiple="multiple">
                                                    <!--<option value="">Semua Unit</option>-->
                                                    @foreach ($datdon as $item)
                                                    <option value="{{$item->id}}">{{$item->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="col-md-2">
                                        <!-- <label class="form-label">Status Kunjungan</label> -->
                                        <select required id="status" class="cek1 multi" style="width:100%" name="status[]" multiple="multiple">
                                            <!--<option value="">Semua Status</option>-->
                                            @foreach ($datstat as $item)
                                            <option value="{{$item->status}}">{{$item->status}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <!-- <label class="form-label">Range Tanggal</label> -->
                                        <input type="text" name="daterange" class="form-control datess ceks" id="daterange" placeholder="Range Tanggal" autocomplete="off" value="" />
                                    </div>

                                    <div class="col-md-2">
                                        <!-- <label class="form-label">Status Donatur</label> -->
                                        <select required id="warning" class="cek2 multi" multiple="multiple" style="width:100%" name="warning[]">
                                            <!--<option value="">Semua Status</option>-->
                                            <option value="aktif">Aktif</option>
                                            <option value="nonaktif">Non-Aktif</option>
                                            <option value="warning">Warning</option>
                                        </select>
                                    </div>
                                    <!-- <div class="col-md-2"> -->
                                    <!-- <label></label> -->
                                    <!-- <a href="javascript:void(0)" class="btn btn-success btn-sm filtt  mt-9">Adv Search</a>
                                </div> -->

                                </div>
                            </form>
                        </div>
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" >
                                <thead>
                                    @if(Auth::user()->level == 'admin')
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th class="cari">Petugas</th>
                                        <th class="cari">Donatur</th>
                                        <th class="cari">Program</th>
                                        <th class="cari">Nomor HP</th>
                                        <th class="cari">Alamat</th>
                                        <th class="cari">Jalur Kolekting</th>
                                        <th class="cari">Kota</th>
                                        <th class="cari">Status</th>
                                        <th>Dikolek</th>
                                        <th>Registrasi</th>
                                        <th>Aksi</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    @elseif(Auth::user()->level == 'kacab' || Auth::user()->level == 'agen')
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th class="cari">Petugas</th>
                                        <th class="cari">Donatur</th>
                                        <th class="cari">Program</th>
                                        <th class="cari">Nomor HP</th>
                                        <th class="cari">Alamat</th>
                                        <th class="cari">Jalur Kolekting</th>
                                        <th class="cari">Status</th>
                                        <th>Dikolek</th>
                                        <th>Registrasi</th>
                                        <th>Aksi</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    @else
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th>Nama Petugas</th>
                                        <th>Nama Donatur</th>
                                        <th>Program</th>
                                        <th>Nomor HP</th>
                                        <th>Alamat</th>
                                        <th>Jalur Kolekting</th>
                                        <th>Status</th>
                                        <th>Dikolek</th>
                                        <th class="cari">Registrasi</th>
                                    </tr>
                                    @endif
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--</div>-->
@endsection