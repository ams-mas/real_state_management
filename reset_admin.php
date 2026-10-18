<?php
declare(strict_types=1);

/**
 * One-time local administrator repair utility.
 * Run from the project directory in a terminal, never through a browser:
 * PowerShell:
 *   $env:ADMIN_USERNAME = "admin"
 *   $env:ADMIN_PASSWORD = "admin@1234"
 *   php reset_admin.php
 *   Remove-Item Env:ADMIN_USERNAME, Env:ADMIN_PASSWORD
 *
 * Delete this file after use.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit("Not found.\n");
}

$username = trim((string)getenv('ADMIN_USERNAME'));
$password = (string)getenv('ADMIN_PASSWORD');

if ($username === '' || $password === '') {
    fwrite(STDERR, "Set ADMIN_USERNAME and ADMIN_PASSWORD environment variables first.\n");
    exit(1);
}
if (!preg_match('/^[A-Za-z0-9_.@-]{3,64}$/', $username)) {
    fwrite(STDERR, "Invalid username format.\n");
    exit(1);
}
if (strlen($password) < 8) {
    fwrite(STDERR, "Password must be at least 8 characters.\n");
    exit(1);
}

try {
    $pdo = require __DIR__ . '/config/database.php';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $find = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $find->execute([$username]);
    $existing = $find->fetch();

    if ($existing) {
        $update = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin', is_active = 1 WHERE id = ?");
        $update->execute([$hash, (int)$existing['id']]);
        $userId = (int)$existing['id'];
        $action = 'admin_password_reset';
        echo "Existing administrator account updated.\n";
    } else {
        $insert = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role, is_active) VALUES (?, ?, ?, 'admin', 1)");
        $insert->execute([$username, $hash, 'مدیر سیستم']);
        $userId = (int)$pdo->lastInsertId();
        $action = 'admin_account_created';
        echo "Administrator account created.\n";
    }

    try {
        $log = $pdo->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)');
        $log->execute([$userId, $action, 'users', $userId, json_encode(['source' => 'local_cli_repair'], JSON_UNESCAPED_UNICODE)]);
    } catch (Throwable $ignored) {
        // Do not fail a successful credential repair if optional audit logging fails.
    }
    echo "Username: {$username}\nPassword was set from ADMIN_PASSWORD.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Repair failed. Check that installation completed and config/database.local.php is present.\n");
    error_log('Admin repair error: ' . $e->getMessage());
    exit(1);
}
