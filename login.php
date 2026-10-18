<?php
declare(strict_types=1);
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$token = (string)($_POST['csrf_token'] ?? '');
if (empty($_SESSION['login_csrf']) || !hash_equals($_SESSION['login_csrf'], $token)) {
    $_SESSION['login_error'] = 'درخواست معتبر نیست. لطفاً دوباره وارد شوید.';
    header('Location: index.php');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '' || strlen($username) > 64 || strlen($password) > 1024) {
    $_SESSION['login_error'] = 'نام کاربری یا رمز عبور نادرست است.';
    header('Location: index.php');
    exit;
}

try {
    $pdo = require __DIR__ . '/config/database.php';
    $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role, is_active FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !(int)$user['is_active'] || !password_verify($password, (string)$user['password_hash'])) {
        $_SESSION['login_error'] = 'نام کاربری یا رمز عبور نادرست است.';
        header('Location: index.php');
        exit;
    }

    if (password_needs_rehash((string)$user['password_hash'], PASSWORD_DEFAULT)) {
        $rehash = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $rehash->execute([password_hash($password, PASSWORD_DEFAULT), (int)$user['id']]);
    }

    session_regenerate_id(true);
    unset($_SESSION['login_csrf'], $_SESSION['login_error']);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = (string)$user['username'];
    $_SESSION['full_name'] = (string)$user['full_name'];
    $_SESSION['role'] = (string)$user['role'];

    $update = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?');
    $update->execute([(int)$user['id']]);
    $log = $pdo->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (?, ?, ?, ?, ?)');
    $log->execute([(int)$user['id'], 'login', 'users', (int)$user['id'], substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45)]);

    header('Location: dashboard.php');
    exit;
} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['login_error'] = 'ورود انجام نشد. تنظیمات پایگاه داده را بررسی کنید یا با مدیر سیستم تماس بگیرید.';
    header('Location: index.php');
    exit;
}
