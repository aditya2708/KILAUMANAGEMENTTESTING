@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Kehadiran</a></li>-->
        <!--    </ol>-->
        <!--</div>-->


        <!--modal-->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="getoh">

                                </div>
                            </div>
                        </div>
                        <div class="row" id="collapseDetail">
                            
                        </div>
                        <div class="row d-flex justify-content-center ">
                                <div class="collapse multi-collapse " id="show-collapse" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="map" style="width:100%;height:150px;"></div>
                                        </div>
                                        <div class="col-md-12">
                                            <table class="mt-3"width="100%">
                                            <tbody class="gap-3">
                                                <tr class="mb-2">
                                                    <td style="vertical-align:top; width:40%;" >Status</td>
                                                    <td style="vertical-align:top;"> : </td>
                                                    <td id="stat"></td>
                                                </tr>
                                                <tr  class="mb-2">
                                                    <td style="vertical-align:top; width:40%;">Lampiran</td>
                                                    <td style="vertical-align:top;"> : </td>
                                                    <td class="d-flex align-items-center"><a id ="lampi" class="btn btn-info btn-xxs" target="_blank">Lihat</a></td>
                                                </tr>
                                                 <tr  class="mb-2">
                                                    <td style="vertical-align:top; width:40%;">Keterangan</td>
                                                    <td style="vertical-align:top;"> : </td>
                                                    <td id="keter"></td>
                                                </tr>
                                                 <tr  class="mb-2">
                                                    <td style="vertical-align:top; width:40%;">Foto</td>
                                                    <td style="vertical-align:top;"> : </td>
                                                    <td class="d-flex align-items-center"><a id="fot" class="btn btn-info btn-xxs" target="_blank">Lihat</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
                        
                    </div>
                </div>
            </div>
        </div>
        <form method="GET" action="{{url('kehadiran/exportk')}}" >
        <!--modal-->
        <div class="modal fade" id="detailRekap">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header gap-2">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Rekap <span id="nama"></span></h5>
                            <div class="btn-group">
                                 <button type="button" class="btn btn-success btn-xs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Rekap Kehadiran"  style="width:100%;">
                                    <i class="fa fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item expDetail" type="submit" value="xls" name="tombol2">.XLS</button></li>
                                    <li><button class="dropdown-item expDetail" type="submit" value="csv" name="tombol2">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                         <table id="user_table_rekap" class="table table-striped">
                            <thead id="top">
                            </thead>
                            <tbody id="body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12">
                         @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                                    
                            <div class="row" style="margin-bottom: 35px">
                                <div class="col-12">
                                    <button type="button" style="height: 55px; font-size: 1.25rem !important" class="btn btn-primary btn-block btn-sm " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                                </div>
                            </div>
                                                    
                        @endif
                        
                        <div class="row">
                            <div class="col-lg-12">
                                
                                <div class="card">
                                    <div class="card-header" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                                        <h4 class="card-title">Filter</h4>
                                        <div class="pull-right">
                                            <a href="javascript:void(0)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="card-body collapse multi-collapse" id="multiCollapseExample1">
                                            <div class="basic-form">
                                                <input type="hidden" name="namaKaryawan" id="namkar">
                                                <input type="hidden" name="idKaryawan" id="idkar">
                                                <input type="hidden" name="com" id="idCom">
                                                <!--<input type="hidden" name="com" id="com">-->
                                               
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Pilih</label>
                                                        <select id="plhtgl" class="form-control" name="plhtgl">
                                                            <option value="0">Periode</option>
                                                            <option value="1">Bulan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3" id="blnbln" hidden>
                                                        <label>Bulan&Tahun :</label>
                                                        <input type="text" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                    </div>
    
                                                    <div class="col-md-6 mb-3" id="tgldari">
                                                        <label>Range Tanggal</label>
                                                        <input type="text" name="daterange" class="form-control datess ceks" id="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" autocomplete="off" value="" />
                                                    </div>
    
                                                </div>
                                                <div class="row">
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label>Unit Kerja</label>
                                                        <select id="kot" class="form-control js-example-basic-single cek2" name="kantor[]" multiple="multiple" style="width: 100%">
                                                            @if(count($kantor ) > 0)
                                                            <option value="">Pilih Unit</option>
                                                            @foreach($kantor as $kantors)
                                                            <option value="{{$kantors->id}}">{{$kantors->unit}}</option>
                                                            @endforeach
                                                            @else
                                                            <option value="">Tidak ada</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label>Jabatan</label>
                                                        <select id="jab" class="form-control js-example-basic-single cek1" name="jabatan[]" multiple="multiple" style="width: 100%">
                                                            @if(count($jabatan ) > 0)
                                                            <option value="">Pilih Jabatan</option>
                                                            @foreach($jabatan as $jabatans)
                                                            <option value="{{$jabatans->id}}">{{$jabatans->jabatan}}</option>
                                                            @endforeach
                                                            @else
                                                            <option value="">Tidak ada</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label>Status</label>
                                                        <select id="aktif" class="form-control cek3" name="aktif" style="width: 100%">
                                                            <option value="1">Aktif</option>
                                                            <option value="0">Nonaktif</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label>Presensi</label>
                                                        <select id="status" class="form-control js-example-basic-single" name="status[]" multiple="multiple" style="width: 100%">
                                                            <option value="">Pilih Presensi</option>
                                                            <option value="Hadir">Hadir</option>
                                                            <option value="Terlambat">Terlambat</option>
                                                            <option value="Bolos">Bolos</option>
                                                            <option value="Sakit">Sakit</option>
                                                            <option value="Cuti">Cuti</option>
                                                            <option value="Cuti Penting">Cuti Penting</option>
                                                            <option value="Perdin">Perdin</option>
                                                        </select>
                                                    </div>
                                                    <!--<div class="col-md-12 mb-3">-->
                                                    <!--    <label>Karyawan</label>-->
                                                    <!--    <select id="karyawan" class="form-control karyawan" name="karyawan" style="width: 100%;">-->
                                                    <!--        <option value="">Pilih Karyawan</option>-->
    
                                                    <!--    </select>-->
                                                    <!--</div>-->
                                                </div>
                                                    
                                                    
                                                    <!--</div>-->
                                                
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" id="filter" class="btn btn-primary btn-block btn-sm" >filter</button>
                                                    </div>
                                                </div>
                                                
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header" data-bs-toggle="collapse" href="#multiCollapseExample2" role="button" aria-expanded="false" aria-controls="multiCollapseExample2">
                                        <h4 class="card-title">Grafik Kehadiran</h4>
                                        <div class="pull-right">
                                            <a href="javascript:void(0)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="card-body collapse multi-collapse" id="multiCollapseExample2">
                                        <div id="kehadiran"> </div>
                                        <div class="mb-3 mt-4">
                                            <h4 class="fs-15 font-w600">Penanda</h4>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#26E023"></rect>
                                                    </svg>
                                                    Hadir (<span id="presen_hadir"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="hadir"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#FFDA7C"></rect>
                                                    </svg>
                                                    Terlambat (<span id="presen_terlambat"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="terlambat"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#FF86B1"></rect>
                                                    </svg>
                                                    Bolos (<span id="presen_bolos"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="bolos"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#F5DEB3"></rect>
                                                    </svg>
                                                    Perdin (<span id="presen_perdin"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="perdin"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#61CFF1"></rect>
                                                    </svg>
                                                    Sakit (<span id="presen_sakit"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="sakit"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#708090"></rect>
                                                    </svg>
                                                    Cuti (<span id="presen_cuti"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="cuti"></span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between  mb-4">
                                                <span class="fs-15 font-w500">
                                                    <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="20" height="20" rx="6" fill="#E0FFFF"></rect>
                                                    </svg>
                                                    Cuti Penting (<span id="presen_cuti_penting"></span>%)
                                                </span>
                                                <span class="fs-15 font-w600" id="cuti_penting"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Data Kehadiran</h4>
                                <div class="d-flex justify-content-end">
                                    <div class="btn-group">
                                         <button type="button" class="btn btn-success btn-sm dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Rekap Kehadiran"  style="width:100%;">
                                            <i class="fa fa-download"></i> Kehadiran
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item expKehadiran" type="submit" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item expKehadiran" type="submit" value="csv" name="tombol">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="user_table" class="table table-striped" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Nama Karyawan</th>
                                                        <th>Masuk</th>
                                                        <th>Pulang</th>
                                                        <th>Terlambat</th>
                                                        <th>Status</th>
                                                        <th>Jumlah Hari</th>
                                                        <th>Aksi</th>
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
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Data Rekap Kehadiran</h4>
                                <div class="d-flex justify-content-end">
                                    <div class="btn-group">
                                         <button type="button" class="btn btn-success btn-sm dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Kehadiran" style="width:100%;">
                                            <i class="fa fa-download"></i> Rekap
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item expRekap" type="submit" value="xls" name="tombol1">.XLS</button></li>
                                            <li><button class="dropdown-item expRekap" type="submit" value="csv" name="tombol1">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="user_table1" class="table table-striped" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Karyawana</th>
                                                        <th>Jabatan</th>
                                                        <th>Hadir</th>
                                                        <th>Sakit</th>
                                                        <th>Terlambat</th>
                                                        <th>Perdin</th>
                                                        <th>Bolos</th>
                                                        <th>Cuti</th>
                                                        <th>Cuti Penting</th>
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
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection