<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIS — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0d1117;
            overflow: hidden;
        }

        /* ── Left Panel ── */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #0a2540 0%, #0e3460 50%, #1a5276 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            top: -150px; right: -150px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            bottom: -100px; left: -80px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 56px;
            position: relative; z-index: 1;
        }

        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff;
            box-shadow: 0 8px 24px rgba(46,204,113,0.35);
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .hero-title {
            font-size: 46px;
            font-weight: 700;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 20px;
            position: relative; z-index: 1;
        }
        .hero-title span {
            background: linear-gradient(90deg, #2ecc71, #1abc9c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 15px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 48px;
            position: relative; z-index: 1;
        }

        .stats {
            display: flex;
            gap: 36px;
            position: relative; z-index: 1;
        }
        .stat-item { }
        .stat-num {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
        }
        .stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Floating Circles decoration */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.07);
        }
        .deco-circle.c1 { width:240px;height:240px; top:12%;right:8%; }
        .deco-circle.c2 { width:160px;height:160px; top:18%;right:14%; }
        .deco-circle.c3 { width:80px;height:80px;  top:26%;right:20%; }

        /* ── Right Panel ── */
        .right-panel {
            width: 480px;
            background: #0d1117;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 52px;
            position: relative;
        }

        .login-box {
            width: 100%;
        }

        .login-heading {
            font-size: 26px;
            font-weight: 700;
            color: #f0f6fc;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .login-subheading {
            font-size: 14px;
            color: #8b949e;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #c9d1d9;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b949e;
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 10px;
            color: #f0f6fc;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input::placeholder { color: #484f58; }
        .form-input:focus {
            border-color: #2ecc71;
            box-shadow: 0 0 0 3px rgba(46,204,113,0.12);
        }
        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: #2ecc71; }

        /* password toggle */
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%; transform: translateY(-50%);
            background: none; border: none;
            color: #8b949e; cursor: pointer;
            font-size: 14px;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-pw:hover { color: #c9d1d9; }

        .form-input.is-invalid { border-color: #f85149; }
        .invalid-feedback {
            color: #f85149;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #2ecc71;
            cursor: pointer;
        }
        .remember-label span {
            font-size: 13px;
            color: #8b949e;
        }
        .forgot-link {
            font-size: 13px;
            color: #58a6ff;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #79c0ff; text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(46,204,113,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(46,204,113,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0;
        }
        .divider-line {
            flex: 1;
            height: 1px;
            background: #21262d;
        }
        .divider-text {
            font-size: 12px;
            color: #484f58;
        }

        .alert-error {
            background: rgba(248,81,73,0.1);
            border: 1px solid rgba(248,81,73,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            color: #f85149;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-note {
            text-align: center;
            margin-top: 32px;
            font-size: 12px;
            color: #484f58;
        }
        .footer-note a {
            color: #2ecc71;
            text-decoration: none;
        }
        .footer-note a:hover { text-decoration: underline; }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 36px 28px; }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="deco-circle c1"></div>
        <div class="deco-circle c2"></div>
        <div class="deco-circle c3"></div>

        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-hospital"></i></div>
            <div>
                <div class="brand-name">HMIS</div>
                <div class="brand-sub">Health Management Information System</div>
            </div>
        </div>

        <h1 class="hero-title">
            Smarter Healthcare<br>
            <span>Management</span>
        </h1>

        <p class="hero-desc">
            A unified platform for managing patient records, clinical reports,
            appointments, and hospital operations — all in one place.
        </p>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-num">100%</div>
                <div class="stat-label">Secure</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">24/7</div>
                <div class="stat-label">Access</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">Fast</div>
                <div class="stat-label">Reports</div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <div class="login-box">
            <h2 class="login-heading">Welcome back</h2>
            <p class="login-subheading">Sign in to your HMIS account</p>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrap">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="doctor@hospital.com"
                            required autocomplete="email" autofocus
                        >
                        <i class="fa-solid fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <div class="invalid-feedback"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required autocomplete="current-password"
                        >
                        <i class="fa-solid fa-lock input-icon"></i>
                        <button type="button" class="toggle-pw" onclick="togglePassword()" id="pwToggle">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                </button>
            </form>

            <div class="footer-note">
                &copy; {{ date('Y') }} HMIS &mdash; Health Management Information System
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
