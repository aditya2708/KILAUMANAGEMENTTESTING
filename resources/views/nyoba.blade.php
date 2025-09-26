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
    <meta name="description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template">
    <meta property="og:title" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template">
    <meta property="og:description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template">
    <meta property="og:image" content="https:/fillow.dexignlab.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">

    <!-- PAGE TITLE HERE -->
    <title>Admin</title>

    <!-- FAVICONS ICON -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet"> -->
    <link rel="shortcut icon" type="image/png" href="{{asset('images/favicon.png')}}">
    <link href="{{asset('vendor/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet">
    <link href="{{asset('vendor/owl-carousel/owl.carousel.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/nouislider/nouislider.min.css')}}">
    
    <!--<link href="{{asset('css/b.tabs.css')}}" rel="stylesheet">-->

    <!-- Datatable -->
    <link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">

    <!-- select2 -->
    <link rel="stylesheet" href="{{asset('vendor/select2/css/select2.min.css')}}">

    @if(Request::segment(2) == 'create' && Request::segment(1) == 'karyawan')
    <!-- step wizard -->
    <link href="{{asset('vendor/jquery-smartwizard/dist/css/smart_wizard.min.css')}}" rel="stylesheet">
    @endif

    <!-- Style css -->
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

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
    @if(Request::segment(1) == 'capaian-kolekting' || Request::segment(1) == 'dashboard' || Request::segment(1) == 'capaian-omset')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.6/css/highcharts.css" />
    @endif
    
    @if(Request::segment(1) == 'program' || Request::segment(1) == 'coa' || Request::segment(1) == 'saldo-awal')
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.css" rel="stylesheet">
    @endif
    
    <!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">-->
    
    <!--<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>-->
    
    <!--<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">-->
    
    <style>
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


        .buttons-collection {
            margin-left: 15px;
            margin-top: -2px;
            height: 30px;
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
    @if(Request::segment(1) == 'pengeluaran')
    <style>
        .select2-selection__rendered{
          word-wrap: break-word !important;
          text-overflow: inherit !important;
          white-space: normal !important;
        }
        
        .select2-selection--single {
          height: 100% !important;
        }
    </style>
    @endif
    @if(Request::segment(1) == 'karyawan' && Request::segment(2) == 'edit')
    <style>
        .bigdrop {
            width: 600px !important;

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
        <div class="nav-header">
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
                    <h2 class="">Kilau</h2>
                    <!--<span class="brand-sub-title">(masih belum tau)</span>-->
                </div>
            </a>
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
                                {{$ah}}
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item d-flex align-items-center">
                                <div class="input-group search-area">
                                    <input type="text" class="form-control" placeholder="Search here...">
                                    <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                                </div>
                            </li>
                            
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<svg width="28" height="28" viewbox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.3333 19.8333H23.1187C23.2568 19.4597 23.3295 19.065 23.3333 18.6666V12.8333C23.3294 10.7663 22.6402 8.75902 21.3735 7.12565C20.1068 5.49228 18.3343 4.32508 16.3333 3.80679V3.49996C16.3333 2.88112 16.0875 2.28763 15.6499 1.85004C15.2123 1.41246 14.6188 1.16663 14 1.16663C13.3812 1.16663 12.7877 1.41246 12.3501 1.85004C11.9125 2.28763 11.6667 2.88112 11.6667 3.49996V3.80679C9.66574 4.32508 7.89317 5.49228 6.6265 7.12565C5.35983 8.75902 4.67058 10.7663 4.66667 12.8333V18.6666C4.67053 19.065 4.74316 19.4597 4.88133 19.8333H4.66667C4.35725 19.8333 4.0605 19.9562 3.84171 20.175C3.62292 20.3938 3.5 20.6905 3.5 21C3.5 21.3094 3.62292 21.6061 3.84171 21.8249C4.0605 22.0437 4.35725 22.1666 4.66667 22.1666H23.3333C23.6428 22.1666 23.9395 22.0437 24.1583 21.8249C24.3771 21.6061 24.5 21.3094 24.5 21C24.5 20.6905 24.3771 20.3938 24.1583 20.175C23.9395 19.9562 23.6428 19.8333 23.3333 19.8333Z" fill="#717579"></path>
										<path d="M9.9819 24.5C10.3863 25.2088 10.971 25.7981 11.6766 26.2079C12.3823 26.6178 13.1838 26.8337 13.9999 26.8337C14.816 26.8337 15.6175 26.6178 16.3232 26.2079C17.0288 25.7981 17.6135 25.2088 18.0179 24.5H9.9819Z" fill="#717579"></path>
									</svg>
                                    <span class="badge light text-white bg-warning rounded-circle" id="cont">0</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:auto;">
										<ul class="timeline" id="notif">
											    
										</ul>
									</div>
                                    <a class="all-notification" href="javascript:void(0);">See all notifications <i class="ti-arrow-end"></i></a>
                                </div>
                            </li>
                            

                            <li class="nav-item dropdown ">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <span style="font-size: 16px;">Hi, {{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
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
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('partials.sidebar_z')
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="min-height: 0px">
            <div class="container-fluid">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <!--<p>-->
                            <!--    <a id="btn-add-tab" type="button" class="btn btn-primary pull-right" funurl="https://kilauindonesia.org/kilau/nyoba_d">Dashboard</a>-->
                            <!--    <a id="btn-add-tab" type="button" class="btn btn-primary pull-right" funurl="https://kilauindonesia.org/kilau/nyoba_d">Transaksi</a>-->
                            <!--    <a id="btn-add-tab" type="button" class="btn btn-primary pull-right" funurl="https://kilauindonesia.org/kilau/nyoba_d">Donatur</a>-->
                            <!--    <a id="btn-add-tab" type="button" class="btn btn-primary pull-right" funurl="https://kilauindonesia.org/kilau/nyoba_d">Karyawan</a>-->
                                
                            <!--</p>-->
                            <!--<ul class="nav nav-list" id="menuSideBar">-->
                            <!--    <li mid="tab1" funurl="https://kilauindonesia.org/kilau/nyoba_d"><a tabindex="-1" href="javascript:void(0);">jQueryScript</a></li>-->
                            <!--    <li mid="tab2" funurl="https://www.cssscript.com"><a tabindex="-1" href="javascript:void(0);">CSSScript</a></li>-->
                            <!--</ul>-->
                            
                            <div class="default-tab" id="mainFrameTabs">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item  active">
                                        <a href="#bTabs_dashboard" class="nav-link active" data-bs-toggle="tab" aria-expanded="false" role="tab">Dashboard</a>
                                    </li>
                                </ul>
                                
                                
                                <div class="tab-content" id="tab-content">
                                    <div id="bTabs_dashboard" class="tab-pane fade show active " role="tabpanel" style="height: 100%;">
                                        <!--<div class="pt-4">-->
                                            <iframe frameborder="0" scrolling="yes" style="width:100%;height:100%;border:0px;" src="{{ url('dashboard_tab') }}"></iframe>
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>

            </div>
        </div>
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
    <script src="{{asset('vendor/global/global.min.js')}}"></script>
    <script src="{{asset('vendor/jquery-nice-select/js/jquery.nice-select.min.js')}}"></script>

    <!-- Datatable -->
    <script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>

    <!-- DateRange -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <!-- toast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- select2 -->
    <script src="{{asset('vendor/select2/js/select2.full.min.js')}}"></script>

    <!-- toggle -->
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script src="{{asset('vendor/owl-carousel/owl.carousel.js')}}"></script>

    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/dlabnav-init.js')}}"></script>
    <script src="{{asset('js/demo.js')}}"></script>
    <script src="{{asset('js/styleSwitcher.js')}}"></script>

    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>

    @if(Request::segment(1) == 'dashboard')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.6/js/highstock.js"></script>
    @endif

    @if(Request::segment(1) == 'capaian-kolekting' || Request::segment(1) == 'kehadiran' || Request::segment(1) == 'capaian-omset')
     <script src="https://code.highcharts.com/stock/highstock.js"></script>
    @endif
    
    @if(Request::segment(1) == 'coa' || Request::segment(1) == 'program' || Request::segment(1) == 'trial-balance')
    <script src="{{ asset('js/dataTables.treeGrid.js')}}"></script>
    @endif
    
    @if(Request::segment(1) == 'analisis-transaksi' || Request::segment(1) == 'analisis-donatur')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    @endif
    
    @if(Request::segment(1) == 'laporan-karyawan')
    <script src="{{ asset('js/recorder.js')}}"></script>
    @endif
    
    @if(Request::segment(1) == 'program' || Request::segment(1) == 'coa' || Request::segment(1) == 'saldo-awal' )
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.20.2/dist/extensions/treegrid/bootstrap-table-treegrid.min.js"></script>
    @endif
    
    <script src="{{asset('js/b.tabs.js')}}"></script>
    
    <script>
        
        $(function(){
            var win_h = window.outerHeight;
            var wih = window.outerHeight;
            
        	var calcHeight = function(){
        		$('#mainFrameTabs').height(win_h - (wih/3));
        	};
        	$('a',$('#menu')).on('click', function(e) {
        		e.stopPropagation();
        		var li = $(this).closest('li');
        		var menuId = $(li).attr('mid');
        		var url = $(li).attr('funurl');
        		var title = $(this).text();
        		$('#mainFrameTabs').bTabsAdd(menuId,title,url);
        	});
        	
        	$('#mainFrameTabs').bTabs({
        		resize : calcHeight
        	});
        });
    </script>
    
    <script type="text/javascript">
    
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
        
        
        var keuangan = '<?= Auth::user()->keuangan ?>'
        var level = '<?= Auth::user()->level ?>'
        
        function myFunction(id) {
            $('#modalsy').modal('show');
            var body = '';
            var footer = '';
            
            $.ajax({
                    url: "changenotif/" + id,
                    method: "POST",
                    dataType: "json",
                    success: function(data) {
                        console.log(data)
                    }
            });
            
          $.ajax({
                url: "getPengTransBy/" + id,
                dataType: "json",
                success: function(response) {
                    // console.log(response)
                    var data = response
                    if(data.bukti != null){
                        var bukti = `<a href="https://kilauindonesia.org/datakilau/bukti/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                    }else{
                        var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    }
                    
                    if(data.approval == 0){
                        var tolak = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Note</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.note+`</text>
                                </div>
                            </div>`;
                    }else{
                        var tolak = ``;
                    }
                    
                    if(data.user_approve != null){
                           var con = `<div class="mb-3 row">
                                    <label class="col-sm-4 ">User Confirm</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                       <text>`+data.user_approve+`</text>
                                    </div>
                                </div>`
                        }else{
                            var con = ``;
                    }
                    
                    var number_string = data.jumlah.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    body = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.tanggal+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.user_insert+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Jenis Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.akun+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_hide">
                                        <text>`+rupiah+`</text>
                                   </div>
                                   <div style="display: none" id="input_hide">
                                        <input class="form-control" id="ednom" name="ednom" placeholder="`+data.nominal+`"/>
                                   </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_hide">
                                       <text>`+data.ket_penerimaan+`</text>
                                    </div>
                                    <div style="display: none" id="text_hide">
                                       <textarea id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Bukti</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+bukti+`</text>
                                </div>
                            </div>
                            ` + tolak + con;
                            
                    if(level == 'admin' || level == 'kacab' || keuangan == 'keuangan pusat'){
                        if (data.acc == 0) {
                            var footer = ``
                        } else if (data.acc == 1) {
                            var footer = `
                            <a href="javascript:void(0)" class="btn btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>`
                        } else if (data.acc == 2) {
                            var footer = `
                            <div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-warning editod" id="` + data.id + `" >Edit</a>
                                <button type="button" class="btn btn-success aksi" id="` + data.id + `" data="acc" type="submit">Approve</button>
                                <a href="javascript:void(0)" class="btn btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `
                        } else {
                            var footer = ``;
                        }
                    }else{
                        if(data.acc == 2){
                            var footer = `<div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-warning editod" id="` + data.id + `">Edit</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `   
                        }
                    }
                    
                    
                    $('#bodayy').html(body)
                    $('#footayy').html(footer)
                }
            })
        }
        
        function notif(){
                console.log('y')
                var uwuh = '';
                
                $.ajax({
                    url: "notifya",
                    method: "GET",
                    success: function(data) {
                        var datas = data.data;
                        // console.log(datas)
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
        
        
        $(document).ready(function() {
            
            var win_h = window.outerHeight; 
            
            console.warn(win_h)
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            notif();
            // window.setInterval('notif()', 1000);

            
        });
        
        
        
    </script>

    @extends('dashboard.js')
    @extends('core.js')
    @extends('transaksi.js')
    @extends('donatur.js')
    @extends('kolekting.js')
    @extends('karyawan.js')
    @extends('presensi.js')
    @extends('fins.js')
    @extends('setting.js')
    @extends('hcm.js')
    @extends('penerima-manfaat.js')
    @extends('sales.js')
    @extends('crm.js')
    @extends('jabatan.js')
    @extends('report-management.js')
    @extends('program.js')
    @extends('akuntasi.js')
    

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