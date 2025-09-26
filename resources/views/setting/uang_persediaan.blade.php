@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
      
         <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Entry Uang Persediaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                               
                                <div class="row">
                                    <div class="col-lg-12">
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Tanggal :</label>
                                            <div class="col-sm-9">
                                                 <input type="text" class="col-lg-3 mb-3 form-control dates " name="tgl" id="tgl" autocomplete="off" placeholder="{{date('Y-m') }}">
                                                <!--<input  type="date" class=" col-lg-3 mb-3 form-control " id="tgl" name="tgl">-->
                                            </div>
                                        </div>
                                        
                                        
                                          <div class="mb-3 row">
                                            <label class="col-sm-3">Kantor :</label>
                                            <div class="col-sm-9">
                                            <select class="form-control input-sm real" name="kantor" id="kantor">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($kantor as $val)
                                                    <option value="{{$val->id}}"  data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Nominal :</label>
                                            <div class="col-sm-9">
                                                <input type="text"  name="nominal" id="nominal" onkeyup="rupiah(this);"  value="" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Pembayaran :</label>
                                            <div class="col-sm-9">
                                               <select required class="form-control input-sm" name="bayar" id="bayar">
                                                    <option value="">Pilih Jenis</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="bank">Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Jenis :</label>
                                            <div class="col-sm-9">
                                                 <select required class="form-control input-sm" name="jenis" id="jenis">
                                                    <option value="">Pilih Jenis</option>
                                                    <option value="bulan">Bulan</option>
                                                    <option value="transaksi">Transaksi</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                </div>
                            </div>

                        </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                               <button type="button" class="btn btn-success btn-sm simpan coba " id="smp1" >Simpan</button>
                            <!--<button type="button" class="btn btn-success simpan"  id="smp1">Simpan</button>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
        
        
        <!--<div class="modal fade" id="modals" >-->
        <!--    <div class="modal-dialog modal-lg" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">Detail Pengajuan</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="basic-form" id="bodai">-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                  <div class="modal-footer">-->
        <!--                    <div id="footai">-->
        <!--                    </div>-->
        <!--                </div>    -->
                        
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
            
             
            
            
        <!--</div>-->
        
      
        
        <!--<div class="modal fade" id="modal-reject" >-->
        <!--    <div class="modal-dialog modal-dialog-centered" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">Alasan Pengajuan di Reject</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form class="form-horizontal" method="post" id="reject_form" enctype="multipart/form-data">-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="basic-form">-->
        <!--                        <div id="rej"></div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="modal-footer">-->
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
        <!--                    <button type="submit" class="btn btn-primary blokkk" id="smpnz">Submit</button>-->
        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <!-- End Modal -->



        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                     <form method="GET" action="{{url('')}}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <!--<button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Tambah</button>-->
                        <!--    @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')-->
                        <!--        <a class="btn btn-warning btn-xxs" data-bs-toggle="modal" data-bs-target="#waktu" href="#" style="float:right">Edit Min Waktu Pengajuan</a>-->
                        <!--          <a href="{{url('downloadformat/export')}}" name="data1" value="data1" class= "btn btn-primary btn-xxs" style="float:right">Download Format</a>-->
                        <!--<a class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right">Import</a>-->
                        <!--<button type="submit" class="btn btn-primary btn-xxs" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>-->
                        <!--    @endif-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                   
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label >Unit :</label>
                                        <select class="form-control cekk" name="kntr" id="kntr">
                                            <option value="">- Pilih -</option>
                                            @foreach($kantor as $val)
                                            <option value="{{$val->id}}">{{$val->unit}}</option>
                                            @endforeach
                                        </select>
                                </div>


                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Periode :</label>
                                    <input type="text" class="form-control tgl dates" id="dari" name="dari">
                                </div>


                                
                                <!-- <div class="col-lg-3 mb-3">-->
                                <!--    <label >Jenis :</label>-->
                                <!--        <select class="form-control cekj" name="jns" id="jns">-->
                                <!--             <option value="bulan">Bulan</option>-->
                                <!--              <option value="transaksi">Transaksi</option>-->
                                <!--        </select>-->
                                <!--</div>-->

                                <!--<div class="col-lg-3 mb-3">-->
                                <!--    <label >Pembayaran :</label>-->
                                <!--        <select class="form-control cekb" name="byr" id="byr">-->
                                <!--             <option value="bank">Bank</option>-->
                                <!--              <option value="cash">Cash</option>-->
                                <!--        </select>-->
                                <!--</div>-->
                                
                                
                              

                                
                                  <div class="pull-right" style="display: none" id="one">
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            
                            
                            <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>




                                </div>
                        
                            <div class="pull-right"> 

                            </div>
                              
                            </div>
                        </div>
                        <br>
                        
                          <div class="row">
                                    <label class="red">*Catatan.</label>
                                    <label class="red">Perubahan Uang Persediaan Jenis Per Transaksi Tidak Boleh Lebih Besar Dari Jenis Per Bulan</label>
                                    <!--<label class="red"></label>-->
                                </div>
                        <div class="table-responsive">
                            <!--<button id="aksis">buka</button>-->
                        <!--<div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">-->
                                <table id="user_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                             <th class=" hide_column">id</th>
                                             <th>Tanggal</th>
                                             <th>Kantor</th>
                                             <th>Jenis Per</th>
                                             <th>Bayar Per</th>
                                             <th>Nominal</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                    </form>    
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection