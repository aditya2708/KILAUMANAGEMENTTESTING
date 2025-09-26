@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <!-- modal -->
            <div class="modal fade" id="exampleModal">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Target Bulan Ini</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" action="{{url('target')}}">
                            @csrf
                            <div class="modal-body">as
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Masukan Target Bulan Ini</label>
                                            <input type="text" name="target" class="form-control" aria-describedby="name" placeholder="Contoh: 100.000.000">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Kota</label>
                                            <select required id="bank" class="form-control default-select wide" name="kota">
                                                <option selected value="">- Target Capaian Untuk Cabang -</option>
                                                <option value="bandung">Bandung</option>
                                                <option value="sumedang">Sumedang</option>
                                                <option value="indramayu">Indramayu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modali">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Rincian potongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Kantor</label>
                                        <select id="kota" class="form-control" onchange="kota()">
                                            <option value="" selected>Pilih Kantor</option>
                                            @foreach ($datacabang as $cabang)
                                            <option value="{{$cabang->id}}">{{$cabang->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select id="bulan" class="form-control " onchange="kota()">
                                            <option value="" selected>Pilih Bulan</option>
                                            @foreach ($rincian as $rin)
                                            <option value="{{$rin->bulan}}">{{$rin->namebulan}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="siap">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Belum dikunjungi</th>
                                            <th>Tutup 1x</th>
                                            <th>Potongan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="a">
                                    </tbody>
                                    <tfoot id="total">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalbelum">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Rincian yang Belum di Assignment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Kantor</label>
                                        <select id="kota2" class="form-control " onchange="kota2()">
                                            <option value="" selected>Pilih Kantor</option>
                                            @foreach ($datacabang as $cabang)
                                            <option value="{{$cabang->id}}">{{$cabang->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bulan</label>
                                        <select id="bulan2" class="form-control " onchange="kota2()">
                                            <option value="" selected>Pilih Bulan</option>
                                            @foreach ($rincian as $rin)
                                            <option value="{{$rin->bulan}}">{{$rin->namebulan}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="table-responsive">
                                <table class="table table-striped" id="oyen">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Belum di Assignment</th>
                                            <th>Tutup 1x</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bel">
                                    </tbody>
                                    <tfoot id="tot">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
            
            <!--Modal 2-->
            <div class="modal fade" id="modaldonasi" >
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="uhuy">
                                    <thead>
                                        <tr>
                                            <th>Nama Donatur</th>
                                            <th>Total Donasi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="div">
                                        
                                    </tbody>
                                </table>
                            </div>
                            
                            <!--<div class="row">-->
                            <!--    <div class="col-lg-8">-->
                            <!--        <label for="" class="label-control"><b>Nama Donatur</b></label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-4">-->
                            <!--        <label for="" class="label-control"><b>Total Donasi</b></label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div id="div">-->

                            <!--</div>-->

                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="modalwar">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document" >
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="muoh">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Donasi</th>
                                            <th>Tidak Donasi</th>
                                            <th>Tutup</th>
                                            <th>Tutup 2x</th>
                                            <th>Ditarik</th>
                                            <th>Kotak Hilang</th>
                                            <th>Transaksi Diatas Minimal</th>
                                            <th>Total Kunjungan</th>
                                            <th>Capaian Target</th>
                                            <th>Capaian Kunjungan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="div1">
                                        
                                    </tbody>
                                </table>
                            </div>
                            
                            
                            <!--<div class="row">-->
                            <!--    <div class="col-lg-2">-->
                            <!--        <label for="" class="label-control">Tanggal&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Donasi&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Tidak Donasi&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Tutup&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Tutup 2x&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Ditarik&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Kotak Hilang&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Transaksi Diatas Minimal&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Total Kunjungan&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Capaian Target&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-1">-->
                            <!--        <label for="" class="label-control">Capaian Kunjungan&nbsp;&nbsp;</label>-->
                            <!--    </div>-->
                            <!--    <div id="div1"></div>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modaldon" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Rincian Donasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#modalwar" data-bs-toggle="modal">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="okd">
                                    <thead>
                                        <tr>
                                            <th>Nama Donatur</th>
                                            <th>Total Donasi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="boy">
                                        
                                    </tbody>
                                    <tfoot id="boys">
                                        
                                    </tfoot>
                                </table>
                            </div>
                            <!--<div class="row">-->
                            <!--    <div class="col-lg-8">-->
                            <!--        <label for="" class="label-control"><b>Nama Donatur</b></label>-->
                            <!--    </div>-->
                            <!--    <div class="col-lg-4">-->
                            <!--        <label for="" class="label-control"><b>Total Donasi</b></label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div id="boy">-->

                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>
            
            
            <!--Modal Kacab-->
            <div class="modal fade" id="modalkacab">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Target Bulan Ini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                  <div class="modal-body">
                   <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Nama Karyawan</th>
                          <th>Total Kolekting</th>
                          <th>Bulan</th>
                        </tr>
                        </thead>
                        <tbody id="tab_target">
                         
                        </tbody>
                        <tfoot>
                      
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="modaltarget">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Target Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                    <form method="post" action="{{url('targetkacc')}}" >
                  <div class="modal-body">
                      @csrf
                          <div class="form">
                                <label for="name">Target Bulan Ini</label>
                                <input type="text" name="targetkac" id="targetkac" class="form-control" aria-describedby="name" placeholder="Contoh: 100.000.000" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                          </div>
                          
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
                </div>
              </div>
            </div>
                
              
            <div class="modal fade" id="belumdikunjungi" >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian petugas yang memiliki potongan <?php $dt = new DateTime(); echo $dt->format('M/Y'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                  <div class="modal-body">
                   <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Nama Karyawan</th>
                          <th>Belum dikunjungi</th>
                          <th>Tutup 1x</th>
                          <th>Potongan</th>
                        </tr>
                        </thead>
                        <tbody id="tab_blmdikunjungi">
                         
                        </tbody>
                        <tfoot>
                      
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="belumdiassignment" >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian petugas yang belum mengunjungi donatur <?php $dt = new DateTime(); echo $dt->format('M/Y'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                  <div class="modal-body">
                   <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Nama Karyawan</th>
                          <th>Jumlah</th>
                        </tr>
                        </thead>
                        <tbody id="belummas">
                         
                        </tbody>
                        <tfoot>
                      
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="modalmodalan">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail <span id="jddl"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="saya" class="table table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Donatur</th>
                                                    <th>Tanggal</th>
                                                    <th>Pembayaran</th>
                                                </tr>
                                            </thead>
                                            <tbody id="kapten">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="modal-footer">-->
                            <!--<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>-->
                            <!--<button type="submit" class="btn btn-primary">Simpan</button>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
            
            <!--End Modal-->

        <div class="row">
            
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Filter</h4>
                                        <div class="pull-right">
                                            <button id="vs" class="btn btn-xxs btn-success" data-value="no" style="margin-right: 10px;">Show Kolom Versus</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <div class="row">
                                                <?php $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first(); ?>
                                                @if(Auth::user()->kolekting == 'admin')
                                                <div class="col-md-6 mb-3">
                                                    <select required id="cu" class="form-control default-select wide" name="field">
                                                        <option value="nama">Berdasarkan Tim Kolektor</option>
                                                        <option value="kota">Berdasarkan Kantor</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3" id="kotas" style="display:block;">
                                                    <select class="form-control default-select wide" id="val_kot" name="kotas">
                                                        <option value="">- Pilih Unit -</option>
                                                        @foreach($kotas as $kot)
                                                        <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @elseif(Auth::user()->kolekting == 'kacab')
                                                    @if($k == null)
                                                    <div class="col-md-6 mb-3">
                                                        <select required id="cu" class="form-control default-select wide" name="field">
                                                            <option value="nama">Berdasarkan Tim Kolektor</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="kotas" style="display:block;">
                                                    </div>
                                                    @else
                                                        <div class="col-md-6 mb-3">
                                                            <select required id="cu" class="form-control default-select wide" name="field">
                                                                <option value="nama">Berdasarkan Tim Kolektor</option>
                                                                <option value="kota">Berdasarkan Kantor</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3" id="kotas" style="display:block;">
                                                            <select class="form-control default-select wide" id="val_kot" name="kotas">
                                                                <option value="">- Pilih Unit -</option>
                                                                @foreach($kotas as $kot)
                                                                <option value="{{$kot->id}}">{{$kot->unit}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                <input type="hidden" id="val">
                                                <input type="hidden" id="val1">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Status Approval</label>
                                                    <select id="approve" class="form-control default-select wide" name="approve">
                                                        <option value="">- Pilih Status Approval -</option>
                                                        <option value="2">Pending</option>
                                                        <option value="1" selected>Approved</option>
                                                        <option value="3">Rejected</option>
                                                        <option value="0">All</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Pilih</label>
                                                    <select id="plhtgl" class="form-control default-select wide" name="plhtgl">
                                                        <option value="0">Periode</option>
                                                        <option value="1">Bulan</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3" id="blnbln" hidden>
                                                    <label>Bulan&Tahun :</label>
                                                    <input type="text" class="form-control daterange cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tgldari">
                                                    <label>Dari</label>
                                                    <input type="date" class="form-control" id="darii" name="dari">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tglke">
                                                    <label>Ke</label>
                                                    <input type="date" class="form-control" id="sampaii" name="sampai">
                                                </div>
                                            </div>
                                            <div id="versus_bln" style="display:none">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3" id="blnbln1" hidden>
                                                        <label>Bulan&Tahun :</label>
                                                        <input type="text" class="form-control daterange cek4" name="blns" id="blns1" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="tgldari1">
                                                        <label>Dari</label>
                                                        <input type="date" class="form-control" id="dari2" name="dari2">
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="tglke1">
                                                        <label>Ke</label>
                                                        <input type="date" class="form-control" id="sampai2" name="sampai2">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary btn-sm col-md-12" id="filterr">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--ama disini-->
                    
                </div>
            </div>
        </div>
        <!--disini-->
        
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <!--<div class="card-header">-->
                    <!--    <h4 class="card-title">Default Tab</h4>-->
                    <!--</div>-->
                    <div class="card-body">
                    <!-- Nav tabs -->
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#home"><i class="la la-list-alt me-2"></i> Report Assignment</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#profile"><i class="la la-money-bill-wave me-2"></i> Capaian Omset</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#contact"><i class="la la-route me-2"></i> Capaian Kunjungan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#message"><i class="la la-users me-2"></i> Capaian Donatur</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="home" role="tabpanel">
                                    <div class="pt-4">
                                        <div class="row mt-4">
                                            <!--<div class="col-sm-12">-->
                                                <div class="table-responsive">
                                                    <table class="table table-striped" id="use_use" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Kolektor</th>
                                                                <th>Assignment Hari Ini</th>
                                                                <th>Assignment Aktif Hari Ini</th>
                                                                <th>Total Assignment Aktif</th>
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
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        <!--</div>-->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile">
                                    <div class="pt-4">
                                        @if(Auth::user()->kolekting == 'admin')
                                        <div class="d-flex justify-content-end  mb-3">
                                            <a class="btn btn-xxs btn-info " data-bs-toggle="modal" data-bs-target="#modali" href="javascript:void(0)" onclick="kota()" style="margin-right: 10px;">Potongan</a>
                                            <a class="btn btn-xxs btn-danger" data-bs-toggle="modal" data-bs-target="#modalbelum" href="javascript:void(0)" onclick="kota2()">Belum Assignment</a>
                                        </div>
                                        @elseif(Auth::user()->kolekting == 'kacab')
                                        <div class="d-flex justify-content-end mb-3">
                                            <span class="btn btn-info btn-xxs" data-bs-toggle="modal" data-bs-target="#belumdikunjungi" id="count" href="javascript:void(0)"></span>
                                            <span class="btn btn-danger btn-xxs" data-bs-toggle="modal" data-bs-target="#belumdiassignment" id="countnot" href="javascript:void(0)"></span>
                                                 
                                            <input type="hidden" id="val" value="">
                                        </div>
                                        @endif
                                        
                                        <div class="row mt-4">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <div id="gett" style="display:blok"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="contact">
                                    <div class="pt-4">
                                        <div class="row">
                                                <div id="cap_kol" style="display:blok"></div>
                                                <div class="table-responsive">
                                                    <table id="user_table1" class="table table-striped" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th id="jdl"></th>
                                                                <th>Donasi</th>
                                                                <th>Tidak Donasi</th>
                                                                <th>Tutup</th>
                                                                <th>Tutup 2x</th>
                                                                <th>Ditarik</th>
                                                                <th>Kotak Hilang</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                
                                                        </tbody>
                                                        <tfoot>
                                
                                                        </tfoot>
                                                    </table>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="message">
                                    <div class="pt-4">
                                        
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label>Pembayaran</label>
                                                <select id="bayar" class="hehe" multiple="multiple" name="bayar[]">
                                                    @foreach($pem as $p)
                                                    <option value="{{$p->pembayaran}}">{{$p->pembayaran}}</option>
                                                    @endforeach
                                                </select>
                                                <small>*jika tidak dipilih akan otomatis seluruh pembayaran (transfer, dijemput, noncash dan teller)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <!--<div class="col-12">-->
                                                <div class="table-responsive">
                                                    <table  class="table table-striped" id="capaiandonatur" width="100%">
                                                        <thead>
                                                            <tr align="center">
                                                                <th>No</th>
                                                                <th>Petugas</th>
                                                                <th>Donasi</th>
                                                                <th>Tidak Donasi</th>
                                                                <th>Tutup</th>
                                                                <th>Tutup 2x</th>
                                                                <th>Ditarik</th>
                                                                <th>Kotak Hilang</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                            
                                                        </tbody>
                                                        <tfoot>
                                                            <tr align="center">
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                <!--</div>-->
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
</div>
@endsection