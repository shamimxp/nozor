@extends('frontend.layouts.app')
@section('content')
<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ url('/') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
            <span></span> Pages <span></span> Login
        </div>
    </div>
</div>

<section class="wm-auth-page">
    <div class="container">
        <div class="wm-auth-shell">
            <aside class="wm-auth-visual" aria-hidden="false">
                <div class="wm-auth-visual__media" style="background-image: url('{{ asset('web/assets/imgs/banner/wm-bestsells-banner.jpg') }}');"></div>
                <div class="wm-auth-visual__shade"></div>
                <div class="wm-auth-visual__grid"></div>

                <div class="wm-auth-visual__content">
                    <img
                        src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}"
                        alt="WOOD Machinery"
                        class="wm-auth-visual__logo"
                    >
                    <p class="wm-auth-visual__kicker">Customer account</p>
                    <h2>Industrial tools for serious workshops</h2>
                    <p class="wm-auth-visual__lead">
                        Sign in to track orders, manage deliveries, and shop woodworking machinery built for production floors.
                    </p>
                    <ul class="wm-auth-visual__points">
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5M4 19h16M8 16V10M12 16V7M16 16v-4"/></svg>
                            </span>
                            <span>Order history &amp; status</span>
                        </li>
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/></svg>
                            </span>
                            <span>Secure phone-based access</span>
                        </li>
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M3 7h11l3 5h2M5 7v8M14 7v5"/></svg>
                            </span>
                            <span>Faster checkout next visit</span>
                        </li>
                    </ul>
                </div>
            </aside>

            <div class="wm-auth-form">
                <div class="wm-auth-form__inner">
                    <p class="wm-auth-form__chip">Welcome back</p>
                    <h1 class="wm-auth-form__title">Login</h1>
                    <p class="wm-auth-form__sub">
                        Don’t have an account?
                        <a href="{{ route('register') }}">Create here</a>
                    </p>

                    <form method="post" action="{{ route('login') }}" class="wm-auth-form__fields">
                        @csrf
                        <div class="wm-auth-field">
                            <label for="login-phone">Phone number</label>
                            <input
                                id="login-phone"
                                type="text"
                                required
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Your phone number *"
                                autocomplete="tel"
                                autofocus
                            >
                            @error('phone')
                                <span class="wm-auth-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="wm-auth-form__row">
                            <label class="wm-auth-check" for="exampleCheckbox1">
                                <input class="form-check-input" type="checkbox" name="remember" id="exampleCheckbox1" value="1">
                                <span>Remember me</span>
                            </label>
                        </div>

                        <button type="submit" class="wm-auth-submit" name="login">Log in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
