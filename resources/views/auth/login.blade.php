<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PanganCek</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background: linear-gradient(135deg, #f97316 0%, #2ECC71 100%); */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .login-left {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .logo {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo-subtitle {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .login-illustration {
            width: 100%;
            max-width: 280px;
            margin-top: 30px;
        }

        .login-right {
            padding: 60px 40px;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            color: #f97316;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-group input.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #f97316;
        }

        .form-check label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
        }

        .form-footer a {
            color: #f97316;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: #fb923c;
        }

        .divider {
            margin: 25px 0;
            text-align: center;
            position: relative;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #999;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .register-link {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .logo {
                font-size: 36px;
            }

            .login-illustration {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">PanganCek</div>
            <div class="logo-subtitle">Cek Kualitas Pangan dengan Mudah</div>
            <svg class="login-illustration" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="150" r="120" fill="rgba(255,255,255,0.2)" />
                <path d="M150 120 L200 80 L250 120 L230 180 L170 180 Z" fill="white" opacity="0.9" />
                <circle cx="200" cy="140" r="30" fill="rgba(46,204,113,0.3)" />
                <rect x="180" y="180" width="40" height="60" rx="5" fill="white" opacity="0.9" />
            </svg>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Selamat Datang!</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="nama@email.com">
                    @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="Masukkan password">
                    @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat Saya</label>
                </div>

                <button type="submit" class="btn-login">
                    Masuk
                </button>

                @if (Route::has('password.request'))
                    <div class="form-footer">
                        <a href="{{ route('password.request') }}">Lupa Password?</a>
                    </div>
                @endif

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="form-footer register-link">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
