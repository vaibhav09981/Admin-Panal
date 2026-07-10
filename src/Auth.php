<?php

namespace App;

use PDO;

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function attempt(string $email, string $password): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    public static function login(array $user): void
    {
        self::start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }

    public static function current(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
    }

    public static function requireLogin(): ?array
    {
        $user = self::current();
        if (!$user) {
            return null;
        }
        return $user;
    }
}
