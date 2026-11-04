@extends('layouts.auth.seller_auth')
@section('content')
    <div class="auth-wrapper auth-v2">
        <div class="auth-inner row m-0">
            <a class="brand-logo" href="javascript:void(0);">
                <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" height="28">
                <h2 class="brand-text text-primary ml-1">My Panel</h2>
            </a>
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <img class="img-fluid" src="{{ asset('admin/app-assets/images/pages/login-v2.svg') }}" alt="Login V2" />
                </div>
            </div>
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title font-weight-bold mb-1">Welcome to Worker Panel! 👋</h2>
                    <p class="card-text mb-2">Sign in with your phone number and password.</p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="auth-login-form mt-2" action="{{ route('mypanel.login.submit') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input class="form-control @error('phone') is-invalid @enderror" id="phone" type="text" name="phone" placeholder="Enter your phone number" autofocus tabindex="1" value="{{ old('phone') }}" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input class="form-control form-control-merge @error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="············" tabindex="2" />
                                <div class="input-group-append"><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="remember-me" type="checkbox" name="remember" tabindex="3" />
                                <label class="custom-control-label" for="remember-me"> Remember Me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" tabindex="4">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
