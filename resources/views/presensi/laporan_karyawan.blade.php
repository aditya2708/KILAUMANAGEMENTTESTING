@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan Karyawan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->
        <input type="hidden" id="id_lap_hide">
        <input type="hidden" id="tgl_hide">
        <!-- modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-lg modal-center modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h3 class="modal-title">Kegiatan  <span id="gore"></span></h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div> 
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <button type="button" class="btn btn-info btn-xs" data-bs-target="#capaianOmset" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">Capaian Omset</button>
                            </div>
                        </div>
                            
                            <!--<hr>-->
                            <!--<div class="d-flex bd-highlight mb-3">-->
                            <!--    <div class="p-2 flex-fill bd-highlight">-->
                            <!--        <button type="button" class="btn btn-info btn-sm" data-bs-target="#capaianOmset" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">Capaian Omset</button>-->
                            <!--    </div>-->
                            <!--</div>-->
                        <style>
                            .pencet {
                                cursor: pointer;
                            }
                            .okeee {
                                border : 1.5px solid #DCDCDC;
                                
                            }
                            .kanan {
                                float: right;
                                font-size: 11px;
                            }
                        </style>
                            
                        <div class="row" id="yyas">   
                            
                            <!--<div ></div>-->
                            
                        </div>
                        
                        <!--<div class="row">-->
                        <!--    <div class="col-md-12">-->
                        <!--        <div class="box box-success">-->
                        <!--            <div class="box-header with-border">-->
                        <!--                <h4 class="box-title">List Rencana</h4>-->
                        <!--            </div>-->
                        <!--            <div class="box-body">-->
                        <!--                <div class="table-responsive">-->
                        <!--                    <table class="table table-striped" id="hehed" width="100%">-->
                        <!--                        <thead>-->
                        <!--                            <tr>-->
                        <!--                                <th>No</th>-->
                        <!--                                <th>Tanggal</th>-->
                        <!--                                <th>Tugas</th>-->
                        <!--                                <th>Capaian</th>-->
                        <!--                                <th>Target</th>-->
                        <!--                                <th>Status</th>-->
                        <!--                                <th>Aksi</th>-->
                        <!--                                <th></th>-->
                        <!--                            </tr>-->
                        <!--                        </thead>-->
                        <!--                        <tbody id="ooh">-->
                                                    
                        <!--                        </tbody>-->
                        <!--                    </table>-->
                        <!--                </div>-->

                        <!--            </div>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div id="prospek"></div>
                                
                                <div id="kunjungann"></div>
                                
                                <div id="transferr"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Laporan</h4>

                                        <div class="box-tools pull-right">
                                            <!--<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                                            <div id="tgl">

                                            </div>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div id="lapo">

                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Progress Kerjaan</h4>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div id="progres">

                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-primary collapsed-box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Feedback Atasan</h4>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <form id="aplodfeed">
                                            <div id="cons">
    
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->

                            </div>
                        </div>
                        <!--<input type="file" accept="audio/*" capture id="recorder" />-->
                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
                        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="capaianOmset" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Capaian Omset</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#exampleModal" data-bs-toggle="modal">
                        </button>
                    </div>
                    <!--<form id="formy">-->
                        <div class="modal-body">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped" width="100%" id="yess">
                                        <thead>
                                            <tr>
                                                <th>Program</th>
                                                <th>target</th>
                                                <th>Bulanan</th>
                                                <th>Harian</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ohoy">
                                                    
                                        </tbody>
                                        <tfoot id="heyy">
                                            
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="detailnya" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Detail Kegiatan <span id="sirsir"></span></h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#exampleModal" data-bs-toggle="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <style>
                                    .isDisabled {
                                        color: currentColor;
                                        cursor: not-allowed;
                                        opacity: 0.5;
                                        text-decoration: none;
                                    }
                                </style>
                                <input type="hidden" id="id_ren_hide">
                                <table width="100%">
                                    <tbody id="yyoyo">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="yiyiy">
                        
                    </div>
                </div>
            </div>
        </div>
                

        <div class="row">
            <!--<div class="col-lg-12">-->
                <div class="card">
                         <form method="GET" action="{{url('exportlk')}}">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">Data Laporan</h4>
                        <div class="d-flex justify-content-end gap-2">
                          <input type="hidden" id="idCom" name="com"/>
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                            @if(Auth::user()->kepegawaian == 'admin')
                                <div class="btn-group">
                                     <button type="button" class=" btn btn-success btn-xxs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Laporan" style="width:100%;">
                                        <i class="fa fa-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item exp" type="submit" value="xls" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item exp" type="submit" value="csv" name="tombol">.CSV</button></li>
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                     <div class="col-lg-3 mb-3">
                                        <label>Pilih</label>
                                        <select class="form-control cek11" name="plhtgl" id="plhtgl" style="width: 100%">
                                            <option value="0">Priode</option>
                                            <option value="1">Bulan</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-3" id="tglss">
                                        <label>Range Tanggal</label>
                                        <input type="text" name="daterange"  class="form-control datess ceks" id="daterange" placeholder="mm/dd/yyyy - mm/dd/yyyy" autocomplete="off" value="" />
                                    </div>
                                    
                                    
                                    <div class="col-lg-3 mb-3" id="blnbln" hidden="true">
                                        <label>Bulan&amp;Tahun :</label>
                                        <input type="text" class="form-control dates cek00" name="blns" id="blns" autocomplete="off" placeholder="{{ date('Y-m') }}">
                                    </div>
                                    
                                    <div class="col-lg-3 mb-3">
                                        <label>Unit Kerja</label>
                                        <select id="kota" class="form-control cek2 js-example-basic-single" multiple="multiple" name="kota[]" style="width: 100%" placeholder='Pilih Unit Kerja'>
                                            @if(count($kota) > 0)
                                            <option value="">Pilih Unit</option>
                                            @foreach($kota as $kk)
                                            <option value="{{$kk->id}}">{{$kk->unit}}</option>
                                            @endforeach
                                            @else
                                            <option value="">Tidak ada</option>
                                            @endif
                                        </select>
                                    </div>
    
                                    <div class="col-lg-3 mb-3">
                                        <label>Jabatan</label>
                                        <select id="jabatan" class="form-control cek2 js-example-basic-single" multiple="multiple" name="jabatan[]" style="width: 100%" placeholder='Pilih Jabatan'>
                                            @if(count($jabatan) > 0)
                                            <option value="">Pilih Jabatan</option>
                                            @foreach($jabatan as $jabatans)
                                            <option value="{{$jabatans->id}}">{{$jabatans->jabatan}}</option>
                                            @endforeach
                                            @else
                                            <option value="">Tidak ada</option>
                                            @endif
                                        </select>
                                    </div>
                                        <div class="col-lg-3 mb-3">
                                            <label>Karyawan</label>
                                           <select id="karyawanSelect" class="form-control karyawanSelect js-example-basic-single" multiple="multiple" name="nama_karyawan[]" style="width: 100%" placeholder='Pilih Jabatan'>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-end relative">
                                        <div class="col-sm-3 d-flex justify-content-end">
                                           <input type="text" class="form-control search-table" id="search-table" name="search" autocomplete="off" style="width:200px; height:30px; margin-top:30px; position:absolute; z-index:2;" placeholder="Search here.....">
                                        </div>
                                    </div>
                            </div>
                            </div>
                        </form>
                        <div class="row">
                            <!--<div class="col--12">-->
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Id Karyawan</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Kelola</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                </div>
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            <!--</div>-->
        </div>
        
    </div>
</div>
@endsection