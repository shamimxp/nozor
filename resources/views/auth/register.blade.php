@extends('frontend.layouts.app')
@section('content')
<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ url('/') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
            <span></span> Pages <span></span> Register
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
                    <p class="wm-auth-visual__kicker">New customer</p>
                    <h2>Join Bangladesh’s wood machinery marketplace</h2>
                    <p class="wm-auth-visual__lead">
                        Create an account to order CNC routers, saws, edge banders, and workshop equipment with reliable delivery support.
                    </p>
                    <ul class="wm-auth-visual__points">
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/></svg>
                            </span>
                            <span>Verified product catalogue</span>
                        </li>
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18M5 7l1.5 12h11L19 7M9 7V5a3 3 0 016 0v2"/></svg>
                            </span>
                            <span>Save carts &amp; reorder faster</span>
                        </li>
                        <li>
                            <span class="wm-auth-visual__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92V19a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 013 3.18 2 2 0 015 1h2.09a2 2 0 012 1.72c.13.98.36 1.94.7 2.86a2 2 0 01-.45 2.11L8.09 8.91a16 16 0 006 6l1.22-1.22a2 2 0 012.11-.45c.92.34 1.88.57 2.86.7A2 2 0 0122 16.92z"/></svg>
                            </span>
                            <span>Phone-first account access</span>
                        </li>
                    </ul>
                </div>
            </aside>

            <div class="wm-auth-form">
                <div class="wm-auth-form__inner">
                    <p class="wm-auth-form__chip">Get started</p>
                    <h1 class="wm-auth-form__title">Create an Account</h1>
                    <p class="wm-auth-form__sub">
                        Already have an account?
                        <a href="{{ route('login') }}">Login</a>
                    </p>

                    <form method="post" action="{{ route('register') }}" class="wm-auth-form__fields">
                        @csrf
                        <div class="wm-auth-field">
                            <label for="register-name">Full name</label>
                            <input
                                id="register-name"
                                type="text"
                                required
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Full name *"
                                autocomplete="name"
                                autofocus
                            >
                        </div>

                        <div class="wm-auth-field">
                            <label for="register-phone">Phone number</label>
                            <input
                                id="register-phone"
                                type="text"
                                required
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Phone number *"
                                autocomplete="tel"
                            >
                            @error('phone')
                                <span class="wm-auth-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="wm-auth-form__row">
                            <label class="wm-auth-check" for="exampleCheckbox12">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox12" value="1" required>
                                <span>I agree to terms &amp; Policy.</span>
                            </label>
                        </div>

                        <button type="submit" class="wm-auth-submit" name="login">Submit &amp; Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
