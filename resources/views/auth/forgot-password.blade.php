<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PA - Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="PA" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('newAdmin/images/pa.png') }}">

    <!-- App css -->
    <link href="{{ asset('newAdmin/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('newAdmin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('newAdmin/css/theme.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>
    @if (session('error') || session('success') || session('status'))
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px;">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>
    @endif

    <div class="bg-primary" style="background: linear-gradient(to right, #251C4B 0%, #251C4B 13%, #251C4B 92%, #251C4B 97%); min-height:100vh;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center min-vh-100">
                        <div class="w-100 d-block bg-white shadow-lg rounded my-5">
                            <div class="row">
                                <div class="col-lg-5 d-none d-lg-block bg-login rounded-left" style="background: url('{{ asset('newAdmin/images/pa.png') }}') no-repeat center center; background-size: contain;" ></div>
                                <div class="col-lg-7">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <a href="{{ route('login') }}" class="d-block mb-5">
                                                <img src="{{ asset('newAdmin/images/pa.png') }}" alt="app-logo" height="18" />
                                            </a>
                                        </div>
                                        <h1 class="h5 mb-1">Forgot Password</h1>
                                        <form method="POST" action="{{ route('password.email') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-user" id="email" placeholder="Email Address">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>

                                            <button type="submit" class="btn btn-save">Reset Link</button>

                                        </form>

                                        <div class="row mt-4">
                                            <div class="col-12 text-center">
                                                <p class="text-muted mb-2">
                                                    <a href="{{ route('login') }}" class="text-muted font-weight-medium ml-1">Back to login</a>
                                                </p>
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

    <!-- jQuery  -->
    <script src="{{ asset('newAdmin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('newAdmin/js/waves.js') }}"></script>
    <script src="{{ asset('newAdmin/js/simplebar.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('newAdmin/js/theme.js') }}"></script>
    <script src="{{ asset('newAdmin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    setTimeout(function() {
        let alertEl = document.querySelector('.alert');
        if (alertEl) {
            alertEl.classList.remove('show');
            alertEl.classList.add('fade');
            setTimeout(() => alertEl.remove(), 300);
        }
    }, 4000); // auto close after 4 seconds
</script>

</body>

</html>
