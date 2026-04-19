<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahipso Clinic Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --text: #000000;
            --muted: #000000;
            --line: #d7d7d7;
            --brand: #bf3a30;
            --brand-deep: #8d241d;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Poppins", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: var(--text);
            background: #f4f6f8;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(217, 224, 234, 0.9);
            border-radius: 4px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
            overflow: hidden;
        }

        .auth-topline {
            height: 4px;
            background: linear-gradient(90deg, var(--brand), #ef8d75);
        }

        .auth-body {
            padding: 1.7rem;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1.2rem;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            overflow: hidden;
            border: 1px solid var(--line);
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-brand h1 {
            margin: 0;
            font-size: 1rem;
        }

        .auth-brand p,
        .auth-subtitle,
        .auth-note {
            margin: 0.2rem 0 0;
            color: var(--muted);
        }

        .auth-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .auth-subtitle {
            margin-top: 0.4rem;
            line-height: 1.5;
        }

        .alert {
            padding: 0.95rem 1rem;
            border-radius: 4px;
            border: 1px solid #f0cccc;
            background: #fff2f2;
            color: #000000;
            margin: 1.2rem 0;
        }

        .alert ul {
            margin: 0;
            padding-left: 1.1rem;
        }

        .field {
            margin-top: 1rem;
        }

        .field label {
            display: block;
            margin-bottom: 0.45rem;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .field input {
            width: 100%;
            border: 1px solid #d9e0ea;
            background: #f9fbfd;
            color: var(--text);
            border-radius: 4px;
            padding: 0.9rem 0.95rem;
        }

        .field input:focus {
            outline: none;
            border-color: #000000;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            font-size: 0.92rem;
        }

        .remember-row a {
            color: #000000;
            text-decoration: none;
            font-weight: 600;
        }

        .submit-button {
            width: 100%;
            margin-top: 1.2rem;
            padding: 0.92rem 1rem;
            border: none;
            border-radius: 4px;
            background: #000000;
            color: white;
            font-weight: 700;
            cursor: pointer;
            box-shadow: none;
        }

        .auth-footer {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border-top: 1px solid var(--line);
            padding-top: 1rem;
            margin-top: 1.2rem;
            font-size: 0.82rem;
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-topline"></div>
            <div class="auth-body">
                <div class="auth-brand">
                    <div class="brand-mark">
                        <img src="{{ asset('mahipso-logo.png') }}" alt="Mahipso Clinic logo">
                    </div>
                    <div>
                        <h1>Mahipso Clinic</h1>
                        <p>Staff Portal</p>
                    </div>
                </div>

                <h2 class="auth-title">Secure login</h2>
                <p class="auth-subtitle">Sign in to access your clinic dashboard, patients, appointments, and records.</p>

                @if ($errors->any())
                    <div class="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert" style="background:#ffffff;color:#000000;border-color:#000000;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <div class="remember-row">
                        <label>
                            <input type="checkbox" name="remember"> Keep me signed in
                        </label>
                        <a href="#">Forgot password?</a>
                    </div>

                    <button class="submit-button" type="submit">Sign In</button>
                </form>

                <div class="auth-footer">
                    <div class="auth-note">Access is provisioned by the administrator.</div>
                    <div class="auth-note">Mahipso Clinic management system</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
