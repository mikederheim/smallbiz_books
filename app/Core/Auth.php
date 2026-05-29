<?php
class Auth {
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function check(): bool { return isset($_SESSION['user']); }
    public static function requireLogin(): void { if (!self::check()) { header('Location: index.php?r=login'); exit; } }
    public static function login(string $email, string $password): bool {
        $stmt = Database::get()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email']];
            return true;
        }
        return false;
    }
    public static function logout(): void { session_destroy(); }
}
