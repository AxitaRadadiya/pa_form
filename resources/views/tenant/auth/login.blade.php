<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Innoveza CRM" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('newAdmin/images/innoveza-n.png') }}">

    <!-- App css -->
    {{-- <link href="{{ asset('newAdmin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('newAdmin/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('newAdmin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('newAdmin/css/theme.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('newAdmin/css/theme.css') }}" rel="stylesheet" type="text/css" />


</head>

<body>
 
    <div class="bg-primary" style="background: linear-gradient(to right, #1F5051 0%, #33999A 13%, #319293 92%, #1F5051 97%);">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center min-vh-100">
                        <div class="w-100 d-block bg-white shadow-lg rounded my-5">
                            <div class="row">
                                <div class="col-lg-5 d-none d-lg-block bg-login rounded-left" style="background: url('{{ asset('newAdmin/images/innoveza-n.png') }}') no-repeat center center; background-size: contain;" ></div>
                                <div class="col-lg-7">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <a href="{{ route('login') }}" class="d-block mb-5">
                                                <img src="{{ asset('newAdmin/images/innoveza-name.png') }}" alt="app-logo" height="18" />
                                            </a>
                                        </div>
                                        <h1 class="h5 mb-1">Welcome Back!</h1>
                                        <form method="POST" action="{{ route('newlogin') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-user" id="email" placeholder="Email Address">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control form-control-user" id="password" placeholder="Password" required autocomplete="current-password">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Log In</button>

                                        </form>

                                        <div class="row mt-4">
                                            <div class="col-12 text-center">
                                                @if (Route::has('password.request'))
                                                    <p class="text-muted mb-2">
                                                        <a href="{{ route('password.request') }}" class="text-muted font-weight-medium ml-1">Forgot your password?</a>
                                                    </p>
                                                @endif
                                                {{-- <p class="text-muted mb-0">Don't have an account? <a href="auth-register.html" class="text-muted font-weight-medium ml-1"><b>Sign Up</b></a></p> --}}
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
    <!-- end page -->

    <!-- jQuery  -->
    <script src="{{ asset('newAdmin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/waves.js') }}"></script>
    <script src="{{ asset('newAdmin/js/simplebar.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('newAdmin/js/theme.js') }}"></script>

</body>

</html>
