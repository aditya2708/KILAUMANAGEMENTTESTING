@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
    
         <div class="col-lg-12">
                    <div class="row">
                            <div class="col-xl-2 col-sm-2">
                                <a href="{{ url('approve-anggaran') }}" target="_blank">
                                    <div class="widget-stat hoverlink card">
                                        <div class="card-body  p-4">
                                                <div class="media-body">
                                                    <p class="mb-1">Pengajuan</p>
                                                        <h4 class="mb-0" id="jmlpengajuan">0</h4>
                                                </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        
                        <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Pencairan</p>
                                                <h4 class="mt-0" id="saldow">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>
                       
                        
                        
                        
                        <div class="col-xl-2 col-sm-2">
                            <a href="{{ url('pengeluaran') }}" target="_blank">
                                <div class="widget-stat hoverlink card">
                                    <div class="card-body p-4">
                                            <div class="media-body">
                                                <p class="mb-1">Pengeluaran</p>
                                                    <h4 class="mb-0" id="jmlpengeluaran">0</h4>
                                            </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-2 col-sm-2">
                            <a href="{{ url('penerimaan') }}" target="_blank">
                                <div class="widget-stat hoverlink card">
                                    <div class="card-body p-4">
                                            <div class="media-body">
                                                <p class="mb-1">Penerimaan</p>
                                                    <h4 class="mt-0" id="jmlpenerimaan">0</h4>
                                            </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    
                       
                       
                        <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Pertangung Jawaban</p>
                                            <h4 class="mb-0" id="saldow">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>
                      
                       
                        <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Penutupan</p>
                                                <h4 class="mt-0" id="jmlpenutup">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>

                </div>
        
        
        
                 <div class="col-lg-12">
                    <div class="row">
                       <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Cash</p>
                                            <h4 class="mt-0" id="jmlcash">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div> 
                        
                         <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1"> Bank</p>
                                            <h4 class="mt-0" id="jmlbank">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div> 
                       
                         <div class="col-xl-2 col-sm-2">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Saldo Cash Bank</p>
                                            <h4 class="mt-0" id="jmlsaldo">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div> 
                        
                    </div>

                </div>
      
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                     <form method="GET" action="{{url('kas-bank/export')}}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <!--<a href="{{url('downloadformat/export')}}" name="data1" value="data1" class= "btn btn-primary btn-xxs" style="float:right">Download Format</a>-->
                        <!--<a class="btn btn-primary btn-xxs" data-bs-toggle="modal" data-bs-target="#modalb" href="#" style="float:right">Import</a>-->
                        <!--    <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Tambah</button>-->
                        <button type="submit" class="btn btn-primary btn-xxs" data-bs-toggle="tooltip" data-bs-placement="top" title="Ekspor Data">Export</button>
                           
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
                                
                             <div class="col-md-3 mb-3">
                                <label>Periode :</label>
                                <input type="date" class="form-control dates cek4" name="blns" id="blns" >
                            </div>


                                <div class="col-md-3 mb-3">
                                    <label>Kantor</label>
                                        <select required class="form-control cek3" name="kntr" id="kntr">
                                            <!--<option value="">- Pilih -</option>-->
                                            @foreach($kantor as $val)
                                                <option value="{{$val->id}}" data-value="{{$val->unit}}" {{$val->id == Auth::user()->id_kantor ? 'selected' : ''}}> {{$val->unit}} </option>
                                            @endforeach
                                        </select>
                                </div> 
                               

                                <!--<div class="col-lg-3 mb-3" id="tgldari">-->
                                <!--    <label>Dari :</label>-->
                                <!--    <input type="date" class="form-control cekd" id="dari" name="dari">-->
                                <!--</div>-->

                                <!--<div class="col-lg-3 mb-3" id="tglke">-->
                                <!--    <label>Sampai :</label>-->
                                <!--    <input type="date" class="form-control cekt" id="sampai" name="sampai">-->
                                <!--</div>-->
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Status</label>
                                    <select class="form-control cek2" name="stts" id="stts">
                                        <option value="y">Aktif</option>
                                        <option value="n">In Aktif</option>
                                    </select>
                                </div>
                                
                                 <div class="col-lg-3 mb-3">
                                    <label>waktu</label>
                                    <select class="form-control  cek1" name="waktu" id="waktu">
                                        <option value="realtime">Realtime</option>
                                        <option value="clossing">Clossing</option>
                                    </select>
                                </div>

                                
                                  <div class="pull-right" style="display: none" id="one">
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            
                            
                            <!--<button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>-->




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
                                            <th>COA</th>
                                            <th>Nama Akun</th>
                                            <th>Saldo Awal</th>
                                            <th>Debet</th>
                                            <th>Kredit</th>
                                            <th>Saldo Akhir</th>
                                            <th>Last Opname</th>
                                            <th>Kantor</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>Total :</th>
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




