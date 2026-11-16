<?php
declare(strict_types=1);

/* Yönetici kimlik doğrulama işlemleri */

const LOGIN_MAX_ATTEMPTS = 6;
const LOGIN_WINDOW_MINUTES = 15;

function admin_count(): int
{
    return (int) db_value('SELECT COUNT(*) FROM admins');
}

function current_admin(): ?array
{
    static $admin = false;
    if ($admin !== false) {
        return $admin;
    }
    $id = $_SESSION['admin_id'] ?? null;
    if (!is_int($id)) {
        return $admin = null;
    }
    $idle = max(5, (int) config('app.admin_idle_minutes', 120)) * 60;
    $last = (int) ($_SESSION['admin_last_activity'] ?? 0);
    if ($last === 0 || time() - $last > $idle) {
        admin_logout();
        return $admin = null;
    }
    $_SESSION['admin_last_activity'] = time();
    $row = db_one('SELECT id, username, email, last_login_at FROM admins WHERE id = ?', [$id]);
    if ($row === null) {
        admin_logout();
    }
    return $admin = $row;
}

function login_locked(string $ip): bool
{
    db_exec('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
    $count = (int) db_value(
        'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > (NOW() - INTERVAL ' . LOGIN_WINDOW_MINUTES . ' MINUTE)',
        [$ip]
    );
    return $count >= LOGIN_MAX_ATTEMPTS;
}

/** @return 'ok'|'invalid'|'locked' */
function attempt_login(string $username, string $password): string
{
    $ip = client_ip();
    if (login_locked($ip)) {
        return 'locked';
    }
    $user = db_one('SELECT id, password_hash FROM admins WHERE username = ? OR email = ? LIMIT 1', [$username, $username]);
    // Kullanıcı yoksa da aynı sürede yanıt vermek için sahte doğrulama yapılır.
    $hash = $user['password_hash'] ?? password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT);
    $valid = password_verify($password, $hash) && $user !== null;

    if (!$valid) {
        db_exec('INSERT INTO login_attempts (ip, username) VALUES (?, ?)', [$ip, mb_substr($username, 0, 100)]);
        return 'invalid';
    }

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        db_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
    }
    db_exec('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
    db_exec('UPDATE admins SET last_login_at = NOW() WHERE id = ?', [$user['id']]);

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $user['id'];
    $_SESSION['admin_last_activity'] = time();
    unset($_SESSION['_csrf']);
    return 'ok';
}

function admin_logout(): void
{
    unset($_SESSION['admin_id'], $_SESSION['admin_last_activity'], $_SESSION['_csrf']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Yönetici hesabı oluşturur.
 * @return array<int,string> Hata mesajları (boşsa başarılı)
 */
function create_admin(string $username, string $email, string $password, string $passwordConfirm): array
{
    $errors = [];
    if (!preg_match('/^[a-zA-Z0-9_.\-]{3,50}$/', $username)) {
        $errors[] = 'Kullanıcı adı 3-50 karakter olmalı; yalnızca harf, rakam, nokta, tire ve alt çizgi içerebilir.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errors[] = 'Geçerli bir e-posta adresi girin.';
    }
    $errors = array_merge($errors, password_problems($password, $passwordConfirm));
    if ($errors === [] && db_value('SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?', [$username, $email])) {
        $errors[] = 'Bu kullanıcı adı veya e-posta zaten kayıtlı.';
    }
    if ($errors === []) {
        db_exec('INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)', [
            $username, $email, password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
    return $errors;
}

/** @return array<int,string> */
function password_problems(string $password, string $confirm): array
{
    $errors = [];
    if (mb_strlen($password) < 10) {
        $errors[] = 'Şifre en az 10 karakter olmalı.';
    } elseif (!preg_match('/\p{L}/u', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Şifre en az bir harf ve bir rakam içermeli.';
    }
    if (strlen($password) > 200) {
        $errors[] = 'Şifre çok uzun.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Şifreler birbiriyle eşleşmiyor.';
    }
    return $errors;
}
