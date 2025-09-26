@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Transaksi</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">List Transaksi</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" id="alasan_form">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <span id="form_result"></span>
                                @csrf
                                <div class="form">
                                    <label for="name">Alasan Transaksi Ditolak</label>
                                    <input type="text" name="alasan" class="form-control" id="alasan" aria-describedby="name">
                                    <input type="hidden" name="approval" class="form-control" id="approval" aria-describedby="name" value="0">
                                    <input type="hidden" name="notif" class="form-control" id="notif" aria-describedby="name" value="1">
                                    <input type="hidden" name="id_hidden" id="id_hidden" />
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

        <div class="modal fade" id="modalkwitansi" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="form">
                                <label>Klik kirim nomor donatur lalu klik kirim kwitansi</label>
                                <input type="hidden" id="id_hide" name="id_hide">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="kon">

                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modkwi" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                        <form>
                    <div class="modal-body">
                            <div class="form">
                                <label>Klik kirim nomor donatur lalu klik kirim kwitansi</label>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <div id="keks">

                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal3" >
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                        <form>
                    <div class="modal-body">
                            <div class="form">
                                <label>Pilih Akses</label>
                                <select class="form-control">
                                    <option>Approve</option>
                                    <option>Reject</option>
                                </select>
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
        <!-- End modal -->

        <?php
            if(Auth::user()->level == 'spv'){
                $k = null;
            }else{
                $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
            }
        
            if(Auth::user()->level == 'admin' || Auth::user()->level == 'operator pusat'){
                $kot_adm = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
            }else{
                if($k != null){
                    $kot_adm =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                }
            }
        
        $stat_adm = App\Models\Transaksi::select('status')->whereRaw("status IS NOT NULL")->distinct()->get();
        $pembay = App\Models\Transaksi::select('pembayaran')->whereRaw("pembayaran = 'noncash' OR pembayaran =  'teller' OR pembayaran = 'dijemput' OR pembayaran = 'transfer'")->distinct()->get();
        
        ?>

        <!-- row -->
        <form action={{ url('transaksi-export') }}>
            <div class="row">
                
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xl-3 col-sm-3" id="qtys">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                            <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
    											<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
    											<path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
    											<path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">kuantitas</p>
                                                <h4 class="mb-0" id="qty">0</h4>
                                                    <!-- <span class="badge badge-primary">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="donaturs">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
    											<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
    											<circle cx="12" cy="7" r="4"></circle>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Donatur</p>
                                                <h4 class="mb-0" id="donatur">0</h4>
                                                    <!-- <span class="badge badge-info">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="caps">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Capaian</p>
                                                <h4 class="mb-0" id="cap">0</h4>
                                                    <!-- <span class="badge badge-success">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="growths">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                        <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
    											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
    											<polyline points="14 2 14 8 20 8"></polyline>
    											<line x1="16" y1="13" x2="8" y2="13"></line>
    											<line x1="16" y1="17" x2="8" y2="17"></line>
    											<polyline points="10 9 9 9 8 9"></polyline>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Growth</p>
                                                <h4 class="mb-0" id="growth">0</h4>
                                                        <!-- <span class="badge badge-warning">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                
                <div class="col-12" >
                    <div class="card">
                        <!--<h4>Filter</h4>-->
                        <div class="card-body">
                            <div class="d-flex bd-highlight mb-3">
                                    
                                <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                    
                                    <label class="form-label">Pilih</label>
                                    <select id="plhtgl" class="default-select form-control wide" name="plhtgl">
                                        <option value="0">Periode</option>
                                        <option value="1">Bulan</option>
                                        <option value="2">Tahun</option>
                                    </select>
                                </div>
                                        
                                <div class="me-auto p-2 bd-highlight" style="width: 20%" id="rangetgl">
                                    <label class="form-label">Range Tanggal</label>
                                    <input type="text" class="form-control cek1"  autocomplete="off" id="daterange" name="daterange" value="{{ $tglll }}" placeholder="{{ $tglll }}" />
                                </div>
                                    
                                    
                                <div class="me-auto p-2 bd-highlight" style="width: 15%" id="blni" hidden>
                                    <label class="form-label">Dari Bulan</label>
                                    <input type="text" id="blns" name="blns" class="goa form-control cek2" autocomplete="off" placeholder="{{date('m-Y') }}" />
                                </div>
                                    
                                <div class="me-auto p-2 bd-highlight" style="width: 15%" id="blnii" hidden>
                                    <label class="form-label">Sampai Bulan</label>
                                    <input type="text" id="blnnnn" name="blnnnn" class="goa form-control cek3" autocomplete="off" />
                                </div>
                                    
                                <div class="me-auto p-2 bd-highlight" style="width: 15%" id="tahun_hide" hidden>
                                    <label class="form-label">Tahun :</label>
                                    <input type="text" class="form-control year cek4" name="thnn" id="thnn" autocomplete="off" placeholder="{{date('Y') }}">
                                </div>
                                
                                @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level == 'operator pusat')
                                    <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                        <label class="form-label">Unit</label>
                                        <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">
                                            @foreach($kot_adm as $op)
                                            <option value="{{$op->id}}">{{$op->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                            
                                @if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang')
                                    @if($k != null)
                                    <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                        <label class="form-label">Unit</label>
                                        <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">
                                            @foreach($kot_adm as $op)
                                            <option value="{{$op->id}}">{{$op->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                @endif
                                
                                <div class="me-auto p-2 bd-highlight" style="width: 15%">
                                    <label class=" form-label">Status Transaksi</label>
                                    <select class="multi cek8" name="statak[]" multiple="multiple" id="statak">
                                        <option value="0">Rejected</option>
                                        <option value="2" {{ $pending == [] ? '' : 'selected' }}>Pending</option>
                                        <option value="1">Approved</option>
                                    </select>
                                </div>
                                
                                <div class="me-auto p-2 bd-highlight"></div>
                                <div class="me-auto p-2 bd-highlight"></div>
                                <div class="me-auto p-2 bd-highlight"></div>
                                
                                
                                <div class="p-2 bd-highlight mt-4">
                                    <label></label>
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="float:right; margin-right:15px">Advance</button>
                                </div>
                                
                                <div class="p-2 bd-highlight mt-4">
                                    <div class="btn-group">
                                         <!--data-bs-toggle="dropdown" aria-expanded="false" -->
                                         <button type="button" class="btn btn-primary btn-sm dropdown-toggle exp"  style=" margin-right: 20px;" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ekspor
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row d-flex justify-content-center">
                                <div class="bg-collaps rounded" style="width: 97%;">
                                    <div class="collapse" id="collapseExample" >
                                        <div class="row mt-3">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Status</label>
                                                <select class="multi cek7" name="statuus[]" multiple="multiple" id="statuus">
                                                    @foreach($stat_adm as $op)-->
                                                    <option value="{{$op->status}}">{{$op->status}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                                
                                            <!--<div class="mb-3 col-md-3">-->
                                            <!--    <label class=" form-label">Status Transaksi</label>-->
                                            <!--    <select class="multi cek8" name="statak[]" multiple="multiple" id="statak">-->
                                            <!--        <option value="0">Rejected</option>-->
                                            <!--        <option value="2">Pending</option>-->
                                            <!--        <option value="1">Approved</option>-->
                                            <!--    </select>-->
                                            <!--</div>-->
                                            
                                            @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                            <!--<div class="mb-3 col-md-3">-->
                                            <!--    <label class="form-label">Unit</label>-->
                                            <!--    <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">-->
                                            <!--        @foreach($kot_adm as $op)-->
                                            <!--        <option value="{{$op->id}}">{{$op->unit}}</option>-->
                                            <!--        @endforeach-->
                                            <!--    </select>-->
                                            <!--</div>-->
                                            
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Kolektor</label>
                                                <select id="kol" class="multi cek6" multiple="multiple" name="kol[]">
                                                <!--<option value="">Tidak ada</option>-->
                                                 @foreach($kolektor as $kolek)
                                                <option value="{{$kolek->name}}">{{$kolek->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            
                                            @if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang')
                                               <!-- @if($k != null)-->
                                                <!--<div class="mb-3 col-md-3">-->
                                                <!--    <label class="form-label">Unit</label>-->
                                                <!--    <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">-->
                                                <!--        @foreach($kot_adm as $op)-->
                                                <!--        <option value="{{$op->id}}">{{$op->unit}}</option>-->
                                                <!--        @endforeach-->
                                                <!--    </select>-->
                                                <!--</div>-->
                                                
                                               <!--<div class="mb-3 col-md-3">-->
                                               <!--     <label class="form-label">Kolektor haha</label>-->
                                               <!--     <select id="kol" class="multi cek6 sij" name="kol[]" multiple="multiple">-->
                                                    <!--<option value="">Tidak ada</option>-->
                                               <!--     </select>-->
                                               <!-- </div>-->
                                               <!-- @else-->
                                                <!--@endif-->
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Kolektor</label>
                                                    <select id="kol" class=" cek6 multi" name="kol[]" multiple="multiple">
                                                        @foreach($kolektor as $kolek)
                                                        <option value="{{$kolek->name}}">{{$kolek->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                                
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Dari Nominal</label>
                                                <input type="number" min="0" class="form-control cek9" name="min" id="min" placeholder="Contoh 100000 ">
                                            </div>
                                                
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Sampai Nominal</label>
                                                <input type="number" min="0" class="form-control cek10" name="max" id="max" placeholder="Contoh 100000 ">
                                            </div>
                                                
                                            <div class="mb-3 col-md-3">
                                                <label class=" form-label">Pembayaran</label>
                                                <select class="multi cek11" name="bayar[]" multiple="multiple" id="bayar">
                                                    @foreach($pembay as $pp)
                                                    <option value="{{$pp->pembayaran}}">{{$pp->pembayaran}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3 col-md-3" id="pembbang" style="display: none">
                                                <label class=" form-label">Bank</label>
                                                <select class="form-control cekk" name="bank" id="bank">
                                                    <option value="">Pilih Bank</option>
                                                    @foreach($bang as $ban)
                                                    <option value="{{$ban->id_bank}}">{{$ban->nama_bank}} {{$ban->no_rek}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class=" mb-3 col-md-3">
                                                <label >Program</label>
                                                <select id="program" class="crot ceksi" style="width:100%" name="program">
                                                    <option value=""></option>
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
    
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Transaksi</h4>
                            <div class="pull-right">
                                @if(isset($gagal))
                                    <div class="alert alert-danger">{{ $gagal }}</div>
                                @endif

                                
                                <!--<a href="javascript:void(0)" class="btn btn-dark rounded-lg filtt light" style=" margin-right: 20px;">Adv Search</a>-->
                                <!--<h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>-->
                                <button type="button" class="btn btn-success btn-sm" style="float:right;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm light filtt" style=" margin-right:15px">Adv Search</a>
                            </div>
                        </div>
                        <div class="card-body">
                                <input type="hidden" id="advsrc" value="tutup">
                            <div class="table-responsive">
                                <table id="user_table" class="table table-striped" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th class="cari" style="width:50px;">ID Transaksi</th>
                                            <th class="cari" style="width:50px;">ID Donatur</th>
                                            <th class="cari" style="width:150px;">Kolektor</th>
                                            <th class="cari" style="width:150px;">Donatur</th>
                                            <th style="display: none">created_at</th>
                                            <th class="cari" style="width:200px;">Program</th>
                                            <th class="cari">Pembayaran</th>
                                            <th class="cari">Status</th>
                                            <th class="cari">Jumlah</th>
                                            <th class="cari">Keterangan</th>
                                            <th class="cari">Alamat Donatur</th>
                                            <th>Tgl</th>
                                            <!--<th>Edit</th>-->
                                            <th>Status Transaksi</th>
                                            <!--<th>Hapus</th>-->
                                            <!--<th>Akses</th>-->
                                            <th>Aksi</th>
                                            <!--<th></th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="display: none"></th>
                                            <th><b>Total :</b></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>

</div>
@endsection