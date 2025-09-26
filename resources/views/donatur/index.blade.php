@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

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
        
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Foto Donatur <span id="ehehe"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center">
                            <div class="p-2 bd-highlight">
                                <img src="" alt="Zoomed Image" id="zoomedImg" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!--<form method="GET" action="{{url('donatur/export')}}">-->
        <!--filter-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php
                            if(Auth::user()->level == 'spv'){
                                $k = null;
                            }else{
                                $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
                                
                            }
                            
                            
                            if(Auth::user()->level == 'admin'){
                                $datdon = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
                            }else{
                                if($k != null){
                                  $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                }
                            }
                            
                            
                        ?>

                        <div class="d-flex bd-highlight mb-3">
                                    
                            <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                <label class="form-label">Status Kunjungan</label> 
                                <select id="status" class="cek1 multi"  name="status[]" multiple="multiple" >
                                        <!--<option value="">Semua Status</option>-->
                                    @foreach ($datstat as $item)
                                    <option value="{{$item->status}}">{{$item->status}}</option>
                                    @endforeach
                                </select>
                            </div>
                                    
                            <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                <label class="form-label">Unit</label> 
                                <select id="kota" class="cek multi" name="kota[]" multiple="multiple" style="width: 100%">
                                    @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                    
                                        @foreach ($datdon as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                                
                                    @else if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang')
                                            
                                        @if($k != null)
                                            @foreach ($datdon as $item)
                                            <option value="{{$item->id}}">{{$item->unit}}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            </div>
                                    
                            <div class="me-auto p-2 bd-highlight">
                                <label class="form-label">Tanggal Registrasi</label> 
                                <input type="text" name="daterange" class="form-control datess ceks" id="daterange" autocomplete="off" value="" />
                            </div>
                            
                            <div class="me-auto p-2 bd-highlight">
                                <label class="form-label">Prospek Donatur</label> 
                                <select class="form-control psss"  name="prosp" id="prosp" >
                                    <option value="closing">Closing</option>
                                    <option value="open">Open</option>
                                </select>
                            </div>
                            
                            <div class="me-auto p-2 bd-highlight"></div>
                            <div class="me-auto p-2 bd-highlight"></div>
                            <div class="me-auto p-2 bd-highlight"></div>
                            <div class="me-auto p-2 bd-highlight"></div>
                            
                            <div class="p-2 bd-highlight mt-4">
                                <label></label>
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="float:right; margin-right:15px">Advance</button>
                            </div>
                        </div>
                                
                                
                        <div class="row d-flex justify-content-center ">
                            <div class="bg-collaps rounded" style="width: 97%;">
                                <div class="collapse" id="collapseExample" >
                                    <div class="row">
                                        <div class=" mb-3 col-md-3">
                                                            <!--<label class="form-label mt-3">Aktif Donatur</label>-->
                                            <label class="form-label mt-3">Status Donatur</label> 
                                            <select id="warning" class="cek2 multi" multiple="multiple" style="width:100%" name="warning[]" value="">
                                                            <!--<option value="">Semua Status</option>-->
                                                <option value="aktif">Aktif</option>
                                                <option value="nonaktif">Non-Aktif</option>
                                                <option value="warning">Warning</option>
                                            </select>
                                        </div>
                                                    
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">User Insert</label>
                                            <select id="ui" class="multi" style="width:100%" name="ui[]">
                                                <option value="">Semua</option>
                                                @foreach ($user_insert as $item)
                                                @if($item->user_insert != '' || $item->user_insert != null)
                                                    <?php $aw = App\Models\User::select('name')->where('id', $item->user_insert)->first(); ?>
                                                @endif
                                                            
                                                <option value="{{$item->user_insert == '' ? 'unknown' : $item->user_insert}}">{{ $item->user_insert == '' ? 'Tidak Diketahui' : ucfirst($aw->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Jenis Kelamin</label>
                                            <select id="jk" class="form-control" style="width:100%" name="jk">
                                                <option value="">Semua</option>
                                                @foreach ($datjen as $item)
                                                <option value="{{$item->jk == '' ? 'unknown' : $item->jk}}">{{ $item->jk == '' ? 'Tidak Diketahui' : ucfirst($item->jk)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                                    
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Petugas</label>
                                            <select class="multi" name="petugas" id="petugas[]">
                                                <option value="" >Semua</option>
                                                @foreach ($petugas as $item)
                                                <option value="{{$item->petugas == '' ? 'unknown' : $item->petugas}}">{{ $item->petugas == '' ? 'Tidak Diketahui' : ucfirst($item->petugas)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Program</label>
                                            <select id="program" class="crot ceksi" style="width:100%" name="program">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                                    
                                        <div class=" mb-3 col-md-3">
                                                        <!--<label class="form-label mt-3">Jenis Transaksi Aktif</label>-->
                                                        
                                            <select class="mt-3" id="tt" name="tt" panelheight="auto" style="width:120px;border:0; background: #f8f8f8">
                                                <option value="aktif" selected="selected">Transaksi Aktif</option>
                                                <option value="pasif">Transaksi Pasif</option>
                                            </select>
                                                        
                                            <div id="ta"  style="display: block">
                                                <input type="text" name="traktif" class="form-control datess" id="traktif" autocomplete="off" />
                                            </div>
                                                        
                                            <div id="tn" style="display: none" >
                                                <input type="text" name="traknon" class="form-control datess" id="traknon" autocomplete="off" />
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Mempunyai Koordinat</label> 
                                            <select class="form-control coco"  name="koordinat" id="koordinat" >
                                                <option value="">Semua</option>
                                                <option value="1">Iya</option>
                                                <option value="0">Tidak ada</option>
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
                    

        <!-- row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Donatur</h4>
                        <div class="pull-right">
                            <!--<div class="p-2 bd-highlight">-->
                                <button type="submit" class="btn btn-primary light" style="float:right; margin-right:15px" id="export">Export</button>
                            <!--</div>-->
                            <!--<div class="p-2 bd-highlight">-->
                                <a class="btn btn-primary light" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right; margin-right:15px">Import</a>
                            <!--</div>-->
                        </div>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" >
                                <thead>
                                    @if(Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->level == 'operator pusat')
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th></th>
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
                                    </tr>
                                    @elseif(Auth::user()->level == 'agen')
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th></th>
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
                                        <th></th>
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
        
        <!--</form>-->
    </div>
</div>
@endsection