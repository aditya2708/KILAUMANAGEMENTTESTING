@extends('template_nyoba')
@section('konten')
<!--<div class="content-body">-->
    <div class="container-fluid" style="padding-top: 1.7rem">

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
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="form_result"></span>
                        <form method="post" id="alasan_form">
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
                            <!--<a class="btn btn-success" target="_blank" href="#">Kirim Langsung ke Donatur</a>-->
                            <!--<a class="btn btn-primary" data-bs-dismiss="modal" data-toggle="modal" data-target="#modkwi" href="">Kirim Melalui Admin</a>-->
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
                    <div class="modal-body">
                        <form>
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
                    <div class="modal-body">
                        <form>
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
                $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            }
        
            if(Auth::user()->level == 'admin'){
                $kot_adm = App\Models\Kantor::all();
            }else{
                if($k != null){
                    $kot_adm =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
                }
            }
        
        $stat_adm = App\Models\Transaksi::select('status')->whereRaw("status IS NOT NULL")->distinct()->get();
        
        ?>

        <!-- row -->
        <div class="row">
            <div class="col-12" >
                <div class="card">
                    <div class="card-body">
                        <!-- Default accordion -->
                        <div class="accordion accordion-primary" id="accordion-one">
                            <div class="accordion-item">
                                <div class="accordion-header  rounded-lg col-md-6" id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-controls="collapseOne" aria-expanded="true" role="button">
                                    <span class="accordion-header-icon"></span>
                                    <span class="accordion-header-text">Filter Data</span>
                                    <span class="accordion-header-indicator"></span>
                                </div>

                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion-one">
                                    <div class="accordion-body-text">
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Pilih</label>
                                                <select id="plhtgl" class="default-select form-control wide" name="plhtgl">
                                                    <option value="0">Periode</option>
                                                    <option value="1">Bulan</option>
                                                    <option value="2">Tahun</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-3" id="rangetgl">
                                                <label class="form-label">Range Tanggal</label>
                                                <input type="text" class="form-control cek1" autocomplete="off" id="daterange" name="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" />
                                            </div>

                                            <div class="mb-3 col-md-3" id="blni" hidden>
                                                <label class="form-label">Dari Bulan</label>
                                                <input type="text" id="blns" name="blns" class="goa form-control cek2" autocomplete="off" placeholder="{{date('m-Y') }}" />
                                            </div>

                                            <div class="mb-3 col-md-3" id="blnii" hidden>
                                                <label class="form-label">Sampai Bulan</label>
                                                <input type="text" id="blnnnn" name="blnnnn" class="goa form-control cek3" autocomplete="off" placeholder="{{date('m-Y') }}" />
                                            </div>

                                            <div class="mb-3 col-md-3" id="tahun_hide" hidden>
                                                <label class="form-label">Tahun :</label>
                                                <input type="text" class="form-control year cek4" name="thnn" id="thnn" autocomplete="off" placeholder="{{date('Y') }}">
                                            </div>
    
                                            @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Unit</label>
                                                <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">
                                                    <!--<option value="">Pilih Unit</option>-->
                                                    @foreach($kot_adm as $op)
                                                    <option value="{{$op->id}}">{{$op->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Kolektor</label>
                                                <select id="kol" class="multi cek6" multiple="multiple" name="kol[]">
                                                    <!--<option value="">Tidak ada</option>-->
                                                </select>
                                            </div>
                                            @endif
                                            
                                            @if(Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan cabang')
                                                @if($k != null)
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Unit</label>
                                                    <select id="kota" class="multi cek5" name="kota[]" multiple="multiple">
                                                        <!--<option value="">Pilih Unit</option>-->
                                                        @foreach($kot_adm as $op)
                                                        <option value="{{$op->id}}">{{$op->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Kolektor</label>
                                                    <select id="kol" class="multi cek6 sij" name="kol[]" multiple="multiple">
                                                        <!--<option value="">Tidak ada</option>-->
                                                    </select>
                                                </div>
                                                @else
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Kolektor</label>
                                                    <select id="kol" class=" cek6 multi" name="kol[]" multiple="multiple">
                                                        <!--<option value="">Pilih Kolektor</option>-->
                                                        @foreach($kolektor as $kolek)
                                                        <option value="{{$kolek->name}}">{{$kolek->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                            @endif

                                            <!--<div class="mb-3 col-md-3">-->
                                            <!--    <label class="form-label">Status</label>-->
                                            <!--    <select id="statuus" class="default-select form-control wide cek7" name="statuus">-->
                                            <!--        <option value="">Pilih Status</option>-->
                                            <!--        @foreach($stat_adm as $op)-->
                                            <!--        <option value="{{$op->status}}">{{$op->status}}</option>-->
                                            <!--        @endforeach-->
                                            <!--    </select>-->
                                            <!--</div>-->
                                            
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Status</label>
                                                <select class="multi cek7" name="statuus[]" multiple="multiple" id="statuus">
                                                    @foreach($stat_adm as $op)-->
                                                    <option value="{{$op->status}}">{{$op->status}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label class=" form-label">Status Transaksi</label>
                                                <select class="multi cek8" name="statak[]" multiple="multiple" id="statak">
                                                    <option value="0">Rejected</option>
                                                    <option value="2">Pending</option>
                                                    <option value="1">Approved</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Dari Nominal</label>
                                                <input type="number" min="0" class="form-control cek9" name="min" id="min" placeholder="Contoh 100000 ">
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Sampai Nominal</label>
                                                <input type="number" min="0" class="form-control cek10" name="max" id="max" placeholder="Contoh 100000 ">
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Transaksi</h4>
                        <div class="pull-right">
                            <!--<a href="javascript:void(0)" class="btn btn-dark rounded-lg filtt light" style=" margin-right: 20px;">Adv Search</a>-->
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>
                            <a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="cari">ID Transaksi</th>
                                        <th class="cari">ID Donatur</th>
                                        <th class="cari">Kolektor</th>
                                        <th class="cari">Donatur</th>
                                        <th style="display: none">created_at</th>
                                        <th class="cari">Sub Program</th>
                                        <th class="cari">Keterangan</th>
                                        <th class="cari">Pembayaran</th>
                                        <th class="cari">Status</th>
                                        <th class="cari">Jumlah</th>
                                        <th class="cari">Alamat Donatur</th>
                                        <th>Tgl</th>
                                        <th>Status Transaksi</th>
                                        <th>Hapus</th>
                                        <th>Akses</th>
                                        <th>Kwitansi</th>
                                    </tr>
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