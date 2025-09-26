<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Kilau Indonesia">
	<meta property="og:title" content="Kilau Indonesia">
	<meta property="og:description" content="Berbagi Teknologi">
	<meta property="og:image" content="https://kilauindonesia.org/kilau/upload/BT-LOGO.png">
    <meta name="format-detection" content="telephone=no">

    <!-- PAGE TITLE HERE -->
    @if (Request::segment(1) == 'add-karyawan')
    <title>Form Input Data Karyawan</title>
    @else
    <title>Admin</title>
    @endif
    
    <?php $u = DB::table('users')->select('perus')->whereRaw("perus IS NOT NULL")->distinct()->pluck('perus')->toArray(); ?>
    
    @foreach($u as $yes)
    
    @if(Request::segment(1) == $yes)
        @php
            $url = 'kilau/';
        @endphp
    @else
        @php
            $url = '';
        @endphp
    @endif
    
    @endforeach
    <!-- FAVICONS ICON -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet"> -->
    <link rel="shortcut icon" href="https://kilauindonesia.org/kilau/upload/kilaubiru2.png">

    <link href="{{asset('vendor/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet">
    <link href="{{asset($url .'vendor/owl-carousel/owl.carousel.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset($url .'vendor/nouislider/nouislider.min.css')}}">

    <!-- Datatable -->
    <link href="{{asset($url .'vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">

    <!-- select2 -->
    <link rel="stylesheet" href="{{asset($url .'vendor/select2/css/select2.min.css')}}">

    @if(Request::segment(2) == 'create' && Request::segment(1) == 'karyawan')
    <!-- step wizard -->
    <link href="{{asset($url .'vendor/jquery-smartwizard/dist/css/smart_wizard.min.css')}}" rel="stylesheet">
    @endif
    
    @if(Request::segment(1) == 'diroh_handsome')
    <link href="{{asset($url .'css/stylemulti.css')}}" rel="stylesheet">
    @endif
    
    @if(Request::segment(1) == 'donatur')
    <link href="{{asset($url .'css/jquery.dataTables.colResize.css')}}" rel="stylesheet">
    @endif
    
    
    <!--@if(Request::segment(1) == 'donatur' && Request::segment(2) == '')-->
    <!--<link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@3.11.0/css/mdb.min.css" rel="stylesheet">-->
    <!--@endif-->
    <!--@if(Request::segment(1) == 'buku-harian' && Request::segment(2) == '')-->
    <!--<link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@3.11.0/css/mdb.min.css" rel="stylesheet">-->
    <!--@endif-->
    <!-- Style css -->
    <link href="{{asset($url .'css/style.css')}}" rel="stylesheet">

    <!-- datepicker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

    <!-- DateRange -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- toast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    
     <!-- font -->
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500' rel='stylesheet'>
    <!--@if(Request::segment(1) == 'capaian-kolekting' || Request::segment(1) == 'dashboard' || Request::segment(1) == 'capaian-omset')-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.6/css/highcharts.css" />
    <!--@endif-->
    
    @if((Request::segment(1) == 'program' || Request::segment(1) == 'coa' || Request::segment(1) == 'trial-balance' || Request::segment(1) == 'saldo-awal') || (Request::segment(2) == 'program' || Request::segment(2) == 'coa' || Request::segment(2) == 'trial-balance' || Request::segment(2) == 'saldo-awal'))
    
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <link href="{{ asset($url .'bt_table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.css">

    <!--<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.20.2/dist/extensions/sticky-header/bootstrap-table-sticky-header.css">-->
    <link rel="stylesheet" href="{{ asset($url .'bt_table/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.css')}}">
    <!--<link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">-->
    
    @endif
    
    <!--<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">-->
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <!--@if(Request::segment(1) == 'diroh_handsome')-->
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.2/dist/css/bootstrap.min.css">-->
    <!--@endif-->
    
    <!--<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css'>-->
     <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-v4C6PDVaaM30v--n"></script>
    
    <style>
    	@media all {
    		.page-break	{
    			display: none;
    		}
    	}
    
    	@media print {
    		.page-break	{
    			display: block;
    			page-break-before: always;
    		}
    	}
    </style>
    
    <style>
        /*table.dataTable tbody tr, table.dataTable tbody td {*/
        /*  background: #fff !important; */
        /*}*/
        .hover-side:hover{
            background-color: #f3f3f3 !important;
        }
        /*.hover-side{*/
        /*    height:30px;*/
        /*    width:100%;*/
        /*}*/
        
        .relative-container {
          position: relative;
        }
        
        .absolute-button {
          position: absolute;
          bottom: 0;
          z-index:999;
          right: 0;
        }
        
        /*.select2-selection{*/
        /*    height: 3rem;*/
        /*}*/
        
        .table > :not(caption) > * > * {
            padding: 0.5rem 0.5rem;
            background-color: #fff;
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg); 
        }
    
        body {
            font-family: 'poppins', sans-serif;
            /* font-size: 11px; */
        }
        
        .hide_column {
            display: none;
        }

         thead tr input {
            width: 100%;
            display: none;
        }
        
        .cari input {
            width: 100%;
            padding: 3px;
            display: none;
        } 
        
        
        
        .filters {
            display: none;
        }
        
        .bg-collaps{
            background-color: #F8F8F8;
        }


        .buttons-collection {
            margin-left: 15px;
            /*margin-top: -2px;*/
            /*height: 30px;*/
        }

        .switch {
            display: inline-block;
            height: 30px;
            position: relative;
            width: 55px;
            margin-top: 8px;
        }
            
        .switch input {
            display: none;
        }

        .slider {
            background-color: #ccc;
            bottom: 0;
            cursor: pointer;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: .4s;
        }

        .slider:before {
            background-color: #fff;
            bottom: 4px;
            content: "";
            height: 22px;
            left: 4px;
            position: absolute;
            transition: .4s;
            width: 22px;
        }

        input:checked+.slider {
            background-color: #66bb6a;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
        
        .jembt {
            background: red !important; 
        }

        .slider.round {
            border-radius: 7px;
        }

        .slider.round:before {
            border-radius: 10%;
        }

        td.merah {
            background: #d9534f;
            color: #FFF;
        }

        .buttons-collection {
            margin-left: 15px;
            margin-top: -2px;
            height: 30px;
        }
        
        .red {
          color: red !important;
        }
        
        .perusahaan:hover {
            cursor:pointer;
            box-shadow:none !important;
        }
        
        .btn-perusahaan {
            border:none;
            background:none;
            padding:0;
            margin:0;
        }

        mark {
            background-color:#efe4fb; 
            color: #6C4BAE; 
            font-weight: bold; 
        }


        textarea#tugas {
            /*font-size: 15px;*/
        	/*width:100%;*/
        	/*box-sizing:border-box;*/
        	/*direction:rtl;*/
        	/*display:block;*/
        	/*max-width:100%;*/
        	/*line-height:1.5;*/
        	/*padding:15px 15px 30px;*/
        	/*border-radius:3px;*/
        	/*border:1px solid #F7E98D;*/
        	/*font:13px Tahoma, cursive;*/
        	/*transition:box-shadow 0.5s ease;*/
        	/*box-shadow:0 4px 6px rgba(0,0,0,0.1);*/
        	/*font-smoothing:subpixel-antialiased;*/
        	/*background:linear-gradient(#F9EFAF, #F7E98D);*/
        	/*background:-o-linear-gradient(#F9EFAF, #F7E98D);*/
        	/*background:-ms-linear-gradient(#F9EFAF, #F7E98D);*/
        	/*background:-moz-linear-gradient(#F9EFAF, #F7E98D);*/
        	/*background:-webkit-linear-gradient(#F9EFAF, #F7E98D);*/
        }
        
        
    </style>
    
   <style>
        .dropdown-menu-eleh {
            inset: -5px 10px auto auto !important;
        }
    </style>

    <style>
        .bigdrop {
            width: 600px !important;

        }

        .droppp {
            width: 500px !important;

        }
        
        .drops {
            width: 350px !important;

        }

        /* Add Animation - Zoom in the Modal */
        .modal-content,
        #caption {
            animation-name: zoom;
            animation-duration: 0.6s;
        }
        
        .merah {
                background:#d9534f;
                color: #FFF;
        }
    </style>
    
    @if(Request::segment(1) == 'karyawan' && Request::segment(2) == 'edit' || Request::segment(2) == 'karyawan' && Request::segment(3) == 'edit')
    <style>
        .bigdrop {
            width: 600px !important;

        }
    </style>
    @endif
  
   
  @if(Request::segment(1) == 'pengajuan-perubahan' || Request::segment(2) == 'pengajuan-perubahan')
   <style>
        .modal-container {
            display: flex;
        }
        
        .modal {
            position: fixed;
            top: 0;
            height: 100%;
        }
        
        .modal-left {
            left: 0;
            width: 45%; 
        }
        
        .modal-right {
            right: 0;
            width: 45%;
        }
    </style>
    @endif
    
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloaderx">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <?php 
            $p = \App\Models\Profile::where(['id_com' => Auth::user()->id_com])->first();
        ?>
        
        <div class="nav-header">
        @if (Auth::user()->id_com == 1)
        <a href="javascript:void(0)" class="brand-logo">
                <svg class="logo-abbr" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                	 width="71.5px" height="71.5px" viewBox="0 0 71.5 71.5" style="enable-background:new 0 0 71.5 71.5;" xml:space="preserve">
                <style type="text/css">
                	.st0{fill:none;}
                </style>
                <path class="st0" d="M35.8,1.3C16.7,1.3,1.3,16.7,1.3,35.8s15.5,34.5,34.5,34.5s34.5-15.5,34.5-34.5S54.8,1.3,35.8,1.3z M61.8,48.7
                	c-1.6-0.4-3.2-0.9-4.8-1.3c-3.3-0.9-6.7-1.8-10-2.6c-0.1,0-0.2-0.1-0.4-0.2c0,0,0-0.1,0-0.1c0.2-0.1,0.5-0.1,0.7-0.1
                	c3.9-0.7,7.8-1.3,11.7-2c0.3,0,0.4,0,0.4,0.3c0.7,1.9,1.4,3.7,2.1,5.6c0,0.1,0.1,0.2,0.2,0.3C61.8,48.6,61.8,48.6,61.8,48.7z
                	 M32.4,41.6c1.3,0,2.5,0,3.7,0c0,0.1,0,0.3,0,0.4c0,2.3,0,4.6,0,6.9c0,0.8,0.1,1.5,0.2,2.3c0.3,1.3,1.3,2,2.6,2
                	c1.3-0.1,2.3-0.9,2.5-2.2c0.1-0.6,0.1-1.2,0.1-1.8c0-1.6,0.1-3.3,0-4.9c-0.1-1.5-0.5-2.9-1.1-4.2c-1.9-4.1-5.1-6.4-9.6-6.9
                	c-3.8-0.4-7,0.9-9.9,3.2c-0.1,0.1-0.2,0.2-0.3,0.2c0-0.1,0.1-0.1,0.1-0.2c2.4-3,5.4-4.7,9.2-5c5.2-0.5,9.5,1.3,12.8,5.3
                	c1.1,1.3,1.8,2.8,2.4,4.4c0.1,0.3,0.2,0.5,0.2,0.8c0,2.6,0,5.2-0.1,7.8c0,0.8-0.1,1.6-0.3,2.4c-0.5,2.4-2.5,4.1-4.9,4.4
                	c-1.1,0.1-2.2,0.1-3.2-0.2c-2.2-0.6-3.8-2.4-4.1-4.7c-0.1-1-0.2-2.1-0.2-3.1c0-2.2,0-4.4,0-6.6C32.4,41.8,32.4,41.7,32.4,41.6z
                	 M39.1,18.2c2.3,0.2,4.5,0.4,6.8,0.6c-3.2,4.2-6.4,8.4-9.6,12.6c0,0-0.1,0-0.1,0C37.2,27,38.1,22.6,39.1,18.2z M25.2,32.3
                	c-2.4-4-4.7-8-7.1-12c1.6-1.4,3.2-2.8,4.9-4.2C23.8,21.5,24.6,26.9,25.2,32.3C25.3,32.3,25.3,32.3,25.2,32.3z M49.1,23.4
                	c1.9,1,3.8,1.9,5.8,2.9c-4.6,2.7-9.1,5.4-13.7,8.1c0,0,0,0-0.1,0C43.9,30.7,46.5,27,49.1,23.4z M30.4,30.5c-0.1-0.9-0.3-1.9-0.4-2.8
                	c-0.4-2.7-0.8-5.5-1.2-8.2c-0.1-0.6-0.2-1.1-0.2-1.7c0-0.1,0.1-0.3,0.2-0.3c1.9-0.8,3.8-1.6,5.8-2.5C33.2,20.2,31.9,25.4,30.4,30.5
                	C30.5,30.5,30.5,30.5,30.4,30.5z M12.4,21.7c2.7,4.6,5.4,9.1,8.1,13.6c0,0,0,0,0,0.1c-0.1,0-0.2-0.1-0.2-0.1
                	c-3.4-2.6-6.9-5.2-10.3-7.8c-0.1-0.1-0.3-0.2-0.2-0.4C10.6,25.3,11.5,23.5,12.4,21.7z M45,38.8c3.7-2.3,7.3-4.5,11-6.8
                	c1.4,1.5,2.9,3.1,4.4,4.7C55.2,37.4,50.1,38.1,45,38.8C45,38.9,45,38.8,45,38.8z"/>
                </svg>

                <div class="brand-title">
                    <h2 class="">{{$p->alias}}</h2>
                    <!--<span class="brand-sub-title">(masih belum tau)</span>-->
                </div>
            </a>
            
            @else
             <a href="javascript:void(0)" class="brand-logo">
                <svg class="logo-abbr" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                	 width="71.5px" height="71.5px" viewBox="0 0 71.5 71.5" style="enable-background:new 0 0 71.5 71.5;" xml:space="preserve">
                <style type="text/css">
                	.st0{fill:none;}
                </style>
                </svg>

                <div class="brand-title">
                    <h2 class="" style="margin-left: -10%">{{$p->alias}}</h2>
                    <!--<span class="brand-sub-title">(masih belum tau)</span>-->
                </div>
            </a>
            @endif
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Chat box start
        ***********************************-->
        
        <!--**********************************
            Chat box End
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header border-bottom">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                <?php $u = DB::table('users')->select('perus')->whereRaw("perus IS NOT NULL")->distinct()->pluck('perus')->toArray(); ?>
                                
                                @if(in_array(Request::segment(1), $u))
                                <?php
                                    $tuh = Request::segment(2);
                                    if(Request::segment(4) == null){
                                        $wew = explode('-', $tuh);
                                        if (count($wew) > 1) {
                                            $ah = ucfirst($wew[0])  . ' ' . ucfirst($wew[1]);
                                        } else {
                                            $ah = ucfirst($wew[0]) ;
                                        }
                                    }else{
                                        $ah = ucfirst(Request::segment(3)) . ' ' . ucfirst(Request::segment(2));
                                    }
                                ?>
                                @else
                                 <?php
                                    $tuh = Request::segment(1);
                                    if(Request::segment(3) == null){
                                        $wew = explode('-', $tuh);
                                        if (count($wew) > 1) {
                                            $ah = ucfirst($wew[0])  . ' ' . ucfirst($wew[1]);
                                        } else {
                                            $ah = ucfirst($wew[0]) ;
                                        }
                                    }else{
                                        $ah = ucfirst(Request::segment(2)) . ' ' . ucfirst(Request::segment(1));
                                    }
                                ?>
                                @endif
                                
                                {{$ah}}
                            </div>
                        </div>
                        
                        <ul class="navbar-nav header-right">
                            <!-- Example single danger button -->
                            <!--<div class="nav-item">-->
                            <!--    <div class="btn-group">-->
                            <!--  <button type="button" class="btn btn-primary dropdown-toggle" id="countUser" data-bs-toggle="dropdown" aria-expanded="false">-->
                            <!--    Aktif-->
                            <!--  </button>-->
                            <!--  <ul class="dropdown-menu" id="loopUsers">-->
                            <!--  </ul>-->
                            <!--</div>-->
                            
                            <!--</div>-->
                            <li class="nav-item dropdown d-flex align-items-center">
                                <div class="input-group  search-area">
                                    <input type="text" class="form-control nav-link " id="searchSidebar" autocomplete="off" style="z-index:99;" placeholder="Search here..."  role="button" data-bs-toggle="dropdown" >
                                    <span class="input-group-text "><a href="javascript:void(0)" ><i class="flaticon-381-search-2"></i></a></span>
                                    <!--<div>-->
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <div  class="widget-media dlab-scroll p-3" style="height:auto;" >
        										<ul class="timeline" style="width: 300px; overflow: auto;" id="search_si">
                                                    <li class="hover-side">
                                                        <div class="timeline-panel">
                                                            <div class="media-body">
                                                                <h4 class="d-flex justify-content-start align-items-center ">
                                                                    <a>Cari Page...</a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </li>
                                                 </ul>
        									</div>
                                        </div>
                                    <!--</div>-->
                                </div>
                            </li>
         <!--                   <div class="nav-item dropdown notification_dropdown">-->
         <!--                       <div  class="nav-link" role="button" data-bs-toggle="dropdown">-->
									<!--<svg width="28" height="28" viewbox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">-->
									<!--	<path d="M23.3333 19.8333H23.1187C23.2568 19.4597 23.3295 19.065 23.3333 18.6666V12.8333C23.3294 10.7663 22.6402 8.75902 21.3735 7.12565C20.1068 5.49228 18.3343 4.32508 16.3333 3.80679V3.49996C16.3333 2.88112 16.0875 2.28763 15.6499 1.85004C15.2123 1.41246 14.6188 1.16663 14 1.16663C13.3812 1.16663 12.7877 1.41246 12.3501 1.85004C11.9125 2.28763 11.6667 2.88112 11.6667 3.49996V3.80679C9.66574 4.32508 7.89317 5.49228 6.6265 7.12565C5.35983 8.75902 4.67058 10.7663 4.66667 12.8333V18.6666C4.67053 19.065 4.74316 19.4597 4.88133 19.8333H4.66667C4.35725 19.8333 4.0605 19.9562 3.84171 20.175C3.62292 20.3938 3.5 20.6905 3.5 21C3.5 21.3094 3.62292 21.6061 3.84171 21.8249C4.0605 22.0437 4.35725 22.1666 4.66667 22.1666H23.3333C23.6428 22.1666 23.9395 22.0437 24.1583 21.8249C24.3771 21.6061 24.5 21.3094 24.5 21C24.5 20.6905 24.3771 20.3938 24.1583 20.175C23.9395 19.9562 23.6428 19.8333 23.3333 19.8333Z" fill="#717579"></path>-->
									<!--	<path d="M9.9819 24.5C10.3863 25.2088 10.971 25.7981 11.6766 26.2079C12.3823 26.6178 13.1838 26.8337 13.9999 26.8337C14.816 26.8337 15.6175 26.6178 16.3232 26.2079C17.0288 25.7981 17.6135 25.2088 18.0179 24.5H9.9819Z" fill="#717579"></path>-->
									<!--</svg>-->
         <!--                           <span class="badge light text-white bg-warning rounded-circle" id="cont">0</span>-->
         <!--                       </div>-->
         <!--                       <div class="dropdown-menu dropdown-menu-end">-->
         <!--                           <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:auto;">-->
									<!--	<ul class="timeline" id="notif">-->
											   
									<!--	</ul>-->
									<!--</div>-->
         <!--                           <a class="all-notification">See all notifications <i class="ti-arrow-end"></i></a>-->
         <!--                       </div>-->
         <!--                   </div>-->
                            
                            <div class="nav-item dropdown notification_dropdown" >
                                <div class="nav-link"role="button" data-bs-toggle="dropdown" >
									<svg onclick="notif_peng()" height="28" width="28"  viewbox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.3333 19.8333H23.1187C23.2568 19.4597 23.3295 19.065 23.3333 18.6666V12.8333C23.3294 10.7663 22.6402 8.75902 21.3735 7.12565C20.1068 5.49228 18.3343 4.32508 16.3333 3.80679V3.49996C16.3333 2.88112 16.0875 2.28763 15.6499 1.85004C15.2123 1.41246 14.6188 1.16663 14 1.16663C13.3812 1.16663 12.7877 1.41246 12.3501 1.85004C11.9125 2.28763 11.6667 2.88112 11.6667 3.49996V3.80679C9.66574 4.32508 7.89317 5.49228 6.6265 7.12565C5.35983 8.75902 4.67058 10.7663 4.66667 12.8333V18.6666C4.67053 19.065 4.74316 19.4597 4.88133 19.8333H4.66667C4.35725 19.8333 4.0605 19.9562 3.84171 20.175C3.62292 20.3938 3.5 20.6905 3.5 21C3.5 21.3094 3.62292 21.6061 3.84171 21.8249C4.0605 22.0437 4.35725 22.1666 4.66667 22.1666H23.3333C23.6428 22.1666 23.9395 22.0437 24.1583 21.8249C24.3771 21.6061 24.5 21.3094 24.5 21C24.5 20.6905 24.3771 20.3938 24.1583 20.175C23.9395 19.9562 23.6428 19.8333 23.3333 19.8333Z" fill="#717579"></path>
										<path d="M9.9819 24.5C10.3863 25.2088 10.971 25.7981 11.6766 26.2079C12.3823 26.6178 13.1838 26.8337 13.9999 26.8337C14.816 26.8337 15.6175 26.6178 16.3232 26.2079C17.0288 25.7981 17.6135 25.2088 18.0179 24.5H9.9819Z" fill="#717579"></path>
									</svg>
                                    <span class="badge light text-white bg-danger rounded-circle" id="cont_peng">0</span>
                                </div>
                                <div class="dropdown-menu dropdown-menu-end" >
                                    <div  class="widget-media dlab-scroll p-3" style="height:auto;">
										<ul class="timeline" id="notif_peng">
										
										</ul>
									</div>
                                    <a class="all-notification" href="javascript:void(0);">See all notifications <i class="ti-arrow-end"></i></a>
                                </div>
                            </div>
                            
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link test" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" >
                                <svg  height="18" width="18" viewBox="0 0 8.4666669 8.4666669" id="svg8" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg" fill="#5c5c5c" stroke="#5c5c5c"  height="28" width="28" ><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs id="defs2"></defs> <g id="layer1" transform="translate(0,-288.53332)"> <path d="M 10.462891 16.070312 C 4.9244824 18.277775 0.99608795 23.682844 0.99609375 30 A 1.0001 1.0001 0 0 0 2 31.003906 L 30 31.003906 A 1.0001 1.0001 0 0 0 30.996094 30 C 30.996099 23.68349 27.06876 18.278206 21.53125 16.070312 C 19.99994 17.27111 18.07881 17.996094 15.996094 17.996094 C 13.912967 17.996094 11.992832 17.271538 10.462891 16.070312 z " id="path935" style="color:#7a7575;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:medium;line-height:normal;font-family:sans-serif;font-variant-ligatures:normal;font-variant-position:normal;font-variant-caps:normal;font-variant-numeric:normal;font-variant-alternates:normal;font-feature-settings:normal;text-indent:0;text-align:start;text-decoration:none;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#7a7575;letter-spacing:normal;word-spacing:normal;text-transform:none;writing-mode:lr-tb;direction:ltr;text-orientation:mixed;dominant-baseline:auto;baseline-shift:baseline;text-anchor:start;white-space:normal;shape-padding:0;clip-rule:nonzero;display:inline;overflow:visible;visibility:visible;opacity:1;isolation:auto;mix-blend-mode:normal;color-interpolation:sRGB;color-interpolation-filters:linearRGB;solid-color:#7a7575;solid-opacity:1;vector-effect:none;fill:#7a7575;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:1.99999988;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;paint-order:stroke fill markers;color-rendering:auto;image-rendering:auto;shape-rendering:auto;text-rendering:auto;enable-background:accumulate" transform="matrix(0.26458333,0,0,0.26458333,0,288.53332)"></path> <path d="M 15.996094 1.0039062 C 11.589664 1.0039062 8.0019573 4.5916469 8.0019531 8.9980469 C 8.0019573 13.404485 11.589664 17 15.996094 17 C 20.402524 17 23.998043 13.404485 23.998047 8.9980469 C 23.998043 4.5916469 20.402524 1.0039062 15.996094 1.0039062 z " id="path940" style="color:#7a7575;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;font-size:medium;line-height:normal;font-family:sans-serif;font-variant-ligatures:normal;font-variant-position:normal;font-variant-caps:normal;font-variant-numeric:normal;font-variant-alternates:normal;font-feature-settings:normal;text-indent:0;text-align:start;text-decoration:none;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#7a7575;letter-spacing:normal;word-spacing:normal;text-transform:none;writing-mode:lr-tb;direction:ltr;text-orientation:mixed;dominant-baseline:auto;baseline-shift:baseline;text-anchor:start;white-space:normal;shape-padding:0;clip-rule:nonzero;display:inline;overflow:visible;visibility:visible;opacity:1;isolation:auto;mix-blend-mode:normal;color-interpolation:sRGB;color-interpolation-filters:linearRGB;solid-color:#7a7575;solid-opacity:1;vector-effect:none;fill:#7a7575;fill-opacity:1;fill-rule:nonzero;stroke:none;stroke-width:1.99999988;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-dashoffset:0;stroke-opacity:1;paint-order:stroke fill markers;color-rendering:auto;image-rendering:auto;shape-rendering:auto;text-rendering:auto;enable-background:accumulate" transform="matrix(0.26458333,0,0,0.26458333,0,288.53332)"></path> </g> </g>
                                </svg>
                                 <!--<span class="position-absolute  start-100 translate-middle badge light text-white rounded-pill bg-danger" style="margin-top:-7px; font-size:14px;" id="totalUser">-->
                                 <!--           0-->
                                 <!--         </span>-->
                                   <!--<span class="badge light text-white bg-danger rounded-circle" id="countUser">0</span>-->
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" >
                                    <div  class="widget-media dlab-scroll">
										    <div class="d-flex justify-content-center fs-4 mb-2 mt-1">Online</div>
										    <hr>
										<ul class="timeline p-3" id="viewUsersActive"  style="width: 350px; height: 240px;  overflow: auto;">
										</ul>
									</div>
                                </div>
                            </li>
                            
                            
                            <li class="nav-item dropdown ">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    @if (Auth::check())
                                    <span style="font-size: 16px;">Hi, {{ Auth::user()->name }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end "  >
                                       @if (Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                      <a href="{{ url('riwayat-perubahan') }}" class="dropdown-item ai-icon">
                                        <span class="ms-2">Riwayat Perubahan Transaksi</span></a>
                                        
                                        <a href="{{ url('perubahan-donatur') }}" class="dropdown-item ai-icon">
                                        <span class="ms-2">Riwayat Perubahan Donatur</span>
                                    </a>
                                       @endif
                                    <!--<a href="app-profile.html" class="dropdown-item ai-icon">-->
                                    <!--    <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
                                    <!--        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>-->
                                    <!--        <circle cx="12" cy="7" r="4"></circle>-->
                                    <!--    </svg>-->
                                    <!--    <span class="ms-2">Profile </span>-->
                                    <!--</a>-->
                                    <!--<a href="email-inbox.html" class="dropdown-item ai-icon">-->
                                    <!--    <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
                                    <!--        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>-->
                                    <!--        <polyline points="22,6 12,13 2,6"></polyline>-->
                                    <!--    </svg>-->
                                    <!--    <span class="ms-2">Inbox </span>-->
                                    <!--</a>-->
                                    <a href="{{ url('logout') }}" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        <span class="ms-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                            
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        
        
        <!--Modal Perusahaan-->
        
        <div class="modal  fade" id="modalPerusahaan">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content  ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-start row" id="pilih-perusahaan">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        
        <!--End Modal Perusahaan-->
        
        <!--Modal-->
        <div class="modal  fade" id="detail_peng">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content  ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Notification </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form d-flex flex-wrap " id="detail_peng_body">
                                
                        </div>
                        <br />
                        <div class="basic-form d-flex flex-wrap " id="detail_canclos_body">
                                
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!--End Modal-->        
        
        <!--Modal-->
        <div class="modal fade" id="modalsy" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_forms" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="bodayy">
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="footayy">
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End Modal-->
        
        <!--modal program per user-->
        <div class="modal fade" id="modprogser"  data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Set Target Program Per User <span id="dino"></span></h4>
                        <div id="tutupin"></div>
                    </div>
                        <div class="modal-body">
                            <div class="d-flex">
                                <div class="bd-highlight">
                                    <div class="form-check form-switch ">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked style="height : 20px; width : 40px;">
                                            <label for="flexSwitchCheckChecked" class="mt-1 ms-1">Show/Hide Program yang memiliki Target Omset</label>
                                        </div>
                                </div>
                            </div>
                            <input type="hidden" id="toggleVal" name="toggleData"> 
                            <div class="d-flex bd-highlight mt-3">
                                <div class="bd-highlight"><span class="badge bg-info">Target Omset : <span id="targetku"></span></span></div>
                            </div>
                            
                            <br>
                            
                            <div class="table-responsive">
                                
                                <table class="table table-striped" id="ttbbll">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Program</th>
                                            <th>Total Target</th>
                                            <th class="sisget">Sisa Target</th>
                                            <th class="gett">Target</th>
                                            <th>Penawaran</th>
                                            <th>Follow Up</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody id="progBod">
                                        
                                    </tbody>
                                    <tfoot id="progFoot">
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="ezzz">Simpan</button>
                        </div>
                </div>
            </div>
        </div>
        <!--end modal-->
        
        
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('partials.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        @yield('konten')
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © 2022 <a href="javascript:void(0)" onclick="return confirm('terima kasih :)')">Berbagi Teknologi</a>. All Rights Reserved</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset($url .'vendor/global/global.min.js')}}"></script>
    <script src="{{asset($url .'js/custom.min.js')}}"></script>
    <script src="{{asset($url .'js/dlabnav-init.js')}}"></script>
    <script src="{{asset($url .'js/demo.js')}}"></script>
    <script src="{{asset($url .'js/styleSwitcher.js')}}"></script>
    
    
    <script src="{{asset($url .'vendor/jquery-nice-select/js/jquery.nice-select.min.js')}}"></script>

    <!-- Datatable -->
    <!--<script src="{{asset($url .'vendor/datatables/js/jquery.dataTables.min.js')}}"></script>-->
    <!--<script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>-->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>
    
    <!-- Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!--<script src="{{asset($url .'js/summernote-paper-size-master/summernote-paper-size.js')}}"></script>-->
    <!--<script src="{{asset($url .'js/summernote-paper-size-master/lang/en-US.js')}}"></script>-->
    <!--<script src="{{asset($url .'js/summernote-pagebreak-master/summernote-pagebreak.js')}}"></script>-->
    
    <!-- DateRange -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <!-- toast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- select2 -->
    <script src="{{asset($url .'vendor/select2/js/select2.full.min.js')}}"></script>

    <!-- toggle -->
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script src="{{asset($url .'vendor/owl-carousel/owl.carousel.js')}}"></script>

    <!--Tabledit-->
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>

    @if(Request::segment(1) == 'dashboard' || Request::segment(2) == 'dashboard')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.6/js/highstock.js"></script>
    @endif

     <script src="https://code.highcharts.com/stock/highstock.js"></script>
    
    @if(Request::segment(1) == 'riwayat-perubahan' || Request::segment(2) == 'riwayat-perubahan')
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/4.2.1/css/fixedColumns.bootstrap5.min.css" rel="stylesheet">
    @endif
    
    <script src="{{ asset($url .'js/dataTables.treeGrid.js')}}"></script>
    
    @if(Request::segment(1) == 'analisis-transaksi' || Request::segment(1) == 'analisis-donatur')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    @endif
    
    @if(Request::segment(1) == 'transaksi-funnel' || Request::segment(2) == 'transaksi-funnel')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/funnel.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    @endif
    
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    
    @if(Request::segment(1) == 'laporan-karyawan' || Request::segment(2) == 'laporan-karyawan')
    <script src="{{ asset($url .'js/recorder.js')}}"></script>
    @endif
    
    
    @if((Request::segment(1) == 'program' || Request::segment(1) == 'coa' || Request::segment(1) == 'trial-balance' || Request::segment(1) == 'saldo-awal') || (Request::segment(2) == 'program' || Request::segment(2) == 'coa' || Request::segment(2) == 'trial-balance' || Request::segment(2) == 'saldo-awal'))
    <!--<script src="{{ asset($url .'js/dataTables.treeGrid.js')}}"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="{{ asset($url .'bt_table/dist/bootstrap-table.min.js')}}"></script>
    <script src="{{ asset($url .'bt_table/dist/extensions/treegrid/bootstrap-table-treegrid.min.js')}}"></script>
    <script src="{{ asset($url .'bt_table/dist/extensions/resizable/bootstrap-table-resizable.js')}}"></script>
    <script src="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.min.js"></script>

    <!--<script src="https://unpkg.com/bootstrap-table@1.20.2/dist/extensions/sticky-header/bootstrap-table-sticky-header.js"></script>-->
    <script src="{{ asset($url .'bt_table/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.js')}}"></script>
    @endif
    
   
    
    @if(Request::segment(1) == 'diroh_handsome')
    <script src="{{ asset($url .'js/multitabs.js')}}"></script>
    @endif
    
    @if(Request::segment(1) == 'perencanaan')
    <script src="{{ asset($url .'js/index.global.js')}}"></script>
    @endif
    
    <!--@if(Request::segment(1) == 'donatur')-->
    <script src="{{asset($url .'js/jquery.dataTables.colResize.js')}}"></script>
    <!--@endif-->
    
    <!--<script src='https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/js/materialize.min.js'></script>-->
    
    <script type="text/javascript">
        
        var routee = "{{ Request::segment(1) }}";
        console.log(routee)
    
         var ntf = null;

        function convertToRupiah(objek) {
            separator = ".";
            a = objek.value;
            b = a.replace(/[^\d]/g, "");
            c = "";
            panjang = b.length;
            j = 0;
            for (i = panjang; i > 0; i--) {
                j = j + 1;
                if (((j % 3) == 1) && (j != 1)) {
                    c = b.substr(i - 1, 1) + separator + c;
                } else {
                    c = b.substr(i - 1, 1) + c;
                }
            }
            if (c <= 0) {
                objek.value = '';
            } else {
                objek.value = 'Rp. ' + c;
            }

        }

        function Angka(objek) {
            a = objek.value;
            b = a.replace(/[^\d]/g, "");

            if (b <= 0) {
                objek.value = '';
            } else {
                objek.value = b;
            }

        }
        
    //     var handlePerfectScrollbar = function() {
    // 		if(jQuery('.dlabnav-scroll').length > 0)
    // 		{
    // 			//const qs = new PerfectScrollbar('.dlabnav-scroll');
    // 			var qs = new PerfectScrollbar('.dlabnav-scroll');
    			
    // 			qs.isRtl = false;
    // 		}
    // 	}
     
        function usersActiveLogin() {
             $.ajax({
                url: "{{ url('countusers') }}",
                success: function(res) {
                    var html = ''; // Membuat variabel untuk menyimpan HTML yang akan ditambahkan
                    $('#totalUser').html(res.length)
                    for (var i = 0; i < res.length; i++) {
                        var item = res[i];
                        // alert(item)
                        if(item.aksi == 'Login'){
                            html +=`<li>
                                        <div class="timeline-panel">
        									<div class="media-body">
        										<div class="mb-1"><i class="fas fa-user fs-4"></i>  ${item.name} </div>
        										<small class="d-block d-flex align-items-center"><div class="m-2"style="width:8px; height:8px; border-radius:50%; background-color: green;"></div>Online</small>
        									</div>
        								</div>
        							</li>`;
                        }
                    }
                    // Setelah loop selesai, tambahkan semua elemen ke dalam #loopUsers
                    $('#viewUsersActive').html(html);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
        
        function notif(){
                console.log('y')
                var uwuh = '';
                
                $.ajax({
                    url: "{{ route('notifya') }}",
                    method: "GET",
                    success: function(data) {
                        var datas = data.data;
                        if(datas.length > 0){
                            for (var i = 0; i < datas.length; i++) {
                            
                            if(datas[i].one == 'pengeluaran'){
                                var apatuh = 'Pengeluaran';
                            }else if(datas[i].two == 'transaksi'){
                                var apatuh = 'Transaksi';
                            }
                            
                            uwuh += `<li id="`+datas[i].id+`" onclick="myFunction(this.id)" style="cursor: pointer">
                                    <div class="timeline-panel">
    									<div class="media me-2 media-info">X</div>
    									<div class="media-body">
    										<h6 class="mb-1"> <text class="text-info">`+datas[i].user_approve+`</text> Rejected `+apatuh+` <text class="text-danger">`+datas[i].nama_coa+`</text></h6>
    											<small class="d-block">`+datas[i].tanggal+`</small>
    									</div>
    								</div>
    							</li>`;
                            }
                            
                        }else{
                               uwuh = `<a href="javascript:void(0);" style="display: block; padding: 0.9375rem 1.875rem 0; text-align: center;"> Tidak Ada <i class="ti-arrow-end"></i></a>` 
                        }
							
                        $('#notif').html(uwuh)
                        $('#cont').html(data.itung)
                        setTimeout(function() {
                          notif();
                       }, 3000);
                    }
                });
            }
        

        function notif_peng(){
             $.ajax({
                url: "{{ url('notif-pengumuman') }}",
                success: function(data){
                    var datas = data;
                    var notif ="";
                    var j_lem = ""
                    if(datas.length > 0){
                        $.each(datas.slice(0, 4), function(key,valueObj){
                            if(valueObj.tab == 'peng'){
                                notif +=`<li>
                                            <div class="timeline-panel">
            									<div class="media-body">
            										<h6 class="mb-1"> <text class="text-info">${valueObj.jenis} 
            										${(valueObj.jenis == "Lembur" && valueObj.jam_awal != null)  ? '/jam':  valueObj.jenis == "Lembur" && valueObj.jam_awal == null ? '/hari': '' }</text></h6>
            										<small class="d-block">${valueObj.jam_awal == null ? valueObj.tgl_awal + ' s/d ' + valueObj.tgl_akhir: valueObj.jam_awal + ' s/d ' + valueObj.jam_akhir}</small>
            										<small class="d-block">${valueObj.isi}</small>
            									</div>
            								</div>
            							</li>`;
                            }else{
                                notif +=`
                                        <li>
                                            <div class="timeline-panel">
            									<div class="media-body" onclick="silit('${valueObj.isi}','${valueObj.ket}','${valueObj.tgl_awal}','${valueObj.tgl_akhir}')" style="cursor: pointer">
            										<h6 class="mb-1"> <text class="text-danger">${valueObj.jenis}</text></h6>
            										<small class="d-block">${valueObj.tgl_awal}</small>
            										<small class="d-block">${valueObj.isi}</small>
            										<small class="d-block">${valueObj.ket}</small>
            									</div>
            								</div>
            							</li>`;
                            }
                        });
                    }else{
                        notif = `<a href="javascript:void(0);" style="display: block; padding: 0.9375rem 1.875rem 0; text-align: center;"> Tidak Ada <i class="ti-arrow-end"></i></a>` 
                    }
                    $('#notif_peng').html(notif);
                    $('#cont_peng').html(datas.length);
                }
            })
        }
       
        
        function naninu(){
            $.ajax({
                url: "{{ url('badge_doang') }}",
                success: function(data){
                    
                    var transaksi = '';
                    if(data.transaksi[0].transaksi > 0){
                        transaksi = `<span class="badge badge-danger text-white badge-sm float-end">!</span>`
                        $('#t_badge').html(transaksi);
                        $('#t1_badge').html(transaksi);
                        $('#t2_badge').html(transaksi);
                    }else{
                        $('#t_badge').html(transaksi);
                        $('#t1_badge').html(transaksi);
                        $('#t2_badge').html(transaksi);
                    }
                    
                    var pengeluaran = '';
                    if(data.pengeluaran[0].pengeluaran > 0){
                        pengeluaran = `<span class="badge badge-danger text-white badge-sm float-end">!</span>`
                        $('#p_badge').html(pengeluaran);
                    }else{
                        $('#p_badge').html(pengeluaran);
                    }
                    
                    var buku = '';
                    if(data.pengeluaran[0].pengeluaran > 0 || data.transaksi[0].transaksi > 0){
                        buku = `<span class="badge badge-danger text-white badge-sm float-end">!</span>`
                        $('#b_badge').html(buku);
                    }else{
                        $('#b_badge').html(buku);
                    }
                }
            })
        }
        
        function silit(a, b, c, d){
            
            const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Perubahan Terdeteksi!',
                        text: 'Akun ' + a + ' ' + b + ' pada ' + c + ' perlu diperiksa dan dilakukan proses closing ulang !!!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',

                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('on_link_param') }}",
                                method: 'POST',
                                data: {
                                    p1: d,
                                    link: 'trial'
                                },
                                success: function(data) {
                                    window.location.href = "{{ url('trial-balance') }}";
                                }
                            })
                            // window.location.href = "{{ url('trial-balance') }}";
                        } 
                        // else if (result.dismiss === Swal.DismissReason.cancel) {
                        //     window.location.href = "{{ url('/karyawan') }}";
                        // }
                    })
            // alert('Akun ' + a + ' ' + b + ' pada ' + c + ' perlu diperiksa dan dilakukan proses closing ulang !!!')
                        
            //  $.ajax({
            //         url: "{{ url('trial-balance') }}",
            //         method: 'POST',
            //         data: {
            //             bat_bln: '08-2023'
            //         },
            //         success: function(data) {
            //             window.location.href = this.url;
            //             alert('COA ' + a + ' ' + b + ' ' + c)
            //         }
            //     })
            // console.log(a)
        }
                    
            // $.ajax({
            //     url: "{{ url('countusers') }}",
            //     success: function(res) {
            //         var html = ''; // Membuat variabel untuk menyimpan HTML yang akan ditambahkan
        
            //         for (var i = 0; i < res.length; i++) {
            //             var item = res[i];
            //             console.log(item)
            //             html += `<li class="dropdown-item">${item.name}</li>`;
            //         }
        
            //         // Setelah loop selesai, tambahkan semua elemen ke dalam #loopUsers
            //         $('#loopUsers').html(html);
            //     },
            //     error: function(err) {
            //         console.log(err);
            //     }
            // });
        $(document).ready(function() {
            var textArray = [];
            
            // let token = localStorage.getItem("authToken");

            // if (!token) {
            //     window.location.href = "https://home.kilauindonesia.org";
            // }
        
            // $.ajax({
            //     url: "https://kilauindonesia.org/api/protected-data",
            //     type: "GET",
            //     headers: { "Authorization": "Bearer " + token },
            //     success: function(response) {
            //         console.log("Data:", response);
            //     },
            //     error: function() {
            //         alert("Token tidak valid, silakan login kembali!");
            //         localStorage.removeItem("authToken");
            //         window.location.href = "https://home.kilauindonesia.org";
            //     }
            // });

            // Menggunakan .each() untuk mengiterasi melalui elemen-elemen dengan class 'arraySidebar'
            $('.arraySidebar').each(function() {
                textArray.push($(this).text());
            });

            // console.log(textArray);
            
            $(document).on('keyup', '#searchSidebar', function(e) {
                var searchTerm = $(this).val().toLowerCase();
            
                $.ajax({
                    url: 'search-sidebar',
                    data: {
                        menu: textArray,
                        query: searchTerm
                    },
                    success: function(res) {
                        var html = '';
                        if (res.length > 0) {
                            for (var i = 0; i < res.length; i++) {
                                var menuParent = res[i].menu_parent;
                                var menu = res[i].menu.toLowerCase();
            
                                var resultText = `${menu}`;
                                var highlightedText = resultText.replace(new RegExp(searchTerm, 'gi'), match => `<mark>${match}</mark>`);
            
                                var fadedMenuParent = `<span style="opacity: 0.7;">${menuParent}</span>`;
            
                                html += `   <li class="hover-side">
                                                <div class="timeline-panel">
                                                    <div class="media-body">
                                                        <h4 class="d-flex justify-content-start align-items-center ">
                                                            <a href="${res[i].link}">${fadedMenuParent}/${highlightedText}</a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </li>`;
                            }
                        } else {
                        html += `
                                    <li class="hover-side">
                                        <div class="timeline-panel">
                                            <div class="media-body">
                                                <h4 class="d-flex justify-content-start align-items-center">
                                                    <a href="">Cari Page..</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </li>
                                `
                        }
            
                        $('#search_si').html(html);
                    }
                });
            });
            
            $(document).on('keypress', '#searchSidebar', function(e) {
                if (e.which === 13) { 
                    $('#search_si a:first').focus();
                }
            });



            $.ajax({
                url:'company-layout',
                success: function(res){
                    var data = res;
                    var card = '';
                    var secCard = '';
                    
                    for(var i = 0; i < data.length; i++){
                        if(data[i].logo == 'Kilau Biru.png'){
                            var image = `https://kilauindonesia.org/kilau/upload/${data[i].logo}`;
                        }else{
                            var image = "https://kilauindonesia.org/kilau/upload/BT-LOGO.png";
                        }
                        card += `
                        <button class="btn-perusahaan col-lg-4 col-md-6 col-sm-12 ceker p-2" value="${data[i].id_com}" data-nama="${data[i].name}"  id="com"  name="com">
                              <div class="border card mb-3 d-flex justify-content-center align-items-center shadow-md perusahaan cursor-pointer">
                                  <div class="row g-0">
                                    <div class="col-md-4 d-flex align-items-center p-3">
                                      <img src="${image}" class="img-fluid" alt="Logo">
                                    </div>
                                    <div class="col-md-7">
                                      <div class="card-body d-flex align-items-center">
                                        <div class="">${data[i].name}</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </button>
                        `;
                    }
                    secCard += `
                            <button class="btn-perusahaan col-lg-12 ceker p-2" value="0" data-nama="Semua Perusahaan"  id="com"  name="com">
                                <div class="border card mb-3 d-flex justify-content-center align-items-center shadow-md perusahaan cursor-pointer">
                                  <div class="row g-0">
                                    <div class="col-12">
                                      <div class="card-body d-flex align-items-center">
                                        <div class="fs-4">Semua Perusahaan</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </button>
                        `;
                    
                    $('#pilih-perusahaan').html(secCard + card)
                }
            })
            
            
                  usersActiveLogin()
                   
            const ps = new
            PerfectScrollbar('.dlabnav-scroll', {
                wheelSpeed: 2,
                wheelPropagation: true,
                minScrollbarLength: 20
            });
            
            naninu();
            notif_peng(); 
            
            var seg_ar = '<?= Request::segment(1) ?>'
            
            if(seg_ar != 'trial-balance'){
                $.ajax({
                    url: "{{ url('off_link_param') }}",
                    data: {
                        link: 'trial'
                    },
                    success: function(data){
                        console.log('link trial off')
                    }
                })
            }else{
                console.log('get link trial')
            }
            
            if(seg_ar != 'transaksi'){
                $.ajax({
                    url: "{{ url('off_link_param') }}",
                    data: {
                        link: 'transaksi'
                    },
                    success: function(data){
                        console.log('link transaksi off')
                    }
                })
            }else{
                console.log('get transaksi trial')
            }
        
            $('#t1_badge, #t2_badge').on('click', function(){
                // var now = new Date();
                // var tahunAwal = new Date(now.getFullYear(), 0, 1);
    
                // // Format tanggal sebagai "dd/mm/yyyy"
                // var dd = String(tahunAwal.getDate()).padStart(2, '0');
                // var mm = String(tahunAwal.getMonth() + 1).padStart(2, '0'); // Januari dimulai dari 0
                // var yyyy = tahunAwal.getFullYear();
    
                // var tanggalAwalTahun = dd + '/' + mm + '/' + yyyy;
    
                // // Format tanggal saat ini sebagai "dd/mm/yyyy"
                // var ddSekarang = String(now.getDate()).padStart(2, '0');
                // var mmSekarang = String(now.getMonth() + 1).padStart(2, '0');
                // var yyyySekarang = now.getFullYear();
    
                // var tanggalSekarang = ddSekarang + '/' + mmSekarang + '/' + yyyySekarang;
                
                var today = new Date();
                var firstDateOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

                // Format the dates as "yyyy/mm/dd"
                var formattedToday = today.getFullYear() +'-'+ (today.getMonth() + 1).toString().padStart(2, '0') +'-'+ today.getDate().toString().padStart(2, '0');
                var formattedFirstDateOfMonth = firstDateOfMonth.getFullYear()  +'-'+ (firstDateOfMonth.getMonth() + 1).toString().padStart(2, '0') +'-'+ firstDateOfMonth.getDate().toString().padStart(2, '0');
                
                // console.log([formattedToday ])
                var statak = 2;
                var daterange = `${formattedFirstDateOfMonth} s.d. ${formattedToday}`;
                
                // var dari = '';
                // var sampai= '';
                // var kota= '';
                // var kol= '';
                // var blns= '';
                // var blnnnn= '';
                // var statuus= '';
                // var min= '';
                // var max= '';
                // var thnn= '';
                // var plhtgl= '';
                // var bayar= '';
                // // console.log(statak);
                // var bank
                
                
                // $.ajax({
                //     url: "{{ url('transaksi') }}",
                //     method: 'POST',
                //     data: {
                //         daterange: daterange,
                //         sampai: sampai,
                //         dari: dari,
                //         kota: kota,
                //         kol: kol,
                //         blns: blns,
                //         blnnnn: blnnnn,
                //         statuus: statuus,
                //         max: max,
                //         min: min,
                //         plhtgl: plhtgl,
                //         thnn: thnn,
                //         statak: statak,
                //         bayar: bayar,
                //         bank: bank,
                //         tab: 'tabxx'
                //     },
                //     success: function(data) {
                //         window.location.href = this.url;
                //         console.log(data)
                //     }
                // })
                
                $.ajax({
                    url: "{{ url('on_link_param') }}",
                    method: 'POST',
                    data: {
                        p1: statak,
                        p2: daterange,
                        link: 'transaksi'
                    },
                    success: function(data) {
                        window.location.href = "{{ url('transaksi') }}";
                    }
                })
            })
                   
            	 // detail notif
            $('.all-notification').on('click', function(){
                $('#detail_peng').modal('show')
                $.ajax({
                    url: "{{ url('notif-pengumuman') }}",
                    success: function(data){
                        var datas = data;
                        var notif ="";
                        var notif_cc ="";
                        var j_lem = ""
                        if(datas.length > 0 ){
                            $.each(datas, function(key,valueObj){
                				if(valueObj.tab == 'peng'){
                				    notif +=`<div class="p-2 col-md-6 col-lg-4 col-12">
                                                <div class="timeline-panel shadow border rounded p-3">
                									<div class="media-body">
                										<h6 class="mb-1"> <text class="text-info">${valueObj.jenis} 
                										${(valueObj.jenis == "Lembur" && valueObj.jam_awal != null)  ? '/jam':  valueObj.jenis == "Lembur" && valueObj.jam_awal == null ? '/hari': '' }</text></h6>
            											<small class="d-block">${valueObj.jam_awal == null ? valueObj.tgl_awal + ' s/d ' + valueObj.tgl_akhir: valueObj.jam_awal + ' s/d ' + valueObj.jam_akhir}</small>
            											<small class="d-block">${valueObj.isi}</small>
                									</div>
                								</div>
                							</div>`;
                                }else{
                                    notif_cc +=`<div class="p-2 col-md-6 col-lg-4 col-12">
                                                <div class="timeline-panel shadow border rounded p-3">
                									<div class="media-body" onclick="silit('${valueObj.isi}','${valueObj.ket}','${valueObj.tgl_awal}','${valueObj.tgl_akhir}')" style="cursor: pointer">
                										<h6 class="mb-1"> <text class="text-danger">${valueObj.jenis}</text></h6>
                										<small class="d-block">${valueObj.tgl_awal}</small>
                										<small class="d-block">${valueObj.isi}</small>
            										    <small class="d-block">${valueObj.ket}</small>
                									</div>
                								</div>
                							</div>`;
                                }
                            });
                        }else{
                            notif = `<a href="javascript:void(0);" style="display: block; padding: 0.9375rem 1.875rem 0; text-align: center;"> Tidak Ada <i class="ti-arrow-end"></i></a>` 
                            notif_cc = `<div></div>`
                        }
                        $('#detail_peng_body').html(notif);
                        $('#detail_canclos_body').html(notif_cc);
                    }
                })
            })
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            setInterval(function() {
                notif_peng();
            }, 20000)
        });
        
        function hitungTotal() {
            var total = 0;
            var totil = 0;
            
            $('#ttbbll tbody tr').each(function(){
                var value = parseInt($(this).find('td:eq(3)').text());
                if(!isNaN(value)){
                    console.log(total += value)
                    
                }
            });
            
            $('.aww').each(function(){
                var xalue = parseInt($(this).val());
                if(!isNaN(xalue)){
                    console.log(totil += xalue)
                }
            });
            
            $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
            $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
        }

        @if(Request::segment(1) == 'setting-target' || Request::segment(2) == 'setting-target')
        $(document).on('click', '.progser', function() {
            hitungTotal()
            
            var ea = $('#user_table').DataTable().row(this).data();
            var id_spesial = ea.id_spesial
            var unit = $('#units2').val()
            $('#toggleVal').val(true)
            var toggle = $('#toggleVal').val()
            $('#dino').html('('+ea.id_jenis+')')
            $('#modprogser').modal('show')
            
            // $('#toggleVal').val(true)
            
            $.ajax({
                url: "{{ url('getProgSer') }}",
                method: "GET",
                data: {
                    toggle: toggle,
                    datay: ea,
                    unit: unit
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil');
                    
                    var masuk = [];
                    
                    var to = data.to
                    var progs =  data.prog
                    var tk =  data.tk
                    var sistar =  data.sistar
                    var klepong
                    var enak = 0
                    var banget
                    
                    var id_kantor = $('#units2').val();
                    var id_target = ea.id_targetnya;
                    var tanggal = ea.tahun;
                    var id_kar = ea.id_spesial;
                    
                    var tai = [];
                    
                    $('#targetku').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(to.target == null ? 0 : to.target));
                    
                    if(progs.length > 0){
                        for(var u = 0; u < progs.length; u++){
                            
                            var uiui = sistar[u].id == null ? 0: sistar[u].id;
                            
                            klepong += `
                            <tr>
                                <td>${u+1}</td>
                                <td>${progs[u].program}</td>
                                <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(progs[u].target)}</td>
                                <td id="gila${u}">${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}</td>
                                <input id="sisa${u}" type="hidden"/>
                                <td>
                                    <input type="hidden" id="inpt${u}">
                                    <input class="aww" data-id-progser="${uiui}" data-id="${progs[u].id_jenis}" data-kantor="${id_kantor}"
                                        data-idtarget="${id_target}" data-tgl="${tanggal}" data-sisa="${sistar[u].sisa_target == '' ? progs[u].target: sistar[u].sisa_target}" data-kar="${id_kar}" data-sisa-target = "${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}" 
                                        data-target="${progs[u].target}" data-index="${u}" id="infut`+u+`" type="number" data-penawaran= "${sistar[u].penawaran}" data-closing= "${sistar[u].closing}" data-follow= "${sistar[u].followup}" min="0"/>
                                </td>
                                <td><input min="0" type="number" size="50" id="penawaran${u}" data-index="${u}" class="pee" /></td>
                                <td><input min="0" type="number" size="50" id="followup${u}" data-index="${u}" class="pek"/></td>
                                <td><input min="0" type="number" size="50" id="closing${u}" data-index="${u}" class="pes"/></td>
                            </tr>
                            `
                            enak += progs[u].target
                        }
                        
                        banget = `
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(enak)}</td>
                                <td id="totsis"></td>
                                <td id="totinput"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        `
                        
                        
                        $('#progBod').html(klepong)
                        $('#progFoot').html(banget)
                        
                        
                        var yes = sistar;
                        
                        // console.log([progs, sistar])
                        
                        for(var xx = 0; xx < yes.length; xx++){
                                // tai.push({value: yes[xx].target, id_program: yes[xx].program})
                            $(`#inpt${xx}`).val(yes[xx].target)
                            $(`#infut${xx}`).val(yes[xx].target)
                            $(`#closing${xx}`).val(yes[xx].closing)
                            $(`#penawaran${xx}`).val(yes[xx].penawaran)
                            $(`#followup${xx}`).val(yes[xx].followup)
                        }
                        
                    }
                    
                    
                    var total = 0;
                    var totil = 0;
                    
                    $('#ttbbll tbody tr').each(function(){
                        var value = parseInt($(this).find('td:eq(3)').text());
                        if(!isNaN(value)){
                            console.log(total += value)
                            
                        }
                    });
                    
                    $('.aww').each(function(){
                        var xalue = parseInt($(this).val());
                        if(!isNaN(xalue)){
                            console.log(totil += xalue)
                        }
                    });
                    
                    $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
                    $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
                }
            });
            
            
            $('#flexSwitchCheckChecked').on('change', function(){
                if ($(this).prop('checked')) {
                    $('#toggleVal').val(true)
                    var toggle = $('#toggleVal').val()
                } else {
                    $('#toggleVal').val(false)
                    var toggle = $('#toggleVal').val()
                        
                }
                    
                $.ajax({
                    url: "{{ url('getProgSer') }}",
                    method: "GET",
                    data: {
                        toggle: toggle,
                        datay: ea,
                        unit: unit
                    },
                    dataType: "json",
                    success: function(data) {
                        toastr.success('Berhasil');
                            
                        var masuk = [];
                        
                        var to = data.to
                        var progs =  data.prog
                        var tk =  data.tk
                        var sistar =  data.sistar
                        var klepong
                        var enak = 0
                        var banget
                        
                        var id_kantor = $('#units2').val();
                        var id_target = ea.id_targetnya;
                        var tanggal = ea.tahun;
                        var id_kar = ea.id_spesial;
                        
                        var tai = [];
                        
                        $('#targetku').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(to.target == null ? 0 : to.target));
                        
                        if(progs.length > 0){
                            for(var u = 0; u < progs.length; u++){
                                
                                var uiui = sistar[u].id == null ? 0: sistar[u].id;
                                
                                klepong += `
                                <tr>
                                    <td>${u+1}</td>
                                    <td>${progs[u].program}</td>
                                    <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(progs[u].target)}</td>
                                    <td id="gila${u}">${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}</td>
                                    <input id="sisa${u}" type="hidden"/>
                                    <td>
                                        <input type="hidden" id="inpt${u}">
                                        <input class="aww" data-id-progser="${uiui}" data-id="${progs[u].id_jenis}" data-kantor="${id_kantor}"
                                            data-idtarget="${id_target}" data-tgl="${tanggal}" data-sisa="${sistar[u].sisa_target == '' ? progs[u].target: sistar[u].sisa_target}" data-kar="${id_kar}" data-sisa-target = "${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}" 
                                            data-target="${progs[u].target}" data-index="${u}" id="infut`+u+`" type="number" data-penawaran= "${sistar[u].penawaran}" data-closing= "${sistar[u].closing}" data-follow= "${sistar[u].followup}" min="0"/>
                                    </td>
                                    <td><input min="0" type="number" size="50" id="penawaran${u}" data-index="${u}" class="pee" /></td>
                                    <td><input min="0" type="number" size="50" id="followup${u}" data-index="${u}" class="pek"/></td>
                                    <td><input min="0" type="number" size="50" id="closing${u}" data-index="${u}" class="pes"/></td>
                                </tr>
                                    `
                                enak += progs[u].target
                            }
                                
                            banget = `
                                <tr>
                                    <td>Total</td>
                                    <td></td>
                                    <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(enak)}</td>
                                    <td id="totsis"></td>
                                    <td id="totinput"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            `
                                
                                
                            $('#progBod').html(klepong)
                            $('#progFoot').html(banget)
                                
                                
                            var yes = sistar;
                                
                            // console.log([progs, sistar])
                            
                            for(var xx = 0; xx < yes.length; xx++){
                                    // tai.push({value: yes[xx].target, id_program: yes[xx].program})
                                $(`#inpt${xx}`).val(yes[xx].target)
                                $(`#infut${xx}`).val(yes[xx].target)
                                $(`#closing${xx}`).val(yes[xx].closing)
                                $(`#penawaran${xx}`).val(yes[xx].penawaran)
                                $(`#followup${xx}`).val(yes[xx].followup)
                            }
                                
                        }
                            
                            
                        var total = 0;
                        var totil = 0;
                            
                        $('#ttbbll tbody tr').each(function(){
                            var value = parseInt($(this).find('td:eq(3)').text());
                            if(!isNaN(value)){
                                console.log(total += value)
                                
                            }
                        });
                        
                        $('.aww').each(function(){
                            var xalue = parseInt($(this).val());
                            if(!isNaN(xalue)){
                                console.log(totil += xalue)
                            }
                        });
                            
                        $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
                        $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
                    }
                });
            })
        })
        
        @elseif(Request::segment(1) == 'perencanaan' || Request::segment(2) == 'perencanaan')
        $(document).on('click', '.progser', function() {
            hitungTotal()
            $('#dino').html($(this).attr('data-nama'))
            $('#modprogser').modal('show')
            var id_spesial = $(this).attr('data-id')
            var unit = $('#unit').val()
            $('#toggleVal').val(true)
            var toggle = $('#toggleVal').val()
            
            var tahun = $('#tanggal').val();
            var tggls
            
            console.log(tahun)
            
            if (tahun == ''){
                var currentDates = new Date();
                var years = currentDates.getFullYear();
                var months = ("0" + (currentDates.getMonth() + 1)).slice(-2); // Adding 1 because getMonth() returns zero-based month
                var formattedDates = years + "-" + months;
                    
                tggls = formattedDates;
            }else{
                tggls = tahun
            }
            
            var ea = {
                'tahun' : tggls,
                'id_spesial' : id_spesial
            };
            
            // var ea = JSON.stringify(yess);
            
            // console.log(ea);
            
            $.ajax({
                url: "{{ url('getProgSer') }}",
                method: "GET",
                data: {
                    toggle: toggle,
                    datay: ea,
                    unit: unit
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil');
                    
                    var masuk = [];
                    
                    var to = data.to
                    var progs =  data.prog
                    var tk =  data.tk
                    var sistar =  data.sistar
                    var klepong
                    var enak = 0
                    var banget
                    
                    var id_kantor = unit;
                    var id_target = ea.id_targetnya;
                    var tanggal = ea.tahun;
                    var id_kar = ea.id_spesial;
                    
                    var tai = [];
                    
                    $('#targetku').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(to.target == null ? 0 : to.target));
                    
                    if(progs.length > 0){
                        for(var u = 0; u < progs.length; u++){
                            
                            var uiui = sistar[u].id == null ? 0: sistar[u].id;
                            
                            klepong += `
                            <tr>
                                <td>${u+1}</td>
                                <td>${progs[u].program}</td>
                                <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(progs[u].target)}</td>
                                <td id="gila${u}">${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}</td>
                                <input id="sisa${u}" type="hidden"/>
                                <td>
                                    <input type="hidden" id="inpt${u}">
                                    <input class="aww" data-id-progser="${uiui}" data-id="${progs[u].id_jenis}" data-kantor="${id_kantor}"
                                        data-idtarget="${id_target}" data-tgl="${tanggal}" data-sisa="${sistar[u].sisa_target == '' ? progs[u].target: sistar[u].sisa_target}" data-kar="${id_kar}" data-sisa-target = "${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}" 
                                        data-target="${progs[u].target}" data-index="${u}" id="infut`+u+`" type="number" data-penawaran= "${sistar[u].penawaran}" data-closing= "${sistar[u].closing}" data-follow= "${sistar[u].followup}" min="0"/>
                                </td>
                                <td><input min="0" type="number" size="50" id="penawaran${u}" data-index="${u}" class="pee" /></td>
                                <td><input min="0" type="number" size="50" id="followup${u}" data-index="${u}" class="pek"/></td>
                                <td><input min="0" type="number" size="50" id="closing${u}" data-index="${u}" class="pes"/></td>
                            </tr>
                            `
                            enak += progs[u].target
                        }
                        
                        banget = `
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(enak)}</td>
                                <td id="totsis"></td>
                                <td id="totinput"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        `
                        
                        
                        $('#progBod').html(klepong)
                        $('#progFoot').html(banget)
                        
                        
                        var yes = sistar;
                        
                        // console.log([progs, sistar])
                        
                        for(var xx = 0; xx < yes.length; xx++){
                                // tai.push({value: yes[xx].target, id_program: yes[xx].program})
                            $(`#inpt${xx}`).val(yes[xx].target)
                            $(`#infut${xx}`).val(yes[xx].target)
                            $(`#closing${xx}`).val(yes[xx].closing)
                            $(`#penawaran${xx}`).val(yes[xx].penawaran)
                            $(`#followup${xx}`).val(yes[xx].followup)
                        }
                        
                    }
                    
                    
                    var total = 0;
                    var totil = 0;
                    
                    $('#ttbbll tbody tr').each(function(){
                        var value = parseInt($(this).find('td:eq(3)').text());
                        if(!isNaN(value)){
                            console.log(total += value)
                            
                        }
                    });
                    
                    $('.aww').each(function(){
                        var xalue = parseInt($(this).val());
                        if(!isNaN(xalue)){
                            console.log(totil += xalue)
                        }
                    });
                    
                    $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
                    $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
                }
            });
        })
        @endif
        
        $('#ezzz').on('click', function(){
            var coba = $('.aww').attr('data-total');
            
            var totalinput = parseInt($('#totinput').text().replace(/\D/g, ''))
            var targetKu = parseInt($('#targetku').text().replace(/\D/g, ''))
            
            var ngeri = [];
            let result = [];
            let tempArray = [];
            
            
            if(totalinput < targetKu){
                alert('Total Target Kurang dari Target Perbulan Anda')
            }else if(totalinput > targetKu ){
                alert('Total Target lebih dari Target Perbulan Anda')
            }else{
                $('.aww').each(function(){
                    var program = $(this).attr('data-id');
                    var name = $(this).attr('id');
                    var xalue = $(this).val()
                    var kantor = $(this).attr('data-kantor');
                    var tgl = $(this).attr('data-tgl');
                    var kar = $(this).attr('data-kar');
                    var id_target = $(this).attr('data-idtarget');
                    var sisa = $(this).attr('data-sisa');
                    var target = $(this).attr('data-target');
                    var idnya = $(this).attr('data-id-progser');
                    
                    var follow = $(this).attr('data-follow');
                    var closing = $(this).attr('data-closing');
                    var penawaran = $(this).attr('data-penawaran');
                    
                    if(!isNaN(xalue) && xalue != null || !isNaN(xalue) && xalue != ''){
                        ngeri.push({
                            idnya: idnya,
                            nama : name, 
                            value : xalue, 
                            program : program, 
                            id_target: id_target,
                            kantor: kantor, 
                            tgl: tgl, 
                            kar: kar, 
                            target: target,
                            sisa: sisa,
                            penawaran: penawaran,
                            closing: closing,
                            follow: follow
                        })
                    }
                    
                });
                
                $.ajax({
                    url: "{{ url('postProgSer') }}",
                    method: "POST",
                    data: {
                        ngeri: ngeri
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data)
                            
                        // Swal.fire({
                        //     type: 'success',
                        //     title: 'Berhasil',
                        //     text: 'Data Berhasil Disimpan!',
                        //     width: 500
                        // });
                        toastr.success('Berhasil');
                        $('#modprogser').modal('hide');

                    }
                })   
            }
        })
        
    </script>
    @extends('dashboard.js')
    @extends('core.js')
    @extends('auth.js')
    @extends('transaksi.js')
    @extends('donatur.js')
    @extends('kolekting.js')
    @extends('karyawan.js')
    @extends('presensi.js')
    @extends('fins.js')
    @extends('fins.js1')
    @extends('setting.js')
    @extends('skema.js')
    @extends('hcm.js')
    @extends('penerima-manfaat.js')
    @extends('sales.js')
    @extends('crm.js')
    @extends('jabatan.js')
    @extends('report-management.js')
    @extends('program.js')
    @extends('voting.js')
    @extends('akuntasi.js')
    @extends('akuntasi.js_trial')
    @extends('another-company.js')
    @extends('fins-home.js')
    @extends('fins-budget.js')
    @extends('fins-laporan.js')
    @extends('diroh.js')
    @extends('bukti-setor.js')
    @extends('perencanaan.js')
    @extends('kpi.js')
    @extends('jam-kerja.js')
    @extends('generate-file.js')
    @extends('notif.js')
    
    <script>
        function cardsCenter() {
            jQuery('.card-slider').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                //center:true,
                slideSpeed: 3000,
                paginationSpeed: 3000,
                dots: true,
                navText: ['<i class="fas fa-arrow-left"></i>', '<i class="fas fa-arrow-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    576: {
                        items: 1
                    },
                    800: {
                        items: 1
                    },
                    991: {
                        items: 1
                    },
                    1200: {
                        items: 1
                    },
                    1600: {
                        items: 1
                    }
                }
            })
        }

        jQuery(window).on('load', function() {
            setTimeout(function() {
                cardsCenter();
            }, 1000);
        });
    </script>

</body>

</html>