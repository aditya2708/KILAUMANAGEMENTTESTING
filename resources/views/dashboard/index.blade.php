@extends('template')
@section('konten')
@if (Auth::check())

<div class="content-body">
    
    <!--modal-->
    <div class="modal fade"  id="modal-detail">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  style="max-width: 90%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Detail Omset <span id="names"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                        
                <div class="modal-body" >
                    <div class="table-responsive">
                        <table class="table table-striped" width="100%" id="hg">
                            <thead>
                    	            
                                <tr>
                                    <th>Tanggal</th>
                                    <th>ID Transaksi</th>
                                    <th>Donatur</th>
                                    <th>Program</th>
                    	            <th>Jumlah</th>
                    	            <th>Petugas</th>
                    	            <th>Pembayaran</th>
                    	        </tr>
                            </thead>
                            <tbody id="vcc">
                        		            
                            </tbody>
                            <tfoot id="ccv">
                                
                            </tfoot>
                        </table>
                    </div>
                </div>
           </div>
        </div>
    </div>
        
    <!--end modal    -->
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-7">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card tryal-gradient">
                                    <div class="card-body tryal row">
                                    
                                        <div class="col-xl-7 col-sm-6">
                                            <form method="POST" action="{{ url('dashboard/ubahaja')}}">
                                            @csrf
                                            <span >
                                                <button type="button" class="btn btn-xxs btn-rounded btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali" id="pp" style="display:none"><i class="fa fa-arrow-left"></i></button>
                                                <button type="submit" class="btn btn-xxs btn-rounded btn-success" data-toggle="tooltip" data-placement="top" title="Simpan" id="xx" style="display:none; margin-left: 10px"><i class="fa fa-bookmark"></i></button>
                                            </span>
                                            <!--<div id="zz">-->
                                                <div id="aa" style="display: block">
                                                    @if(is_object($pp))
                                                        <h2>{{ $pp->title }}</h2>
                                                        <span>{{ $pp->des }}</span>
                                                    @else
                                                        <h2>Data tidak tersedia</h2>
                                                    @endif

                                                </div>
                                                <div id="bb" style="display: none">
                                                    <!--<form method="POST" action="#">-->
                                                        <input type="hidden" id="id_c" name="id_c" value="{{ optional($pp)->id_com ?? NULL}}">
                                                        <h2><input class="form-control" name="tit" id="tit" style="background: transparent; padding: 20px 10px; border-style: dashed; height: auto; font-size: 2rem; font-weight: 700; color: #fff;" value="{{optional($pp)->title ?? 'Tidak ada judul' }}"></h2>
                                                        <span><input class="form-control" name="des" id="des" style="background: transparent; padding: 20px 10px; border-style: dashed; height: auto;  font-size: 1rem; font-weight: 400; color: #fff;" value="{{ optional($pp)->des ?? 'Tidak ada deskripsi' }}"></span>                                
                                                </div>
                                            </form>
                                            <!--</div>-->
                                            <!-- <a href="javascript:void(0);" class="btn btn-rounded  fs-15 font-w500">Try Free Now</a> -->
                                        </div>
                                        <div class="col-xl-5 col-sm-6">
                                            <img src="{{ asset('images/chart.png') }}" alt="" class="sd-shape">
                                        </div>
                                        <span ><button type="button" class="btn btn-xxs btn-rounded btn-info" data-toggle="tooltip" data-placement="top" title="Edit" id="zz" ><i class="fa fa-pen"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            
                            @if(Auth::user()->kolekting != null )
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header border-0 flex-wrap">
                                        <h4 class="fs-20 font-w700 mb-2">Grafik Transaksi</h4>
                                        <!-- <div class="d-flex align-items-center project-tab mb-2"> -->
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div id="container1" style="height: 450px; min-width: 550px; display: block"></div>
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Target</h4>
                                    </div>
                                    <div class="card-body">
                                        
                                        
                                       
                                        <!-- Nav tabs -->
                                        <div class="custom-tab-1">
                                            <ul class="nav nav-tabs">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#home1"><i class="la la-home me-2"></i> Kantor</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#profile1"><i class="la la-user me-2"></i> Petugas</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                                    <div class="pt-4">
                                                        <div class="table-responsive recentOrderTable">
                                                            <table class="table verticle-middle table-responsive-md" id="target_kantor">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Kantor</th>
                                                                        <th>Target</th>
                                                                        <th>Capaian</th>
                                                                        <th>Sisa</th>
                                                                        <!--<th>Jumlah</th>-->
                
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tk">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="profile1">
                                                    <div class="pt-4">
                                                        <div class="table-responsive recentOrderTable">
                                                            <table class="table verticle-middle table-responsive-md" id="target_user">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Petugas</th>
                                                                        <th>Target</th>
                                                                        <th>Capaian</th>
                                                                        <th>Sisa</th>
                                                                        <!--<th>Jumlah</th>-->
                
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tu">
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
                            
                            <div class="col-xl-12">
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
                            
                            @elseif(Auth::user()->level != null)
                            
                             <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header border-0 flex-wrap">
                                        <h4 class="fs-20 font-w700 mb-2">Grafik Transaksi</h4>
                                        <!-- <div class="d-flex align-items-center project-tab mb-2"> -->
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div id="container1" style="height: 450px; min-width: 550px; display: block"></div>
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-xl-12">
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
                            
                            @else
                            
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Data Kehadiran Hari ini</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive recentOrderTable">
                                            <table class="table verticle-middle table-responsive-md" id="user_tablex">
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
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @endif
                        </div>

                    </div>
                    <div class="col-xl-5">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    
                                    <div class="col-xl-6 col-sm-6" onclick="window.location.href='{{ url('karyawan')}}';" style="cursor: pointer;">
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
                                    
                                    @if(Auth::user()->level != null)
                                    <div class="col-xl-6 col-sm-6" onclick="window.location.href='{{ url('donatur')}}';" style="cursor: pointer;">
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
                                    @endif
                                    
                                    @if(Auth::user()->level != null)
                                    <div class="col-xl-6 col-sm-6" onclick="window.location.href='{{ url('transaksi')}}';" style="cursor: pointer;">
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
                                    @endif

                                    <div class="col-xl-6 col-sm-6" onclick="window.location.href='{{ url('kantor')}}';" style="cursor: pointer;">
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
                            <div class="col-xl-12 col-lg-12">
                                <div class="row">
                                    @if(Auth::user()->kepegawaian != null)
                                    <div class="col-xl-12 col-xxl-12 col-sm-12">
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
                                    @if(Auth::user()->kolekting != null)
                                    <div class="col-xl-12 col-xxl-12 col-sm-12">
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
                                                                <td>{{ 'Rp. ' . number_format($val->Omset, 0, ',', '.'); }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endif
@endsection