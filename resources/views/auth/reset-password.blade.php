@extends('front-end.layouts.app')
@section('title', 'Reset Your Password')
@section('content')
<section class="user-login section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="block">
                    <!-- Image -->
                    <div class="image align-self-center"><img class="img-fluid" src="{{ asset('newAdmin/images/pa.png') }}"
                            alt="desk-image"></div>
                    <!-- Content -->
                    <div class="content text-center">
                        <div class="logo">
                            <a href="{{ route('home') }}"><img src="{{ asset('newAdmin/images/pa.png') }}" alt="" height="40px" width="150px"></a>
                        </div>
                        <div class="title-text">
                            <h3>Reset Password</h3>
                        </div>
                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="email" name="email" value="{{ old('email',$request->email) }}" required autofocus autocomplete="username" class="form-control main mb-0" id="email" placeholder="Email Address">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                            <input type="password" name="password" class="form-control main mt-4 mb-0" id="password" placeholder="Password" required autocomplete="current-password">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                            <input type="password" name="password_confirmation" class="form-control main mt-4 mb-0" id="confirm_password" placeholder="Confirm Password" required autocomplete="current-password">
                                @if ($errors->has('password_confirmation'))
                                    <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                @endif

                            <!-- Submit Button -->
                            <div>   
                             <button class="btn btn-main-sm mt-4">Reset Password</button>
                            </div>
                        </form>
                        {{-- <div class="new-acount">
                            <a href="{{ route('password.request') }}">Forget your password?</a>
                            <p>Don't Have an account? <a href="{{ route('signup') }}"> SIGN UP</a></p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

