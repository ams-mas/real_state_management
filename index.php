<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8">
    <title>ورود به سیستم مدیریت رهنمای معاملات</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Tahoma, Arial, sans-serif;
            color: #1f2937;
            background: #f3f5f9;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px 16px;
            background: #f3f5f9;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 32px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .06);
        }
        .brand-mark {
            width: 52px;
            height: 52px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: #1d4ed8;
            color: #fff;
            font-size: 26px;
            font-weight: 700;
        }
        h1 {
            margin: 0;
            text-align: center;
            font-size: 22px;
            line-height: 1.6;
            font-weight: 700;
        }
        .subtitle {
            margin: 8px 0 28px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.8;
        }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
        }
        input {
            width: 100%;
            min-height: 46px;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #fff;
            color: #111827;
            font: inherit;
            font-size: 14px;
            outline: none;
        }
        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .13);
        }
        .password-wrap { position: relative; }
        .password-wrap input { padding-left: 76px; }
        .toggle-password {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            padding: 5px 2px;
            border: 0;
            background: transparent;
            color: #1d4ed8;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
        }
        .submit-button {
            width: 100%;
            min-height: 46px;
            margin-top: 6px;
            border: 0;
            border-radius: 7px;
            background: #1d4ed8;
            color: #fff;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }
        .submit-button:hover { background: #1e40af; }
        .submit-button:focus-visible, .toggle-password:focus-visible {
            outline: 3px solid #93c5fd;
            outline-offset: 3px;
        }
        .footer {
            margin: 24px 0 0;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.8;
        }
        @media (max-width: 420px) {
            .login-card { padding: 26px 20px; }
            h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand-mark" aria-hidden="true">م</div>
        <h1>سیستم مدیریت رهنمای معاملات</h1>
        <p class="subtitle">برای ورود، مشخصات حساب خود را وارد کنید.</p>

        <form action="login.php" method="post" autocomplete="on">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['login_csrf'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="field">
                <label for="username">نام کاربری</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       placeholder="نام کاربری خود را وارد کنید" required autofocus>
            </div>

            <div class="field">
                <label for="password">رمز عبور</label>
                <div class="password-wrap">
                    <input id="password" name="password" type="password" autocomplete="current-password"
                           placeholder="رمز عبور خود را وارد کنید" required>
                    <button class="toggle-password" type="button" id="togglePassword"
                            aria-controls="password" aria-pressed="false">نمایش رمز</button>
                </div>
            </div>

            <button class="submit-button" type="submit">ورود به سیستم</button>
        </form>

        <p class="footer">این سیستم برای استفاده کارمندان مجاز دفتر طراحی شده است.</p>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');

        toggleButton.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            toggleButton.textContent = showPassword ? 'پنهان کردن' : 'نمایش رمز';
            toggleButton.setAttribute('aria-pressed', String(showPassword));
        });
    </script>
</body>
</html>
