<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('1.svg') }}" type="image/svg+xml">
    <title>Daftar - PanganCek</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(1200px 1200px at -10% -10%, rgba(255, 122, 89, .18), transparent 50%),
                radial-gradient(900px 900px at 110% 0%, rgba(95, 124, 255, .20), transparent 55%),
                radial-gradient(900px 900px at 120% 120%, rgba(46, 213, 115, .15), transparent 55%),
                var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .register-left {
            background: linear-gradient(135deg, #2ECC71 0%, #27ae60 100%);
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

        .register-illustration {
            width: 100%;
            max-width: 280px;
            margin-top: 30px;
        }

        .register-right {
            padding: 60px 40px;
        }

        .register-header {
            margin-bottom: 35px;
        }

        .register-header h2 {
            color: #2ECC71;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
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
            border-color: #2ECC71;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.1);
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

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2ECC71 0%, #27ae60 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 25px;
            text-align: center;
        }

        .form-footer a {
            color: #2ECC71;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: #27ae60;
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

        .login-link {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .register-container {
                grid-template-columns: 1fr;
            }

            .register-left {
                padding: 40px 30px;
            }

            .register-right {
                padding: 40px 30px;
            }

            .logo {
                font-size: 36px;
            }

            .register-illustration {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-left">
            <img src="{{ asset('2.svg') }}" alt="">
        </div>

        <div class="register-right">
            <div class="register-header">
                <h2>Daftar Akun Baru</h2>
                <p>Isi form di bawah untuk membuat akun</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" type="text" class="@error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" required autocomplete="name" autofocus
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com">
                    @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Konfirmasi Password</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required
                        autocomplete="new-password" placeholder="Ulangi password">
                </div>

                <button type="submit" class="btn-register">
                    Daftar
                </button>

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="form-footer login-link">
                    Sudah punya akun? <a href="{{ route('login') }}">Login Sekarang</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
