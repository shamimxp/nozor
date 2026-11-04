@extends('layouts.auth.seller_auth')
@section('page-title', 'Dealer Login')

@section('content')
<div class="wm-dealer">
    <aside class="wm-dealer__visual">
        <div class="wm-dealer__glow wm-dealer__glow--1"></div>
        <div class="wm-dealer__glow wm-dealer__glow--2"></div>
        <div class="wm-dealer__grid" aria-hidden="true"></div>

        <div class="wm-dealer__visual-top">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery" class="wm-dealer__brand-mark">
            <span class="wm-dealer__live"><i></i> Business portal online</span>
        </div>

        <div class="wm-dealer__visual-copy">
            <p class="wm-dealer__visual-kicker">Dealer Business Portal</p>
            <h1>Run your dealership with clarity</h1>
            <p>One place for order requests, wallet credit and payment history — built for WOOD Machinery partners.</p>

            <div class="wm-dealer__features">
                <div class="wm-dealer__feature">
                    <span class="wm-dealer__feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18M5 7l1.5 12h11L19 7M9 7V5a3 3 0 016 0v2"/></svg>
                    </span>
                    <div>
                        <strong>Order pipeline</strong>
                        <span>Request stock &amp; track fulfillment</span>
                    </div>
                </div>
                <div class="wm-dealer__feature">
                    <span class="wm-dealer__feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg>
                    </span>
                    <div>
                        <strong>Wallet credit</strong>
                        <span>Balance, top-ups &amp; ledger</span>
                    </div>
                </div>
                <div class="wm-dealer__feature">
                    <span class="wm-dealer__feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5M4 19h16M8 16V10M12 16V7M16 16v-4"/></svg>
                    </span>
                    <div>
                        <strong>Payment history</strong>
                        <span>Transparent dealer settlements</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="wm-dealer__chart" aria-hidden="true">
            <span style="--h:42%"></span>
            <span style="--h:68%"></span>
            <span style="--h:54%"></span>
            <span style="--h:82%"></span>
            <span style="--h:70%"></span>
            <span style="--h:92%"></span>
        </div>
    </aside>

    <section class="wm-dealer__form-side">
        <div class="wm-dealer__form-wrap">
            <div class="wm-dealer__mobile-brand">
                <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery">
            </div>

            <div class="wm-dealer__chip">Secure dealer access</div>
            <h1 class="wm-dealer__title">Sign in to your business account</h1>
            <p class="wm-dealer__sub">Use your registered phone number and password to open the dealer panel.</p>

            @if($errors->any())
                <div class="wm-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="auth-login-form" action="{{ route('dealer.login.submit') }}" method="POST">
                @csrf
                <div class="wm-field">
                    <label for="phone">Business phone</label>
                    <input
                        class="wm-input @error('phone') is-invalid @enderror"
                        id="phone"
                        type="text"
                        name="phone"
                        placeholder="Enter registered phone number"
                        autofocus
                        tabindex="1"
                        value="{{ old('phone') }}"
                        autocomplete="username"
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
                            autocomplete="current-password"
                        >
                        <button type="button" class="wm-pass__toggle" data-toggle-pass="#password" aria-label="Show password">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                </div>

                <div class="wm-dealer__meta">
                    <label class="wm-check">
                        <input type="checkbox" name="remember" tabindex="3">
                        <span>Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" class="wm-dealer__btn" tabindex="4">
                    <span>Access Dealer Panel</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <div class="wm-dealer__foot">
                <span>&copy; {{ date('Y') }} WOOD Machinery</span>
                <span>Dealer Network</span>
            </div>
        </div>
    </section>
</div>

<script>
    document.body.classList.add('wm-auth-dealer');
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
