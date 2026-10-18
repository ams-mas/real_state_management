<?php
declare(strict_types=1);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$token = (string)($_POST['csrf_token'] ?? '');
if (empty($_SESSION['login_csrf']) || !hash_equals((string)$_SESSION['login_csrf'], $token)) {
    $_SESSION['login_error'] = 'درخواست معتبر نیست. دوباره تلاش کنید.';
    header('Location: index.php');
    exit;
}
$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');
if ($username === '' || $password === '' || strlen($username) > 64) {
    $_SESSION['login_error'] = 'نام کاربری یا رمز عبور نادرست است.';
    header('Location: index.php');
    exit;
}
try {
    $pdo = require __DIR__ . '/config/database.php';
    $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role, is_active FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    if (!$user || !(int)$user['is_active'] || !password_verify($password, (string)$user['password_hash'])) {
        $_SESSION['login_error'] = 'نام کاربری یا رمز عبور نادرست است.';
        header('Location: index.php');
        exit;
    }
    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int)$user['id'],
        'username' => (string)$user['username'],
        'full_name' => (string)$user['full_name'],
        'role' => (string)$user['role'],
        'dashboard_csrf' => bin2hex(random_bytes(32)),
    ];
    $update = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
    $update->execute(['id' => (int)$user['id']]);
    $log = $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (:id, :action, :details)');
    $log->execute(['id' => (int)$user['id'], 'action' => 'login', 'details' => json_encode(['message' => 'Successful login'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
    header('Location: dashboard.php');
    exit;
} catch (Throwable $e) {
    error_log('Login failed: ' . $e->getMessage());
    $_SESSION['login_error'] = 'ورود ممکن نشد. پایگاه داده را بررسی کنید.';
    header('Location: index.php');
    exit;
}
