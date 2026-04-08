@extends('layouts.auth.admin_auth')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    :root {
        --primary-blue: #0050ff;
        --secondary-blue: #003dbb;
        --accent-orange: #ff9d00;
        --accent-yellow: #ffc107;
        --text-white: #ffffff;
        --input-bg: #ffffff;
        --shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    /* Override parent layouts if necessary */
    .app-content, .content-wrapper, .content-body {
        padding: 0 !important;
        margin: 0 !important;
        background: #f4f7f6 !important;
    }

    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
        background: #eef2f3;
    }

    .auth-container {
        display: flex;
        width: 1000px;
        max-width: 95%;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow);
        min-height: 600px;
    }

    .auth-left {
        flex: 1.2;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #fff;
    }

    .auth-left img {
        width: 100%;
        max-width: 450px;
        height: auto;
    }

    .auth-right {
        flex: 1;
        background: var(--primary-blue);
        padding: 60px 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: var(--text-white);
        position: relative;
    }

    .auth-right::after {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        z-index: 0;
    }

    .auth-right-content {
        position: relative;
        z-index: 1;
    }

    .auth-right h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 50px;
        color: var(--text-white) !important; /* Force white */
    }

    .form-group {
        position: relative;
        margin-bottom: 25px;
    }

    .form-group svg {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        z-index: 10;
        width: 20px;
        height: 20px;
    }

    .form-control {
        width: 100%;
        padding: 18px 20px 18px 55px !important;
        border-radius: 50px !important;
        border: none !important;
        background: var(--input-bg) !important;
        font-size: 1rem !important;
        color: #333 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
    }

    .form-control::placeholder {
        color: #aaa;
    }

    .form-control:focus {
        outline: none !important;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.4) !important;
    }

    .password-strength-container {
        margin-top: -10px;
        margin-bottom: 40px;
    }

    .password-strength-text {
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: block;
        opacity: 0.9;
    }

    .strength-bar {
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .strength-bar-fill {
        width: 75%;
        height: 100%;
        background: var(--accent-yellow);
        border-radius: 10px;
    }

    .auth-buttons {
        display: flex;
        gap: 20px;
        margin-top: 30px;
    }

    .btn-create {
        flex: 1.4;
        background: linear-gradient(90deg, #ff8c00, #ffb300);
        border: none;
        padding: 15px 20px;
        border-radius: 50px;
        color: #fff !important;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-signin {
        flex: 1;
        background: transparent;
        border: 2px solid var(--text-white);
        padding: 15px 20px;
        border-radius: 50px;
        color: var(--text-white) !important;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    .btn-signin:hover {
        background: var(--text-white);
        color: var(--primary-blue) !important;
    }

    .alert {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 0.9rem;
    }

    @media (max-width: 992px) {
        .auth-container {
            width: 500px;
        }
        .auth-left {
            display: none;
        }
        .auth-right {
            border-radius: 24px;
        }
    }

    @media (max-width: 576px) {
        .auth-container {
            width: 100%;
            height: 100vh;
            border-radius: 0;
            box-shadow: none;
        }
        .auth-right {
            border-radius: 0;
            padding: 40px 25px;
        }
        .auth-right h1 {
            font-size: 2.5rem;
        }
        .auth-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-left">
        <img src="{{asset('admin')}}/app-assets/images/pages/1.avif" alt="Illustration">
    </div>
    <div class="auth-right">
        <div class="auth-right-content">
            <h1>Welcome!</h1>
            
            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <i data-feather="user"></i>
                    <input type="email" name="email" class="form-control" placeholder="Your e-mail" value="{{ old('email') }}" required autofocus>
                </div>
                
                <div class="form-group">
                    <i data-feather="lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Your password" required>
                </div>

                <!-- <div class="password-strength-container">
                    <span class="password-strength-text">Password strength</span>
                    <div class="strength-bar">
                        <div class="strength-bar-fill"></div>
                    </div>
                </div> -->

                <div class="auth-buttons">
                    <!-- <a href="{{ route('admin.register') }}" class="btn-create">Create account</a> -->
                    <button type="submit" class="btn-signin">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection

