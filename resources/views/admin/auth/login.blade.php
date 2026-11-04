@extends('layouts.auth.admin_auth')

@section('content')
<div class="wm-admin">
    <div class="wm-admin__grid"></div>
    <div class="wm-admin__orb wm-admin__orb--1"></div>
    <div class="wm-admin__orb wm-admin__orb--2"></div>
    <div class="wm-admin__scan"></div>

    <div class="wm-admin__shell">
        <div class="wm-admin__top">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery">
            <span class="wm-admin__badge">Admin Console</span>
        </div>

        <h1 class="wm-admin__title">System Access</h1>
        <p class="wm-admin__sub">Authenticate to enter the WOOD Machinery control center.</p>

        @if($errors->any())
            <div class="wm-alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST" class="auth-login-form">
            @csrf
            <div class="wm-field form-group">
                <label for="email">Admin email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    tabindex="1"
                >
            </div>

            <div class="wm-field form-group">
                <label for="password">Password</label>
                <div class="input-group input-group-merge form-password-toggle">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control form-control-merge @error('password') is-invalid @enderror"
                        placeholder="Enter secure password"
                        required
                        tabindex="2"
                    >
                    <div class="input-group-append">
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="wm-admin__btn" tabindex="3">Enter Console</button>
        </form>

        <div class="wm-admin__status">
            <span><i></i> Secure channel</span>
            <span>Ops · Stock · Finance</span>
        </div>
        <div class="wm-admin__foot">&copy; {{ date('Y') }} WOOD Machinery</div>
    </div>
</div>

<script>
    document.body.classList.add('wm-auth-admin');
</script>
@endsection
