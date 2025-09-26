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
        
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        
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
        
        $(document).ready(function() {
            
             
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        
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

</body>

</html>