@extends('layouts.auth.seller_auth')
@section('page-title', 'Worker Login')

@section('content')
<div class="wm-worker">
    <span class="wm-worker__spark"></span>
    <span class="wm-worker__spark"></span>
    <span class="wm-worker__spark"></span>
    <span class="wm-worker__spark"></span>
    <span class="wm-worker__spark"></span>

    <header class="wm-worker__topbar">
        <a class="wm-worker__topbar-logo" href="javascript:void(0);">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery">
        </a>
        <div class="wm-worker__topbar-tag">My Panel · Shop Floor</div>
    </header>

    <section class="wm-worker__hero">
        <svg class="wm-worker__gear" viewBox="0 0 100 100" fill="none" aria-hidden="true">
            <path fill="#f0a202" d="M43.5 8.2l2.4 10.8 9.8-5.2 5.2 9.8 10.8-2.4 2.4 10.8 10.8 2.4-2.4 10.8 9.8 5.2-5.2 9.8 5.2 9.8-9.8 5.2 2.4 10.8-10.8 2.4-2.4 10.8-10.8-2.4-5.2 9.8-9.8-5.2-2.4 10.8-10.8-2.4 2.4-10.8-9.8-5.2 5.2-9.8-5.2-9.8 9.8-5.2-2.4-10.8 10.8-2.4 2.4-10.8 10.8 2.4 5.2-9.8 9.8 5.2zM50 35a15 15 0 100 30 15 15 0 000-30z"/>
        </svg>
        <svg class="wm-worker__gear wm-worker__gear--sm" viewBox="0 0 100 100" fill="none" aria-hidden="true">
            <path fill="#f0a202" d="M43.5 8.2l2.4 10.8 9.8-5.2 5.2 9.8 10.8-2.4 2.4 10.8 10.8 2.4-2.4 10.8 9.8 5.2-5.2 9.8 5.2 9.8-9.8 5.2 2.4 10.8-10.8 2.4-2.4 10.8-10.8-2.4-5.2 9.8-9.8-5.2-2.4 10.8-10.8-2.4 2.4-10.8-9.8-5.2 5.2-9.8-5.2-9.8 9.8-5.2-2.4-10.8 10.8-2.4 2.4-10.8 10.8 2.4 5.2-9.8 9.8 5.2zM50 35a15 15 0 100 30 15 15 0 000-30z"/>
        </svg>

        <a class="wm-worker__logo" href="javascript:void(0);">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery">
        </a>
        <div class="wm-worker__tag">My Panel · Shop Floor</div>
        <h1>Your <em>manufacture</em> day starts here</h1>
        <p>Check assigned orders, invoices and payouts — built for workers on the floor.</p>

        <ul class="wm-worker__points">
            <li>Assigned manufacture orders</li>
            <li>Invoice &amp; payment history</li>
            <li>Profile &amp; password update</li>
        </ul>
    </section>

    <section class="wm-worker__panel">
        <div class="wm-worker__panel-inner">
            <div class="wm-worker__panel-head">
                <h2>Worker sign in</h2>
                <span>Phone + password</span>
            </div>

            @if($errors->any())
                <div class="wm-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="auth-login-form" action="{{ route('mypanel.login.submit') }}" method="POST">
                @csrf
                <div class="wm-field">
                    <label for="phone">Phone number</label>
                    <input
                        class="wm-input @error('phone') is-invalid @enderror"
                        id="phone"
                        type="text"
                        name="phone"
                        placeholder="Enter your phone number"
                        autofocus
                        tabindex="1"
                        value="{{ old('phone') }}"
                    >
                </div>

                <div class="wm-field">
                    <label for="password">Password</label>
                    <div class="wm-pass">
                        <input
                            class="wm-input @error('password') is-invalid @enderror"
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            tabindex="2"
                        >
                        <button type="button" class="wm-pass__toggle" data-toggle-pass="#password" aria-label="Show password">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                </div>

                <div class="wm-worker__meta">
                    <label class="wm-check wm-check--light">
                        <input type="checkbox" name="remember" tabindex="3">
                        <span>Remember Me</span>
                    </label>
                </div>

                <button type="submit" class="wm-worker__btn" tabindex="4">Open My Panel</button>
            </form>

            <div class="wm-worker__note">&copy; {{ date('Y') }} WOOD Machinery — Worker access</div>
        </div>
    </section>
</div>

<script>
    document.body.classList.add('wm-auth-worker');
    document.querySelectorAll('[data-toggle-pass]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.querySelector(btn.getAttribute('data-toggle-pass'));
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            if (window.feather) {
                btn.innerHTML = show ? '<i data-feather="eye-off"></i>' : '<i data-feather="eye"></i>';
                feather.replace();
            }
        });
    });
</script>
@endsection
