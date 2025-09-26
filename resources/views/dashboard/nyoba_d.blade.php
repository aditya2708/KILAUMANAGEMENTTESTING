@extends('template_nyoba')
@section('konten')
<!--<div class="content-body">-->
    <div class="container-fluid" style="padding-top: 1.7rem">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card tryal-gradient">
                                    <div class="card-body tryal row">
                                        <div class="col-md-7 col-sm-6">
                                            <h2>Manage your Employees</h2>
                                            <span>For better employees properly and correctly</span>
                                            <!-- <a href="javascript:void(0);" class="btn btn-rounded  fs-15 font-w500">Try Free Now</a> -->
                                        </div>
                                        <div class="col-md-5 col-sm-6">
                                            <img src="images/chart.png" alt="" class="sd-shape">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan pusat')
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header border-0 flex-wrap">
                                        <h4 class="fs-20 font-w700 mb-2">Grafik Transaksi</h4>
                                        <!-- <div class="d-flex align-items-center project-tab mb-2"> -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="container1" style="height: 450px; min-width: 550px; display: block"></div>
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </div>
                                    
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Transaksi</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive recentOrderTable">
                                            <table class="table verticle-middle table-responsive-md" id="user_table">
                                                <thead>
                                                    <tr>
                                                        <th>ID Transaksi</th>
                                                        <th>Kolektor</th>
                                                        <th>Donatur</th>
                                                        <th>Sub Program</th>
                                                        <th>Status</th>
                                                        <th>Jumlah</th>

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
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6 col-sm-6">
                                        <div class="widget-stat card">
                                            <div class="card-body p-4">
                                                <div class="media ai-icon">
                                                    <span class="me-3 bgl-primary text-primary">
                                                        <!-- <i class="ti-user"></i> -->
                                                        <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </span>
                                                    <div class="media-body">
                                                        <p class="mb-1">Total Karyawan</p>
                                                        <h4 class="mb-0">{{ number_format($tot_kar,0,',','.'); }}</h4>
                                                        <!-- <span class="badge badge-primary">+3.5%</span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-sm-6">
                                        <div class="widget-stat card">
                                            <div class="card-body p-4">
                                                <div class="media ai-icon">
                                                    <span class="me-3 bgl-info text-info">
                                                        <!-- <i class="ti-user"></i> -->
                                                        <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </span>
                                                    <div class="media-body">
                                                        <p class="mb-1">Total Donatur</p>
                                                        <h4 class="mb-0">{{ number_format($tot_don,0,',','.'); }}</h4>
                                                        <!-- <span class="badge badge-info">+3.5%</span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-sm-6">
                                        <div class="widget-stat card">
                                            <div class="card-body p-4">
                                                <div class="media ai-icon">
                                                    <span class="me-3 bgl-success text-success">
                                                        <!-- <i class="ti-user"></i> -->
                                                        <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </span>
                                                    <div class="media-body">
                                                        <p class="mb-1">Total Transaksi</p>
                                                        <h4 class="mb-0">{{ number_format($tot_tar,0,',','.'); }}</h4>
                                                        <!-- <span class="badge badge-success">+3.5%</span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-sm-6">
                                        <div class="widget-stat card">
                                            <div class="card-body p-4">
                                                <div class="media ai-icon">
                                                    <span class="me-3 bgl-warning text-warning">
                                                        <!-- <i class="ti-user"></i> -->
                                                        <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </span>
                                                    <div class="media-body">
                                                        <p class="mb-1">Total Kantor</p>
                                                        <h4 class="mb-0">{{ number_format($tot_kan,0,',','.'); }}</h4>
                                                        <!-- <span class="badge badge-warning">+3.5%</span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12 col-lg-12">
                                <div class="row">
                                    @if(Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan pusat')
                                    <div class="col-sm-6 col-md-12 col-sm-6">
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <div>
                                                    <h4 class="fs-20 font-w700">Grafik Kehadiran</h4>
                                                    <!-- <span class="fs-14 font-w400 d-block">Lorem ipsum dolor sit amet</span> -->
                                                </div>
                                            </div>
                                            <div class="card-body">
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
                                            <!-- <div class="card-footer border-0 pt-0">
                                                <a href="javascript:void(0);" class="btn btn-outline-primary d-block btn-rounded">Update Progress</a>

                                            </div> -->
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-sm-6 col-md-12 col-sm-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Capaian Omset Tertinggi</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive recentOrderTable">
                                                    <table class="table verticle-middle table-responsive-md">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama</th>
                                                                <th>Omset</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <? $no = 1 ?>
                                                            @foreach($data as $val)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $val->name }}</td>
                                                                <td>{{ 'Rp. ' . number_format($val->Omset, 0, ',', '.') }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
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
    </div>
<!--</div>-->
@endsection