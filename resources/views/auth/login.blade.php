

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Kilau Indonesia">
	<meta property="og:title" content="Kilau Indonesia">
	<meta property="og:description" content="Berbagi Teknologi">
	<meta property="og:image" content="https://kilauindonesia.org/kilau/upload/BT-LOGO.png">
	<meta name="format-detection" content="telephone=no">
	
	 <!--PAGE TITLE HERE -->
	<title>Admin</title>
	
	 <!--FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="{{asset('images/favicon.png')}}">
    <link href="{{asset('vendor/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									 <!--<div class="text-center mb-3">-->
										<!--<a href="index.html"><img src="images/logo-full.png" alt=""></a>-->
									<!--</div> -->
                                    <h4 class="text-center mb-4">Sign in your account</h4>
                                    <form id="login-form">
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Email</strong></label>
                                            <input type="email" class="form-control" placeholder="hello@example.com" name="username" id="email">
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Password</strong></label>
                                            <input type="password" class="form-control" placeholder="********" id="password" name="pass">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************-->
    <!--    Scripts-->
    <!--***********************************-->-->
    <!-- Required vendors -->
    <script src="{{asset('vendor/global/global.min.js')}}"></script>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.min.js"></script>
    <script src="{{asset('vendor/sweetalert2/dist/sweetalert2.min.js')}}"></script>
    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/dlabnav-init.js')}}"></script>
	<script src="{{asset('js/styleSwitcher.js')}}"></script>
    <script>
    $(document).ready(function() {
        
        
        // $.ajax({
                    
        //     url: "https://berbagipendidikan.org/sim/api/getuser",
        //     type: "GET",
        //     dataType: "JSON",
        //     success:function(response){
        //         console.log(response)        
        //     }
        // })
                
        

        // $(".btn-login").click( function() {
        $('#login-form').submit(function (event){
             event.preventDefault();

            var email = $("#email").val();
            var password = $("#password").val();
            var token = $("meta[name='csrf-token']").attr("content");

            if(email.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Alamat Email Wajib Diisi !'
                });

            } else if(password.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Password Wajib Diisi !'
                });

            } else {

                $.ajax({
                    
                    url: "https://kilauindonesia.org/api/login_sso",
                    type: "POST",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        "email": email,
                        "password": password,
                        "_token": token
                    },

                    success:function(response){
                        // console.log(response);
                        
                        if(response.error){
                            Swal.fire({
                                type: 'error',
                                title: 'Login Gagal!',
                                text: 'silahkan coba lagi!',
                                width: 400,
                                height: 200,
                            });
                        }

                        $.ajax({

                            url: "{{ url('masuk') }}",
                            type: "POST",
                            dataType: "JSON",
                            cache: false,
                            data: {
                                // "email": email,
                                // "password": password,
                                "email" : response.berhasil.email,
                                "token" : response.token,
                                "_token": token
                            },
        
                            success:function(response){
        
                                if (response.success == true) {

                                    Swal.fire({
                                        type: 'success',
                                        title: 'Login Berhasil!',
                                        text: 'Anda akan di arahkan dalam beberapa Detik',
                                        timer: 1500,
                                        width: 500,
                                        
                                        showCancelButton: false,
                                        showConfirmButton: false
                                    })
                                        .then (function() {
                                            window.location.href = response.url
                                        });
        
                                } else {
        
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Login Gagal!',
                                        text: response.message,
                                        width: 500,
                                        
                                    });
        
                                }
        
                            },
                        })

                    },

                    // error:function(response){

                    //     Swal.fire({
                    //         type: 'error',
                    //         title: 'Login Gagal!',
                    //         text: 'silahkan coba lagi!',
                    //         width: 400,
                    //         height: 200,
                    //     });

                    //     console.log(response);

                    // }

                });

            }

        });

    });
</script>
</body>
</html>