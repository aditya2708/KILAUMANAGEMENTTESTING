@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!-- Modal -->
        
        <!--<div class="modal fade" id="modal-import" >-->
        <!--    <div class="modal-dialog modal-dialog-centered" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">Upload</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form class="form-horizontal" method="post" id="upload_form" enctype="multipart/form-data">-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="input-group mb-3">-->
        <!--                        <span class="input-group-text">Pilih File</span>-->
        <!--                        <div class="form-file">-->
        <!--                            <input type="file" name="file" id="file" class="form-file-input form-control" required>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <div class="modal-footer">-->
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
        <!--                    <button type="submit" class="btn btn-primary " id="smpp">Simpan</button>-->

        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
           <div class="modal fade" id="waktu" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Minimal Waktu Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form12" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                              
                                                <div class="col-md-12">
                                                    <label >Minimal Waktu:</label>
                                                    <input type="text" name="waktu" id="waktu" class="form-control" placeholder="Hari" required>
                                                </div>

                                            </div>
                                        

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <button type="button" class="btn btn-success btn-sm update"  >Simpan</button>
                            <!--<button type="submit" class="btn btn-primary blokkk" id="smpn">Simpan</button>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
    <!--<div class="modal fade" id="waktu">-->
    <!--        <div class="modal-dialog modal-dialog-centered" role="document">-->
    <!--            <div class="modal-content">-->
    <!--                <div class="modal-header">-->
    <!--                    <h5 class="modal-title">Edit Minimal Waktu </h5>-->
    <!--                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
    <!--                </div>-->
                    
    <!--                <form action="" method="POST" enctype="multipart/form-data">-->
					<!--{{ csrf_field() }}-->
 
		
    <!--                            <div class="col-md-6">-->
    <!--                                <label >Minimal Waktu:</label>-->
    <!--                                <input type="text" name="waktu" class="form-control"  placeholder="Hari" required>-->
    <!--                            </div>-->

				<!-- <div class="pull-right">-->
				<!--	<input style="margin: 0px; margin-bottom: 10px; margin-left: 10px" type="submit" value="Upload" class="btn btn-primary">-->
				<!--</div>-->
				<!--</form>-->
                    
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
        
        
            <div class="modal fade" id="modalb">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Data Anggaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <form action="{{url('pengajuananggaran/import')}}" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
 
					<!--<div class="form-group">-->
					<!--	<b>Pilih File</b><br/>-->
					<!--	<input type="file" name="file">-->
					<!--</div>-->
 
                            <div class="input-group mb-3">
                                <span class="input-group-text">Pilih File</span>
                                <div class="form-file">
                                    <input  type="text" name="waktu" class="form-file-input form-control" required>
                                </div>
                            </div>
 
				 <div class="pull-right">
					<input style="margin: 0px; margin-bottom: 10px; margin-left: 10px" type="submit" value="Upload" class="btn btn-primary">
				</div>
				</form>
                    
                </div>
            </div>
        </div>
        
       
        <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Entry Anggaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <h3 style="margin-top: -10px">Informasi</h3>
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="col-lg-6">
                                        
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Tanggal</label>
                                            <div class="col-sm-6">
                                                <!--<text>{{ date('Y-m-d H:i:s') }}</text>-->
                                                <input type="date" class="form-control" value="" id="tgl_now" name="tgl_now">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Nama Akun :</label>
                                            <div class="col-sm-9">
                                                <select class="js-example-basic-single saldd real" name="saldo_dana" id="saldo_dana" style="width:100%">
                                                    <option value="">- Pilih -</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                           <div class="mb-3 row">
                                            <label class="col-sm-3">Jabatan :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm" name="jbt" id="jbt">
                                                    <option value="">- Pilih -</option>
                                                    @foreach($jabat as $val)
                                                    <option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                       <!--<div class="mb-3 row">-->
                                       <!--     <label class="col-sm-3">Realisasi :</label>-->
                                       <!--     <div class="col-sm-9">-->
                                       <!--         <input type="text"name="realisasi" id="realisasi" value="" onkeyup="rupiah(this);" class="form-control form-control-sm">-->
                                       <!--     </div>-->
                                       <!-- </div>-->
                                    
                                    
                                    </div>

                                    <div class="col-lg-6">
                                      
                                          <div class="mb-3 row">
                                            <label class="col-sm-3">Nominal :</label>
                                            <div class="col-sm-9">
                                                <input type="text"  name="nominal_m" id="nominal_m" onkeyup="rupiah(this);"  value="" class="form-control form-control-sm">
                                                <!--<p id="terbilang" style="font-size:12px"></p>-->
                                            </div>
                                        </div>
                                        
                                        
                                           
                                                <div class="mb-3 row">
                                                    <label class="col-sm-3 col-form-label">Jenis :</label>
                                                    <div class="col-sm-9">
                                                        <select required class="form-control input-sm" name="jenis" id="jenis">
                                                            <option value="">Pilih Jenis</option>
                                                            <option value="anggaran">Anggaran</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                         <div class="mb-3 row">
                                            <label class="col-sm-3">Kantor :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control input-sm real" name="kantor" id="kantor">
                                                    <!--<option value="">- Pilih -</option>-->
                                                    @foreach($kantor as $val)
                                                    <option value="{{$val->id}}"  data-value="{{$val->unit}}">{{$val->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
        
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <hr style="margin: 0px; margin-bottom: 10px">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                              
                                
                                                <div class="col-md-4">
                                                    <label >Keterangan :</label>
                                                    <input type="text" name="ket" id="ket" class="form-control dodo">
                                                </div>

                                    
                                                <div class="col-md-4">
                                                    <label class="col-sm-3">Referensi</label>
                                                  <select class="form-control input-sm" name="referensi" id="referensi">
                                                    <option value="">- Pilih -</option>
                                                    <!--@foreach($jabat as $val)-->
                                                    <!--<option value="{{$val->id}}" data-value="{{$val->jabatan}}">{{$val->jabatan}}</option>-->
                                                    <!--@endforeach-->
                                                </select>
                                                </div>
                                                
                                            <div class="col-md-1 mb-3">
                                                <label>&nbsp;</label>
                                                <a id="add" class="btn btn-primary okkk"><i class="fa fa-plus"></i></a>
                                            </div>
                                            </div>
                                        
                                     
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                           <div class="panel panel-default">
                                <div class="panel-body">
                                    <table id="user_table_1" class="table table-bordered ">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Coa</th>
                                                <th>Nama Akun</th>
                                                <th>Nominal</th>
                                                <th>Kantor</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableag">

                                        </tbody>
                                        <tfoot id="footag">

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                            <button type="submit" class="btn btn-success blokkk" id="smpn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
        
        
        <div class="modal fade" id="modals" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="bodai">
                                
                                <!--     <div  id="rek_hide">-->
                                <!--         <div class="mb-3 row">-->
                                <!--        <label class="col-sm-3">Relokasi Dari :</label>-->
                                <!--       <div class="col-sm-9">-->
                                <!--            <select class="js-example-basic-single1 salddd " name="relokasi" id="relokasi" style="width:100%">-->
                                <!--            <option value=""></option>-->
                                <!--            </select>-->
                                <!--        </div>-->
                                <!--    </div>-->
                          
                                <!--</div>-->
                            </div>
                            
                            
                            <!--<div id="rek_hide">-->
                            <!--  <div  class="col-md-1">-->
                            <!--    <label>&nbsp;</label>-->
                            <!--    <a id="add" class="btn btn-primary"><i class="fa fa-plus"></i></a>-->
                            <!--  </div>-->
                            <!--</div>-->
                            
                        </div>
                      
                        
                       <!--<div class="table-responsive">-->
                       <!--         <table id="user_table_1" class="table table-bordered ">-->
                       <!--             <thead>-->
                       <!--                 <tr>-->
                       <!--                     <th>Nama Akun</th>-->
                       <!--                     <th>Saldo Anggaran</th>-->
                       <!--                     <th>Jenis</th>-->
                       <!--                     <th>Dari</th>-->
                       <!--                     <th>Nominal</th>-->
                       <!--                     <th>Total</th>-->
                       <!--                 </tr>-->
                       <!--             </thead>-->
                       <!--             <tbody id="table">-->

                       <!--             </tbody>-->
                       <!--             <tfoot id="foot">-->

                       <!--             </tfoot>-->
                       <!--         </table>-->
                       <!--     </div>-->
                            
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                        
                    </form>
                </div>
            </div>
            
             
            
            
        </div>
        
        <div class="modal fade" id="modal-default3" >
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-reject" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Alasan Pengajuan di Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="reject_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div id="rej"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
                            <button type="submit" class="btn btn-primary blokkk" id="smpnz">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->



        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                     <form method="GET" action="{{url('pengajuan-anggaran/export')}}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Tambah</button>
                            @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                <a class="btn btn-warning btn-xxs me-1" data-bs-toggle="modal" data-bs-target="#waktu" href="#" style="float:right">Edit Min Waktu Pengajuan</a>
                                <a href="{{url('downloadformat/export')}}" name="data1" value="data1" class= "btn btn-primary btn-xxs me-1" style="float:right">Download Format</a>
                                <a class="btn btn-primary btn-xxs me-1" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right">Import</a>
                               <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-xxs dropdown-toggle exp me-1" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                    <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                   
                            <div class="row">
                                <!--<div class="col-lg-3 mb-3">-->
                                <!--    <label>Pilih Via</label>-->
                                <!--    <select class="form-control default-select wide cek" name="via" id="via">-->
                                <!--        <option value="">All</option>-->
                                <!--        <option value="pengeluaran">Pengeluaran</option>-->
                                <!--        <option value="penyaluran">Penyaluran</option>-->
                                <!--        <option value="mutasi">Mutasi</option>-->
                                        <!--<option value="2">Tahun</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                                
                            <div class="col-lg-3 mb-3">
                                    <label >Periode :</label>
                                        <select class="form-control cekp" name="periodenya" id="periodenya">
                                             <option value="">Pilih</option>
                                             <option value="harian">harian</option>
                                             <option value="bulan">bulan</option>
                                             <!--<option value="tahun">tahun</option>-->
                                        </select>
                                </div>
                         
                            
                                <div class="col-lg-3 mb-3">
                                    <label >Unit :</label>
                                        <select class="form-control cekk" name="kntr" id="kntr">
                                            @foreach($kantor as $val)
                                            <option value="{{$val->id}}">{{$val->unit}}</option>
                                            @endforeach
                                        </select>
                                </div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Status</label>
                                    <select class="form-control default-select wide ceks" name="stts" id="stts">
                                        <option value="">All</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Pending</option>
                                        <option value="0">Rejected</option>
                                    </select>
                                </div>

                                <div hidden id="harian_hide" class="row">
                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Dari  :</label>
                                    <input type="date" class="form-control cekd" id="dari" name="dari">
                                </div>
                            
                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Sampai:</label>
                                    <input type="date" class="form-control cekt" id="sampai" name="sampai">
                                </div>
                            </div>
                            
                            <div hidden id="bulanan_hide" class="row">
                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Dari Bulan:</label>
                                    <input type="text" class="col-lg-3 mb-3 form-control bulan cekd" name="darib" id="darib" autocomplete="off" placeholder="{{date('Y-m') }}">
                                </div>
                            
                            
                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Sampai Bulan:</label>
                                    <input type="text" class="col-lg-3 mb-3 form-control bulan cekt" name="sampaib" id="sampaib" autocomplete="off" placeholder="{{date('Y-m') }}">
                                </div>
                            </div>
                            
                            <div hidden id="tahunan_hide" class="row">
                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>dari Tahuan:</label>
                                    <input type="text" class="col-lg-3 mb-3 form-control tahun cekd" name="darit" id="darit" autocomplete="off" placeholder="{{date('Y') }}">
                                </div>

                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Sampai Tahuan:</label>
                                    <input type="text" class="col-lg-3 mb-3 form-control tahun cekt" name="sampait" id="sampait" autocomplete="off" placeholder="{{date('Y') }}">
                                </div>
                            </div>      
                            
                                  <div class="pull-right" style="display: none" id="one">
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            
                            
                            <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>




                            <!--<a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>-->
                                </div>
                            <!--<button type="button" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>-->

                            <!--<button type="submit" class="btn btn-success btn-block" name="data1" value="data1" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>-->

                            <div class="pull-right"> 

                            <!--<a href="javascript:void(0)" class="btn btn-primary light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>-->
                            </div>
                              
                            </div>
                        </div>
                        <br>
                        
                        <div class="table-responsive">
                            <!--<button id="aksis">buka</button>-->
                        <!--<div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">-->
                                <table id="user_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>Nama Akun</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Anggaran</th>
                                             <th>Relokasi</th>
                                             <th>Tambahan</th>
                                             <th>Total</th>
                                             <th>Realisasi</th>
                                             <th>Kantor</th>
                                             <th>Jabatan</th>
                                             <th>Referensi</th>
                                             <th>Program</th>
                                             <th>User Input</th>
                                             <th>Keuangan Approver</th>
                                             <th>Direktur Approver</th>
                                             <th>User Reject</th>
                                             <th>Keterangan Relokasi</th>
                                             <th>Alasan</th>
                                             <th>status</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Total :</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
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
                        
                    </form>    
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


