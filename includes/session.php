<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../database/scripts/database.php';
require_once __DIR__ . '/../database/scripts/user.class.php';

if (!isset($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32));}

class Session {
    private static ?Session $instance = null;

    private function __construct() {}

    public static function getInstance(): Session {
        if (self::$instance === null) self::$instance = new Session();
        return self::$instance;
    }

    public function login(User $user): void {
        $_SESSION['user_id'] = $user->id;
    }

    public function logout(): void {
        unset($_SESSION['user_id']);
    }

    public function getUser(): ?User {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        return User::getUser((int)$_SESSION['user_id']);
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isClient(): bool {
        $user = $this->getUser();
        return $user !== null && User::isClient($user->id);
    }

    public function isFreelancer(): bool {
        $user = $this->getUser();
        return $user !== null && User::isFreelancer($user->id);
    }

    public function isAdmin(): bool {
        $user = $this->getUser();
        return $user !== null && User::isAdmin($user->id);
    }

    public function getCsrfToken(): string {
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken(string $token): bool {
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}