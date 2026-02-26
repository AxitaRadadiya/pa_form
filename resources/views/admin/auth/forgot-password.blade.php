@extends('admin.layouts.app')
@section('title', 'Forgot Password')
@section('content')
<section class="user-login section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="block">
                    <!-- Image -->
                    <div class="image align-self-center"><img class="img-fluid" src=""
                            alt="desk-image"></div>
                    <!-- Content -->
                    <div class="content text-center">
                        <div class="logo">
                            <a href="{{ route('login') }}"><img src="" alt="" height="40px" width="150px"></a>
                        </div>
                        <div class="title-text">
                            <h3>Forgot Password</h3>
                        </div>
                         @if (session('status'))
                            <div class="alert alert-success mt-3" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control main mb-0" id="email" placeholder="Email Address">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif

                            <!-- Submit Button -->
                            <div>   
                             <button class="btn btn-main-sm mt-4">Reset Link</button>
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