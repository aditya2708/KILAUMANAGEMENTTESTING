@extends('template')
@section('konten')

<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">-->

<div class="content-body">
    <div class="container-fluid">
        <!--modal-->
        <div class="modal fade"  id="moddet">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"  style="max-width: 60%;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="exampleModalLabel">Detail Data <span id="names"></span></h3>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row">
                                <style>
                                    .table-bordered > tbody > tr > td, 
                                    .table-bordered > tbody > tr > th, 
                                    .table-bordered > tfoot > tr > td, 
                                    .table-bordered > tfoot > tr > th, 
                                    .table-bordered > thead > tr > th, 
                                    .table-bordered > thead > tr > td 
                                    {
                                        border: 1px solid #cecece; 
                                    }
                                    
                                </style>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                	    <table class="table table-bordered " width="100%" id="hoxxx">
                                	        <thead id="hoc">
                                	            
                                		      </thead>
                                		      <tbody id="hoccc">
                                		            
                                		      </tbody>
                                		</table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        
        <!--<div class="modal fade" id="moddetrenpros">-->
        <!--    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"  style="max-width: 60%;" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h3 class="modal-title" >Detail <span id="namess"></span></h3>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#modtri" data-bs-toggle="modal"></button>-->
        <!--            </div>-->
                        
        <!--            <div class="modal-body">-->
                        
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <div class="modal fade"  id="modtri">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"  style="max-width: 60%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Entri KPI</span></h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        
                    <div class="modal-body" style="background: #e9e9e9">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12">
                                    <div class="card">
                                        
                                        <div class="card-body">
                                        <h5 class="text-center mb-3">Nama Karyawan</h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="form-control select2nya" id="karyawan" name="karyawan">
                                                        <option value="" disabled>Pilih</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            
                                            <div id="detdet" style="display: none">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"> <h5 class="text-center" style="margin-top: 3px">Bukti Dukung</h5></div>
                                                                <div class="col-md-4">
                                                                    <div class="d-flex justify-content-end">
                                                                        <button type="button" id="tutups" class="btn btn-close mb-2" aria-label="Close"></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                           
                                                            
                                                            <div class="table-responsive">
                                                                <table id="cc" class="table table-striped" width="100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tanggal</th>
                                                                            <th>Parent</th>
                                                                            <th>Laporan</th>
                                                                            <th>Capaian</th>
                                                                            <th>Lampiran</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="cobsa">
                                                                        
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center">List Kehadiran</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <td>Hadir</td>
                                                                        <td>Sakit</td>
                                                                        <td>Terlambat</td>
                                                                        <td>Perdin</td>
                                                                        <td>Bolos</td>
                                                                        <td>Cuti</td>
                                                                        <td>Cuti Penting</td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="waw">
                                                                    
                                                                </tbody>
                                                            </table>
                                                            <!--<hr>-->
                                                            <!--<table class="table table-striped" id="kphi" width="100%">-->
                                                                
                                                            <!--</table>-->
                                                            
                                                            <!--<hr>-->
                                                        
                                                            <!--<div class="input-group">-->
                                                            <!--    <input class="form-control" min="0.0" max="100" type="number" step="0.1" name="kpi_hadir" id="kpi_hadir" autocomplete="off" placeholder="1 - 5">-->
                                                            <!--    <span class="input-group-text" id="kpi_hadir">%</span>-->
                                                            <!--</div>-->
                                                        
                                                            <!--<small>*maksimal input 5</small>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">KPI Sikap</h5>
                                                        
                                                        <div class="input-group">
                                                            <input class="form-control" min="0.0" max="5.0" type="number" step="0.1" name="kpi_sikap" id="kpi_sikap" autocomplete="off" placeholder="0 - 5">
                                                            <span class="input-group-text" id="kpi_sikap">%</span>
                                                        </div>
                                                        
                                                        <small>*maksimal input 5</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">KPI Kehadiran</h5>
                                                        
                                                        <div class="input-group">
                                                            <input class="form-control" min="0.0" max="5.0" type="number" step="0.1" name="kpi_hadir" id="kpi_hadir" autocomplete="off" placeholder="0 - 5">
                                                            <span class="input-group-text" id="kpi_sikap">%</span>
                                                        </div>
                                                        
                                                        <small>*maksimal input 5</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">Proses</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" id="wiw" width="100%">
                                                                <thead>
                                                                    <tr style="font-weight: 500;">
                                                                        <td>Bagian</td>
                                                                        <td>Parent</td>
                                                                        <td>Rencana</td>
                                                                        <td>Satuan</td>
                                                                        <!--<td>Metode</td>-->
                                                                        <td>Target</td>
                                                                        <td>Mulai</td> 
                                                                        <td>Selesai</td> 
                                                                        <!--<td>Realisasi</td>-->
                                                                        <!--<td>Bukti</td>-->
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="bodpros" val="proses">
                                                                    
                                                                </tbody>
                                                                
                                                                <!-- <body>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td td colspan="2">Jumlah</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td id="itungcil"></td>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td td colspan="2">Total Tugas</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td id="tugs"></td>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td td colspan="2">(Jum / ToTug)</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td id="tungcep"></td>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td colspan="2">Indikator</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td id="guraa"></td>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td td colspan="2">KPI</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td id="tungtung"></td>
                                                                    </tr>
                                                                </body> -->
                                                                
                                                            </table>
                                                        </div>
                                                        <!--<hr>-->
                                                        
                                                        <!--<div class="input-group">-->
                                                        <!--    <input class="form-control" min="0.0" max="25" type="number" step="0.1" name="kpi_proses" id="kpi_proses" autocomplete="off" placeholder="1 - 25">-->
                                                        <!--    <span class="input-group-text" id="kpi_proses">%</span>-->
                                                        <!--</div>-->
                                                        
                                                        <!--<small>*maksimal input 25</small>-->
                                                    
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">Hasil</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" id="wew" width="100%">
                                                                <thead>
                                                                    <tr style="font-weight: 500;">
                                                                        <td>Bagian</td>
                                                                        <td>ID</td>
                                                                        <!--<th>Jenis</th>-->
                                                                        <td>Rencana</td>
                                                                        <td>Satuan</td>
                                                                        <!--<th>Metode</th>-->
                                                                        <td>Target</td>
                                                                        <!--<td>Realisasi</td>-->
                                                                        <!--<td>Bukti</td>-->
                                                                        <!--<th>Validasi</th>-->
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="bodhas" val="hasil">
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!--<hr>-->
                                                        
                                                        <!--<div class="input-group">-->
                                                        <!--    <input class="form-control" min="0.0" max="65" type="number" step="0.1" name="kpi_hasil" id="kpi_hasil" autocomplete="off" placeholder="1 - 65">-->
                                                        <!--    <span class="input-group-text" id="kpi_hasil">%</span>-->
                                                        <!--</div>-->
                                                        
                                                        <!--<small>*maksimal input 65</small>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">KPI Proses</h5>
                                                        
                                                        <div class="input-group">
                                                            <input class="form-control" min="0.0" max="25" type="number" step="0.1" name="kpi_proses" id="kpi_proses" autocomplete="off" placeholder="0 - 25">
                                                            <span class="input-group-text" id="kpi_proses">%</span>
                                                        </div>
                                                        
                                                        <small>*maksimal input 25</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="text-center mb-3">KPI Hasil</h5>
                                                        
                                                        <div class="input-group">
                                                            <input class="form-control" min="0.0" max="65" type="number" step="0.1" name="kpi_hasil" id="kpi_hasil" autocomplete="off" placeholder="0 - 65">
                                                            <span class="input-group-text" id="kpi_hasi">%</span>
                                                        </div>
                                                        
                                                        <small>*maksimal input 65</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!--<div class="col-lg-12">-->
                                    <!--    <div class="row">-->
                                    <!--        <div class="col-lg-12">-->
                                    <!--            <div class="card">-->
                                    <!--                <div class="card-body">-->
                                    <!--                    <h5 class="text-center mb-3">Capaian</h5>-->
                                    <!--                    <div class="table-responsive">-->
                                    <!--                        <table class="table table-bordered" width="100%">-->
                                    <!--                            <thead>-->
                                    <!--                                <tr>-->
                                    <!--                                    <td>Tanggal</td>-->
                                    <!--                                    <td>Kunjungan</td>-->
                                    <!--                                    <td>Penawaran</td>-->
                                    <!--                                </tr>-->
                                    <!--                            </thead>-->
                                    <!--                        </table>-->
                                    <!--                    </div>-->
                                    <!--                </div>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="modal-footer">
                        <button type="button" id="rorsih" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                    <div class="col-md-3 mb-3">
                                        <label>Unit</label>
                                        <select required class="form-control cek1 zzzzz" name="unit" id="unit">
                                        </select>
                                    </div>
                                    @else
                                    <div class="col-md-3 mb-3">
                                        <label>Unit</label>
                                        <select required class="form-control cek1 zzzzz" name="unit" id="unit">
                                        @if(count($units) > 0)
                                            <option selected="selected" value="">Pilih Unit</option>
                                            @foreach($units as $u)
                                            <option value="{{$u->id}}">{{$u->unit}}</option>
                                            @endforeach
                                        @else
                                            <!--<option value="">Tidak Ada</option>-->
                                        @endif
                                        </select>
                                    </div>
                                     @endif
                                     
                                     <div class="col-md-3 mb-3">
                                        <label>Bulan</label>
                                        <input type="month" name="bln" id="bln" autocomplete="off" value="{{date('Y-m')}}" class="form-control cek2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                            <button class="btn btn-xxs btn-primary btn-rounded" style="margin-right: 10px" id="kpi" type="button">Entri KPI</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>ID Karyawan</th>
                                        <th>Nama</th>
                                        <th>Attitude (%)</th>
                                        <th>Proses (%)</th>
                                        <th>Ouput (%)</th>
                                        <th>KPI (%)</th>
                                        <th>Tj Jabatan</th>
                                        <th>Perhitungan</th>
                                        <th>Potongan</th>
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