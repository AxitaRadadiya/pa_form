<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="PA" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('newAdmin/images/pa.png') }}">

    <!-- App css -->
    {{-- <link href="{{ asset('newAdmin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('newAdmin/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('newAdmin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('newAdmin/css/theme.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('newAdmin/css/theme.css') }}" rel="stylesheet" type="text/css" />


</head>

<body>
    @if (session('error') || session('success'))
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
    </div>
@endif
 
    <div class="bg-primary" style="background: linear-gradient(to right, #251C4B 0%, #251C4B 13%, #251C4B 92%, #251C4B 97%);">
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
                                        <h1 class="h5 mb-1">Welcome Back!</h1>
                                        <form id="loginForm" method="POST" action="#">
                                            @csrf
                                            <input type="hidden" id="finalAction" name="_finalAction" value="">
                                            <div id="step-email" class="form-group">
                                                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-user" id="email" placeholder="Email Address">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>

                                            <div id="step-password" class="form-group" style="display:none;">
                                                <input type="password" name="password" class="form-control form-control-user" id="password" placeholder="Password" autocomplete="current-password">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>

                                            <div id="step-mobile" class="form-group" style="display:none;">
                                                <input type="text" name="mobile" class="form-control form-control-user" id="mobile" placeholder="Mobile Number">
                                                @if ($errors->has('mobile'))
                                                    <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                                @endif
                                            </div>

                                            <button type="submit" id="primaryBtn" class="btn btn-save">Next</button>
                                            <a href="{{ route('login') }}" id="cancelBtn" class="btn btn-link" style="display:none;">Cancel</a>
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
    <script src="{{ asset('newAdmin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

 {{--  --}}

<script>
    (function(){
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordGroup = document.getElementById('step-password');
        const mobileGroup = document.getElementById('step-mobile');
        const primaryBtn = document.getElementById('primaryBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        let currentStage = 'email';
        let resolvedRole = null;

        function showStage(stage){
            currentStage = stage;
            if(stage === 'email'){
                document.getElementById('step-email').style.display = '';
                passwordGroup.style.display = 'none';
                mobileGroup.style.display = 'none';
                primaryBtn.textContent = 'Next';
                cancelBtn.style.display = 'none';
            } else if(stage === 'password'){
                document.getElementById('step-email').style.display = '';
                passwordGroup.style.display = '';
                mobileGroup.style.display = 'none';
                primaryBtn.textContent = 'Log In';
                cancelBtn.style.display = '';
            } else if(stage === 'mobile'){
                document.getElementById('step-email').style.display = '';
                passwordGroup.style.display = 'none';
                mobileGroup.style.display = '';
                primaryBtn.textContent = 'Log In';
                cancelBtn.style.display = '';
            }
        }

        async function checkRole(email){
            const resp = await fetch('{{ url('/check-role') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email })
            });
            if(!resp.ok) throw new Error('Network error');
            return resp.json();
        }

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            if(currentStage === 'email'){
                const email = emailInput.value.trim();
                if(!email) return;
                primaryBtn.disabled = true;
                primaryBtn.textContent = 'Checking...';
                try{
                    const data = await checkRole(email);
                    resolvedRole = data.role || 'user';
                    if(resolvedRole === 'admin'){
                        showStage('password');
                    } else {
                        showStage('mobile');
                    }
                }catch(err){
                    alert('Unable to check role. Please try again.');
                }finally{
                    primaryBtn.disabled = false;
                }
            } else if(currentStage === 'password'){
                // submit to normal login route with password
                form.action = '{{ route('login') }}';
                // remove our placeholder hidden field
                document.getElementById('finalAction').name = '';
                form.submit();
            } else if(currentStage === 'mobile'){
                // submit to user-login route
                form.action = '{{ url('/user-login') }}';
                document.getElementById('finalAction').name = '';
                form.submit();
            }
        });

        cancelBtn.addEventListener('click', function(e){
            e.preventDefault();
            showStage('email');
        });

        // initialize
        showStage('email');
    })();

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
