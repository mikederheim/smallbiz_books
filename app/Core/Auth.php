<?php
class Auth {
    public static function user(): ?array { return $_SESSION['user'] ?? null; }

    public static function hasUsers(): bool {
        $stmt = Database::get()->query('SELECT COUNT(*) AS c FROM users');
        $row = $stmt->fetch();
        return ((int)($row['c'] ?? 0)) > 0;
    }

    public static function check(): bool { return isset($_SESSION['user']); }

    public static function requireLogin(): void {
        if (!self::check()) { header('Location: index.php?r=login'); exit; }
    }

    public static function login(string $email, string $password): bool {
        $stmt = Database::get()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $isMike = strtolower((string)$user['email']) === 'mike.derheim@mivent.com';
            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_superuser' => $isMike || !empty($user['is_superuser']),
                'can_manage_users' => $isMike || !empty($user['can_manage_users']),
                'can_manage_companies' => $isMike || !empty($user['can_manage_companies']),
                'can_view_reports' => $isMike || (array_key_exists('can_view_reports', $user) ? !empty($user['can_view_reports']) : true),
                'can_manage_transactions' => $isMike || (array_key_exists('can_manage_transactions', $user) ? !empty($user['can_manage_transactions']) : true),
            ];
            return true;
        }
        return false;
    }

    public static function logout(): void { session_destroy(); }

    public static function isSuperuser(): bool {
        $u = self::user();
        return $u && (strtolower((string)$u['email']) === 'mike.derheim@mivent.com' || !empty($u['is_superuser']));
    }

    public static function can(string $permission): bool {
        if (self::isSuperuser()) return true;
        $u = self::user();
        return $u && !empty($u[$permission]);
    }

    public static function requirePermission(string $permission): void {
        self::requireLogin();
        if (!self::can($permission)) die('Access denied');
    }

    public static function canAccessCompany(int $companyId): bool {
        if (self::isSuperuser()) return true;
        $u = self::user();
        if (!$u || $companyId <= 0) return false;
        $stmt = Database::get()->prepare('SELECT 1 FROM user_company_access WHERE user_id=? AND company_id=? LIMIT 1');
        $stmt->execute([(int)$u['id'], $companyId]);
        return (bool)$stmt->fetchColumn();
    }
}
